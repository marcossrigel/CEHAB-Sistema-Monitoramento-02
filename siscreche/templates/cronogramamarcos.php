<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$tipo_usuario = $_SESSION['tipo_usuario'] ?? 'usuario';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

include_once('config.php');
mysqli_set_charset($conexao, "utf8mb4");

$id_iniciativa = (int) ($_GET['id_iniciativa'] ?? 0);
$id_usuario = (int) ($_SESSION['id_usuario'] ?? 0);

if (isset($_POST['etapa'])) {
    $id_etapa_custom = $_POST['id_etapa_custom'] ?? [];
    $etapa = $_POST['etapa'] ?? [];
    $inicio_previsto = $_POST['inicio_previsto'] ?? [];
    $termino_previsto = $_POST['termino_previsto'] ?? [];
    $inicio_real = $_POST['inicio_real'] ?? [];
    $termino_real = $_POST['termino_real'] ?? [];
    $evolutivo = $_POST['evolutivo'] ?? [];
    $ids = $_POST['ids'] ?? [];
    $tipo_etapa = $_POST['tipo_etapa'] ?? [];

    if (count($etapa) === 0) {
        echo "<p style='color:red;text-align:center'>Nenhuma linha foi enviada.</p>";
        exit;
    }

    for ($i = 0; $i < count($etapa); $i++) {
        $etapa_custom = mysqli_real_escape_string($conexao, $id_etapa_custom[$i] ?? '');
        $id_existente = intval($ids[$i] ?? 0);
        $etp = mysqli_real_escape_string($conexao, $etapa[$i]);

        $ini_prev = trim($inicio_previsto[$i]) !== '' ? "'" . mysqli_real_escape_string($conexao, $inicio_previsto[$i]) . "'" : "NULL";
        $ter_prev = trim($termino_previsto[$i]) !== '' ? "'" . mysqli_real_escape_string($conexao, $termino_previsto[$i]) . "'" : "NULL";
        $ini_real = trim($inicio_real[$i]) !== '' ? "'" . mysqli_real_escape_string($conexao, $inicio_real[$i]) . "'" : "NULL";
        $ter_real = trim($termino_real[$i]) !== '' ? "'" . mysqli_real_escape_string($conexao, $termino_real[$i]) . "'" : "NULL";

        $evo_raw = trim($evolutivo[$i]);
        $evo = $evo_raw !== '' ? "'" . mysqli_real_escape_string($conexao, $evo_raw) . "'" : "NULL";

        $tipo = mysqli_real_escape_string($conexao, $tipo_etapa[$i] ?? 'linha');

        if ($id_existente > 0) {
            $query = "UPDATE marcos SET 
                tipo_etapa='$tipo',
                etapa='$etp',
                id_etapa_custom='$etapa_custom',
                inicio_previsto=$ini_prev,
                termino_previsto=$ter_prev,
                inicio_real=$ini_real,
                termino_real=$ter_real,
                evolutivo=$evo
              WHERE id = $id_existente AND id_usuario = $id_usuario";
        } else {
            $query = "INSERT INTO marcos (
                id_usuario, id_iniciativa, id_etapa_custom, tipo_etapa, etapa,
                inicio_previsto, termino_previsto, inicio_real, termino_real, evolutivo
              ) VALUES (
                '$id_usuario', '$id_iniciativa', '$etapa_custom', '$tipo', '$etp',
                $ini_prev, $ter_prev, $ini_real, $ter_real, $evo
              )";
        }

        if (!mysqli_query($conexao, $query)) {
            echo "Erro: " . mysqli_error($conexao);
            exit;
        }
    }
}

$query_nome = "SELECT iniciativa FROM iniciativas WHERE id = $id_iniciativa";
$resultado_nome = mysqli_query($conexao, $query_nome);
$linha_nome = mysqli_fetch_assoc($resultado_nome);
$nome_iniciativa = $linha_nome['iniciativa'] ?? 'Iniciativa Desconhecida';

$query_dados = "SELECT * FROM marcos WHERE id_usuario = $id_usuario AND id_iniciativa = $id_iniciativa";
$dados = mysqli_query($conexao, $query_dados);

function formatarParaBrasileiro($valor) {
    return number_format((float)$valor, 2, ',', '.');
}
?>

<style>
:root {
    --color-dark: #1d2129;
}
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
html, body {
    font-family: 'Poppins', sans-serif;
    background: #e3e8ec;
    min-height: 100vh;
}
.table-container {
    width: 95%;
    margin: 40px auto;
    background: #fff;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    overflow-x: auto;
}
.campo-etapa-subtitulo {
    font-weight: bold;
    width: 100%;
    min-width: 200px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    padding: 4px 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
    text-align: center;
}
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 8px 15px;
    table-layout: fixed;          
}
th, td {
    text-align: left;
    padding: 10px;
}
td[contenteditable] {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 8px;
    min-width: 120px;
}
td[contenteditable]:focus {
    outline: none;
    border: 1px solid #4da6ff;
    background-color: #f0f8ff;
}
input[type="text"],
input[type="date"] {
    height: 20px;
    padding: 4px 8px;
    font-size: 13px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-family: 'Poppins', sans-serif;
    width: 100%;
    box-sizing: border-box;
}
.main-title {
    font-size: 26px;
    color: var(--color-dark);
    text-align: center;
    margin-bottom: 20px;
}
.button-group {
    margin-top: 20px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
}
.button-group button {
    padding: 10px 20px;
    background-color: #4da6ff;
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}
.button-group button:hover {
    background-color: #3399ff;
}
textarea {
    resize: vertical; 
}
@media (max-width: 768px) {
    .main-title {
        font-size: 20px;
        padding: 0 10px;
    }
    table {
        font-size: 13px;
        display: block;
        overflow-x: auto;
    }
    td[contenteditable], input[type="text"], input[type="date"], input[type="number"], textarea {
        min-width: 90px;
        font-size: 13px;
    }
    .button-group {
        flex-direction: column;
        align-items: center;
    }
    .button-group button {
        width: 100%;
        max-width: 250px;
    }
}
</style>

<div class="table-container">
  <div class="main-title"><?php echo htmlspecialchars($nome_iniciativa); ?> - Cronograma de Marcos</div>
  <form method="post" action="<?= $_SERVER['REQUEST_URI']; ?>">

    <table id="spreadsheet">
      <thead>
        <tr>
          <th style="width: 65px;">ID</th>
          <th>Etapa</th>
          <th>Início Previsto</th>
          <th>Término Previsto</th>
          <th>Início Real</th>
          <th>Término Real</th>
          <th>% Evolutivo</th>
        </tr>
      </thead>

      <tbody>
        <?php while ($linha = mysqli_fetch_assoc($dados)) { ?>
          <tr data-id="<?= $linha['id'] ?>">
          
          <td style="max-width:50px;">
            <input type="text" name="id_etapa_custom[]" value="<?php echo htmlspecialchars($linha['id_etapa_custom']); ?>" 
              style="width: 60px; font-size: 13px; padding: 4px 6px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; text-align: center;">
          </td>

          
          <td>
              <?php if ($linha['tipo_etapa'] === 'subtitulo') { ?>
                <input type="text" name="etapa[]" value="<?php echo htmlspecialchars($linha['etapa']); ?>" 
                  class="campo-etapa-subtitulo" style="font-weight: bold; text-align: center;">
                <?php } else { ?>

                <textarea name="etapa[]" rows="2" class="campo-etapa" 
                  style="width:100%; font-family:'Poppins', sans-serif; font-size:13px; padding:4px 8px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;"><?php echo htmlspecialchars($linha['etapa']); ?></textarea>
              <?php } ?>
              <input type="hidden" name="ids[]" value="<?php echo $linha['id']; ?>">
              <input type="hidden" name="tipo_etapa[]" value="<?php echo htmlspecialchars($linha['tipo_etapa']); ?>">
            </td>

            <td><input type="date" name="inicio_previsto[]" value="<?php echo $linha['inicio_previsto']; ?>"></td>
            <td><input type="date" name="termino_previsto[]" value="<?php echo $linha['termino_previsto']; ?>"></td>
            <td><input type="date" name="inicio_real[]" value="<?php echo $linha['inicio_real']; ?>"></td>
            <td><input type="date" name="termino_real[]" value="<?php echo $linha['termino_real']; ?>"></td>
            <td><input type="number" name="evolutivo[]" value="<?php echo $linha['evolutivo']; ?>" min="0" max="100" step="0.1" placeholder="0 a 100%"></td>
            
          </tr>
        <?php } ?>
      </tbody>

    </table>
    <div class="button-group">
      <button type="button" onclick="addTitleRow()">Adicionar Etapa</button>
      <button type="button" onclick="addRow()">Adicionar Sub-Etapa</button>
      <button type="button" onclick="deleteRow()">Excluir Linha</button>
      <button type="submit" name="salvar" id="submit" style="background-color:rgb(42, 179, 0);">Salvar</button>
      
      <?php
      $voltar_url = 'index.php?page=visualizar';
      if ($tipo_usuario === 'admin' && isset($_GET['diretoria'])) {
          $diretoria = urlencode($_GET['diretoria']);
          $voltar_url .= "&diretoria=$diretoria";
      }
      ?>
      <button type="button" onclick="window.location.href='index.php?page=visualizar';">&lt; Voltar</button>
      <div style="margin-top: 10px;">
        <input type="text" id="idExcluir" placeholder="ID para excluir (ex: 3.1)" style="padding: 5px; width: 160px;">
        <button type="button" onclick="excluirLinhaPorId()">Excluir Linha Específica</button>
      </div>
    
    </div>

      <div id="modalInserirLinha" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); z-index: 9999;">
      <div style="background: #fff; padding: 20px; width: 320px; margin: 100px auto; border-radius: 10px; text-align: center;">
        <h3>Inserir Linha</h3>
        <label style="font-weight: bold;">Tipo da Linha:</label><br>
        <select id="tipoInsercao" style="margin: 10px 0; padding: 6px; width: 100%;">
          <option value="linha">Sub-Etapa</option>
          <option value="subtitulo">Etapa</option>
        </select>
        <p>Digite o ID antes do qual deseja inserir:</p>
        <input type="text" id="idParaInserir" placeholder="Ex: 2.1" style="padding: 6px; width: 100%;">
        <div style="margin-top: 15px;">
          <button onclick="confirmarInsercaoLinha()">Inserir</button>
          <button onclick="fecharModalInserirLinha()" style="margin-left: 10px;">Cancelar</button>
        </div>
      </div>
    </div>

  </form>

</div>

<script>

let ultimoIdEtapa = 0;
let subEtapasPorEtapa = {};

document.querySelector('form').addEventListener('submit', function(event) {
  const form = this;
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  const linhas = table.rows;
  let temLinhaValida = false;

  let tituloIndex = -1;
  let datasInicio = [];
  let datasTermino = [];


  for (let i = 0; i < linhas.length; i++) {
    const linha = linhas[i];
    const id = linha.getAttribute('data-id');
    const cells = linha.cells;

    const etapaField = cells[0].querySelector('textarea, input');
    
    let tipo = 'linha';
    if (etapaField?.placeholder === 'Título') {
      tipo = 'subtitulo';
    } else {
      const idValor = linha.querySelector('input[name="id_etapa_custom[]"]')?.value.trim();
      if (idValor && !idValor.includes('.')) {
        tipo = 'subtitulo'; // número inteiro sem ponto: ex 6, 7, 8 → é título
      }
    }

    const campos = [
      etapaField?.value.trim() || '',
      cells[1].querySelector('input')?.value.trim() || '',
      cells[2].querySelector('input')?.value.trim() || '',
      cells[3].querySelector('input')?.value.trim() || '',
      cells[4].querySelector('input')?.value.trim() || '',
      cells[5].querySelector('input')?.value.trim() || ''
    ];

    const linhaEstaVazia = campos.every(c => c === '');
    if (linhaEstaVazia) continue;

    temLinhaValida = true;

    if (!id) {
      const inputTipo = document.createElement('input');
      inputTipo.type = 'hidden';
      inputTipo.name = 'tipo_etapa[]';
      inputTipo.value = tipo;
      form.appendChild(inputTipo);

      const inputId = document.createElement('input');
      inputId.type = 'hidden';
      inputId.name = 'ids[]';
      inputId.value = '';
      form.appendChild(inputId);
    }

    if (tipo === 'subtitulo') {
      if (tituloIndex !== -1 && datasInicio.length > 0 && datasTermino.length > 0) {
        preencherDatas(linhas[tituloIndex], datasInicio, datasTermino);
      }
      tituloIndex = i;
      datasInicio = [];
      datasTermino = [];
    } else if (tipo === 'linha') {
      const dtInicio = cells[1].querySelector('input')?.value;
      const dtFim = cells[2].querySelector('input')?.value;
      if (dtInicio) datasInicio.push(dtInicio);
      if (dtFim) datasTermino.push(dtFim);
    }
  }

  if (tituloIndex !== -1 && datasInicio.length > 0 && datasTermino.length > 0) {
    preencherDatas(linhas[tituloIndex], datasInicio, datasTermino);
  }

  function preencherDatas(tituloRow, inicios, fins) {
    const campoInicio = tituloRow.querySelector('input[name="inicio_previsto[]"]');
    const campoFim = tituloRow.querySelector('input[name="termino_previsto[]"]');

    const menorData = inicios.sort()[0];
    const maiorData = fins.sort().reverse()[0];

    if (campoInicio) campoInicio.value = menorData;
    if (campoFim) campoFim.value = maiorData;
  }

  if (!temLinhaValida) {
    event.preventDefault();
    alert('Nenhuma medição válida para salvar!');
  } else {
    const inputs = form.querySelectorAll('textarea, input[type="text"], input[type="number"], input[type="date"]');
    inputs.forEach(input => {
      input.style.backgroundColor = '#e0ffe0';
      setTimeout(() => input.style.backgroundColor = '', 1000);
    });
  }
});

function addTitleRow() {
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  const newRow = table.insertRow();

  ultimoIdEtapa++;
  subEtapasPorEtapa[ultimoIdEtapa] = 0;

  const id = ultimoIdEtapa;
  const campos = ['etapa', 'inicio_previsto', 'termino_previsto', 'inicio_real', 'termino_real', 'evolutivo'];

  const idCell = newRow.insertCell();
  const idInput = document.createElement('input');
  idInput.type = 'text';
  idInput.name = 'id_etapa_custom[]';
  idInput.readOnly = true;
  idInput.value = id;
  idInput.className = 'input-padrao';
  idCell.appendChild(idInput);

  campos.forEach((campo, index) => {
    const cell = newRow.insertCell();
    const input = document.createElement('input');
    input.name = campo + '[]';
    input.className = 'input-padrao';
    if (index === 0) {
      input.placeholder = 'Título';
      input.type = 'text';
    } else {
      input.type = campo === 'evolutivo' ? 'number' : 'date';
      if (campo === 'evolutivo') {
        input.min = 0;
        input.max = 100;
        input.step = 0.1;
        input.placeholder = '0 a 100%';
      }
    }
    cell.appendChild(input);
  });

  // Corrigido: adiciona hidden input para tipo
  const tipoInput = document.createElement('input');
  tipoInput.type = 'hidden';
  tipoInput.name = 'tipo_etapa[]';
  tipoInput.value = 'subtitulo';
  newRow.appendChild(tipoInput);

  const idHidden = document.createElement('input');
  idHidden.type = 'hidden';
  idHidden.name = 'ids[]';
  idHidden.value = '';
  newRow.appendChild(idHidden);
}

function addRow() {
  if (ultimoIdEtapa === 0) {
    alert('Adicione uma Etapa antes de adicionar Sub-Etapas.');
    return;
  }

  const etapaPai = ultimoIdEtapa;
  subEtapasPorEtapa[etapaPai] = (subEtapasPorEtapa[etapaPai] || 0) + 1;

  const subId = `${etapaPai}.${subEtapasPorEtapa[etapaPai]}`;

  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  const newRow = table.insertRow();

  const campos = ['etapa', 'inicio_previsto', 'termino_previsto', 'inicio_real', 'termino_real', 'evolutivo'];

  const idCell = newRow.insertCell();
  const idInput = document.createElement('input');
  idInput.type = 'text';
  idInput.name = 'id_etapa_custom[]';
  idInput.readOnly = true;
  idInput.value = subId;
  idInput.className = 'input-padrao';
  idCell.appendChild(idInput);

  campos.forEach((campo, index) => {
    const cell = newRow.insertCell();
    if (index === 0) {
      const textarea = document.createElement('textarea');
      textarea.name = campo + '[]';
      textarea.rows = 2;
      textarea.className = 'campo-etapa';
      cell.appendChild(textarea);
    } else {
      const input = document.createElement('input');
      input.name = campo + '[]';
      input.type = campo === 'evolutivo' ? 'number' : 'date';
      if (campo === 'evolutivo') {
        input.min = 0;
        input.max = 100;
        input.step = 0.1;
        input.placeholder = '0 a 100%';
      }
      input.className = 'input-padrao';
      cell.appendChild(input);
    }
  });

  // Corrigido: adiciona hidden input para tipo
  const tipoInput = document.createElement('input');
  tipoInput.type = 'hidden';
  tipoInput.name = 'tipo_etapa[]';
  tipoInput.value = 'linha';
  newRow.appendChild(tipoInput);

  const idHidden = document.createElement('input');
  idHidden.type = 'hidden';
  idHidden.name = 'ids[]';
  idHidden.value = '';
  newRow.appendChild(idHidden);
}

function deleteRow() {
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  const lastRow = table.rows[table.rows.length - 1];
  if (!lastRow) return;

  const id = lastRow.getAttribute('data-id');

  if (id) {
    fetch(`templates/marcos_excluir_linha.php?id=${id}`, { method: 'GET' })
      .then(response => {
        if (!response.ok) throw new Error("Erro ao excluir do banco");
        return response.text();
      })
      .then(data => {
        console.log(data);
        table.deleteRow(-1);
      })
      .catch(error => {
        alert("Erro ao excluir no servidor.");
        console.error(error);
      });
  } else {
    table.deleteRow(-1);
    recalcularUltimoIdEtapa();
  }
}

function converterParaFloatBrasileiro(valor) {
  return valor.replace(/\./g, '').replace(',', '.');
}

function converterParaDataISO(dataBR) {
  if (!dataBR.includes('/')) return dataBR;
  const partes = dataBR.split('/');
  if (partes.length === 3) {
    return `${partes[2]}-${partes[1]}-${partes[0]}`;
  }
  return dataBR;
}

document.addEventListener('DOMContentLoaded', () => {
  recalcularUltimoIdEtapa();
  copiarInicioPrevistoDasSubetapas();
});

function recalcularUltimoIdEtapa() {
  ultimoIdEtapa = 0;
  subEtapasPorEtapa = {};
  const ids = document.querySelectorAll('input[name="id_etapa_custom[]"]');
  ids.forEach(input => {
    const valor = input.value.trim();
    if (valor.includes('.')) {
      const [etapaStr, subStr] = valor.split('.');
      const etapa = parseInt(etapaStr);
      const sub = parseInt(subStr);
      if (!isNaN(etapa) && !isNaN(sub)) {
        ultimoIdEtapa = Math.max(ultimoIdEtapa, etapa);
        subEtapasPorEtapa[etapa] = Math.max(subEtapasPorEtapa[etapa] || 0, sub);
      }
    } else {
      const etapa = parseInt(valor);
      if (!isNaN(etapa)) {
        ultimoIdEtapa = Math.max(ultimoIdEtapa, etapa);
        subEtapasPorEtapa[etapa] = subEtapasPorEtapa[etapa] || 0;
      }
    }
  });
}

function copiarInicioPrevistoDasSubetapas() {
  const linhas = document.querySelectorAll('#spreadsheet tbody tr');
  const mapaSub1 = {};

  linhas.forEach(linha => {
    const idInput = linha.querySelector('input[name="id_etapa_custom[]"]');
    const inicioPrevistoInput = linha.querySelector('input[name="inicio_previsto[]"]');
    const tipo = linha.querySelector('input[name="tipo_etapa[]"]')?.value;

    if (!idInput || !inicioPrevistoInput) return;

    const idValor = idInput.value.trim();

    if (tipo === 'linha' && idValor.endsWith('.1')) {
      const etapaPai = idValor.split('.')[0];
      mapaSub1[etapaPai] = inicioPrevistoInput.value;
    }
  });

  linhas.forEach(linha => {
    const idInput = linha.querySelector('input[name="id_etapa_custom[]"]');
    const inicioPrevistoInput = linha.querySelector('input[name="inicio_previsto[]"]');
    const tipo = linha.querySelector('input[name="tipo_etapa[]"]')?.value;

    if (!idInput || !inicioPrevistoInput) return;

    const idValor = idInput.value.trim();

    if (tipo === 'subtitulo' && mapaSub1[idValor]) {
      inicioPrevistoInput.value = mapaSub1[idValor];
    }
  });
}

function excluirLinhaPorId() {
  const idParaExcluir = document.getElementById('idExcluir').value.trim();
  if (!idParaExcluir) {
    alert('Digite um ID válido (ex: 2.1 ou 3)');
    return;
  }

  const linhas = document.querySelectorAll('#spreadsheet tbody tr');
  let linhaEncontrada = false;

  linhas.forEach((linha, index) => {
    const inputId = linha.querySelector('input[name="id_etapa_custom[]"]');
    const idBanco = linha.getAttribute('data-id');

    if (inputId && inputId.value.trim() === idParaExcluir) {
      linhaEncontrada = true;

      if (idBanco) {
        // Se já existe no banco, exclui via PHP
        fetch(`templates/marcos_excluir_linha.php?id=${idBanco}`, { method: 'GET' })
          .then(response => {
            if (!response.ok) throw new Error('Erro ao excluir no banco');
            return response.text();
          })
          .then(data => {
            linha.remove();
            recalcularUltimoIdEtapa();
            copiarInicioPrevistoDasSubetapas();
          })
          .catch(error => {
            alert('Erro ao excluir no servidor.');
            console.error(error);
          });
      } else {
        // Só no frontend (ainda não salva)
        linha.remove();
        recalcularUltimoIdEtapa();
        copiarInicioPrevistoDasSubetapas();
      }
    }
  });

  if (!linhaEncontrada) {
    alert('ID não encontrado.');
  } else {
    document.getElementById('idExcluir').value = '';
  }

}

function abrirModalInserirLinha() {
  document.getElementById('modalInserirLinha').style.display = 'block';
}

function fecharModalInserirLinha() {
  document.getElementById('modalInserirLinha').style.display = 'none';
  document.getElementById('idParaInserir').value = '';
}

function confirmarInsercaoLinha() {
  const tipo = document.getElementById('tipoInsercao').value;
  const idAlvo = document.getElementById('idParaInserir').value.trim();
  if (!idAlvo) {
    alert('Digite um ID de referência.');
    return;
  }

  const linhas = document.querySelectorAll('#spreadsheet tbody tr');
  let inserido = false;

  linhas.forEach((linha, index) => {
    const inputId = linha.querySelector('input[name="id_etapa_custom[]"]');
    if (inputId && inputId.value.trim() === idAlvo && !inserido) {
      const novaLinha = linha.cloneNode(true);
      novaLinha.querySelectorAll('input, textarea').forEach(input => input.value = '');

      const novoId = tipo === 'subtitulo' ? gerarNovoIdEtapa() : gerarNovoIdSubEtapa(inputId.value.trim());
      novaLinha.querySelector('input[name="id_etapa_custom[]"]').value = novoId;
      novaLinha.querySelector('input[name="tipo_etapa[]"]').value = tipo;

      const tdEtapa = novaLinha.querySelector('textarea, input[type="text"]');
      if (tipo === 'subtitulo') {
        tdEtapa.placeholder = 'Título';
        tdEtapa.className = 'campo-etapa-subtitulo';
      } else {
        tdEtapa.placeholder = '';
        tdEtapa.className = 'campo-etapa';
      }

      linha.parentNode.insertBefore(novaLinha, linha);
      inserido = true;
    }
  });

  if (!inserido) {
    alert('ID de referência não encontrado.');
  }

  fecharModalInserirLinha();
  recalcularUltimoIdEtapa();
  copiarInicioPrevistoDasSubetapas();
}

function gerarNovoIdEtapa() {
  return ++ultimoIdEtapa;
}

function gerarNovoIdSubEtapa(idPai) {
  const numPai = parseInt(idPai);
  subEtapasPorEtapa[numPai] = (subEtapasPorEtapa[numPai] || 0) + 1;
  return `${numPai}.${subEtapasPorEtapa[numPai]}`;
}

</script>