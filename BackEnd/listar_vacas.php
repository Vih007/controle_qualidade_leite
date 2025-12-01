<?php
// Arquivo: BackEnd/listar_vacas.php (REFACTOR)

require("conexao.php"); 
// Agora só precisa incluir a classe ListagemVacas
require_once __DIR__ . '/template/ListagemVacas.php'; 

// Tática de Detecção de Falhas: Protege contra a conexão nula
if (is_null($banco)) {
    echo "<tr><td colspan='5'><strong>Erro: Conexão com o Banco de Dados indisponível.</strong></td></tr>";
    return;
}

// 1. Cria a instância do Template Concreto
$listagemVacas = new ListagemVacas($banco);

// 2. EXECUTA O TEMPLATE METHOD: o único passo que resta!
$listagemVacas->renderizarListagem();

// O código fica muito mais limpo, sem lógica de paginação repetida.
?>