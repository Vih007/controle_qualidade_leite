<?php
require("conexao.php"); // conexão com o banco

try {
    $limite = 10;
    $pagina = isset($_GET['pagina_lotes']) ? (int)$_GET['pagina_lotes'] : 1;
    if ($pagina < 1) $pagina = 1;
    $offset = ($pagina - 1) * $limite;

    $stmt = $banco->prepare("
        SELECT l.id_lote, l.data_lote, l.quantidade_total, t.localizacao AS tanque
        FROM lote_leite l
        INNER JOIN tanque t ON l.id_tanque = t.id_tanque
        LIMIT :limite OFFSET :offset
    ");
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $lotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($lotes) > 0) {
        foreach ($lotes as $lote) {
            echo "<tr data-id='{$lote['id_lote']}'>";
            echo "<td class='id-lote'>{$lote['id_lote']}</td>";
            echo "<td class='data-lote'>{$lote['data_lote']}</td>";         // ajustado
            echo "<td class='quantidade-lote'>{$lote['quantidade_total']} L</td>"; // ajustado
            echo "<td class='tanque-lote'>{$lote['tanque']}</td>";         // já bate com JS
            echo "<td class='acoes'>
                    <button class='btn-editar' onclick='editarLote(this)'>Editar</button>
                    <button class='btn-excluir' onclick='excluirLote({$lote['id_lote']}, this)'>Excluir</button>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'><strong>Nenhum lote encontrado</strong></td></tr>";
    }

    // Paginação
    $total = $banco->query("SELECT COUNT(*) FROM lote_leite")->fetchColumn();
    $totalPaginas = ceil($total / $limite);
    echo "<tr><td colspan='5'>";
    $currentPage = "area_comum_aluno.php";
    if ($pagina > 1) {
        echo "<a href='{$currentPage}?secao=lotes&pagina_lotes=" . ($pagina - 1) . "'>⬅ Anterior</a> ";
    }
    echo "Página $pagina de $totalPaginas";
    if ($pagina < $totalPaginas) {
        echo " <a href='{$currentPage}?secao=lotes&pagina_lotes=" . ($pagina + 1) . "'>Próxima ➡</a>";
    }
    echo "</td></tr>";

} catch (PDOException $e) {
    echo "<tr><td colspan='5'><strong>Erro ao carregar lotes: " . $e->getMessage() . "</strong></td></tr>";
}
?>
