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
        <li><a href="#" onclick="mostrarSecao('gerar_lote_automatico')">Relatório de Lotes</a></li>
        <li><a href="#" onclick="mostrarSecao('score_saude')">Score de Saúde</a></li>
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


         <section id="score_saude" class="conteudo">
            <h3>Relatório de Score de Saúde do Rebanho</h3>
            <p>Esta seção exibe a pontuação de risco de cada animal (máximo de 100). Scores abaixo de 50 (vermelho) indicam alto risco.</p>
            <input type="text" id="buscaScore" placeholder="Buscar vaca por nome..." oninput="filtrarScores()" class="input-busca" style="margin-bottom: 20px;">
            <?php require("../../BackEnd/listar_scores_saude.php"); ?> 
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

    <section id="gerar_lote_automatico" class="conteudo bloco-pagina">
      <form class="form-padrao" id="form-gerar-lote">
          <label for="data_lote_auto">Data da Produção</label>
          <input type="date" id="data_lote_auto" name="data" required>

          <label for="id_tanque_auto">Tanque de Destino</label>
          <select id="id_tanque_auto" name="id_tanque" required>
              <option value="">Selecione um tanque</option>
              <?php
              require_once("../../BackEnd/conexao.php");
              $stmt = $banco->query("SELECT id_tanque, localizacao FROM tanque ORDER BY localizacao");
              foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $t) {
                  echo "<option value='{$t['id_tanque']}'>" . htmlspecialchars($t['localizacao']) . "</option>";
              }
              ?>
          </select>

          <button type="submit" class="btn">Gerar Lote com Produção Agregada</button>
      </form>

      <div id="relatorio-unico-container" style="margin-top:20px;"></div>
  </section>


  </main> 
    <script src="script_professor.js"></script>
    <script>
      document.getElementById("form-gerar-lote").addEventListener("submit", async function(e){
          e.preventDefault();
          const form = e.target;
          const dados = new FormData(form);

          const container = document.getElementById("relatorio-unico-container");
          container.innerHTML = "Gerando lote...";

          try {
              const resp = await fetch("../../BackEnd/salvar_lotes.php", {method:"POST", body:dados});
              const resultado = await resp.json();

              if(resultado.status === "ok") {
                  container.innerHTML = `
                    <div class="relatorio-lote">
                        <h3>Lote Gerado com Sucesso!</h3>
                        <p><strong>ID do Lote:</strong> ${resultado.lote.id}</p>
                        <p><strong>Data:</strong> ${resultado.lote.data}</p>
                        <p><strong>Quantidade Total:</strong> ${resultado.lote.quantidade_total} L</p>
                        <p><strong>Tanque:</strong> ${resultado.lote.tanque}</p>
                        <p><strong>Vacas:</strong> ${resultado.lote.vacas}</p>
                    </div>
                `;
                container.style.display = 'block';
              } else {
                  container.innerHTML = `<p style="color:red;">Erro: ${resultado.mensagem}</p>`;
              }
          } catch(err) {
              container.innerHTML = `<p style="color:red;">Erro ao gerar lote: ${err.message}</p>`;
          }
      });
    </script>

</body>

</html>