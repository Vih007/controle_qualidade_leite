<?php
require("conexao.php");

$id_lote = isset($_GET['id_lote']) ? (int) $_GET['id_lote'] : 0;

if ($id_lote <= 0) {
    echo "<div class='card-relatorio-lote'><p><strong>Informe um ID de lote válido.</strong></p></div>";
    exit;
}

try {
    $sql = "SELECT l.id_lote, l.data_lote, l.quantidade_total, t.localizacao AS tanque,
                   GROUP_CONCAT(DISTINCT v.nome SEPARATOR ', ') AS vacas
            FROM lote_leite l
            LEFT JOIN tanque t ON l.id_tanque = t.id_tanque
            LEFT JOIN producao_lote pl ON l.id_lote = pl.id_lote
            LEFT JOIN producao_leite p ON pl.id_producao = p.id_producao
            LEFT JOIN vacas v ON p.id_vaca = v.id_vaca
            WHERE l.id_lote = :id_lote
            GROUP BY l.id_lote";

    $stmt = $banco->prepare($sql);
    $stmt->bindValue(':id_lote', $id_lote, PDO::PARAM_INT);
    $stmt->execute();
    $lote = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lote) {
        $dataFormat = !empty($lote['data_lote']) ? date("d/m/Y", strtotime($lote['data_lote'])) : '—';
        $quant = number_format($lote['quantidade_total'] ?? 0, 2, ',', '.');
        $tanque = htmlspecialchars($lote['tanque'] ?? 'N/A');
        $vacas = htmlspecialchars($lote['vacas'] ?? 'Nenhuma');

        echo "<div class='card-relatorio-lote'>";
        echo "<div class='card-header'><h3>Relatório do Lote #{$lote['id_lote']}</h3></div>";
        echo "<div class='card-body'>";
        echo "<p><strong>Data:</strong> {$dataFormat}</p>";
        echo "<p><strong>Quantidade Total:</strong> {$quant} L</p>";
        echo "<p><strong>Tanque:</strong> {$tanque}</p>";
        echo "<p><strong>Vacas:</strong> {$vacas}</p>";
        echo "</div></div>";
    } else {
        echo "<div class='card-relatorio-lote'><p><strong>Nenhum dado encontrado para o lote selecionado.</strong></p></div>";
    }

} catch (PDOException $e) {
    echo "<div class='card-relatorio-lote'><p><strong>Erro no relatório: " . htmlspecialchars($e->getMessage()) . "</strong></p></div>";
}
?>
