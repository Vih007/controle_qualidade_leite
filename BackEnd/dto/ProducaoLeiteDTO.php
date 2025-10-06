<?php
/**
 * Data Transfer Object (DTO) para a entidade ProducaoLeite.
 */
class ProducaoLeiteDTO {
    public $idVaca;
    public $quantidade;
    public $data;
    public $idTanque;
    public $idProducao; 

    public function __construct(int $idVaca, float $quantidade, string $data, int $idTanque, int $idProducao = null) {
        $this->idVaca = $idVaca;
        $this->quantidade = $quantidade;
        $this->data = $data;
        $this->idTanque = $idTanque;
        $this->idProducao = $idProducao;
    }
}
?>