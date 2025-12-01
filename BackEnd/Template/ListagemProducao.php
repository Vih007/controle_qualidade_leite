<?php
// Arquivo: BackEnd/template/ListagemProducao.php

require_once __DIR__ . '/ListagemTemplate.php';
require_once __DIR__ . '/../repository/ProducaoLeiteRepository.php'; 
require_once __DIR__ . '/../service/ProducaoLeiteService.php';
require_once __DIR__ . '/../ordenar.php'; 

class ListagemProducao extends ListagemTemplate {
    private $service;
    
    public function __construct($pdoConnection) {
        // Seção: 'producao', Parâmetro de Query: 'pagina_producao', Colunas: 6
        parent::__construct($pdoConnection, 'producao', 'pagina_producao', 6);
        $repository = new ProducaoLeiteRepository($pdoConnection);
        $this->service = new ProducaoLeiteService($repository);
    }

    protected function buscarDados(): array {
        $producoes = $this->service->listarProducoes($this->limite, $this->offset);
        // Lógica de ordenação (específica)
        return quicksortPorData($producoes);
    }

    protected function contarTotal(): int {
        return $this->service->contarTodasProducoes();
    }

    protected function renderizarLinha(array $producao): void {
        // Implementa a renderização HTML específica da tabela de Produção
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
}