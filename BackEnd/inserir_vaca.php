<?php
session_start();
require('conexao.php'); 
require_once __DIR__ . '/repository/VacaRepository.php';
require_once __DIR__ . '/service/VacaService.php';
require_once __DIR__ . '/dto/VacaDTO.php';
include('criar_alerta.php');

if (isset($_POST['nome'], $_POST['id_lote_manejo'])) {
    
    $nome = $_POST['nome'];
    $id_lote_manejo = (int)$_POST['id_lote_manejo'];
    $descarte = 0;
    
    try {
        $vacaDto = new VacaDTO($nome, $id_lote_manejo, $descarte);
        
        $repository = new VacaRepository($banco); 
        $service = new VacaService($repository);
        
        if ($service->cadastrarVaca($vacaDto)) {
            criar_alerta("Animal adicionado com Sucesso", "sucesso");
            header("Location:../FrontEnd/aluno/area_comum_aluno.php?secao=cadastro");
            exit;
        }
        
    } catch (PDOException $e) {
        criar_alerta("Erro no banco de dados: " . $e->getMessage(), "erro");
        header("Location:../FrontEnd/aluno/area_comum_aluno.php?secao=cadastro");
        exit;
    } catch (Exception $e) {
        criar_alerta("Erro de validação: " . $e->getMessage(), "erro");
        header("Location:../FrontEnd/aluno/area_comum_aluno.php?secao=cadastro");
        exit;
    }
} else {
    criar_alerta("Erro: Dados incompletos para o cadastro da vaca.", "erro");
    header("Location:../FrontEnd/aluno/area_comum_aluno.php?secao=cadastro");
    exit;
}
?>