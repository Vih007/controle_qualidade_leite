<?php
require_once 'conexao.php';
require_once 'ordenar.php';

try {
    // Quantidade de testes por página
    $limite = 10;

    // Página atual (vem da URL, ex: ?pagina=2)
    $pagina = isset($_GET['pagina_testes']) ? (int)$_GET['pagina_testes'] : 1;
    if ($pagina < 1) $pagina = 1;

    // Calcula o deslocamento
    $offset = ($pagina - 1) * $limite;

    // Consulta com LIMIT e OFFSET
    $sql = "SELECT tm.id_teste, tm.id_vaca, v.nome AS nome_vaca, tm.data, 
                   tm.resultado, tm.quantas_cruzes, tm.ubere, tm.tratamento, tm.observacoes
            FROM teste_mastite tm
            JOIN vacas v ON tm.id_vaca = v.id_vaca
            LIMIT :limite OFFSET :offset";
    
    $stmt = $banco->prepare($sql);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $testes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Conta o total de registros para calcular páginas
    $total = $banco->query("SELECT COUNT(*) FROM teste_mastite")->fetchColumn();
    $totalPaginas = ceil($total / $limite);

    // Se você ainda quiser aplicar quicksortPorData (embora o banco já ordene melhor com ORDER BY)
    $testes = quicksortPorData($testes);

    // Exibe os testes
    foreach ($testes as $teste) {
        echo "<tr data-id=\"{$teste['id_teste']}\">";
        echo "<td>" . htmlspecialchars($teste['id_teste']) . "</td>";
        echo "<td>" . htmlspecialchars($teste['nome_vaca']) . " (ID: " . htmlspecialchars($teste['id_vaca']) . ")</td>";
        echo "<td>" . date("d/m/Y", strtotime($teste['data'])) . "</td>";
        echo "<td class=\"resultado-teste\">" . htmlspecialchars(ucfirst($teste['resultado'])) . "</td>";
        echo "<td class=\"cruzes-teste\">" . htmlspecialchars($teste['quantas_cruzes']) . "</td>";
        echo "<td class=\"uberes-teste\">" . htmlspecialchars($teste['ubere']) . "</td>";
        echo "<td class=\"tratamento-teste\">" . htmlspecialchars($teste['tratamento']) . "</td>";
        echo "<td class=\"observacoes-teste\">" . htmlspecialchars($teste['observacoes']) . "</td>";
        echo "<td>";
        echo "<button onclick=\"editarTeste(this)\" class=\"btn-editar\">Editar</button>";
        echo "<button onclick=\"excluirTeste({$teste['id_teste']}, this)\" class=\"btn-excluir\">Excluir</button>";
        echo "</td>";
        echo "</tr>";
    }

    // Navegação de páginas
    echo "<tr><td colspan='9'>";
    $currentPage = "area_comum_aluno.php"; // mantém na mesma página

    if ($pagina > 1) {
        echo "<a href='{$currentPage}?secao=teste_mastite&pagina_testes=" . ($pagina - 1) . "'>⬅ Anterior</a> ";
    }
    echo "Página $pagina de $totalPaginas";
    if ($pagina < $totalPaginas) {
        echo " <a href='{$currentPage}?secao=teste_mastite&pagina_testes=" . ($pagina + 1) . "'>Próxima ➡</a>";
    }
    echo "</td></tr>";

} catch (PDOException $e) {
    echo "<tr><td colspan='9'>Erro ao carregar testes de mastite: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>
