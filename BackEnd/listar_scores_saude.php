<?php
require("conexao.php"); 
require_once __DIR__ . DIRECTORY_SEPARATOR . 'repository' . DIRECTORY_SEPARATOR . 'VacaRepository.php'; 
require_once __DIR__ . DIRECTORY_SEPARATOR . 'service' . DIRECTORY_SEPARATOR . 'VacaService.php';

try {
    // Definição da paginação
    $limite = 10;
    $pagina = isset($_GET['pagina_scores']) ? (int)$_GET['pagina_scores'] : 1;
    if ($pagina < 1) $pagina = 1;
    $offset = ($pagina - 1) * $limite;

    $repository = new VacaRepository($banco);
    $service = new VacaService($repository);
    
    // Chamada ao Service
    $vacas = $service->listarScores($limite, $offset); 
    $total = $repository->countAll();
    $totalPaginas = ceil($total / $limite);

    // INÍCIO DO NOVO HTML: Container dos Cards
    echo '<div class="cards-scores-container">'; 

    if (count($vacas) > 0) {
        foreach ($vacas as $vaca) {
            $score = number_format($vaca['health_score'], 2);
            
            // Lógica para determinar a classe de cor (Alto, Médio, Baixo)
            $cor_score_classe = ($score >= 80) ? 'score-alto' : (($score >= 50) ? 'score-medio' : 'score-baixo');

            // Estrutura de Card (Ficha)
            echo "<div class='score-card {$cor_score_classe}'>";
            
            echo "<div class='card-header'>";
            echo "<span class='card-id'>ID: {$vaca['id_vaca']}</span>";
            echo "<h4 class='card-nome'>" . htmlspecialchars($vaca['nome']) . "</h4>";
            echo "</div>"; // card-header

            echo "<div class='card-body'>";
            echo "<p><strong>Lote de Manejo:</strong> " . htmlspecialchars($vaca['nome_lote'] ?? 'N/A') . "</p>";
            echo "</div>"; // card-body

            echo "<div class='card-score-box'>";
            echo "<span>Score de Saúde</span>";
            echo "<span class='card-score-valor'>{$score}</span>";
            echo "</div>"; // card-score-box
            
            echo "</div>"; // score-card
        }
    } else {
        echo "<p><strong>Nenhuma vaca encontrada para exibir o score.</strong></p>";
    }
    
    echo '</div>'; // cards-scores-container

    // Paginação separada (fora do container de cards)
    echo "<div class='paginacao-scores'>";
    $currentPage = "area_professor.php"; 
    $secaoAtual = "score_saude";

    if ($pagina > 1) {
        echo "<a href='{$currentPage}?secao=score_saude&pagina_scores=" . ($pagina - 1) . "'>⬅ Anterior</a> ";
    }
    echo "Página $pagina de $totalPaginas";
    if ($pagina < $totalPaginas) {
       echo "<a href='{$currentPage}?secao={$secaoAtual}&pagina_scores=" . ($pagina + 1) . "'>Próxima ➡</a>";
    }
    echo "</div>"; // paginacao-scores

} catch (PDOException $e) {
    echo "<div><strong>Erro ao carregar scores: " . htmlspecialchars($e->getMessage()) . "</strong></div>";
}
?>

