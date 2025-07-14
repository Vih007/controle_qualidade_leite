<?php
require('conexao.php'); 
header('Content-Type: application/json');

// 1. Recebe o 'id_teste' que o JavaScript envia
$id_teste = $_POST['id_teste'] ?? null;

if (!$id_teste) {
    echo json_encode(['success' => false, 'message' => 'ID do teste não fornecido.']);
    exit;
}

try {
    // 2. Prepara o DELETE na tabela correta (teste_mastite) usando a coluna correta (id_teste)
    $stmt = $banco->prepare("DELETE FROM teste_mastite WHERE id_teste = :id");
    
    $stmt->bindValue(':id', $id_teste, PDO::PARAM_INT);
    
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // 3. Responde com sucesso em JSON
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nenhum teste encontrado com este ID.']);
    }

} catch (PDOException $e) {
    // 4. Captura qualquer erro de banco e responde em JSON
    echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>