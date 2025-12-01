<?php

// --- INCLUSÃO DAS CLASSES PHPMailer ---
// ATENÇÃO: Verifique e ajuste o caminho abaixo de acordo com a sua estrutura de pastas!
// Se o seu gerar_alertas.php estiver na mesma pasta que recuperar_senha.php, este caminho
// é o mesmo que você usou no outro script:
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../BackEnd/PHPMailer-master/src/Exception.php';
require '../BackEnd/PHPMailer-master/src/PHPMailer.php';
require '../BackEnd/PHPMailer-master/src/SMTP.php';


// --- 1. CONFIGURAÇÕES GERAIS ---

// Configurações do Banco de Dados (Adapte se necessário)
$db_host = 'localhost'; 
$db_user = 'root'; 
$db_pass = ''; 
$db_name = 'cql_ifpe1'; // <--- MANTENHA O NOME DO SEU BANCO DE DADOS

// Parâmetros de Detecção
$limite_tentativas = 10;
$periodo_minutos = 5;

// Destinatário do Alerta (Deve ser o e-mail do Professor/Desenvolvedor)
$destinatario_alerta = 'melovitoria763@gmail.com'; // <--- ALTERE PARA O E-MAIL DO DESTINATÁRIO DO ALERTA!


// --- Configurações SMTP do PHPMailer (PREENCHIDO COM SUAS INFORMAÇÕES) ---
$smtp_host     = 'smtp.gmail.com'; 
$smtp_user     = 'melovitoria763@gmail.com';
$smtp_password = 'zphyxqjhpdqyukli'; // Esta é a App Password do seu Gmail
$smtp_port     = 465;
$smtp_secure   = PHPMailer::ENCRYPTION_SMTPS;


// --- 2. CONEXÃO COM O BANCO DE DADOS ---

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de Conexão com o Banco de Dados: " . $e->getMessage() . "\n");
}

// --- 3. CONSULTA DE DETECÇÃO ---

$sql = "
    SELECT 
        ip_origem, 
        COUNT(*) as total_tentativas,
        MAX(data_hora) as ultima_tentativa
    FROM 
        tentativas_login_falhas
    WHERE 
        data_hora > DATE_SUB(NOW(), INTERVAL :periodo MINUTE)
        AND alerta_gerado = 0
    GROUP BY 
        ip_origem
    HAVING 
        total_tentativas >= :limite;
";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':periodo', $periodo_minutos, PDO::PARAM_INT);
$stmt->bindParam(':limite', $limite_tentativas, PDO::PARAM_INT);
$stmt->execute();
$ips_suspeitos = $stmt->fetchAll(PDO::FETCH_ASSOC);


// --- 4. PROCESSAMENTO E ALERTA ---

if (count($ips_suspeitos) > 0) {
    echo "Ataque(s) de Força Bruta detectado(s). Enviando alerta...\n";
    
    foreach ($ips_suspeitos as $ataque) {
        $ip = $ataque['ip_origem'];
        $total = $ataque['total_tentativas'];
        $ultima = $ataque['ultima_tentativa'];

        $assunto = "[ALERTA SEGURANÇA] Força Bruta - IP {$ip}";

        $mensagem = "ATENÇÃO: Ataque de Força Bruta detectado no sistema de login!\n\n";
        $mensagem .= "Detalhes do Ataque:\n";
        $mensagem .= "-----------------------------------------\n";
        $mensagem .= "IP Suspeito: " . $ip . "\n";
        $mensagem .= "Tentativas Falhas: " . $total . " (em {$periodo_minutos} minutos)\n";
        $mensagem .= "Última Tentativa: " . $ultima . "\n";
        $mensagem .= "Ação Recomendada: Bloqueio imediato do IP no firewall.\n";
        $mensagem .= "-----------------------------------------\n";

        
        // b) Enviar o E-mail usando PHPMailer
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $email_enviado = false;
        
        try {
            // Configurações do Servidor SMTP
            $mail->isSMTP();
            $mail->Host       = $smtp_host; 
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp_user; 
            $mail->Password   = $smtp_password; 
            $mail->SMTPSecure = $smtp_secure; 
            $mail->Port       = $smtp_port;

            // Destinatários e Conteúdo
            $mail->setFrom($smtp_user, 'Sistema de Segurança - CQ Leite');
            $mail->addAddress($destinatario_alerta); 

            $mail->isHTML(false); // Texto simples
            $mail->Subject = $assunto;
            $mail->Body    = $mensagem;
            
            $mail->send();
            $email_enviado = true;
            
        } catch (Exception $e) {
            error_log("PHPMailer Error para IP {$ip}: {$mail->ErrorInfo}");
            echo "ERRO PHPMailer: Falha ao enviar o alerta por e-mail para {$ip}. Detalhes: {$mail->ErrorInfo}\n";
            $email_enviado = false;
        }

        // c) Marcar os Registros como Alertados SOMENTE se o e-mail foi enviado
        if ($email_enviado) {
            $sql_update = "
                UPDATE tentativas_login_falhas
                SET alerta_gerado = 1
                WHERE ip_origem = :ip 
                AND data_hora > DATE_SUB(NOW(), INTERVAL :periodo MINUTE)
                AND alerta_gerado = 0;
            ";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->bindParam(':ip', $ip);
            $stmt_update->bindParam(':periodo', $periodo_minutos, PDO::PARAM_INT);
            $stmt_update->execute();
            echo "Alerta enviado e registros atualizados para o IP {$ip}.\n";
        } 
    }
} else {
    echo "Nenhum ataque de Força Bruta detectado neste período.\n";
}


// --- 5. LIMPEZA DE DADOS ANTIGOS (OPCIONAL) ---
$dias_limpeza = 7;
$sql_limpeza = "DELETE FROM tentativas_login_falhas WHERE data_hora < DATE_SUB(NOW(), INTERVAL :dias DAY);";
$stmt_limpeza = $pdo->prepare($sql_limpeza);
$stmt_limpeza->bindParam(':dias', $dias_limpeza, PDO::PARAM_INT);
$stmt_limpeza->execute();

?>