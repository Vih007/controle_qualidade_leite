<?php
// BackEnd/logar.php

// ATENÇÃO: Removemos session_set_cookie_params() daqui para resolver os conflitos de ordem de execução.
// A segurança será aplicada via setcookie() manual após o login bem-sucedido.
session_start(); 

require_once __DIR__ . '/conexao.php';
include __DIR__ . '/criar_alerta.php';

$tempoBloqueio = 300; // 5 minutos em segundos
$maxTentativas = 3;

// Inicializa as variáveis de sessão se ainda não existem
if (!isset($_SESSION['tentativas'])) {
    $_SESSION['tentativas'] = 0;
}
if (!isset($_SESSION['bloqueado_ate'])) {
    $_SESSION['bloqueado_ate'] = 0;
}

// Verifica se o usuário está bloqueado
if (time() < $_SESSION['bloqueado_ate']) {
    $tempoRestante = $_SESSION['bloqueado_ate'] - time();
    criar_alerta("Você errou a senha várias vezes. Aguarde $tempoRestante segundos para tentar novamente.", "erro");
    header("Location: ../FrontEnd/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha_digitada = filter_input(INPUT_POST, 'senha', FILTER_UNSAFE_RAW);

    // Verifica se algum campo está vazio
    if (empty($email) || empty($senha_digitada)) {
        criar_alerta("Por favor, preencha todos os campos.", "erro");
        header("Location: ../FrontEnd/login.php");
        exit();
    }
    
    // --- LÓGICA DE LOGIN COM RETRY E EXCEPTION DETECTION (DISPONIBILIDADE) ---
    $max_db_retries = 3; 
    $db_retries = 0;
    $login_succeeded = false;
    $usuario = false;
    $db_connection_failed = false;
    $error_message = "Erro no servidor. Tente novamente mais tarde.";

    while ($db_retries < $max_db_retries && !$login_succeeded) {
        try {
            if (is_null($banco)) {
                throw new PDOException("Falha na conexão com o banco de dados.");
            }

            $stmt = $banco->prepare("SELECT id_usuario, nome, email, senha, tipo_usuario FROM usuarios WHERE email = :email");
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $db_connection_failed = false; 

            if ($usuario && password_verify($senha_digitada, $usuario['senha'])) {
                $login_succeeded = true;
            }
            break; 
            
        } catch (PDOException $e) {
            $db_retries++;
            $db_connection_failed = true;
            error_log("Erro de conexão/DB (Tentativa $db_retries/$max_db_retries): " . $e->getMessage());
            
            if ($db_retries < $max_db_retries) {
                sleep(1);
            }
        }
    }
    // --- FIM DA SOLUÇÃO DE DISPONIBILIDADE ---

    // LÓGICA DE RESPOSTA FINAL
    if ($login_succeeded) {
        // --- TÁTICA: LIMITAR EXPOSIÇÃO - SOBRESCRITA MANUAL DO COOKIE ---
        // Garante que o cookie PHPSESSID seja enviado com a flag HttpOnly=true
        // Isso resolve o problema de o navegador ignorar a configuração de segurança.
        
        $session_name = session_name();
        $session_id = session_id();
        $session_expire = 0; // Expira ao fechar o navegador
        $secure = false; // Mantenha 'false' para HTTP local. Em produção, use 'true'.
        $httponly = true; // A flag crítica para defesa contra XSS/Roubo de Sessão.

        setcookie(
            $session_name, 
            $session_id, 
            $session_expire, 
            '/', 
            '', 
            $secure, 
            $httponly
        );
        // -------------------------------------------------------------------

        // Login bem-sucedido: limpa as tentativas e prossegue
        $_SESSION['tentativas'] = 0;
        $_SESSION['bloqueado_ate'] = 0;

        // Armazena dados do usuário na sessão
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

        criar_alerta("Bem-vindo(a), " . htmlspecialchars($usuario['nome']) . "!", "sucesso");

        // Redireciona conforme tipo de usuário
        if ($usuario['tipo_usuario'] === 'aluno') {
            header("Location: ../FrontEnd/aluno/area_comum_aluno.php");
        } elseif ($usuario['tipo_usuario'] === 'professor') {
            header("Location: ../FrontEnd/professor/area_professor.php");
        } else {
            criar_alerta("Erro: Tipo de usuário inválido.", "erro");
            header("Location: ../FrontEnd/login.php");
        }
        exit();
    } elseif ($db_connection_failed) {
        // Falha após todas as retries de conexão/DB (Disponibilidade Baixa no Servidor)
        criar_alerta($error_message, "erro");
        header("Location: ../FrontEnd/login.php");
        exit();
    } else {
        // Falha de credenciais (lógica original)
        $_SESSION['tentativas']++;

        if ($_SESSION['tentativas'] >= $maxTentativas) {
            $_SESSION['bloqueado_ate'] = time() + $tempoBloqueio;
            $_SESSION['tentativas'] = 0;
            criar_alerta("Você errou a senha 3 vezes. Aguarde $tempoBloqueio segundos para tentar novamente.", "erro");
        } else {
            $resta = $maxTentativas - $_SESSION['tentativas'];
            criar_alerta("E-mail ou senha incorretos. Você ainda pode tentar mais $resta vez(es).", "erro");
        }

        header("Location: ../FrontEnd/login.php");
        exit();
    }
} else {
    // Redireciona se a requisição não for POST
    header("Location: ../FrontEnd/login.php");
    exit();
}