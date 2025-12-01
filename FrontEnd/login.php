<?php
// FrontEnd/login.php

// --- TÁTICA: LIMITAR EXPOSIÇÃO - Configuração de Cookie Seguro ---
// Esta função DEVE vir antes de session_start() para aplicar as flags de segurança.
session_set_cookie_params([
    'lifetime' => 0, // Duração até o fechamento do navegador
    'path' => '/',
    // 'secure' => false (0) é usado para ambiente HTTP local (XAMPP/WAMP).
    // EM PRODUÇÃO (HTTPS), DEVE SER 'true' (1).
    'secure' => false, 
    'httponly' => true, // ESSENCIAL: Impede que JavaScript acesse o cookie (Defesa contra Roubo de Sessão/XSS).
    'samesite' => 'Lax' 
]);

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