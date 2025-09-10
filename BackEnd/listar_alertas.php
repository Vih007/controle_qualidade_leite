<?php
require("conexao.php");

$sql = "
    SELECT a.id_alerta, a.id_vaca, v.nome AS nome_vaca, a.mensagem, a.lido, a.data_criacao
    FROM alertas a
    INNER JOIN vacas v ON a.id_vaca = v.id_vaca
    ORDER BY a.id_alerta DESC
";

$stmt = $banco->query($sql);
$alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($alertas) > 0) {
    foreach ($alertas as $alerta) {
        echo "<tr data-id='" . htmlspecialchars($alerta['id_alerta']) . "'>";
        echo "<td>" . htmlspecialchars($alerta['nome_vaca']) . "</td>";
        echo "<td>" . htmlspecialchars($alerta['mensagem']) . "</td>";
        echo "<td>" . date('d/m/Y H:i', strtotime($alerta['data_criacao'])) . "</td>";
        
        echo "<td class='status-celula'>";
        if ($alerta['lido'] == 0) {
            // Removido o onclick
            echo "<button class='btn-marcar-lido'>Marcar como Visto</button>";
        } else {
            echo "<span class='icon-visualizado'>&#x2714;&#x2714;</span>";
        }
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'><strong>Nenhum alerta encontrado</strong></td></tr>";
}
?>