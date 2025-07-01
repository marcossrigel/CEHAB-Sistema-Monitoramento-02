<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

<div class="table-container">
  <div class="main-title"><?php echo htmlspecialchars($nome_iniciativa); ?> - Acompanhamento de Pendências</div>

  <form method="post" action="index.php?page=acompanhamento&id_iniciativa=<?php echo $id_iniciativa; ?>">
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
      <button type="button" onclick="window.location.href='index.php?page=visualizar';">&lt; Voltar</button>
    </div>
  </form>
</div>

<script src="js/acompanhamento.js"></script>
