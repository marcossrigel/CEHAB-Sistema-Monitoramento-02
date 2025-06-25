<?php
  session_start();

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  if (!isset($_SESSION['id_usuario'])) {
      header('Location: login.php');
      exit;
  }

  if(isset($_POST['submit']))
  {
    include_once('config.php');
    $iniciativa = trim($_POST['iniciativa']);
    $check_query = "SELECT * FROM iniciativas WHERE iniciativa = '$iniciativa'";
    $check_result = mysqli_query($conexao, $check_query);
    $ib_valor_medio = str_replace(['.', ','], ['', '.'], $_POST['ib_valor_medio']);


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
    
    $result = mysqli_query($conexao, "INSERT INTO iniciativas(id_usuario,iniciativa,data_vistoria,numero_contrato,ib_status,ib_execucao,ib_previsto,ib_variacao,ib_valor_medio,ib_secretaria,ib_orgao,ib_gestor_responsavel,ib_fiscal,ib_numero_processo_sei,objeto,informacoes_gerais,observacoes)
    
    VALUES ('$id_usuario', '$iniciativa','$data_vistoria','$numero_contrato','$ib_status','$ib_execucao','$ib_previsto','$ib_variacao','$ib_valor_medio','$ib_secretaria','$ib_orgao','$ib_gestor_responsavel','$ib_fiscal','$ib_numero_processo_sei','$objeto','$informacoes_gerais','$observacoes')");
    header("Location: formulario.php?sucesso=1&nome=" . urlencode($iniciativa));
    exit;
    
  }

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="formulario.css">
  <title>Formulário</title>
  
<style>
:root {
--color-white: rgb(255, 255, 255);
--color-gray: #e3e8ec;
--color-dark: #1d2129;
--color-blue: #0a6be2;
--color-green: #42b72a;
}

body {
  font-family: Arial, sans-serif;
  background-color: var(--color-gray);
  display: flex;
  justify-content: center;
  margin: 0;
  padding: 40px 20px;
  box-sizing: border-box;
}

.formulario {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  width: 1000px;
}

.main-title {
  text-align: center;
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 20px;
}

.label {
  display: block;
  font-size: 14px;
  color: #333;
  margin-bottom: 4px;
  margin-left: 2px;
  font-weight: bold;
}

.linha {
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
  flex-wrap: nowrap;
}

.campo-pequeno {
  flex: 1;
  min-width: 200px;
  display: flex;
  flex-direction: column;
}

.campo {
  flex: 1;
  min-width: 150px;
}

.campo-longo {
  flex: 2; 
  min-width: 200px;
}

.linha-atividade {
  display: flex;
  gap: 20px;
  margin-top: 20px;
  flex-wrap: wrap;
}

.coluna-textarea {
  flex: 2;
  display: flex;
  flex-direction: column;
}

.coluna-textarea textarea {
  width: 100%;
  box-sizing: border-box;
  height: 100px;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  resize: vertical;
  font-size: 14px;
  font-family: inherit;
}

button.btn {
  background-color: var(--color-green);
  color: var(--color-white);
  text-decoration: none;
  padding: 14px 20px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  text-align: center;
  transition: background 0.3s;
  width: 200px;
  display: inline-block;
  border: none;
  outline: none;
  cursor: pointer;
  margin: 20px auto;   
  display: block;
}

button.btn:hover {
  background-color: #36a420;
}

.texto-login {
  text-align: center;
  color: var(--color-blue);
  font-size: 14px;
  margin-top: 20px auto;
  display: block;
}

a.texto-login{
  color: red;               
  text-decoration: none;  
  font-weight: bold;
}

a.texto-login:hover {
  text-decoration: none;
}

input[type="text"],
input[type="date"],
input[list],
select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  box-sizing: border-box;
  font-size: 14px;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.modal-content {
  background-color: white;
  padding: 30px;
  border-radius: 10px;
  text-align: center;
  max-width: 400px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.hidden {
  display: none;
}

.modal-content button {
  margin-top: 20px;
  padding: 10px 20px;
  border: none;
  background-color:#42b72a;
  color: white;
  border-radius: 6px;
  cursor: pointer;
}

.modal-content button:hover {
  background-color: #42b72a;
}

@media (max-width: 768px) {
  .formulario {
    width: 100%;
    padding: 15px;
  }

  .linha {
    flex-direction: column;
    gap: 10px;
  }

  .linha-atividade {
    flex-direction: column;
  }

  .campo,
  .campo-pequeno,
  .campo-longo,
  .coluna-textarea {
    width: 100%;
    min-width: 100%;
  }

  textarea {
    width: 100% !important;
    box-sizing: border-box;
  }

  button.btn {
    width: 100%;
  }

  .main-title {
    font-size: 20px;
  }
}

  </style>
</head>

<body>
    
  <form class="formulario" action="formulario.php" method="post">
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

  <?php
    $mensagem = '';
    if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1 && isset($_GET['nome'])) {
        $mensagem = 'Iniciativa "' . htmlspecialchars($_GET['nome']) . '" criada com sucesso!';
    }
  ?>

  <div id="modal" class="modal hidden">
    <div class="modal-content">
      <p id="modal-message"></p>
      <button style="background-color: #42b72a;" onclick="voltarParaHome()">Voltar</button>
    </div>
  </div>

  <div id="modal-cancelar" class="modal hidden">
    <div class="modal-content">
      <p>Você deseja cancelar? Os dados preenchidos podem ser perdidos.</p>
      <button id="btn-sim" style="background-color: #dc3545;">Sim</button>
      <button id="btn-nao" style="background-color: #6c757d; margin-left: 10px;">Não</button>
    </div>
  </div>

</body>

<script>

  document.querySelector('form').addEventListener('submit', function(e) {
    const prefixo = document.getElementById('numero_contrato_prefixo').value.padStart(3, '0');
    const ano = document.getElementById('numero_contrato_ano').value;
    document.getElementById('numero_contrato').value = `${prefixo}/${ano}`;
  });

  const execucaoInput = document.querySelector('input[name="ib_execucao"]');
  const previstoInput = document.querySelector('input[name="ib_previsto"]');
  const variacaoInput = document.getElementById('ib_variacao');
  
  function showModal(message) {
    document.getElementById('modal-message').innerText = message;
    document.getElementById('modal').classList.remove('hidden');
  }

  function closeModal() {
    document.getElementById('modal').classList.add('hidden');
  }

  function temCamposPreenchidos() {
    const inputs = document.querySelectorAll('.formulario input, .formulario textarea, .formulario select');
    return Array.from(inputs).some(input => input.value.trim() !== '');
  }

  function confirmarCancelamento(event) {
    event.preventDefault();

    if (temCamposPreenchidos()) {
      document.getElementById('modal-cancelar').classList.remove('hidden');
    } else {
      window.location.href = 'home.php';
    }
  }

  function voltarParaHome() {
    window.location.href = 'home.php';
  }

  function calcularVariacao() {
    const exec = parseFloat(execucaoInput.value.replace(',', '.')) || 0;
    const prev = parseFloat(previstoInput.value.replace(',', '.')) || 0;
    const variacao = (exec - prev).toFixed(2);
    variacaoInput.value = variacao.replace('.', ',');
  }

document.getElementById('btn-sim').addEventListener('click', function() {
  window.location.href = 'home.php';
});

document.getElementById('btn-nao').addEventListener('click', function() {
  document.getElementById('modal-cancelar').classList.add('hidden');
});

document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($mensagem)) { ?>
      showModal(`<?php echo addslashes($mensagem); ?>`);
    <?php } ?>
});

execucaoInput.addEventListener('input', calcularVariacao);
previstoInput.addEventListener('input', calcularVariacao);

</script>

</html>