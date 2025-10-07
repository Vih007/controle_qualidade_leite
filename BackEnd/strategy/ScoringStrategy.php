<?php
// Define a interface comum para todas as regras de pontuação (Estratégia)
interface ScoringStrategy
{
    /**
     * Calcula o novo score após aplicar uma regra específica.
     * * @param float $currentScore O score atual antes da aplicação da estratégia.
     * @param array $historico Dados do histórico da vaca (mastite, produção, etc.).
     * @return float O score ajustado.
     */
    public function calculateAdjustment(float $currentScore, array $historico): float;
}
?>