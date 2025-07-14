<?php
// Importa as classes necessárias do PHPMailer
require '../BackEnd/PHPMailer-master/src/PHPMailer.php';
require '../BackEnd/PHPMailer-master/src/SMTP.php';
require '../BackEnd/PHPMailer-master/src/Exception.php';

// Conexão com o banco de dados
require '../BackEnd/conexao.php';
// Inclui o script para criação de alertas (caso seja utilizado)
include("../BackEnd/criar_alerta.php");

// Usa as classes do PHPMailer de forma organizada com namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Variável que armazenará a mensagem final de retorno (sucesso ou erro)
$mensagem = '';

//Verifica se o formulário foi enviado e se o campo de e-mail está preenchido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    $email = $_POST['email'];

    //Busca o usuário com o e-mail informado
    $stmt = $banco->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    //Se o usuário existe, gera o token e armazena no banco
    if ($usuario) {
        $token = bin2hex(random_bytes(16));
        $id_usuario = $usuario['id_usuario'];
        $data_expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

        //Insere o token no banco na tabela de recuperação
        $stmt = $banco->prepare("INSERT INTO recuperacao_senha (id_usuario, token, data_expiracao) VALUES (:id_usuario, :token, :data_expiracao)");
        $stmt->bindValue(':id_usuario', $id_usuario);
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':data_expiracao', $data_expiracao);
        $stmt->execute();

        //Cria o link com o token gerado para redefinição da senha
        $link = "http://localhost/projetoWeb/FrontEnd/resetar_senha.php?token=$token";

        //Envia o e-mail usando PHPMailer
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8'; // Mantendo a melhoria do novo código
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'melovitoria763@gmail.com';
            $mail->Password = 'zphyxqjhpdqyukli';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->setFrom('melovitoria763@gmail.com', 'Controle de Qualidade do Leite');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Recuperação de senha';
            $mail->Body = "Clique <a href='$link'>aqui</a> para redefinir sua senha.";
            $mail->send();

            $mensagem = "Email enviado com sucesso para $email.";
        } catch (Exception $e) {
            $mensagem = "Erro ao enviar e-mail: {$mail->ErrorInfo}";
        }
    } else {
        $mensagem = "Email não cadastrado.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = "Informe um email válido.";
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Recuperar Senha</title>
  <link rel="shortcut icon" href="imgs/vacaFavicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="style/login.css" />
</head>
<body>
  <main class="fundo-login"> <section class="caixa-login">
      <figure class="imagem-vaca"> <img src="imgs/vacalogin.png" alt="Imagem decorativa de vacas" />
      </figure>
      <h1 class="titulo-recuperacao">Recuperar Senha</h1> <p>Digite o e-mail cadastrado para receber um link de redefinição de senha.</p>
      <?php if (!empty($mensagem)): ?>
        <p class="mensagem"><?php echo htmlspecialchars($mensagem); ?></p> <?php endif; ?>
      <form method="POST"> <label for="email">E-mail</label>
         <!-- Formulário de recuperação de senha -->
        <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required />
        <button type="submit" class="btn">Enviar Link</button>
      </form>
      <nav><a href="login.php">Voltar para login</a></nav>
    </section>
  </main>
</body>
</html>