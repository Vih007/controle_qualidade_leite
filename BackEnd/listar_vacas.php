<?php
require("conexao.php"); // conexão com o banco

try {
    // Quantidade de vacas por página
    $limite = 10;

    // Página atual (vem da URL, ex: ?pagina=2)
    $pagina = isset($_GET['pagina_vacas']) ? (int)$_GET['pagina_vacas'] : 1;
    if ($pagina < 1) $pagina = 1;

    // Calcula deslocamento
    $offset = ($pagina - 1) * $limite;

    // Busca vacas com LIMIT e OFFSET
    $stmt = $banco->prepare("SELECT * FROM vacas LIMIT :limite OFFSET :offset");
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $vacas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Conta total de vacas (para calcular número de páginas)
    $total = $banco->query("SELECT COUNT(*) FROM vacas")->fetchColumn();
    $totalPaginas = ceil($total / $limite);

    // Tabela de vacas
    if (count($vacas) > 0) {
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

    // Navegação (Anterior / Próxima)
    echo "<tr><td colspan='4'>";
    $currentPage = "area_comum_aluno.php"; // mantém na mesma página

    if ($pagina > 1) {
    echo "<a href='{$currentPage}?secao=lista&pagina_vacas=" . ($pagina - 1) . "'>⬅ Anterior</a> ";
    }
    echo "Página $pagina de $totalPaginas";
    if ($pagina < $totalPaginas) {
        echo " <a href='{$currentPage}?secao=lista&pagina_vacas=" . ($pagina + 1) . "'>Próxima ➡</a>";
    }
    echo "</td></tr>";

} catch (PDOException $e) {
    echo "<tr><td colspan='4'><strong>Erro ao carregar vacas: " . $e->getMessage() . "</strong></td></tr>";
}
?>
