<?php
require('../BackEnd/conexao.php');
$mensagem = '';

// Captura o token tanto do GET quanto do POST
$token = $_POST['token'] ?? $_GET['token'] ?? '';

// Se não tiver token, já mostra erro
if (empty($token)) {
    $mensagem = "Token inválido.";
} else {
    // Verifica se o token existe e está válido
    $stmt = $banco->prepare("SELECT id_usuario, data_expiracao FROM recuperacao_senha WHERE token = :token AND usado = 0");
    $stmt->bindValue(':token', $token);
    $stmt->execute();
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$registro) {
        $mensagem = "Token inválido ou já utilizado.";
    } elseif (new DateTime() > new DateTime($registro['data_expiracao'])) {
        $mensagem = "Token expirado. Solicite um novo link de recuperação.";
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Se o formulário foi enviado, processa
        $novaSenha = $_POST['senha'] ?? '';
        $confSenha = $_POST['conf_senha'] ?? '';

        if ($novaSenha !== $confSenha) {
            $mensagem = "As senhas não coincidem.";
        } elseif (strlen($novaSenha) < 6) {
            $mensagem = "A senha deve ter pelo menos 6 caracteres.";
        } else {
            // Atualiza a senha no banco
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

            $stmt = $banco->prepare("UPDATE usuarios SET senha = :senha WHERE id_usuario = :id_usuario");
            $stmt->bindValue(':senha', $senhaHash);
            $stmt->bindValue(':id_usuario', $registro['id_usuario']);
            $stmt->execute();

            // Marca o token como usado
            $stmt = $banco->prepare("UPDATE recuperacao_senha SET usado = 1 WHERE token = :token");
            $stmt->bindValue(':token', $token);
            $stmt->execute();

            $mensagem = "Senha atualizada com sucesso.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Redefinir Senha</title>
  <link rel="shortcut icon" href="imgs/vacaFavicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="style/variaveis.css" />
  <link rel="stylesheet" href="style/login.css" />
</head>
<body>
  <main class="fundo-login">
    <section class="caixa-login">
      <figure class="imagem-vaca">
        <img src="imgs/vacalogin.png" alt="Imagem decorativa de vacas" />
      </figure>
      <h1 class="titulo-recuperacao">Redefinir Senha</h1>
      <?php if (!empty($mensagem)): ?>
        <p class="mensagem"><?php echo htmlspecialchars($mensagem); ?></p>
      <?php endif; ?>
      <?php if (empty($mensagem) || str_contains($mensagem, 'senhas')): ?>
        <form method="POST">
          <!-- formulario para renovar senha-->
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>" />
          <label for="senha">Nova senha</label>
          <input type="password" id="senha" name="senha" placeholder="Digite sua nova senha" required />
          <label for="conf_senha">Confirmar senha</label>
          <input type="password" id="conf_senha" name="conf_senha" placeholder="Confirme a nova senha" required />
          <button type="submit" class="btn">Alterar Senha</button>
        </form>
      <?php endif; ?>
      <nav><a href="login.php">Voltar para login</a></nav>
    </section>
  </main>
</body>
</html>