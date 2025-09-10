<?php
require("conexao.php");

// Pega alertas que ainda não expiraram
// Alertas só podem sumir se tiverem sido lidos
$query = $conn->prepare("
    SELECT * FROM alertas
    WHERE (lido = 0 OR (lido = 1 AND data_criacao >= NOW() - INTERVAL 2 DAY))
    ORDER BY data_criacao DESC
");
$query->execute();
$alertas = $query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($alertas);
?>