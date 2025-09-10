<?php
session_start();
session_unset(); // Limpa as variáveis da sessão
session_destroy(); // Destroi a sessão

// Redireciona para a página de login ou inicial
header("Location: ../FrontEnd/index.html");
exit;
?>
