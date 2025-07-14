<?php
$root = "root"; // Usuário do banco de dados
$sua_senha = ""; // Senha do banco de dados
$nome_banco = "cql_ifpe1"; // Nome do banco de dados

try {
    // Cria uma nova conexão PDO com o banco específico e configura charset UTF-8
    $banco = new PDO("mysql:host=localhost;dbname=$nome_banco;charset=utf8", $root, $sua_senha);
    // Define o modo padrão de busca como associativo
    $banco->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Configura o PDO para lançar exceções em erros
    $banco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexão bem-sucedida";

} catch (PDOException $e) {
    // Termina o script mostrando mensagem de erro na conexão
    die("Erro na conexão: " . $e->getMessage()); 
}
?>

