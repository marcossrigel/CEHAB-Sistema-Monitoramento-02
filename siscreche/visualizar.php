<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

include("config.php");

$id_usuario = $_SESSION['id_usuario'];
$tipo_usuario = $_SESSION['tipo_usuario']; // Você precisa garantir que isso esteja na sessão

if ($tipo_usuario === 'admin' && isset($_GET['diretoria'])) {
    $diretoria = $conexao->real_escape_string($_GET['diretoria']);
    $sql = "SELECT * FROM iniciativas 
            WHERE id_usuario IN (
              SELECT id_usuario FROM usuarios WHERE diretoria = '$diretoria'
            )";
} else {
    $sql = "SELECT * FROM iniciativas WHERE id_usuario = $id_usuario";
}

$resultado = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Visualizar Iniciativas</title>
<style>
body {
  font-family: Arial, sans-serif;
  background-color: #e9eef1;
  margin: 0;
  padding: 20px;
}

.container {
  max-width: 800px;
  margin: auto;
}

h1 {
  font-size: 28px;
  text-align: center;
  margin-bottom: 20px;
  color: #000;
}

.accordion {
  background-color: #fff;
  color: #333;
  cursor: pointer;
  padding: 18px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 18px;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.accordion:hover {
  background-color: #f9f9f9;
}

.panel {
  padding: 0 0 15px 0;
  display: none;
  background-color: white;
  overflow: hidden;
  border-radius: 0 0 10px 10px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 15px;
}

.panel p {
  margin: 10px 18px;
  font-size: 15px;
  line-height: 1.5;
}

.seta {
  font-size: 22px;
  transform: rotate(0deg);
  transition: transform 0.3s ease;
}

.accordion.active .seta {
  transform: rotate(180deg);
}

.button-left {
  margin: 10px 18px 0;
  display: flex;
  justify-content: flex-start;
}

.button-left button {
  padding: 8px 16px;
  background-color: #4da6ff;
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.button-left button:hover {
  background-color: #3399ff;
}

.acoes {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: center;
  margin-top: 20px;
  padding: 0 18px;
}

.acoes button {
  padding: 12px;
  font-size: 15px;
  border: none;
  border-radius: 10px;
  background-color: #f1f5f9;
  color: #333;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  justify-content: center;
  flex: 1 1 45%;
  min-width: 140px;
}

.acoes button:hover {
  background-color: #e0e7ff;
}

.botao-voltar {
  display: flex;
  justify-content: center;
  margin-top: 40px;
}

.botao-voltar button {
  padding: 10px 20px;
  background-color: #4da6ff;
  color: white;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.botao-voltar button:hover {
  background-color: #3399ff;
}

.topo-linha {
  position: relative;
  text-align: center;
  margin-bottom: 50px; 
}

.topo-linha h1 {
  margin: 0;
  font-size: 28px;
  color: #000;
}

.voltar-box {
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
}

.voltar-box button {
  padding: 8px 16px;
  background-color: #4da6ff;
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.voltar-box button:hover {
  background-color: #3399ff;
}


@media (max-width: 768px) {
  .container {
    padding: 15px;
  }
  h1 {
    font-size: 22px;
  }
  .panel p {
    font-size: 14px;
  }
  .acoes {
    flex-direction: column;
  }
  .acoes button {
    flex: 1 1 100%;
  }
}
</style>
</head>
<body>
<div class="container">
  
  <div class="topo-linha">
  <div class="voltar-box">
  </div>
  
  <h1>
  <?php 
    if ($tipo_usuario === 'admin' && isset($_GET['diretoria'])) {
      echo "Iniciativas da Diretoria: " . htmlspecialchars($_GET['diretoria']);
    } else {
      echo "Iniciativas Cadastradas";
    }
  ?>
  </h1>

</div>

  <?php while ($row = $resultado->fetch_assoc()): ?>
    <button class="accordion">
      <strong><?php echo htmlspecialchars($row['iniciativa']); ?></strong>
      <span class="seta">⌄</span>
    </button>
    
    <div class="panel">
      <p>
        <strong>Status:</strong> <?php echo $row['ib_status']; ?> | 
        <strong>Data da Vistoria:</strong> <?php echo $row['data_vistoria']; ?> | 
        <strong>Nº do Contrato:</strong> <?php echo $row['numero_contrato']; ?>
      </p>
      <p><strong>Execução:</strong> <?php echo $row['ib_execucao']; ?> | <strong>Previsto:</strong> <?php echo $row['ib_previsto']; ?> | <strong>Variação:</strong> <?php echo $row['ib_variacao']; ?> | <strong>Valor Medido Acumulado: </strong><?php echo $row['ib_valor_medio']; ?></p>
      <p><strong>Secretaria:</strong> <?php echo $row['ib_secretaria']; ?> | <strong>Órgão:</strong> <?php echo $row['ib_orgao']; ?> | <strong>Processo SEI:</strong> <?php echo $row['ib_numero_processo_sei']; ?></p>
      <p><strong>Gestor Responsável:</strong> <?php echo $row['ib_gestor_responsavel']; ?> | <strong>Fiscal Responsável:</strong> <?php echo $row['ib_fiscal']; ?></p>
      <p><strong>Objeto:</strong> <?php echo $row['objeto']; ?></p>
      <p><strong>Informações Gerais:</strong> <?php echo $row['informacoes_gerais']; ?></p>
      <p><strong>Observações:</strong> <?php echo $row['observacoes']; ?></p>

      <div class="button-left">
        <button onclick="window.location.href='editar_iniciativa.php?id=<?php echo $row['id']; ?>';">Status andamento</button>
      </div>

      <div class="acoes">
        <button onclick="window.location.href='acompanhamento.php?id_iniciativa=<?php echo $row['id']; ?>';">🛠 Acompanhar Pendências</button>
        <button onclick="window.location.href='infocontratuais.php?id_iniciativa=<?php echo $row['id']; ?>';">📋 projeto e licitação </button>
        <button onclick="window.location.href='infocontratuais.php?id_iniciativa=<?php echo $row['id']; ?>';">📄 Informações Contratuais</button>
        <button onclick="window.location.href='medicoes.php?id_iniciativa=<?php echo $row['id']; ?>';">📊 Acompanhamento de Medições</button>
        <button onclick="window.location.href='cronogramamarcos.php?id_iniciativa=<?php echo $row['id']; ?>';">📆 Eventograma</button>
      </div>
    </div>
  <?php endwhile; ?>
  
  <div class="botao-voltar">
    <button onclick="window.location.href='<?php echo $tipo_usuario === "admin" ? "diretorias.php" : "home.php"; ?>';">
      &lt; Voltar
    </button>
  </div>

</div>

<script>
  const accordions = document.querySelectorAll(".accordion");
  accordions.forEach((acc) => {
    acc.addEventListener("click", function () {
      this.classList.toggle("active");
      const panel = this.nextElementSibling;
      if (panel.style.display === "block") {
        panel.style.display = "none";
      } else {
        panel.style.display = "block";
      }
    });
  });
</script>
</body>
</html>
