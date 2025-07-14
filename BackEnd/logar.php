<?php
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

    try {
        // Busca usuário no banco pelo email
        $stmt = $banco->prepare("SELECT id_usuario, nome, email, senha, tipo_usuario FROM usuarios WHERE email = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se usuário existe e senha confere
        if ($usuario && password_verify($senha_digitada, $usuario['senha'])) {
            // Login bem-sucedido: limpa as tentativas
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
        } else {
            // Falha no login: incrementa tentativas
            $_SESSION['tentativas']++;

            // Se atingir o máximo de tentativas, bloqueia por um tempo
            if ($_SESSION['tentativas'] >= $maxTentativas) {
                $_SESSION['bloqueado_ate'] = time() + $tempoBloqueio;
                $_SESSION['tentativas'] = 0;
                criar_alerta("Você errou a senha 3 vezes. Aguarde $tempoBloqueio segundos para tentar novamente.", "erro");
            } else {
                // Mostra quantas tentativas ainda restam
                $resta = $maxTentativas - $_SESSION['tentativas'];
                criar_alerta("E-mail ou senha incorretos. Você ainda pode tentar mais $resta vez(es).", "erro");
            }

            header("Location: ../FrontEnd/login.php");
            exit();
        }
    } catch (PDOException $e) {
        // Registra erro e avisa usuário
        error_log("Erro de login no DB: " . $e->getMessage());
        criar_alerta("Erro no servidor. Por favor, tente novamente mais tarde.", "erro");
        header("Location: ../FrontEnd/login.php");
        exit();
    }
} else {
    // Redireciona se a requisição não for POST
    header("Location: ../FrontEnd/login.php");
    exit();
}
?>
