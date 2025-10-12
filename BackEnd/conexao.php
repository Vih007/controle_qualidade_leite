<?php
$root = "root"; // Usuário do banco de dados
$sua_senha = ""; // Senha do banco de dados
$nome_banco = "cql_ifpe1"; // Nome do banco de dados

$banco = null; // Inicializa a variável para garantir que ela exista

try {
    // Cria uma nova conexão PDO com o banco específico e configura charset UTF-8
    $banco = new PDO("mysql:host=localhost;dbname=$nome_banco;charset=utf8", $root, $sua_senha);
    // Define o modo padrão de busca como associativo
    $banco->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Configura o PDO para lançar exceções em erros
    $banco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexão bem-sucedida";

} catch (PDOException $e) {
    // Em ambiente de teste: Apenas registra o erro e continua o script para ser tratado no logar.php
    error_log("Erro de conexão (teste): " . $e->getMessage()); 
    // A variável $banco permanece como null
}
