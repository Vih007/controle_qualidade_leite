<?php
require_once __DIR__ . '/ScoringStrategy.php';

// Implementa a regra de penalidade por Inatividade de Produção.
class InactivityPenalizationStrategy implements ScoringStrategy
{
    public function calculateAdjustment(float $currentScore, array $historico): float
    {
        // Lógica movida da REGRA B do VacaService.php
        $penalidade_inativade = 0.0;
        $ultima_producao = $historico['ultima_producao'];
        
        if ($ultima_producao) {
            $data_hoje = new DateTime();
            $data_ultima = new DateTime($ultima_producao);
            $dias_inativos = $data_hoje->diff($data_ultima)->days;
            
            // Penaliza por inatividade acima de 30 dias
            if ($dias_inativos > 30) {
                $penalidade_inativade = ($dias_inativos - 30) * 0.5; // -0.5 ponto por dia após 30 dias
            }
        }
        
        return $currentScore - $penalidade_inativade;
    }
}
?>