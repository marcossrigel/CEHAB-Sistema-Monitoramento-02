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
        )
        ORDER BY ordem ASC";
} else {
    $sql = "SELECT * FROM iniciativas 
      WHERE id_usuario = $id_usuario 
      OR id IN (
          SELECT id_iniciativa FROM compartilhamentos WHERE id_compartilhado = $id_usuario
      )
      ORDER BY ordem ASC";
}

$resultado = $conexao->query($sql);
?>

<div class="container">
  <div class="topo-linha" style="position: relative; padding: 20px 0;">
  <h1 style="text-align: center; margin: 0;">
    <?php 
      if ($tipo_usuario === 'admin' && isset($_GET['diretoria'])) {
        echo "Iniciativas da Diretoria: " . htmlspecialchars($_GET['diretoria']);
      } else {
        echo "Iniciativas Cadastradas";
      }
    ?>
  </h1>

  <?php if ($tipo_usuario === 'comum'): ?>
  <a href="index.php?page=compartilhar&id=<?php echo $id_usuario; ?>" 
     style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); text-decoration: none; background-color: #007bff; color: white; padding: 8px 14px; border-radius: 6px; font-weight: bold; font-size: 14px;">
    👥 Compartilhar
  </a>
<?php endif; ?>

</div>

  <div id="sortable">
    <?php while ($row = $resultado->fetch_assoc()): ?>
      <div class="item">
        <button class="accordion" data-id="<?php echo $row['id']; ?>">
          <strong><?php echo htmlspecialchars($row['iniciativa']); ?></strong>
          <span class="seta">⌄</span>
        </button>

        <div class="panel" id="panel-<?php echo $row['id']; ?>">
          <p><strong>Status:</strong> <?php echo $row['ib_status']; ?> | 
             <strong>Data da Vistoria:</strong> <?php echo $row['data_vistoria']; ?> | 
             <strong>Nº do Contrato:</strong> <?php echo $row['numero_contrato']; ?>
          </p>
          <p><strong>Execução:</strong> <?php echo $row['ib_execucao']; ?> | 
             <strong>Previsto:</strong> <?php echo $row['ib_previsto']; ?> | 
             <strong>Variação:</strong> <?php echo $row['ib_variacao']; ?> | 
             <strong>Valor Medido Acumulado:</strong> <?php echo $row['ib_valor_medio']; ?>
          </p>
          <p><strong>Secretaria:</strong> <?php echo $row['ib_secretaria']; ?> | 
             <strong>Órgão:</strong> <?php echo $row['ib_orgao']; ?> | 
             <strong>Processo SEI:</strong> <?php echo $row['ib_numero_processo_sei']; ?>
          </p>
          <p><strong>Gestor Responsável:</strong> <?php echo $row['ib_gestor_responsavel']; ?> | 
             <strong>Fiscal Responsável:</strong> <?php echo $row['ib_fiscal']; ?>
          </p>
          <p><strong>Objeto:</strong> <?php echo $row['objeto']; ?></p>
          <p><strong>Informações Gerais:</strong> <?php echo $row['informacoes_gerais']; ?></p>
          <p><strong>Observações:</strong> <?php echo $row['observacoes']; ?></p>

          <div class="button-left">
            <button onclick="window.location.href='index.php?page=editar_iniciativa&id=<?php echo $row['id']; ?>';">Status andamento</button>
          </div>

          <div class="acoes">
            <button onclick="window.location.href='index.php?page=acompanhamento&id_iniciativa=<?php echo $row['id']; ?>';">🛠 Acompanhar Pendências</button>
            <button onclick="window.location.href='index.php?page=projeto_licitacoes&id_iniciativa=<?php echo $row['id']; ?>';">📋 Projeto e Licitação</button>
            <button onclick="window.location.href='index.php?page=info_contratuais&id_iniciativa=<?php echo $row['id']; ?>';">📄 Informações Contratuais</button>
            <button onclick="window.location.href='index.php?page=medicoes&id_iniciativa=<?php echo $row['id']; ?>';">📊 Acompanhamento de Medições</button>
            <button onclick="window.location.href='index.php?page=cronogramamarcos&id_iniciativa=<?php echo $row['id']; ?>';">📆 Cronograma</button>
          </div>

        </div>
      </div>

    <?php endwhile; ?>
  </div>

  <div class="botao-voltar">
    <button onclick="window.location.href='<?php echo $tipo_usuario === "admin" ? "index.php?page=diretorias" : "index.php?page=home"; ?>';">&lt; Voltar</button>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="js/visualizar.js"></script>