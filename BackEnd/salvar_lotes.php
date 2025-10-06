<?php
session_start();
require("conexao.php");
include(__DIR__ . "/criar_alerta.php");

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método de requisição inválido.");
    }

    // Recebe os dados do formulário
    $data_lote = trim($_POST['data'] ?? '');
    $id_tanque = trim($_POST['id_tanque'] ?? '');

    if (empty($data_lote) || empty($id_tanque)) {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Selecione a Data e o Tanque para o Lote."
        ]);
        exit;
    }

    // 1. Buscar produções não loteadas
    $stmt_prod = $banco->prepare("
        SELECT id_producao, quantidade
        FROM producao_leite
        WHERE DATE(data) = :data_lote AND batched = 0
    ");
    $stmt_prod->bindValue(':data_lote', $data_lote);
    $stmt_prod->execute();
    $producoes_para_lote = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

    if (count($producoes_para_lote) === 0) {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Não há produção de leite não loteada para a data {$data_lote}."
        ]);
        exit;
    }

    // Calcula quantidade total e ids de produção
    $quantidade_total = 0;
    $ids_producao = [];
    foreach ($producoes_para_lote as $prod) {
        $quantidade_total += (float)$prod['quantidade'];
        $ids_producao[] = (int)$prod['id_producao'];
    }

    // Inicia transação
    $banco->beginTransaction();

    // 2. Inserir novo lote
    $stmt_lote = $banco->prepare("
        INSERT INTO lote_leite (data_lote, quantidade_total, id_tanque)
        VALUES (:data_lote, :quantidade_total, :id_tanque)
    ");
    $stmt_lote->bindValue(':data_lote', $data_lote);
    $stmt_lote->bindValue(':quantidade_total', $quantidade_total);
    $stmt_lote->bindValue(':id_tanque', $id_tanque, PDO::PARAM_INT);
    $stmt_lote->execute();

    $novo_id_lote = $banco->lastInsertId();

    // 3. Inserir vínculos na tabela producao_lote
    $sql_links = "INSERT INTO producao_lote (id_lote, id_producao) VALUES ";
    $placeholders = [];
    $values = [];
    foreach ($ids_producao as $id_prod) {
        $placeholders[] = "(?, ?)";
        $values[] = $novo_id_lote;
        $values[] = $id_prod;
    }
    $sql_links .= implode(', ', $placeholders);
    $stmt_links = $banco->prepare($sql_links);
    $stmt_links->execute($values);

    // 4. Marcar produções como batched = 1
    $stmt_update_prod = $banco->prepare("
        UPDATE producao_leite SET batched = 1 WHERE id_producao IN (" . implode(',', $ids_producao) . ")
    ");
    $stmt_update_prod->execute();

    $banco->commit();

    // 5. Buscar relatório do lote recém-criado
    $sql_relatorio = "
        SELECT l.id_lote, l.data_lote, l.quantidade_total, t.localizacao AS tanque,
               GROUP_CONCAT(v.nome SEPARATOR ', ') AS vacas
        FROM lote_leite l
        LEFT JOIN tanque t ON l.id_tanque = t.id_tanque
        LEFT JOIN producao_lote pl ON l.id_lote = pl.id_lote
        LEFT JOIN producao_leite p ON pl.id_producao = p.id_producao
        LEFT JOIN vacas v ON p.id_vaca = v.id_vaca
        WHERE l.id_lote = :id_lote
        GROUP BY l.id_lote
    ";
    $stmt_relatorio = $banco->prepare($sql_relatorio);
    $stmt_relatorio->bindValue(':id_lote', $novo_id_lote, PDO::PARAM_INT);
    $stmt_relatorio->execute();
    $relatorio_lote = $stmt_relatorio->fetch(PDO::FETCH_ASSOC);

    // Retorna JSON com o relatório
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "ok",
        "lote" => [
            "id" => $relatorio_lote['id_lote'],
            "data" => $relatorio_lote['data_lote'],
            "quantidade_total" => $relatorio_lote['quantidade_total'],
            "tanque" => $relatorio_lote['tanque'],
            "vacas" => $relatorio_lote['vacas']
        ]
    ]);
    exit;

} catch (Exception $e) {
    if ($banco->inTransaction()) {
        $banco->rollBack();
    }
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Erro ao criar lote: " . $e->getMessage()
    ]);
    exit;
} catch (PDOException $e) {
    if ($banco->inTransaction()) {
        $banco->rollBack();
    }
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Erro no banco de dados: " . $e->getMessage()
    ]);
    exit;
}
?>
