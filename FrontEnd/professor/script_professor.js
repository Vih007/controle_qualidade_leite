// --- 1. Controle do menu de perfil (dropdown) ---
const botaoPerfilProf = document.getElementById('btnPerfil');
const menuPerfilProf = document.getElementById('menuPerfil');

if (botaoPerfilProf && menuPerfilProf) {
  botaoPerfilProf.addEventListener('click', function (e) {
    e.stopPropagation();
    menuPerfilProf.style.display = (menuPerfilProf.style.display === 'block') ? 'none' : 'block';
  });

  document.addEventListener('click', function (e) {
    if (!menuPerfilProf.contains(e.target) && e.target !== botaoPerfilProf) {
      menuPerfilProf.style.display = 'none';
    }
  });
}

// --- 2. Função de confirmação de saída ---
function confirmarSaida() {
  return confirm("Deseja realmente sair?");
}

// --- 3. Função para abrir seções ---
function mostrarSecao(secao) {
  document.querySelectorAll('.aba-conteudo, .conteudo').forEach(sec => sec.style.display = 'none');
  document.querySelectorAll('nav button.btn').forEach(btn => btn.classList.remove('ativo'));

  const elemento = document.getElementById(secao);
  if (elemento) {
    elemento.style.display = 'block';
  }
}

// --- 4. Funções de edição e exclusão de alunos ---
function editarAluno(button) {
  const row = button.closest('tr');
  const id = row.getAttribute('data-id');

  const nomeCell = row.querySelector('.nome-aluno');
  const emailCell = row.querySelector('.email-aluno');

  const nomeAtual = nomeCell.textContent.trim();
  const emailAtual = emailCell.textContent.trim();

  nomeCell.innerHTML = `<input type="text" class="input-edicao" value="${nomeAtual}">`;
  emailCell.innerHTML = `<input type="email" class="input-edicao" value="${emailAtual}">`;

  button.textContent = 'Salvar';
  button.classList.remove('btn-editar-aluno');
  button.classList.add('btn-salvar-aluno');
  button.onclick = function () {
    const novoNome = nomeCell.querySelector('input').value;
    const novoEmail = emailCell.querySelector('input').value;
    salvarEdicaoAluno(id, novoNome, novoEmail, row, button);
  };
}

function salvarEdicaoAluno(id, nome, email, row, button) {
  fetch('../../BackEnd/editar_aluno.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id=${id}&nome=${encodeURIComponent(nome)}&email=${encodeURIComponent(email)}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      row.querySelector('.nome-aluno').textContent = nome;
      row.querySelector('.email-aluno').textContent = email;
      button.textContent = 'Editar';
      button.classList.remove('btn-salvar-aluno');
      button.classList.add('btn-editar-aluno');
      button.onclick = function () { editarAluno(button); };
    } else {
      alert('Erro ao atualizar aluno: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Erro na requisição:', error);
    alert('Erro na requisição: ' + error);
  });
}

function excluirAluno(id, button) {
  if (confirm('Tem certeza que deseja excluir este aluno?')) {
    const row = button.closest('tr');
    fetch('../../BackEnd/excluir_aluno.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id=${id}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        row.remove();
      } else {
        alert('Erro ao excluir aluno: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Erro ao excluir:', error);
      alert('Erro ao excluir aluno.');
    });
  }
}

// --- 5. Marcar alerta como lido ---
function marcarComoLido(id, btn) {
  const formData = new FormData();
  formData.append('id', id);

  fetch('../../BackEnd/marcar_lido.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(response => {
    if (response.success) {
      const tr = btn.closest('tr');
      const statusCell = tr.querySelector('.status-celula');
      
      if (statusCell) {
        // Adiciona o ícone de visualizado e remove o botão
        statusCell.innerHTML = '<span class="icon-visualizado">&#x2714;&#x2714;</span>';
      }

      // Remove o alerta após 2 dias
      setTimeout(() => { tr.remove(); }, 2 * 24 * 60 * 60 * 1000);
    } else {
      alert("Erro ao marcar como lido: " + response.message);
    }
  })
  .catch(error => {
    console.error("Erro na requisição:", error);
    alert("Erro na requisição: " + error);
  });
}

// --- 6. Carregar alertas (chama listar_alertas.php) ---
function carregarAlertas() {
  fetch('../../BackEnd/listar_alertas.php')
    .then(res => res.text())
    .then(html => {
      const tbody = document.getElementById('conteudo-alertas');
      if (tbody) {
        tbody.innerHTML = html; // insere só as linhas
        adicionarListenersAlerta(); // mantém os botões funcionando
      }
    })
    .catch(error => console.error('Erro ao carregar alertas:', error));
}

// --- Nova Função: Adiciona os listeners de clique aos botões de alerta ---
function adicionarListenersAlerta() {
  document.querySelectorAll('.btn-marcar-lido').forEach(btn => {
    btn.addEventListener('click', function() {
      const tr = this.closest('tr');
      const id = tr.getAttribute('data-id');
      if (id) {
        marcarComoLido(id, this);
      }
    });
  });
}

// --- 7. Inicialização ---
document.addEventListener('DOMContentLoaded', function () {
  console.log("DOM carregado e script_professor.js ativo!");

  // Abre automaticamente a aba de alertas e carrega os dados
  mostrarSecao('alertas');
  carregarAlertas();

  // Botão sair
  const btnSairProf = document.querySelector('button[title="Sair"]');
  if (btnSairProf) {
    btnSairProf.addEventListener('click', function () {
      if (confirmarSaida()) {
        window.location.href = '../../FrontEnd/logout.php';
      }
    });
  }
});
