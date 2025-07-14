<?php
require("conexao.php"); // falha se não houver conexao com o banco de dados

try {
    $stmt = $banco->query("SELECT * FROM vacas"); //realiza um select para carregar todas as vacas do banco de dados
    $vacas = $stmt->fetchAll(PDO::FETCH_ASSOC); //carrega os animais em um array associativo

    //tabela de vacas
    if (count($vacas) > 0) {//verifica se há vacas para listar
        foreach ($vacas as $vaca) {
            echo "<tr data-id='{$vaca['id_vaca']}'>";
            echo "<td>{$vaca['id_vaca']}</td>";
            echo "<td class='nome-vaca'>{$vaca['nome']}</td>";
            echo "<td>{$vaca['descarte']}</td>";
            echo "<td class='acoes'>";
            echo "<button class='btn-editar' onclick='editarVaca(this)'>Editar</button>";
            echo "<button class='btn-excluir' onclick='excluirVaca({$vaca['id_vaca']}, this)'>Excluir</button>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'><strong>Nenhuma vaca encontrada</strong></td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='4'><strong>Erro ao carregar vacas: " . $e->getMessage() . "</strong></td></tr>";
}
?>