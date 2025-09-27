<?php
require_once 'conexao.php'; // Inclui a conexão com o banco
require_once 'ordenar.php'; // Inclui o arquivo com a função de ordenação

try {
    // Quantidade de produções por página
    $limite = 10;

    // Página atual (vem da URL: ?pagina_producao=2)
    $pagina = isset($_GET['pagina_producao']) ? (int)$_GET['pagina_producao'] : 1;
    if ($pagina < 1) $pagina = 1;

    // Calcula o deslocamento
    $offset = ($pagina - 1) * $limite;

    // Busca produções com LIMIT e OFFSET
    $sql = "SELECT pl.id_producao, pl.id_vaca, v.nome AS nome_vaca, 
                   pl.quantidade, pl.data
            FROM producao_leite pl
            JOIN vacas v ON pl.id_vaca = v.id_vaca
            LIMIT :limite OFFSET :offset";
    
    $stmt = $banco->prepare($sql);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $producoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Conta total de registros para calcular páginas
    $total = $banco->query("SELECT COUNT(*) FROM producao_leite")->fetchColumn();
    $totalPaginas = ceil($total / $limite);

    // Ordena por data (se você quiser manter quicksort, senão pode usar ORDER BY no SQL)
    $producoes = quicksortPorData($producoes);

    // Loop para exibir cada produção na tabela
    foreach ($producoes as $producao) {
        echo "<tr data-id=\"{$producao['id_producao']}\" data-id-vaca=\"{$producao['id_vaca']}\">";
        echo "<td>" . htmlspecialchars($producao['id_producao']) . "</td>";
        echo "<td>" . htmlspecialchars($producao['nome_vaca']) . " (ID: " . htmlspecialchars($producao['id_vaca']) . ")</td>";
        echo "<td class=\"quantidade-producao\">" . htmlspecialchars($producao['quantidade']) . " L</td>";
        echo "<td class=\"data-producao\">" . date('d/m/Y H:i', strtotime($producao['data'])) . "</td>";
        echo "<td>";
        echo "<button onclick=\"editarProducao(this)\" class=\"btn-editar\">Editar</button>";
        echo "<button onclick=\"excluirProducao({$producao['id_producao']}, this)\" class=\"btn-excluir\">Excluir</button>";
        echo "</td>";
        echo "</tr>";
    }

    // Navegação de páginas
    echo "<tr><td colspan='5'>";
    $currentPage = "area_comum_aluno.php"; // mantém na mesma página

    if ($pagina > 1) {
        echo "<a href='{$currentPage}?secao=producao&pagina_producao=" . ($pagina - 1) . "'>⬅ Anterior</a> ";
    }
    echo "Página $pagina de $totalPaginas";
    if ($pagina < $totalPaginas) {
        echo " <a href='{$currentPage}?secao=producao&pagina_producao=" . ($pagina + 1) . "'>Próxima ➡</a>";
    }
    echo "</td></tr>";

} catch (PDOException $e) {
    // Caso ocorra erro na query, exibe mensagem na tabela
    echo "<tr><td colspan='5'>Erro ao carregar produções de leite: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>
