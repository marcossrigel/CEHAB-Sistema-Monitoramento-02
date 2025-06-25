<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
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
        $etapa_custom = intval($id_etapa_custom[$i] ?? 0);
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
              id_etapa_custom=$etapa_custom,
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
              '$id_usuario', '$id_iniciativa', $etapa_custom, '$tipo', '$etp',
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

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Medições</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
</head>
<body>
<div class="table-container">
  <div class="main-title"><?php echo htmlspecialchars($nome_iniciativa); ?> - Cronograma de Marcos</div>
  <form method="post" action="cronogramamarcos.php?id_iniciativa=<?php echo $id_iniciativa; ?>">
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
          <tr data-id="<?php echo $linha['id']; ?>">
          
          <td style="max-width:50px;">
            
          <input type="number" name="id_etapa_custom[]" value="<?php echo htmlspecialchars($linha['id_etapa_custom']); ?>" 
            style="width: 60px; font-size: 13px; padding: 4px 6px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; text-align: center;">
          </td>
          
          <td>
              <?php if ($linha['tipo_etapa'] === 'subtitulo') { ?>
                <input type="text" name="etapa[]" value="<?php echo htmlspecialchars($linha['etapa']); ?>" 
                  style="width:100%; min-width:200px; font-family:'Poppins', sans-serif; font-size:13px; padding:4px 8px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
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
      <button type="button" onclick="window.location.href='visualizar.php';">&lt; Voltar</button>
    </div>
  </form>
  
</div>

<script>
document.querySelector('form').addEventListener('submit', function(event) {
  const form = this;
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  const linhas = table.rows;
  let temLinhaValida = false;

  let tituloIndex = -1;
  let datasInicio = [];
  let datasTermino = [];

  let currentTitleIndex = 0;
  let subIndex = 1;

  for (let i = 0; i < linhas.length; i++) {
    const linha = linhas[i];
    const id = linha.getAttribute('data-id');
    const cells = linha.cells;

    const etapaField = cells[0].querySelector('textarea, input');
    const tipo = etapaField?.placeholder === 'Título' ? 'subtitulo' : 'linha';

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

    // Lógica de preenchimento automático para subtítulos
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

  currentTitleIndex++; // Novo título
  subIndex = 1; // Reinicia subtítulos

  const campos = ['etapa', 'inicio_previsto', 'termino_previsto', 'inicio_real', 'termino_real', 'evolutivo'];

  const idCell = newRow.insertCell();
  const idInput = document.createElement('input');
  idInput.type = 'number';
  idInput.name = 'id_etapa_custom[]';
  idInput.readOnly = true;
  idInput.value = currentTitleIndex;
  idInput.style.width = '100%';
  idInput.style.fontSize = '13px';
  idInput.style.padding = '4px 8px';
  idInput.style.border = '1px solid #ccc';
  idInput.style.borderRadius = '6px';
  idInput.style.boxSizing = 'border-box';
  idCell.appendChild(idInput);

  campos.forEach((campo, index) => {
    const cell = newRow.insertCell();
    if (index === 0) {
      const input = document.createElement('input');
      input.type = 'text';
      input.name = campo + '[]';
      input.placeholder = 'Título';
      input.style.width = '100%';
      input.style.fontFamily = "'Poppins', sans-serif";
      input.style.fontSize = '13px';
      input.style.padding = '4px 8px';
      input.style.border = '1px solid #ccc';
      input.style.borderRadius = '6px';
      input.style.boxSizing = 'border-box';
      cell.appendChild(input);
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
      input.style.width = '100%';
      input.style.border = 'none';
      input.style.borderRadius = '6px';
      input.style.height = '20px';
      input.style.padding = '4px 8px';
      input.style.boxSizing = 'border-box';
      cell.appendChild(input);
    }
  });
}

function addRow() {
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  const newRow = table.insertRow();

  const campos = ['etapa', 'inicio_previsto', 'termino_previsto', 'inicio_real', 'termino_real', 'evolutivo'];

  const idCell = newRow.insertCell();
  const idInput = document.createElement('input');
  idInput.type = 'text';
  idInput.name = 'id_etapa_custom[]';
  idInput.readOnly = true;
  idInput.value = `${currentTitleIndex}.${subIndex++}`;
  idInput.style.width = '100%';
  idInput.style.fontSize = '13px';
  idInput.style.padding = '4px 8px';
  idInput.style.border = '1px solid #ccc';
  idInput.style.borderRadius = '6px';
  idInput.style.boxSizing = 'border-box';
  idCell.appendChild(idInput);

  campos.forEach((campo, index) => {
    const cell = newRow.insertCell();
    if (index === 0) {
      const textarea = document.createElement('textarea');
      textarea.name = campo + '[]';
      textarea.rows = 2;
      textarea.className = 'campo-etapa';
      textarea.style.width = '100%';
      textarea.style.fontFamily = "'Poppins', sans-serif";
      textarea.style.fontSize = '13px';
      textarea.style.padding = '4px 8px';
      textarea.style.border = '1px solid #ccc';
      textarea.style.borderRadius = '6px';
      textarea.style.boxSizing = 'border-box';
      textarea.style.resize = 'vertical';
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
      input.style.width = '100%';
      input.style.border = '1px solid #ccc';
      input.style.borderRadius = '6px';
      input.style.height = '20px';
      input.style.padding = '4px 8px';
      input.style.boxSizing = 'border-box';
      cell.appendChild(input);
    }
  });
}

function deleteRow() {
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  const lastRow = table.rows[table.rows.length - 1];


  if (!lastRow) return;

  const id = lastRow.getAttribute('data-id');

  if (id) {
    fetch(`marcos_excluir_linha.php?id=${id}`, { method: 'GET' })
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
  } 
  else {
    table.deleteRow(-1);
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

</script>

</body>

</html>