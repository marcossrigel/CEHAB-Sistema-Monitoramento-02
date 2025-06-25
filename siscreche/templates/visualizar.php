<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

include("config.php");

$id_usuario = $_SESSION['id_usuario'];
$tipo_usuario = $_SESSION['tipo_usuario'];

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

<div class="container">
  <div class="topo-linha">
    <div class="voltar-box"></div>
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
      <p><strong>Execução:</strong> <?php echo $row['ib_execucao']; ?> | <strong>Previsto:</strong> <?php echo $row['ib_previsto']; ?> | <strong>Variação:</strong> <?php echo $row['ib_variacao']; ?> | <strong>Valor Medido Acumulado:</strong> <?php echo $row['ib_valor_medio']; ?></p>
      <p><strong>Secretaria:</strong> <?php echo $row['ib_secretaria']; ?> | <strong>Órgão:</strong> <?php echo $row['ib_orgao']; ?> | <strong>Processo SEI:</strong> <?php echo $row['ib_numero_processo_sei']; ?></p>
      <p><strong>Gestor Responsável:</strong> <?php echo $row['ib_gestor_responsavel']; ?> | <strong>Fiscal Responsável:</strong> <?php echo $row['ib_fiscal']; ?></p>
      <p><strong>Objeto:</strong> <?php echo $row['objeto']; ?></p>
      <p><strong>Informações Gerais:</strong> <?php echo $row['informacoes_gerais']; ?></p>
      <p><strong>Observações:</strong> <?php echo $row['observacoes']; ?></p>

      <div class="button-left">
        <button onclick="window.location.href='index.php?page=editar_iniciativa&id=<?php echo $row['id']; ?>';">Status andamento</button>
      </div>

      <div class="acoes">
        <button onclick="window.location.href='acompanhamento.php?id_iniciativa=<?php echo $row['id']; ?>';">🛠 Acompanhar Pendências</button>
        <button onclick="window.location.href='infocontratuais.php?id_iniciativa=<?php echo $row['id']; ?>';">📋 Projeto e Licitação</button>
        <button onclick="window.location.href='infocontratuais.php?id_iniciativa=<?php echo $row['id']; ?>';">📄 Informações Contratuais</button>
        <button onclick="window.location.href='medicoes.php?id_iniciativa=<?php echo $row['id']; ?>';">📊 Acompanhamento de Medições</button>
        <button onclick="window.location.href='cronogramamarcos.php?id_iniciativa=<?php echo $row['id']; ?>';">📆 Eventograma</button>
      </div>
    </div>
  <?php endwhile; ?>
  
  <div class="botao-voltar">
    <button onclick="window.location.href='<?php echo $tipo_usuario === "admin" ? "diretorias.php" : "index.php"; ?>';">
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
