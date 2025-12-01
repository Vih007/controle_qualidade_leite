<?php
// Arquivo: BackEnd/listar_producao_leite.php (REFACTOR)

require_once 'conexao.php';
require_once __DIR__ . '/template/ListagemProducao.php'; 

// Tática de Detecção de Falhas: Protege contra a conexão nula
if (is_null($banco)) {
    echo "<tr><td colspan='6'><strong>Erro: Conexão com o Banco de Dados indisponível.</strong></td></tr>";
    return;
}

// 1. Cria a instância do Template Concreto
$listagemProducao = new ListagemProducao($banco);

// 2. EXECUTA O TEMPLATE METHOD
$listagemProducao->renderizarListagem();
?>