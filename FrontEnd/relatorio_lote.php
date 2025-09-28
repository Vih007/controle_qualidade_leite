<?php
require("conexao.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Lotes</title>
</head>
<body>
    <h2>Relatório de Lotes</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Data</th>
            <th>Quantidade Total</th>
            <th>Tanque</th>
            <th>Vacas Contribuintes</th>
        </tr>
        <?php
        try {
            $sql = "SELECT l.id, l.data, l.quantidade_total, l.tanque,
                           GROUP_CONCAT(v.nome SEPARATOR ', ') AS vacas
                    FROM lote_leite l
                    LEFT JOIN producao_lote pl ON l.id = pl.lote_id
                    LEFT JOIN producao p ON pl.producao_id = p.id
                    LEFT JOIN vaca v ON p.vaca_id = v.id
                    GROUP BY l.id
                    ORDER BY l.quantidade_total DESC";
            $stmt = $banco->query($sql);
            $relatorio = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($relatorio) {
                foreach ($relatorio as $linha) {
                    echo "<tr>";
                    echo "<td>{$linha['id']}</td>";
                    echo "<td>{$linha['data']}</td>";
                    echo "<td>{$linha['quantidade_total']} L</td>";
                    echo "<td>{$linha['tanque']}</td>";
                    echo "<td>{$linha['vacas']}</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'><strong>Nenhum dado encontrado</strong></td></tr>";
            }

            // lote mais produtivo do mês
            $sqlTop = "SELECT id, data, quantidade_total, tanque
                       FROM lote_leite
                       WHERE MONTH(data) = MONTH(CURRENT_DATE())
                         AND YEAR(data) = YEAR(CURRENT_DATE())
                       ORDER BY quantidade_total DESC
                       LIMIT 1";
            $top = $banco->query($sqlTop)->fetch(PDO::FETCH_ASSOC);

            if ($top) {
                echo "<tr><td colspan='5'><strong>Lote mais produtivo deste mês: "
                     . "ID {$top['id']} | {$top['quantidade_total']} L em {$top['data']} (Tanque: {$top['tanque']})</strong></td></tr>";
            }
        } catch (PDOException $e) {
            echo "<tr><td colspan='5'><strong>Erro: " . $e->getMessage() . "</strong></td></tr>";
        }
        ?>
    </table>
</body>
</html>
