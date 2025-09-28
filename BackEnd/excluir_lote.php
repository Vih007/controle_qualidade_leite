<?php
require("conexao.php");

header('Content-Type: application/json');

try {
    if (!isset($_POST['id_lote'])) {
        throw new Exception("ID do lote não informado.");
    }

    $id = (int) $_POST['id_lote'];

    $stmt = $banco->prepare("DELETE FROM lote_leite WHERE id_lote = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Falha ao excluir lote.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
