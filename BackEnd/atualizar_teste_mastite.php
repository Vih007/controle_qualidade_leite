<?php
require("conexao.php");
header('Content-Type: application/json');

// Adicionado 'observacoes' ao isset para garantir que todos os parâmetros são recebidos
if (isset($_POST['id_teste'], $_POST['resultado'], $_POST['quantas_cruzes'], $_POST['tratamento'], $_POST['observacoes'])) {
    $id_teste = $_POST['id_teste'];
    $resultado = $_POST['resultado']; // Já convertido para 'positivo'/'negativo' no frontend
    $quantas_cruzes = $_POST['quantas_cruzes'];
    $tratamento = $_POST['tratamento'];
    $observacoes = $_POST['observacoes']; // Receber observacoes

    try {
        // ATUALIZAR QUERY: Incluir 'observações' no SET
        $stmt = $banco->prepare("UPDATE teste_mastite SET resultado = :resultado, quantas_cruzes = :quantas_cruzes, tratamento = :tratamento, observacoes = :observacoes WHERE id_teste = :id_teste");

        $stmt->bindValue(':resultado', $resultado);
        $stmt->bindValue(':quantas_cruzes', $quantas_cruzes, PDO::PARAM_INT);
        $stmt->bindValue(':tratamento', $tratamento);
        $stmt->bindValue(':observacoes', $observacoes);
        $stmt->bindValue(':id_teste', $id_teste, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Nenhuma alteração foi feita ou ID não encontrado.']);
            }
        } else {
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['success' => false, 'message' => 'Erro na execução da query: ' . $errorInfo[2]]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro de Banco de Dados: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Parâmetros ausentes na requisição POST.']);
}
?>