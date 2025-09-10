<?php 
session_start(); // **CRÍTICO: Adicione esta linha no topo para iniciar a sessão**
include("../../BackEnd/criar_alerta.php"); // Inclua criar_alerta.php se for usado diretamente aqui para exibir mensagens
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="../imgs/IconeProjeto.png" type="image/x-icon" />
    <link rel="stylesheet" href="../style/usuarios.css" />
    <title>Página do Professor</title>
    <style>
    /* Garante que todas as seções estejam escondidas por padrão */
    .conteudo {
      display: none;
    }

    /* Mostra apenas a lista inicialmente */
    #alertas {
      display: block;
    }

  </style>
</head>

<body>
    <div class="faixa-decorada"></div>
      <header id="menu">
        <div class="logo-container">
          <img src="../imgs/iconePagPreto.png" alt="Logo do projeto" class="logo" />
        <div class="texto-logo">
           <h1 class="barlow-regular">CQL</h1>
              <h2 class="barlow-regular">Controle de Qualidade do Leite</h2>
        </div>
    </div>

    <div class="caixa-perfil">
            <button id="btnPerfil" title="<?php echo htmlspecialchars($_SESSION['nome'] ?? 'Usuário'); ?>">
              <img src="../imgs/perfil.png" alt="Perfil de <?php echo htmlspecialchars($_SESSION['nome'] ?? 'usuário'); ?>">
            </button>

            <section id="menuPerfil" class="info-do-usuario" style="display: none;">
            <p><strong><?php echo htmlspecialchars($_SESSION['nome'] ?? 'Sem nome'); ?></strong></p>
            <p><?php echo htmlspecialchars($_SESSION['email'] ?? 'Sem email'); ?></p>
            </section>

            <button title="Sair">
              <img src="../imgs/sair.png" alt="Botão sair">
            </button>
    </div>
    </header>
    <div class="faixa-decorada"></div>

  <main class="painel">
    <nav class="menu-lateral">
      <h2>Comandos</h2>
      <ul>
        <li><a href="#" onclick="mostrarSecao('alertas')">Alertas</a></li>
        <li><a href="#" onclick="mostrarSecao('alunos')">Alunos</a></li>
        <li><a href="#" onclick="mostrarSecao('relatorios')">Relatórios</a></li>
      </ul>
    </nav>

        <section id="alertas" class="conteudo">
          <h2>Alertas</h2>
          <table class="tabela">
            <thead>
              <tr>
                <th>Vaca</th>
                <th>Alerta</th>
                <th>Data</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="conteudo-alertas">
              <!-- Aqui o JS vai colocar as linhas -->
            </tbody>
          </table>
        </section>

        <section id="alunos" class="conteudo" style="display:none">
            <h2>Alunos</h2>
            <button onclick="mostrarSecao('cadastro_aluno')" class="btn">Cadastrar Novo Aluno</button>
                <table class="tabela" id="conteudo-alunos">
                  <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Ações</th>
                        </tr>
                  </thead>
                    <tbody>
                        <?php require('../../BackEnd/listar_alunos.php') ?>
                    </tbody>
                </table>
         </section>

         <section id="cadastro_aluno" class="conteudo bloco-pagina">
            <h3>Cadastro de Aluno</h3>
            <?php include('../mensagem.php') ?>
            <!-- Formulário para inserir o aluno -->
            <form class="form-padrao" method="POST" action="../../BackEnd/cadastrar_usuario.php">
              <label for="nome">Nome</label>
              <input type="text" id="nome" name="nome" placeholder="Digite o nome do aluno" required>

              <label for="email">Email</label>
              <input type="email" id="email" name="email" placeholder="Digite o email do aluno" required>

              <label for="senha">Senha</label>
              <input type="password" id="senha" name="senha" placeholder="Digite a senha" required>

              <input type="hidden" name="tipo_usuario" value="aluno">

              <button type="submit" class="btn">Cadastrar</button>
            </form>
         </section>


        <section id="relatorios" class="conteudo">
          <h3>Relatórios Recebidos</h3>
            <table class="tabela" id="id-tabela-vacas">
              <thead>
                <tr>
                  <th>Aluno</th>
                  <th>Nome do Arquivo</th>
                  <th>Data de Envio</th>
                  <th>Ações</th> </tr>
              </thead>
              <tbody>
                <?php
                  require('../../BackEnd/listar_relatorio.php');
                ?>
              </tbody>
            </table>
        </section>
  </main> 
    <script src="script_professor.js"></script>
</body>

</html>