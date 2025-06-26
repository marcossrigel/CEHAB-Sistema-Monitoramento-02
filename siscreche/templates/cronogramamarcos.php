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
      
      <?php
      $voltar_url = 'index.php?page=visualizar';
      if ($tipo_usuario === 'admin' && isset($_GET['diretoria'])) {
          $diretoria = urlencode($_GET['diretoria']);
          $voltar_url .= "&diretoria=$diretoria";
      }
      ?>
      <button type="button" onclick="window.location.href='<?php echo $voltar_url; ?>';">&lt; Voltar</button>
    </div>
  </form>
  
</div>

<script src="js/acompanhamento.js"></script>