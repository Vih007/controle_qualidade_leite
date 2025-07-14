<?php
require("conexao.php");
header('Content-Type: application/json');

$id_vaca = $_POST['id_vaca'] ?? null; // Recebe o ID da vaca ou null

// Verifica se o ID foi fornecido
if (!$id_vaca) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit;
}

try {
    // Prepara a query para deletar a vaca pelo ID
    $stmt = $banco->prepare("DELETE FROM vacas WHERE id_vaca = :id");
    $stmt->bindValue(':id', $id_vaca, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) { // Verifica se algum registro foi deletado
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nenhum registro encontrado com este ID.']);
    }
} catch (PDOException $e) {
    // Retorna erro caso a execução da query falhe
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>