<?php
// Arquivo: BackEnd/template/ListagemVacas.php

require_once __DIR__ . '/ListagemTemplate.php';
require_once __DIR__ . '/../repository/VacaRepository.php'; 
require_once __DIR__ . '/../service/VacaService.php';

class ListagemVacas extends ListagemTemplate {
    private $service;
    
    public function __construct($pdoConnection) {
        // O construtor define as variáveis únicas:
        // Seção: 'lista', Parâmetro de Query: 'pagina_vacas', Colunas: 5
        parent::__construct($pdoConnection, 'lista', 'pagina_vacas', 5); 
        $repository = new VacaRepository($pdoConnection);
        $this->service = new VacaService($repository);
    }

    protected function buscarDados(): array {
        return $this->service->listarVacas($this->limite, $this->offset);
    }

    protected function contarTotal(): int {
        return $this->service->contarTodasVacas();
    }

    protected function renderizarLinha(array $vaca): void {
        // Implementa a renderização HTML específica da tabela de Vacas
        echo "<tr data-id='{$vaca['id_vaca']}'>";
        echo "<td>{$vaca['id_vaca']}</td>";
        echo "<td class='nome-vaca'>{$vaca['nome']}</td>";
        echo "<td>" . htmlspecialchars($vaca['nome_lote'] ?? 'N/A') . "</td>";
        echo "<td>{$vaca['descarte']}</td>";
        echo "<td class='acoes'>";
        echo "<button class='btn-editar' onclick='editarVaca(this)'>Editar</button>";
        echo "<button class='btn-excluir' onclick='excluirVaca({$vaca['id_vaca']}, this)'>Excluir</button>";
        echo "</td>";
        echo "</tr>";
    }
}