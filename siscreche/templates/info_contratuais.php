<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

include_once('config.php');

if (!$conexao) {
    die("Erro na conexão com o banco: " . mysqli_connect_error());
}

$id_iniciativa = isset($_POST['id_iniciativa']) ? intval($_POST['id_iniciativa']) : (isset($_GET['id_iniciativa']) ? intval($_GET['id_iniciativa']) : 0);

$query_nome = "SELECT iniciativa FROM iniciativas WHERE id = $id_iniciativa";
$result_nome = mysqli_query($conexao, $query_nome);
$linha_nome = mysqli_fetch_assoc($result_nome);
$nome_iniciativa = $linha_nome['iniciativa'] ?? 'Iniciativa Desconhecida';

$query_busca = "SELECT * FROM contratuais WHERE id_usuario = {$_SESSION['id_usuario']} AND id_iniciativa = $id_iniciativa";
$resultado = mysqli_query($conexao, $query_busca);
$dados = mysqli_fetch_assoc($resultado);

  function formatar_moeda($valor) {
    if ($valor === null || $valor === '') return 'R$ ';
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}

if (isset($_POST['salvar'])) {
    $processo_licitatorio = mysqli_real_escape_string($conexao, $_POST['processo_licitatorio']);
    $empresa = mysqli_real_escape_string($conexao, $_POST['empresa']);
    $data_assinatura_contrato = $_POST['data_assinatura_contrato'];
    $data_os = $_POST['data_os'];
    $prazo_execucao_original = $_POST['prazo_execucao_original'];
    $prazo_execucao_atual = $_POST['prazo_execucao_atual'];

    function limpar_valor_decimal($valor) {
        $valor = trim($valor);
        if ($valor === '' || strtolower($valor) === 'r$') return "NULL";
        $valor = str_replace(['R$', '.', ','], ['', '', '.'], $valor);
        return is_numeric($valor) ? $valor : "NULL";
    }

    $valor_inicial_obra = limpar_valor_decimal($_POST['valor_inicial_obra']);
    $valor_aditivo_obra = limpar_valor_decimal($_POST['valor_aditivo_obra']);
    $valor_total_obra = limpar_valor_decimal($_POST['valor_total_obra']);
    $valor_inicial_contrato = limpar_valor_decimal($_POST['valor_inicial_contrato']);
    $valor_aditivo = limpar_valor_decimal($_POST['valor_aditivo']);
    $valor_contrato = limpar_valor_decimal($_POST['valor_contrato']);

    $cod_subtracao = mysqli_real_escape_string($conexao, $_POST['cod_subtracao']);
    $secretaria_demandante = mysqli_real_escape_string($conexao, $_POST['secretaria_demandante']);

    if ($dados) {
        $query_update = "UPDATE contratuais SET 
            processo_licitatorio='$processo_licitatorio',
            empresa='$empresa',
            data_assinatura_contrato='$data_assinatura_contrato',
            data_os='$data_os',
            prazo_execucao_original='$prazo_execucao_original',
            prazo_execucao_atual='$prazo_execucao_atual',
            valor_inicial_obra=$valor_inicial_obra,
            valor_aditivo_obra=$valor_aditivo_obra,
            valor_total_obra=$valor_total_obra,
            valor_inicial_contrato=$valor_inicial_contrato,
            valor_aditivo=$valor_aditivo,
            valor_contrato=$valor_contrato,
            cod_subtracao='$cod_subtracao',
            secretaria_demandante='$secretaria_demandante'
            WHERE id_usuario={$_SESSION['id_usuario']} AND id_iniciativa=$id_iniciativa";
        mysqli_query($conexao, $query_update);
    } else {
        $query_insert = "INSERT INTO contratuais (
            id_usuario, id_iniciativa, processo_licitatorio, empresa, data_assinatura_contrato, data_os, 
            prazo_execucao_original, prazo_execucao_atual, 
            valor_inicial_obra, valor_aditivo_obra, valor_total_obra, 
            valor_inicial_contrato, valor_aditivo, valor_contrato, 
            cod_subtracao, secretaria_demandante
        ) VALUES (
            {$_SESSION['id_usuario']}, $id_iniciativa, '$processo_licitatorio', '$empresa', '$data_assinatura_contrato', '$data_os', 
            '$prazo_execucao_original', '$prazo_execucao_atual', 
            $valor_inicial_obra, $valor_aditivo_obra, $valor_total_obra, 
            $valor_inicial_contrato, $valor_aditivo, $valor_contrato, 
            '$cod_subtracao', '$secretaria_demandante'
        )";
        mysqli_query($conexao, $query_insert);
    }

    $valor_total_para_medicoes = $valor_inicial_obra;
    $valor_bm_para_medicoes = $valor_aditivo;
    
    $query_verifica_medicao = "SELECT id FROM medicoes WHERE id_usuario = {$_SESSION['id_usuario']} AND id_iniciativa = $id_iniciativa LIMIT 1";
    $result_medicao = mysqli_query($conexao, $query_verifica_medicao);

    if (mysqli_num_rows($result_medicao) > 0) {
        $linha = mysqli_fetch_assoc($result_medicao);
        $id_medicao = $linha['id'];
        $query_update_medicao = "UPDATE medicoes 
                                SET valor_orcamento = $valor_total_para_medicoes, 
                                    valor_bm = $valor_bm_para_medicoes 
                                WHERE id = $id_medicao";
        mysqli_query($conexao, $query_update_medicao);
    } else {
        $query_insert_medicao = "INSERT INTO medicoes 
            (id_usuario, id_iniciativa, valor_orcamento, valor_bm, data_registro)
            VALUES 
            ({$_SESSION['id_usuario']}, $id_iniciativa, $valor_total_para_medicoes, $valor_bm_para_medicoes, NOW())";
        mysqli_query($conexao, $query_insert_medicao);
    }

    header("Location: infocontratuais.php?id_iniciativa=$id_iniciativa");
    exit;
}
?>

  <div class="container">
    <form method="post" action="infocontratuais.php">
      <input type="hidden" name="id_iniciativa" value="<?php echo $id_iniciativa; ?>">
      <div class="main-title"><?php echo htmlspecialchars($nome_iniciativa); ?> - Informações Contratuais</div>
      <table>
        <tr><th class="hide-mobile">Campo</th><th class="hide-mobile">Valor</th></tr>
        <tr><td>Processo Licitatório</td><td><input type="text" name="processo_licitatorio" value="<?php echo $dados['processo_licitatorio'] ?? ''; ?>"></td></tr>
        <tr><td>Empresa</td><td><input type="text" name="empresa" value="<?php echo $dados['empresa'] ?? ''; ?>"></td></tr>
        <tr><td>Data Assinatura do Contrato</td><td><input type="date" name="data_assinatura_contrato" value="<?php echo $dados['data_assinatura_contrato'] ?? ''; ?>" required></td></tr>
        <tr><td>Data da O.S.</td><td><input type="date"  name="data_os" value="<?php echo $dados['data_os'] ?? ''; ?>" required></td></tr>
        <tr><td>Prazo de Execução Original</td><td><input type="text" name="prazo_execucao_original" value="<?php echo $dados['prazo_execucao_original'] ?? ''; ?>"></td></tr>
        <tr><td>Prazo de Execução Atual</td><td><input type="text" name="prazo_execucao_atual" value="<?php echo $dados['prazo_execucao_atual'] ?? ''; ?>"></td></tr>
        
        <tr><td>Valor Inicial da Obra</td><td><input type="text" class="dinheiro" name="valor_inicial_obra" value="<?php echo formatar_moeda($dados['valor_inicial_obra'] ?? ''); ?>"></td></tr>
        <tr><td>Valor de Aditivo da Obra</td><td><input type="text" class="dinheiro" name="valor_aditivo_obra" value="<?php echo formatar_moeda($dados['valor_aditivo_obra'] ?? ''); ?>"></td></tr>
        <tr><td>Valor Total da Obra</td><td><input type="text" class="dinheiro" name="valor_total_obra" value="<?php echo formatar_moeda($dados['valor_total_obra'] ?? ''); ?>"></td></tr>
        <tr><td>Valor Inicial do Contrato</td><td><input type="text" class="dinheiro" name="valor_inicial_contrato" value="<?php echo formatar_moeda($dados['valor_inicial_contrato'] ?? ''); ?>"></td></tr>
        <tr><td>Valor do Aditivo do Contrato</td><td><input type="text" class="dinheiro" name="valor_aditivo" value="<?php echo formatar_moeda($dados['valor_aditivo'] ?? ''); ?>"></td></tr>
        <tr><td>Valor Total do Contrato</td><td><input type="text" class="dinheiro" name="valor_contrato" value="<?php echo formatar_moeda($dados['valor_contrato'] ?? ''); ?>"></td></tr>
        
        <tr><td>Subação (LOA)</td><td><input type="text" name="cod_subtracao" value="<?php echo $dados['cod_subtracao'] ?? ''; ?>"></td></tr>
        <tr><td>Secretaria Demandante</td><td><input type="text" name="secretaria_demandante" value="<?php echo $dados['secretaria_demandante'] ?? ''; ?>"></td></tr>
      </table>
      <div class="button-group">
        <button type="submit" name="salvar" style="background-color:rgb(42, 179, 0);">Salvar</button>
        <button type="button" onclick="window.location.href='index.php?page=visualizar';">&lt; Voltar</button>
      </div>
    </form>
  </div>