<?php
require(__DIR__ . '/conexao_sem_banco.php'); // Inclui conexão sem banco definido

// Cria o banco de dados se não existir
$stmt = $banco->prepare("CREATE DATABASE IF NOT EXISTS cql_ifpe1");

// Executa a criação e mostra mensagem conforme resultado
if ($stmt->execute()) {
    echo "Banco criado com sucesso.";
} else {
    echo "Erro na criação do banco.";
    print_r($stmt->errorInfo()); // Mostra detalhes do erro
}
$stmt->closeCursor(); // Libera o cursor da query
?>