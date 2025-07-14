<?php
try {
    // Cria uma nova conexão PDO com o banco MySQL no localhost, usando UTF-8
    $banco = new PDO("mysql:host=localhost;charset=utf8", "root", "");
    // Configura para lançar exceções em caso de erro no banco
    $banco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Se der erro na conexão, exibe a mensagem e termina o script
    die("Erro na conexão: " . $e->getMessage());
}
?>
