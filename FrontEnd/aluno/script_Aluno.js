
const botaoPerfil = document.getElementById('btnPerfil');
const menuPerfil = document.getElementById('menuPerfil');

botaoPerfil.addEventListener('click', function (e) {
  e.stopPropagation(); // impede que o clique feche a caixa imediatamente
  menuPerfil.style.display = (menuPerfil.style.display === 'block') ? 'none' : 'block';
});

document.addEventListener('click', function (e) {
  if (!menuPerfil.contains(e.target) && e.target !== botaoPerfil) {
    menuPerfil.style.display = 'none';
  }
});

function confirmarSaida() {
  return confirm("Deseja realmente sair?");
}

// Objeto global para armazenar o mapeamento de nome para ID
let vacaNomesParaIds = {};

// Função para mostrar seções
function mostrarSecao(secao) {
  // Esconder todas as seções
  document.querySelectorAll('.conteudo').forEach(function (sec) {
    sec.style.display = 'none';
  });

  // Mostrar apenas a seção desejada
  document.getElementById(secao).style.display = 'block';
}

// NOVA FUNÇÃO: Carregar os nomes e IDs das vacas para o datalist e mapeamento
function carregarNomesVacasDatalist() {
  const vacasNomesDatalist = document.getElementById('vacasNomesDatalist');
  vacasNomesDatalist.innerHTML = ''; // Limpa as opções existentes
  vacaNomesParaIds = {}; // Limpa o mapeamento existente

  // Alterar o endpoint para buscar ID e Nome
  fetch('../../BackEnd/get_vacas_json_com_id.php') // Novo endpoint PHP que retorna ID e Nome
    .then(response => {
      if (!response.ok) {
        throw new Error('Erro ao carregar os nomes das vacas: ' + response.statusText);
      }
      return response.json();
    })
    .then(vacas => {
      vacas.forEach(vaca => {
        const option = document.createElement('option');
        option.value = vaca.nome;
        vacasNomesDatalist.appendChild(option);
        vacaNomesParaIds[vaca.nome] = vaca.id_vaca; // Armazena o mapeamento
      });
    })
    .catch(error => {
      console.error('Erro na requisição para carregar datalist:', error);
    });
}

// Funções para lidar com os inputs de datalist nos formulários de cadastro
function setupDatalistInput(inputElementId, hiddenInputElementId) {
  const inputElement = document.getElementById(inputElementId);
  const hiddenInputElement = document.getElementById(hiddenInputElementId);

  if (inputElement && hiddenInputElement) {
    inputElement.addEventListener('input', function () {
      const selectedName = this.value;
      if (vacaNomesParaIds[selectedName]) {
        hiddenInputElement.value = vacaNomesParaIds[selectedName];
      } else {
        hiddenInputElement.value = ''; // Limpa se o nome não for válido
      }
    });

    // Limpar o input e hidden ao exibir a seção de cadastro
    // Isso pode ser ajustado dependendo de como você quer que os formulários se comportem ao serem mostrados
    inputElement.value = '';
    hiddenInputElement.value = '';
  }
}


// Função para filtrar a tabela de vacas
function filtrarVacasTabela() {
  const input = document.getElementById('buscaVaca');
  const filtro = input.value.toUpperCase();
  const tabela = document.getElementById('id-tabela-vacas');
  const linhas = tabela.getElementsByTagName('tr');

  for (let i = 1; i < linhas.length; i++) { // Começa do 1 para pular o cabeçalho
    const colunaNome = linhas[i].getElementsByTagName('td')[1]; // Coluna do nome

    if (colunaNome) {
      const textoNome = colunaNome.textContent || colunaNome.innerText;

      if (textoNome.toUpperCase().indexOf(filtro) > -1) {
        linhas[i].style.display = '';
      } else {
        linhas[i].style.display = 'none';
      }
    }
  }
}

function editarVaca(button) {
  const row = button.closest('tr');
  const id = row.getAttribute('data-id');
  const nomeCell = row.querySelector('.nome-vaca');
  const nomeAtual = nomeCell.textContent;

  const input = document.createElement('input');
  input.type = 'text';
  input.value = nomeAtual;
  input.className = 'input-edicao';

  nomeCell.textContent = '';
  nomeCell.appendChild(input);
  input.focus();

  button.textContent = 'Salvar';
  button.classList.remove('btn-editar');
  button.classList.add('btn-salvar');
  button.onclick = function () {
    salvarEdicaoVaca(id, input.value, nomeCell, button);
  };
}

function salvarEdicaoVaca(id, novoNome, nomeCell, button) {
  fetch('../../BackEnd/atualizar_vaca.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `id_vaca=${id}&novo_nome=${encodeURIComponent(novoNome)}`
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        nomeCell.textContent = novoNome;
        button.textContent = 'Editar';
        button.classList.remove('btn-salvar');
        button.classList.add('btn-editar');
        button.onclick = function () {
          editarVaca(button);
        };
        carregarNomesVacasDatalist(); // CHAMADA ADICIONADA AQUI
      } else {
        alert('Erro ao atualizar: ' + data.message);
        location.reload();
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Erro ao atualizar a vaca');
      location.reload();
    });
}

function excluirVaca(id, button) {
  if (confirm('Tem certeza que deseja excluir esta vaca?')) {
    const row = button.closest('tr');

    fetch('../../BackEnd/deletar_vaca.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `id_vaca=${id}`
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          row.remove();
          const mensagem = document.createElement('div');
          mensagem.className = 'mensagem-sucesso';
          mensagem.textContent = 'Vaca excluída com sucesso!';
          document.body.appendChild(mensagem);
          setTimeout(() => mensagem.remove(), 3000);
          carregarNomesVacasDatalist(); // CHAMADA ADICIONADA AQUI
        } else {
          alert('Erro ao excluir: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Erro ao excluir a vaca');
      });
  }
}

function filtrarProducao() {
  const input = document.getElementById('buscaProducao');
  const filtro = input.value.toUpperCase();
  const tabela = document.getElementById('id-tabela-producao');
  const linhas = tabela.getElementsByTagName('tr');

  for (let i = 1; i < linhas.length; i++) {
    const colunaNome = linhas[i].getElementsByTagName('td')[1]; // Coluna do nome da vaca

    if (colunaNome) {
      const texto = colunaNome.textContent || colunaNome.innerText;

      if (texto.toUpperCase().indexOf(filtro) > -1) {
        linhas[i].style.display = '';
      } else {
        linhas[i].style.display = 'none';
      }
    }
  }
}

function editarProducao(button) {
  const row = button.closest('tr');
  const id = row.getAttribute('data-id');
  // CORREÇÃO: Usar a classe correta para a célula de quantidade
  const quantidadeCell = row.querySelector('.quantidade-producao');
  const dataCell = row.querySelector('.data-producao');

  const qtdAtual = quantidadeCell.textContent;
  const dataAtual = dataCell.textContent;

  quantidadeCell.innerHTML = `<input type="number" class="input-edicao" value="${qtdAtual}" step="0.1">`;
  dataCell.innerHTML = `<input type="date" class="input-edicao" value="${dataAtual}">`;

  button.textContent = 'Salvar';
  button.classList.remove('btn-editar');
  button.classList.add('btn-salvar');
  button.onclick = function () {
    const novaQtd = quantidadeCell.querySelector('input').value;
    const novaData = dataCell.querySelector('input').value;
    salvarEdicaoProducao(id, novaQtd, novaData, row, button);
  };
}

function salvarEdicaoProducao(id, novaQtd, novaData, row, button) {
  const idVaca = row.getAttribute('data-id-vaca'); // pegar id da vaca da linha

  fetch('../../BackEnd/atualizar_producao_leite.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `id_producao=${id}&id_vaca=${idVaca}&quantidade=${encodeURIComponent(novaQtd)}&data=${encodeURIComponent(novaData)}`
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        row.querySelector('.quantidade-producao').textContent = novaQtd + ' L';
        row.querySelector('.data-producao').textContent = novaData;
        button.textContent = 'Editar';
        button.classList.remove('btn-salvar');
        button.classList.add('btn-editar');
        button.onclick = function () {
          editarProducao(button);
        };
      } else {
        alert('Erro ao atualizar: ' + data.message);
      }
    })
    .catch(error => {
      alert('Erro na requisição: ' + error);
    });
}


function excluirProducao(id, button) {
  if (confirm('Tem certeza que deseja excluir essa produção?')) {
    const row = button.closest('tr');
    if (!row) {
      alert('Erro: não foi possível encontrar a linha da produção para excluir.');
      return;
    }

    fetch('../../BackEnd/excluir_producao_leite.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `id_producao=${id}`
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          row.remove();

          // Mostra uma mensagem de sucesso, igual na exclusão de vaca
          const mensagem = document.createElement('div');
          mensagem.className = 'mensagem-sucesso'; // Use uma classe de CSS para estilizar
          mensagem.textContent = 'Produção excluída com sucesso!';
          document.body.appendChild(mensagem);
          setTimeout(() => mensagem.remove(), 3000); // Remove a mensagem após 3 segundos

        } else {
          // Exibe a mensagem de erro vinda do PHP
          alert('Erro ao excluir: ' + (data.message || 'Ocorreu um erro desconhecido.'));
        }
      })
      .catch(error => {
        console.error('Erro na requisição:', error);
        alert('Erro ao conectar com o servidor para excluir a produção.');
      });
  }
}

function filtrarTeste() {
  const input = document.getElementById('buscaTeste');
  const filtro = input.value.toUpperCase();
  const tabela = document.getElementById('id-tabela-teste');
  const linhas = tabela.getElementsByTagName('tr');

  for (let i = 1; i < linhas.length; i++) {
    const colunaNome = linhas[i].getElementsByTagName('td')[1]; // Coluna do nome da vaca

    if (colunaNome) {
      const texto = colunaNome.textContent || colunaNome.innerText;

      if (texto.toUpperCase().indexOf(filtro) > -1) {
        linhas[i].style.display = '';
      } else {
        linhas[i].style.display = 'none';
      }
    }
  }
}

function editarTeste(button) {
      const row = button.closest('tr');
      const id = row.getAttribute('data-id');

      const resultadoCell = row.querySelector('.resultado-teste');
      const cruzesCell = row.querySelector('.cruzes-teste');
      const ubersCell = row.querySelector('.uberes-teste');
      const tratamentoCell = row.querySelector('.tratamento-teste');
      const observacoesCell = row.querySelector('.observacoes-teste');

      const resultadoAtual = resultadoCell.textContent.trim() === 'Positivo' ? 1 : 0;
      const cruzesAtual = cruzesCell.textContent.trim();
      const tratamentoAtual = tratamentoCell.textContent.trim();
      const observacoesAtual = observacoesCell.textContent.trim();
      const ubersAtuais = ubersCell.textContent.split(',').map(u => u.trim());

      resultadoCell.innerHTML = `
        <select class="input-edicao">
            <option value="1" ${resultadoAtual == 1 ? 'selected' : ''}>Positivo</option>
            <option value="0" ${resultadoAtual == 0 ? 'selected' : ''}>Negativo</option>
        </select>`;

      cruzesCell.innerHTML = `<input type="number" class="input-edicao" value="${cruzesAtual}" min="0" max="4">`;

      // UBERES CHECKBOXES
      const todasUbers = ['D.E', 'D.D', 'T.E', 'T.D'];
      ubersCell.innerHTML = todasUbers.map(ubere => {
        const checked = ubersAtuais.includes(ubere) ? 'checked' : '';
        return `
          <label style="margin-right: 8px;">
            <input type="checkbox" value="${ubere}" class="checkbox-ubere" ${checked}>
            ${ubere}
          </label>`;
      }).join('');

      tratamentoCell.innerHTML = `<input type="text" class="input-edicao" value="${tratamentoAtual}">`;
      observacoesCell.innerHTML = `<textarea class="input-edicao" rows="3">${observacoesAtual}</textarea>`;

      button.textContent = 'Salvar';
      button.classList.remove('btn-editar');
      button.classList.add('btn-salvar');
      button.onclick = function () {
        const novoResultado = resultadoCell.querySelector('select').value;
        const novasCruzes = cruzesCell.querySelector('input').value;
        const novoTratamento = tratamentoCell.querySelector('input').value;
        const novasObservacoes = observacoesCell.querySelector('textarea').value;

        const checkboxes = ubersCell.querySelectorAll('.checkbox-ubere');
        const novasUbers = Array.from(checkboxes)
          .filter(c => c.checked)
          .map(c => c.value)

        salvarEdicaoTeste(id, novoResultado, novasCruzes, novasUbers, novoTratamento, novasObservacoes, row, button);
      };
    }

    // Função para salvar a edição de um registro de teste de mastite.
function salvarEdicaoTeste(id, resultado, cruzes, ubersArrayParam, tratamento, observacoes, row, button) {
  const ubereFormatado = ubersArrayParam.join(', ');
  fetch('../../BackEnd/atualizar_teste_mastite.php', {
    method: 'POST',
    headers: {
     'Content-Type': 'application/x-www-form-urlencoded',
    },
    // Monta o body da requisição com todos os parâmetros que o PHP espera
    body: `id_teste=${id}&resultado=${resultado}&quantas_cruzes=${cruzes}&tratamento=${encodeURIComponent(tratamento)}&observacoes=${encodeURIComponent(observacoes)}&ubere=${encodeURIComponent(ubereFormatado)}`
  })
    .then(res => res.json())
    .then(data => {
    console.log(data); // Sempre bom para depurar a resposta do backend
    if (data.success) {
      // Atualiza as células da tabela com os novos valores
      row.querySelector('.resultado-teste').textContent = resultado == 1 ? 'Positivo' : 'Negativo';
      row.querySelector('.cruzes-teste').textContent = cruzes;
      row.querySelector('.tratamento-teste').textContent = tratamento;
      row.querySelector('.observacoes-teste').textContent = observacoes;
      row.querySelector('.uberes-teste').textContent = ubereFormatado; // Atualiza o texto dos úberes na tabela

      // Volta o botão para o estado "Editar"
      button.textContent = 'Editar';
      button.classList.remove('btn-salvar');
      button.classList.add('btn-editar');
      button.onclick = function () {
        editarTeste(button);
      };
    } else {
      alert('Erro ao atualizar: ' + data.message);
    }
  })
  .catch(error => {
     console.error('Erro na requisição:', error);
     alert('Erro na requisição: ' + error);
  });
}

function excluirTeste(id, button) {
  if (confirm('Tem certeza que deseja excluir este teste?')) {
    const row = button.closest('tr');
    fetch('../../BackEnd/excluir_teste_mastite.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `id_teste=${id}`
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          row.remove();
        } else {
          alert('Erro ao excluir: ' + data.message);
        }
      });
  }
}

// Adicione este listener para o evento 'change' do input de arquivo
// Ele irá apenas atualizar o nome do arquivo selecionado no campo de texto
document.getElementById('arquivo_relatorio').addEventListener('change', function() {
    const fileName = this.files[0] ? this.files[0].name : 'Nenhum arquivo escolhido';
    document.getElementById('nome-arquivo-selecionado').textContent = fileName;
});


/// ... (outras funções do seu script)

// Adicione este listener para o evento 'change' do input de arquivo
// Ele irá apenas atualizar o nome do arquivo selecionado no campo de texto
document.getElementById('arquivo_relatorio').addEventListener('change', function() {
    const fileName = this.files[0] ? this.files[0].name : 'Nenhum arquivo escolhido';
    document.getElementById('nome-arquivo-selecionado').textContent = fileName;
});


// ... (outras funções do seu script)

const inputRelatorio = document.getElementById('arquivo_relatorio');
const btnEnviar = document.getElementById('btn-enviar-relatorio');
const nomeArquivo = document.getElementById('nome-arquivo-selecionado');

inputRelatorio.addEventListener('change', function() {
    if(this.files.length > 0) {
        btnEnviar.disabled = false; // ativa o botão
        nomeArquivo.textContent = this.files[0].name; // mostra nome do arquivo
    } else {
        btnEnviar.disabled = true; // desativa caso nenhum arquivo
        nomeArquivo.textContent = "Nenhum arquivo escolhido";
    }
});

// Listener para o envio do relatório
btnEnviar.addEventListener('click', function (e) {
    e.preventDefault();

    const file = inputRelatorio.files[0];
    if (!file) {
        alert("Nenhum arquivo selecionado!");
        return;
    }

    // Defina o tamanho dos blocos (em bytes)
    const chunkSize = 1024 * 1024; // 1MB por bloco
    const totalChunks = Math.ceil(file.size / chunkSize);
    let currentChunk = 0;
    const fileId = `${file.name}-${file.size}-${file.lastModified}`; // ID único para o arquivo

    // Inicia o upload
    uploadChunk();

    function uploadChunk() {
        const start = currentChunk * chunkSize;
        const end = Math.min(start + chunkSize, file.size);
        const chunk = file.slice(start, end);

        const formData = new FormData();
        formData.append('file_chunk', chunk);
        formData.append('file_name', file.name);
        formData.append('file_id', fileId);
        formData.append('chunk_index', currentChunk);
        formData.append('total_chunks', totalChunks);

        fetch('../../BackEnd/upload_relatorio.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentChunk++;
                if (currentChunk < totalChunks) {
                    console.log(`Bloco ${currentChunk} de ${totalChunks} enviado.`);
                    // Aumentar o progresso na barra de progresso aqui
                    uploadChunk(); // Envia o próximo bloco
                } else {
                    // O upload completo já foi processado no backend
                    // Recarrega a página para exibir a nova lista e a mensagem da sessão
                    window.location.href = 'area_comum_aluno.php?secao=relatorios';
                }
            } else {
                alert('Erro no upload: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro na requisição de upload:', error);
            alert('Erro na requisição: ' + error);
        });
    }
});



// [NOVA FUNÇÃO] - Responsável por carregar o conteúdo PHP dinamicamente via AJAX
function carregarConteudoDaSecao(secao) {
  let url = '';
  let tbodySelector = '';

  if (secao === 'lista') {
    url = '../../BackEnd/listar_vacas.php?pagina_vacas=1';
    tbodySelector = '#id-tabela-vacas tbody';
  } else if (secao === 'producao') {
    url = '../../BackEnd/listar_producao_leite.php?pagina_producao=1';
    tbodySelector = '#id-tabela-producao tbody';
  } else if (secao === 'teste_mastite') {
    url = '../../BackEnd/listar_teste_mastite.php?pagina_testes=1';
    tbodySelector = '#id-tabela-teste tbody';
  }

  if (url && tbodySelector) {
    fetch(url)
      .then(response => response.text())
      .then(html => {
        document.querySelector(tbodySelector).innerHTML = html;
      })
      .catch(error => {
        console.error('Erro ao carregar conteúdo da seção:', error);
        document.querySelector(tbodySelector).innerHTML = '<tr><td colspan="9">Falha ao carregar dados. Verifique o servidor.</td></tr>';
      });
  }
}

// [FUNÇÃO MOSTRARSECAO ATUALIZADA]
function mostrarSecao(secao) {
    // Esconder todas as seções
    document.querySelectorAll('.conteudo').forEach(function (sec) {
        sec.style.display = 'none';
    });

    // Mostrar apenas a seção desejada
    const secaoAlvo = document.getElementById(secao);
    secaoAlvo.style.display = 'block';

    // Se for uma lista, carregar o conteúdo via AJAX
    if (secao === 'lista' || secao === 'producao' || secao === 'teste_mastite') {
        carregarConteudoDaSecao(secao);
    }
    
    // Se for a seção de cadastro de produção ou teste, configure o datalist
    if (secao === 'cadastro_producao') {
        setupDatalistInput('vaca_producao_nome', 'id_vaca_producao_hidden');
    } else if (secao === 'cadastro_teste') {
        setupDatalistInput('vaca_teste_nome', 'id_vaca_teste_hidden');
    }

    // Se houver uma mensagem global, move ela para o topo da seção ativa
    const mensagem = document.getElementById('mensagem-global');
    if (mensagem && secaoAlvo) {
        secaoAlvo.prepend(mensagem);
    }

    // Remove a mensagem se ainda existir após 4 segundos
    if (mensagem) {
        setTimeout(() => {
            mensagem.remove();
        }, 5000);
    }
}


// [FUNÇÃO WINDOW.ONLOAD ATUALIZADA]
window.onload = function () {
  const params = new URLSearchParams(window.location.search);
  const secao = params.get('secao');
  
  // Define 'lista' (Vacas) como padrão se não houver parâmetro na URL
  const secaoInicial = secao || 'lista'; 
  
  mostrarSecao(secaoInicial); 
  carregarNomesVacasDatalist(); 
};