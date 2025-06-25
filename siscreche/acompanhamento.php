<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['id_usuario'])) {
  header('Location: login.php');
  exit;
}

include_once('config.php');

$id_iniciativa = isset($_GET['id_iniciativa']) ? intval($_GET['id_iniciativa']) : 0;

if (isset($_POST['salvar'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $id_iniciativa = intval($_GET['id_iniciativa']);

    $problemas = $_POST['problema'];
    $contramedidas = $_POST['contramedida'];
    $prazos = $_POST['prazo'];
    $responsaveis = $_POST['responsavel'];
    $ids = $_POST['ids'] ?? [];

    for ($i = 0; $i < count($problemas); $i++) {
        $id_existente = intval($ids[$i] ?? 0);
        $problema = mysqli_real_escape_string($conexao, $problemas[$i]);
        $contramedida = mysqli_real_escape_string($conexao, $contramedidas[$i]);
        $prazo_bruto = trim($prazos[$i]);
        $responsavel = mysqli_real_escape_string($conexao, $responsaveis[$i]);

        if ($prazo_bruto === '') {
            $prazo_sql = "NULL";
        } else {
            $prazo_formatado = mysqli_real_escape_string($conexao, $prazo_bruto);
            $prazo_sql = "'$prazo_formatado'";
        }

        if ($id_existente > 0) {
            $query = "UPDATE pendencias 
                      SET problema='$problema', contramedida='$contramedida', prazo=$prazo_sql, responsavel='$responsavel' 
                      WHERE id = $id_existente AND id_usuario = $id_usuario AND id_iniciativa = $id_iniciativa";
        } else {
            $query = "INSERT INTO pendencias (id_usuario, id_iniciativa, problema, contramedida, prazo, responsavel) 
                      VALUES ('$id_usuario', '$id_iniciativa', '$problema', '$contramedida', $prazo_sql, '$responsavel')";
        }

        mysqli_query($conexao, $query);
    }
}

$dados_pendencias = mysqli_query($conexao, "SELECT * FROM pendencias WHERE id_usuario = {$_SESSION['id_usuario']} AND id_iniciativa = $id_iniciativa");

$query_nome = "SELECT iniciativa FROM iniciativas WHERE id = $id_iniciativa";
$resultado_nome = mysqli_query($conexao, $query_nome);
$linha_nome = mysqli_fetch_assoc($resultado_nome);
$nome_iniciativa = $linha_nome['iniciativa'] ?? 'Iniciativa Desconhecida';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Planilha Web</title>
  
  <style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #e3e8ec;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    margin: 0;
    padding: 10px;
  }

  .table-container {
    background: #fff;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 1000px;
    overflow-x: auto;
  }

  .main-title {
    font-size: 26px;
    text-align: center;
    margin-bottom: 20px;
    word-break: break-word;
  }

  table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 12px 15px;
  }

  th {
    text-align: left;
    padding: 10px;
  }

  td {
    padding: 10px;
    word-break: break-word;
  }

  td[contenteditable], td.readonly {
    border: 1px solid #ccc;
    border-radius: 12px;
    background-color: #fff;
    padding: 10px;
    min-width: 120px;
  }

  td.readonly {
    background-color: #f9f9f9;
    color: #555;
    cursor: not-allowed;
  }

  td[contenteditable]:focus {
    outline: none;
    border: 1px solid #4da6ff;
    background-color: #f0f8ff;
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

  #submit {
    background-color: #28a745;
  }

  @media (max-width: 768px) {
    .main-title {
      font-size: 20px;
    }

    table {
      font-size: 13px;
    }

    td[contenteditable], td.readonly {
      min-width: 90px;
      font-size: 13px;
      padding: 8px;
    }

    .button-group button {
      flex: 1 1 100%;
      padding: 12px;
      font-size: 14px;
    }
  }
  </style>

</head>
<body>

<div class="table-container">
  <div class="main-title"><?php echo htmlspecialchars($nome_iniciativa); ?> - Acompanhamento de Pendências</div>

  <form method="post" action="acompanhamento.php?id_iniciativa=<?php echo $id_iniciativa; ?>">
    <table id="spreadsheet">
      <thead>
        <tr>
          <th>Problema</th>
          <th>Contramedida</th>
          <th>Prazo</th>
          <th>Responsável</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($linha = mysqli_fetch_assoc($dados_pendencias)) { ?>
        <tr data-id="<?php echo $linha['id']; ?>">
          <td contenteditable="true"><?php echo htmlspecialchars($linha['problema']); ?></td>
          <td contenteditable="true"><?php echo htmlspecialchars($linha['contramedida']); ?></td>
          
          <?php
            $data = $linha['prazo'];
            if (!$data || $data === '0000-00-00') {
          ?>
              <td contenteditable="true"></td>
          <?php
            } else {
          ?>
              <td class="readonly">
                <?php echo date('d/m/Y', strtotime($data)); ?>
              </td>
          <?php
            }
          ?>
          
          <td contenteditable="true"><?php echo htmlspecialchars($linha['responsavel']); ?></td>
        </tr>
      <?php } ?>
      </tbody>
    </table>

    <div class="button-group">
      <button type="button" onclick="addRow()">Adicionar Linha</button>
      <button type="button" onclick="deleteRow()">Excluir Linha</button>
      <button type="submit" name="salvar" id="submit">Salvar</button>
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

  for (let i = 0; i < linhas.length; i++) {
    const linha = linhas[i];
    const id = linha.getAttribute('data-id');
    const cells = linha.cells;

    let problema = cells[0].innerText.trim();
    let contramedida = cells[1].innerText.trim();
    let prazo = cells[2].innerText.trim();
    let responsavel = cells[3].innerText.trim();

    if (prazo.includes('/')) {
      const partes = prazo.split('/');
      if (partes.length === 3) {
        prazo = `${partes[2]}-${partes[1]}-${partes[0]}`;
      }
    }

    const campos = [problema, contramedida, prazo, responsavel];
    const linhaVazia = campos.every(c => c === '');

    if (linhaVazia) continue;
    temLinhaValida = true;

    ['problema', 'contramedida', 'prazo', 'responsavel'].forEach((campo, idx) => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = campo + '[]';
      input.value = campos[idx];
      form.appendChild(input);
    });

    const inputId = document.createElement('input');
    inputId.type = 'hidden';
    inputId.name = 'ids[]';
    inputId.value = id ? id : '';
    form.appendChild(inputId);
  }

  if (!temLinhaValida) {
    event.preventDefault();
    alert('Nenhuma pendência válida para salvar!');
  }
});

function addRow() {
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  const newRow = table.insertRow();
  const placeholders = ['Problema', 'Contramedida', 'dd/mm/aaaa', 'Responsável'];

  for (let i = 0; i < 4; i++) {
    const newCell = newRow.insertCell();
    newCell.contentEditable = "true";
    newCell.innerText = '';
    newCell.setAttribute('data-placeholder', placeholders[i]);
  }
}

function deleteRow() {
  const table = document.getElementById('spreadsheet').getElementsByTagName('tbody')[0];
  if (table.rows.length > 0) {
    const lastRow = table.rows[table.rows.length - 1];
    const id = lastRow.getAttribute('data-id');

    if (id) {
      fetch('excluir_pendencia.php?id=' + id, { method: 'GET' })
        .then(response => response.text())
        .then(data => {
          table.deleteRow(-1);
        });
    } else {
      table.deleteRow(-1);
    }
  }
}
</script>

</body>
</html>
