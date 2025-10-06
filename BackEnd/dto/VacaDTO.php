<?php
/**
 * Data Transfer Object (DTO) para a entidade Vaca.
 */
class VacaDTO {
    public $nome;
    public $idLoteManejo;
    public $descarte;
    public $healthScore;

    public function __construct(string $nome, int $idLoteManejo, int $descarte = 0) {
        $this->nome = $nome;
        $this->idLoteManejo = $idLoteManejo;
        $this->descarte = $descarte;
    }
}
?>