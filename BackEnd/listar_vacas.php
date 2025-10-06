<?php
require("conexao.php"); 
require_once __DIR__ . '/repository/VacaRepository.php'; 
require_once __DIR__ . '/service/VacaService.php';

try {
    $limite = 10;
    $pagina = isset($_GET['pagina_vacas']) ? (int)$_GET['pagina_vacas'] : 1;
    if ($pagina < 1) $pagina = 1;
    $offset = ($pagina - 1) * $limite;

    $repository = new VacaRepository($banco);
    $service = new VacaService($repository);
    
    $vacas = $service->listarVacas($limite, $offset);

    $total = $repository->countAll();
    $totalPaginas = ceil($total / $limite);

    if (count($vacas) > 0) {
        foreach ($vacas as $vaca) {
            echo "<tr data-id='{$vaca['id_vaca']}'>";
            echo "<td>{$vaca['id_vaca']}</td>";
            echo "<td class='nome-vaca'>{$vaca['nome']}</td>";
            echo "<td>" . htmlspecialchars($vaca['nome_lote'] ?? 'N/A') . "</td>"; // NOVO: Lote de Manejo
            echo "<td>{$vaca['descarte']}</td>";
            echo "<td class='acoes'>";
            echo "<button class='btn-editar' onclick='editarVaca(this)'>Editar</button>";
            echo "<button class='btn-excluir' onclick='excluirVaca({$vaca['id_vaca']}, this)'>Excluir</button>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'><strong>Nenhuma vaca encontrada</strong></td></tr>"; 
    }

    echo "<tr><td colspan='5'>"; 
    $currentPage = "area_comum_aluno.php"; 

    if ($pagina > 1) {
    echo "<a href='{$currentPage}?secao=lista&pagina_vacas=" . ($pagina - 1) . "'>⬅ Anterior</a> ";
    }
    echo "Página $pagina de $totalPaginas";
    if ($pagina < $totalPaginas) {
        echo " <a href='{$currentPage}?secao=lista&pagina_vacas=" . ($pagina + 1) . "'>Próxima ➡</a>";
    }
    echo "</td></tr>";

} catch (PDOException $e) {
    echo "<tr><td colspan='5'><strong>Erro ao carregar vacas: " . $e->getMessage() . "</strong></td></tr>";
}
?>