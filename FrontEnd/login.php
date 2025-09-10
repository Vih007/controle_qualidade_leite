<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="shortcut icon" href="imgs/vacaFavicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="style/variaveis.css" />
  <link rel="stylesheet" href="style/login.css" />
</head>
<body>
  <main class="fundo-login"> <section class="caixa-login"> <figure class="imagem-vaca"> <img src="imgs/vacalogin.png" alt="Imagem decorativa de vacas" />
      </figure>
      <h1 class="titulo-recuperacao">Login</h1> <p>Digite os seus dados de acesso no campo abaixo.</p>
      <?php include('mensagem.php'); ?> <form action="../BackEnd/logar.php" method="POST">
        <!--formulário de login-->
        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" placeholder="Digite seu e-mail" required autofocus />
        <label for="senha">Senha</label>
        <input id="senha" name="senha" type="password" placeholder="Digite sua senha" required />
        <nav><a href="esqueci.php">Esqueci minha senha</a></nav> <button type="submit" class="btn">Acessar</button>
      </form>
    </section>
  </main>
</body>
</html>