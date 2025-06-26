<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

include_once("config.php");

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['page'] ?? '') === 'formulario') {
    $iniciativa = trim($_POST['iniciativa']);
    $check_query = "SELECT * FROM iniciativas WHERE iniciativa = '$iniciativa'";
    $check_result = mysqli_query($conexao, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Erro: Já existe uma iniciativa com esse nome.'); window.history.back();</script>";
        exit;
    }

    $data_vistoria = $_POST['data_vistoria'];
    $numero_contrato = $_POST['numero_contrato'];
    $ib_status = $_POST['ib_status'];
    $ib_execucao = $_POST['ib_execucao'];
    $ib_previsto = $_POST['ib_previsto'];
    $ib_variacao = $_POST['ib_variacao'];
    $ib_valor_medio = str_replace(['.', ','], ['', '.'], $_POST['ib_valor_medio']);
    $ib_secretaria = $_POST['ib_secretaria'];
    $ib_orgao = $_POST['ib_orgao'];
    $ib_gestor_responsavel = $_POST['ib_gestor_responsavel'];
    $ib_fiscal = $_POST['ib_fiscal'];
    $ib_numero_processo_sei = $_POST['ib_numero_processo_sei'];
    $objeto = mysqli_real_escape_string($conexao, $_POST['objeto']);
    $informacoes_gerais = mysqli_real_escape_string($conexao, $_POST['informacoes_gerais']);
    $observacoes = mysqli_real_escape_string($conexao, $_POST['observacoes']);
    $id_usuario = $_SESSION['id_usuario'];

    $result = mysqli_query($conexao, "INSERT INTO iniciativas(id_usuario,iniciativa,data_vistoria,numero_contrato,ib_status,ib_execucao,ib_previsto,ib_variacao,ib_valor_medio,ib_secretaria,ib_orgao,ib_gestor_responsavel,ib_fiscal,ib_numero_processo_sei,objeto,informacoes_gerais,observacoes) VALUES ('$id_usuario', '$iniciativa','$data_vistoria','$numero_contrato','$ib_status','$ib_execucao','$ib_previsto','$ib_variacao','$ib_valor_medio','$ib_secretaria','$ib_orgao','$ib_gestor_responsavel','$ib_fiscal','$ib_numero_processo_sei','$objeto','$informacoes_gerais','$observacoes')");

    header("Location: index.php?page=formulario&sucesso=1&nome=" . urlencode($iniciativa));
    exit;
}
?>

<div class="pagina-formulario">
<link rel="stylesheet" href="assets/css/formulario.css">

<form class="formulario" action="index.php?page=formulario" method="post">
    <h1 class="main-title">Criar uma nova iniciativa</h1>

    <div class="linha">
      
      <div class="campo-pequeno">
        <label class="label">Nome da Iniciativa</label>
        
        <input list="lista-iniciativas" name="iniciativa" class="campo" required placeholder="Digite ou selecione">
        <datalist id="lista-iniciativas">
          <option value="Creche - Lote 01 (Cabrobó)">
          <option value="Creche - Lote 01 (Granito)">
          <option value="Creche - Lote 01 (Lagoa Grande)">
          <option value="Creche - Lote 01 (Ouricuri)">
          <option value="Creche - Lote 02 (Mirandiba)">
          <option value="Creche - Lote 02 (Serra T 01)">
          <option value="Creche - Lote 02 (Serra T 02)">
          <option value="Creche - Lote 02 (Triunfo)">
          <option value="Creche - Lote 02 (Tuparetama)">
          <option value="Creche - Lote 03 (Arcoverde)">
          <option value="Creche - Lote 03 (Custódia)">
          <option value="Creche - Lote 03 (Ibimirim)">
          <option value="Creche - Lote 03 (Itíba)">
          <option value="Creche - Lote 03 (Pedra)">
          <option value="Creche - Lote 04 (Garanhuns Terreno 01)">
          <option value="Creche - Lote 04 (Garanhuns Terreno 02)">
          <option value="Creche - Lote 04 (Paranatama)">
          <option value="Creche - Lote 04 (São Bento do una)">
          <option value="Creche - Lote 05 (Belo Jardim)">
          <option value="Creche - Lote 05 (Brejo da Madre de Deus)">
          <option value="Creche - Lote 05 (Jataúba)">
          <option value="Creche - Lote 05 (Taquaritinga do Norte)">
          <option value="Creche - Lote 05 (São Bento do una)">
          <option value="Creche - Lote 05 (Vertentes)">
          <option value="Creche - Lote 06 (Belém de Maria)">
          <option value="Creche - Lote 06 (Bezerros)">
          <option value="Creche - Lote 06 (Caruaru 06 - Salgado)">
          <option value="Creche - Lote 06 (Caruaru 02 - Vila Cipó)">
          <option value="Creche - Lote 06 (Caruaru 03 - Rendeiras)">
          <option value="Creche - Lote 06 (Caruaru 04 - Xique Xique)">
          <option value="Creche - Lote 06 (Catende)">
          <option value="Creche - Lote 06 (São Joaquim do Monte)">
          <option value="Creche - Lote 07 (Vicência)">
          <option value="Creche - Lote 07 (Timbaúba)">
          <option value="Creche - Lote 07 (Camutanga)">
          <option value="Creche - Lote 07 (Bom Jardim)">
          <option value="Creche - Lote 07 (Araçoiaba)">
          <option value="Creche - Lote 08 (São José da Coroa Grande)">
          <option value="Creche - Lote 08 (Jaboatão Terreno 04 Muribeca)">
          <option value="Creche - Lote 08 (Cabo de Santo Agostinho)">
          <option value="Creche - Lote 08 (Jaboatão Terreno 01 Rio Dourado)">
          <option value="Creche - Lote 08 (Moreno)">
          <option value="Creche - Lote 08 (Jaboatão Terreno 02 Candeias)">
          <option value="Creche - Lote 08 (Ipojuca)">
          <option value="Creche - Lote 09 (Areias)">
          <option value="Creche - Lote 09 (Itamaraca)">
          <option value="Creche - Lote 09 (Camaragibe 01)">
          <option value="Creche - Lote 09 (Igarassu 01)">
          <option value="Creche - Lote 09 (Camaragibe 02)">
          <option value="Creche - Lote 09 (Igarassu 02)">
          <option value="Creche - Lote 09 (Olinda)">
        </datalist>
      
      </div>

      <div class="campo-pequeno">
        <label class="label">Data da Atualização</label>
        <input type="date" name="data_vistoria" class="campo" required>
      </div>

      <div class="campo-pequeno">
        <label class="label">Nº do contrato</label>
        <div style="display: flex;">
          <input type="text" name="numero_contrato_prefixo" id="numero_contrato_prefixo" maxlength="3" placeholder="000" pattern="\d{3}" required style="flex: 0 0 60px; text-align: center;">
          <span style="align-self: center; padding: 0 5px;">/</span>
          <input type="text" name="numero_contrato_ano" id="numero_contrato_ano" maxlength="4" placeholder="2025" pattern="\d{4}" required style="flex: 0 0 70px; text-align: center;">
        </div>
        <input type="hidden" name="numero_contrato" id="numero_contrato">
      </div>

    </div>

    <div class="linha">
      <div class="campo">
        <label class="label">Informações Básicas: </label>
      </div>
    </div>

    <div class="linha">
      <div class="campo">
        <label class="label">Status</label>
        
        <select type="text" name="ib_status" class="campo" required>
          <option value="">Selecione...</option> 
          <option value="Em Execução">Em Execução</option>
          <option value="Paralizado">Paralizado</option>
          <option value="Concluido">Concluido</option>
        </select>
      </div>
      <div class="campo">
        <label class="label">% Execução</label>
        <input type="text" name="ib_execucao" placeholder="visualização" readonly>
      </div>
      <div class="campo">
        <label class="label">% Previsto</label>
        <input type="text" name="ib_previsto">
      </div>
      <div class="campo">
        <label class="label">% Variação</label>
        <input type="text" name="ib_variacao" id="ib_variacao" placeholder="visualização" readonly>
      </div>
      <div class="campo-longo">
        <label class="label">Valor Medido Acumulado</label>
        <input type="text" name="ib_valor_medio">
      </div>
    </div>    

    <div class="linha">
      <div class="campo">
        <label class="label">Secretaria</label>
        <input type="text" name="ib_secretaria" value="SEDUH" readonly>
      </div>
      <div class="campo">
        <label class="label">Órgão</label>
        <input type="text" name="ib_orgao" value="CEHAB" readonly>
      </div>
      
      <div class="campo">
        <label class="label">Gestor Responsável</label>
        <input type="text" name="ib_gestor_responsavel">
      </div>

      <div class="campo">
        <label class="label">Fiscal Responsável</label>
        <input type="text" name="ib_fiscal">
      </div>
    
      <div class="campo-longo">
        <label class="label">Nº Processo SEI</label>
        <input type="text" name="ib_numero_processo_sei">
      </div>
    </div>  

    <div class="linha-atividade">
      <div class="coluna-textarea">
        <label class="label">OBJETO (opcional)</label>
        <textarea name="objeto"></textarea>
      </div>
    </div>

    <br>
    <hr>

    <div class="linha-atividade">
      <div class="coluna-textarea">
        <label class="label">Informações Gerais (opcional)</label>
        <textarea name="informacoes_gerais"></textarea>
      </div>
    </div>

    <div class="linha-atividade">
      <div class="coluna-textarea">
        <label class="label">OBSERVAÇÕES (PONTOS CRÍTICOS) (opcional)</label>
        <textarea name="observacoes"></textarea>
      </div>
    </div>

    <br>

    <button type="submit" name="submit" id="submit" class="btn btn-create-account">Criar</button>
    <a href="#" class="texto-login" onclick="confirmarCancelamento(event)">Cancelar</a>

  </form>
</div>

  <?php
    $mensagem = '';
    if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1 && isset($_GET['nome'])) {
        $mensagem = 'Iniciativa "' . htmlspecialchars($_GET['nome']) . '" criada com sucesso!';
    }
  ?>

  <div id="modal-cancelar" class="modal hidden">
    <div class="modal-content">
      <p>Você deseja cancelar? Os dados preenchidos podem ser perdidos.</p>
      <button id="btn-sim" style="background-color: #dc3545;">Sim</button>
      <button id="btn-nao" style="background-color: #6c757d; margin-left: 10px;">Não</button>
    </div>
  </div>

</body>

<script src="js/formulario.js"></script>

</html>
