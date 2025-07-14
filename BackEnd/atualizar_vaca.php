<?php
require("conexao.php");
header('Content-Type: application/json');

//definindo variaveis e caso não as encontre define null por padrao
$id_vaca = $_POST['id_vaca'] ?? null;
$novo_nome = $_POST['novo_nome'] ?? null;

if (!$id_vaca || !$novo_nome) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

try {
    $stmt = $banco->prepare("UPDATE vacas SET nome = :nome WHERE id_vaca = :id");
    $stmt->bindParam(':nome', $novo_nome);
    $stmt->bindParam(':id', $id_vaca);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>