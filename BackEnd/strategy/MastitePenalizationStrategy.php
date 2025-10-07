<?php
require_once __DIR__ . '/ScoringStrategy.php';

// Implementa a regra de penalidade por Mastite Positiva.
class MastitePenalizationStrategy implements ScoringStrategy
{
    public function calculateAdjustment(float $currentScore, array $historico): float
    {
        // Lógica movida da REGRA A do VacaService.php
        $positivos = $historico['mastite'] ?? 0;
        $penalidade_mastite = $positivos * 5.00; // -5.00 pontos por teste positivo
        
        return $currentScore - $penalidade_mastite;
    }
}
?>