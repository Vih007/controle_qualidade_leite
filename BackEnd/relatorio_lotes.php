<?php
require("conexao.php");

try {
    // Relatório geral dos lotes com vacas
    $sql = "SELECT l.id_lote, l.data_lote, l.quantidade_total, t.localizacao AS tanque,
                   GROUP_CONCAT(v.nome SEPARATOR ', ') AS vacas
            FROM lote_leite l
            LEFT JOIN tanque t ON l.id_tanque = t.id_tanque
            LEFT JOIN producao_lote pl ON l.id_lote = pl.id_lote
            LEFT JOIN producao_leite p ON pl.id_producao = p.id_producao
            LEFT JOIN vacas v ON p.id_vaca = v.id_vaca
            GROUP BY l.id_lote
            ORDER BY l.quantidade_total DESC";

    $stmt = $banco->prepare($sql);
    $stmt->execute();
    $relatorio = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($relatorio) > 0) {
        foreach ($relatorio as $linha) {
            echo "<tr>";
            echo "<td>{$linha['id_lote']}</td>";
            echo "<td>{$linha['data_lote']}</td>";
            echo "<td>{$linha['quantidade_total']} L</td>";
            echo "<td>{$linha['tanque']}</td>";
            echo "<td>{$linha['vacas']}</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'><strong>Nenhum dado encontrado</strong></td></tr>";
    }

    // Lote mais produtivo do mês
    $sqlTop = "SELECT l.id_lote, l.data_lote, l.quantidade_total, t.localizacao AS tanque
               FROM lote_leite l
               LEFT JOIN tanque t ON l.id_tanque = t.id_tanque
               WHERE MONTH(l.data_lote) = MONTH(CURRENT_DATE())
                 AND YEAR(l.data_lote) = YEAR(CURRENT_DATE())
               ORDER BY l.quantidade_total DESC
               LIMIT 1";
    $top = $banco->query($sqlTop)->fetch(PDO::FETCH_ASSOC);

    if ($top) {
        echo "<tr><td colspan='5'><strong>Lote mais produtivo deste mês: "
             . "ID {$top['id_lote']} | {$top['quantidade_total']} L em {$top['data_lote']} (Tanque: {$top['tanque']})</strong></td></tr>";
    }

} catch (PDOException $e) {
    echo "<tr><td colspan='5'><strong>Erro no relatório: " . $e->getMessage() . "</strong></td></tr>";
}
?>
