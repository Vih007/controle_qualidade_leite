<?php
// Arquivo: conexao.php

// ATENÇÃO: Verifique o caminho. Assume que monitorar_saude.php está no mesmo diretório ou em um caminho acessível.
require 'monitorar_saude.php'; 

// --- CONFIGURAÇÕES DO BANCO DE DADOS (AJUSTE) ---
$db_host = 'localhost'; 
$db_user = 'root'; 
$db_pass = ''; // Senha do MySQL (vazia por padrão no XAMPP)
$db_name = 'cql_ifpe1'; // Nome do seu banco de dados

// Variável global de conexão (será um objeto PDO)
$banco = null;

try {
    // Tenta conectar
    $banco = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $banco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Opcional: Se a conexão for bem-sucedida, você pode limpar logs antigos aqui, mas a limpeza 
    // já é tratada dentro da função registrar_e_verificar_falha para simplificar.

} catch (PDOException $e) {
    
    // --- LÓGICA DE DETECÇÃO DE DOs (APÓS FALHA) ---
    // REGISTRA a falha no log e VERIFICA se o limite foi atingido.
    registrar_e_verificar_falha($e);
    
    // Se o limite não foi atingido, apenas exibe o erro padrão e encerra a execução
    // Isso é feito APENAS se a função registrar_e_verificar_falha não tiver forçado um die(503)
    die("Falha na conexão com o banco de dados. O sistema pode estar sob sobrecarga. Código: " . $e->getCode());
}
?>