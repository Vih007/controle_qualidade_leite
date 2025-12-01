<?php
// Arquivo: conexao.php
// Versão sem dependência em monitorar_saude.php

// --- CONFIGURAÇÕES DO BANCO DE DADOS (AJUSTE) ---
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'cql_ifpe1';

// Variável global de conexão (será um objeto PDO)
$banco = null;

try {
    // Tenta conectar
    $banco = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $banco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Erro simples e explícito — sem chamar funções inexistentes
    die("Falha na conexão com o banco de dados: " . $e->getMessage());
}
?>
