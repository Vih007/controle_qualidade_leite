<?php
require("conexao.php");
header('Content-Type: application/json');

if (isset($_POST['id_producao'], $_POST['id_vaca'], $_POST['quantidade'], $_POST['data'])) {
    //Delcarando variaveis
    $id_producao = $_POST['id_producao'];
    $id_vaca = $_POST['id_vaca'];
    $quantidade = $_POST['quantidade'];
    $data = $_POST['data'];


    try {
        $stmt = $banco->prepare("UPDATE producao_leite SET id_vaca = :id_vaca, quantidade = :quantidade, data = :data WHERE id_producao = :id_producao");

        $stmt->bindValue(':id_vaca', $id_vaca, PDO::PARAM_INT);
        $stmt->bindValue(':quantidade', $quantidade);
        $stmt->bindValue(':data', $data);
        $stmt->bindValue(':id_producao', $id_producao, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Verifica se alguma linha foi realmente afetada para confirmar a atualização
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true]);
            } else {
                // Nenhuma linha afetada: ID de produção ou vaca pode não existir, ou dados são os mesmos
                echo json_encode(['success' => false, 'message' => 'Nenhuma alteração foi feita. Verifique se o ID da produção e da vaca existem ou se os dados são diferentes.']);
            }
        } else {
            // Se execute() retorna false (raro com ERRMODE_EXCEPTION, mas possível)
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['success' => false, 'message' => 'Erro na execução da query: ' . $errorInfo[2]]);
        }
    } catch (PDOException $e) {
        // Captura e retorna exceções do PDO (erros de banco de dados, como Foreign Key)
        echo json_encode(['success' => false, 'message' => 'Erro de Banco de Dados: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Parâmetros ausentes na requisição POST.']);
}
?>