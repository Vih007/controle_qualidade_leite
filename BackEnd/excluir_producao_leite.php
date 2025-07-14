<?php

require('conexao.php');
header('Content-Type: application/json');

$id_producao = $_POST['id_producao'] ?? null; // Recebe o ID da produção ou null

// Verifica se o ID da produção foi fornecido
if (!$id_producao) {
    echo json_encode(['success' => false, 'message' => 'ID da produção não fornecido.']);
    exit;
}

try {
    // Prepara a query para deletar a produção pelo ID
    $stmt = $banco->prepare("DELETE FROM producao_leite WHERE id_producao = :id");
    $stmt->bindValue(':id', $id_producao, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) { // Verifica se algum registro foi deletado
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nenhum registro encontrado com este ID.']);
    }

} catch (PDOException $e) {
    // Retorna erro caso a execução da query falhe
    echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>