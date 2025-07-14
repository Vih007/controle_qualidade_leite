<?php
require("conexao.php");

// Consulta os alertas com o nome da vaca (join com a tabela vacas)
$sql = "
    SELECT a.id_alerta, a.id_vaca, v.nome AS nome_vaca, a.mensagem
    FROM alertas a
    INNER JOIN vacas v ON a.id_vaca = v.id_vaca
    ORDER BY a.id_alerta DESC
";

$stmt = $banco->query($sql); // Executa a query SQL diretamente no banco
$alertas = $stmt->fetchAll(PDO::FETCH_ASSOC); // Busca todos os resultados como array associativo

if (count($alertas) > 0) {
    //printa a tabela com os alertas na tela
    foreach ($alertas as $alerta) {
        echo "<tr>";
        echo "<td>{$alerta['nome_vaca']}</td>";
        echo "<td>{$alerta['mensagem']}</td>";
        echo "<td>" . date('d/m/Y H:i') . "</td>"; // Simulando data atual como "data de criação"
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'><strong>Nenhum alerta encontrado</strong></td></tr>";
}
?>
