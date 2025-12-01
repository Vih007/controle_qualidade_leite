<?php
// Arquivo: BackEnd/template/ListagemTemplate.php

// A dependência de conexao.php é necessária para o $this->banco
require_once __DIR__ . '/../conexao.php';

abstract class ListagemTemplate {
    protected $banco;
    protected $limite = 10;
    protected $pagina;
    protected $offset;
    protected $totalRegistros;
    protected $currentPageFile = "area_comum_aluno.php"; // Página de destino da navegação
    protected $secaoNome;
    protected $paginaQueryParam; // Ex: 'pagina_vacas'
    protected $colspan; // Número de colunas na tabela

    public function __construct($pdoConnection, $secaoNome, $paginaQueryParam, $colspan) {
        $this->banco = $pdoConnection;
        $this->secaoNome = $secaoNome;
        $this->paginaQueryParam = $paginaQueryParam;
        $this->colspan = $colspan;
        // Lógica de Paginação (Fixa - faz parte do Template)
        $this->pagina = isset($_GET[$paginaQueryParam]) ? (int)$_GET[$paginaQueryParam] : 1;
        if ($this->pagina < 1) $this->pagina = 1;
        $this->offset = ($this->pagina - 1) * $this->limite;
    }

    // === TEMPLATE METHOD: O ALGORITMO PRINCIPAL (FINAL) ===
    public final function renderizarListagem() {
        if (is_null($this->banco)) {
            echo "<tr><td colspan='{$this->colspan}'><strong>Erro: Conexão com o Banco de Dados indisponível.</strong></td></tr>";
            return;
        }

        try {
            // 1. CHAMA MÉTODOS ABSTRATOS (OPERACÕES PRIMITIVAS)
            $dados = $this->buscarDados();
            $this->totalRegistros = $this->contarTotal();
            $totalPaginas = ceil($this->totalRegistros / $this->limite);

            // 2. LÓGICA DE RENDERIZAÇÃO (FIXA)
            if (count($dados) > 0) {
                foreach ($dados as $item) {
                    $this->renderizarLinha($item); // Chamada à Operação Primitiva
                }
            } else {
                echo "<tr><td colspan='{$this->colspan}'><strong>Nenhum registro de {$this->secaoNome} encontrado</strong></td></tr>";
            }

            // 3. LÓGICA DE PAGINAÇÃO (FIXA)
            $this->renderizarPaginacao($totalPaginas);

        } catch (PDOException $e) {
            echo "<tr><td colspan='{$this->colspan}'><strong>Erro ao carregar {$this->secaoNome}: " . htmlspecialchars($e->getMessage()) . "</strong></td></tr>";
        }
    }
    
    // === OPERAÇÕES PRIMITIVAS (ABSTRATAS) - DEVEM SER IMPLEMENTADAS PELAS CLASSES FILHAS ===
    abstract protected function buscarDados(): array;
    abstract protected function contarTotal(): int;
    abstract protected function renderizarLinha(array $item): void;


    // === LÓGICA REUTILIZÁVEL (MÉTODO HOOK) ===
    protected function renderizarPaginacao(int $totalPaginas) {
        echo "<tr><td colspan='{$this->colspan}' class='paginacao'>";
        
        if ($this->pagina > 1) {
            echo "<a href='{$this->currentPageFile}?secao={$this->secaoNome}&{$this->paginaQueryParam}=" . ($this->pagina - 1) . "'>⬅ Anterior</a> ";
        }

        echo "Página {$this->pagina} de {$totalPaginas}";
        
        if ($this->pagina < $totalPaginas) {
            echo " <a href='{$this->currentPageFile}?secao={$this->secaoNome}&{$this->paginaQueryParam}=" . ($this->pagina + 1) . "'>Próxima ➡</a>";
        }
        echo "</td></tr>";
    }
}
?>