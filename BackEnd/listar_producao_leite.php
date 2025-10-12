<?php
require_once 'conexao.php'; 
require_once 'ordenar.php'; 
require_once __DIR__ . '/repository/ProducaoLeiteRepository.php';
require_once __DIR__ . '/service/ProducaoLeiteService.php';

// Tática de Detecção de Falhas: Protege contra a conexão nula
if (is_null($banco)) {
    echo "<tr><td colspan='6'><strong>Erro: Conexão com o Banco de Dados indisponível.</strong></td></tr>";
    return;
}

try {
    $limite = 10;
    $pagina = isset($_GET['pagina_producao']) ? (int)$_GET['pagina_producao'] : 1;
    if ($pagina < 1) $pagina = 1;
    $offset = ($pagina - 1) * $limite;

    $repository = new ProducaoLeiteRepository($banco);
    $service = new ProducaoLeiteService($repository);
    
    $producoes = $service->listarProducoes($limite, $offset);

    $total = $service->contarTodasProducoes();
    $totalPaginas = ceil($total / $limite);

    $producoes = quicksortPorData($producoes);

    $colspan = 6; 
    
    foreach ($producoes as $producao) {
        echo "<tr data-id=\"{$producao['id_producao']}\" data-id-vaca=\"{$producao['id_vaca']}\">";
        echo "<td>" . htmlspecialchars($producao['id_producao']) . "</td>";
        echo "<td>" . htmlspecialchars($producao['nome_vaca']) . " (ID: " . htmlspecialchars($producao['id_vaca']) . ")</td>";
        echo "<td class=\"quantidade-producao\">" . htmlspecialchars($producao['quantidade']) . " L</td>";
        echo "<td>" . htmlspecialchars($producao['nome_tanque'] ?? 'N/A') . "</td>";
        echo "<td class=\"data-producao\">" . date('d/m/Y H:i', strtotime($producao['data'])) . "</td>";
        echo "<td>";
        echo "<button onclick=\"editarProducao(this)\" class=\"btn-editar\">Editar</button>";
        echo "<button onclick=\"excluirProducao({$producao['id_producao']}, this)\" class=\"btn-excluir\">Excluir</button>";
        echo "</td>";
        echo "</tr>";
    }

    echo "<tr><td colspan='{$colspan}'>";
    $currentPage = "area_comum_aluno.php"; 

    if ($pagina > 1) {
        echo "<a href='{$currentPage}?secao=producao&pagina_producao=" . ($pagina - 1) . "'>⬅ Anterior</a> ";
    }
    echo "Página $pagina de $totalPaginas";
    if ($pagina < $totalPaginas) {
        echo " <a href='{$currentPage}?secao=producao&pagina_producao=" . ($pagina + 1) . "'>Próxima ➡</a>";
    }
    echo "</td></tr>";

} catch (PDOException $e) {
    echo "<tr><td colspan='{$colspan}'>Erro ao carregar produções de leite: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>