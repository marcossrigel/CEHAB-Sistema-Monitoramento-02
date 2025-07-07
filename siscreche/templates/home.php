<div class="container">
  <div class="header">
    <div class="header-text">
      <p>Olá, <?php echo $_SESSION['nome']; ?>!</p>
      <h1>Bem-vindo ao Sistema de Monitoramento</h1>
      <p>Organize e cadastre suas informações com eficiência e facilidade.</p>
    </div>

    <div class="button-group">
      <a href="index.php?page=formulario" class="btn">Criar Iniciativa</a>
      <a href="index.php?page=visualizar" class="btn btn-secondary">Minhas Vistorias</a>
      <button onclick="document.getElementById('modalCompartilhar').style.display='block'">Compartilhar</button>
      <a href="https://www.getic.pe.gov.br/?p=auth_painel" class="texto-login">Sair</a>
    </div>

    <?php if (isset($_GET['compartilhado'])): ?>
      <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0;">
        Iniciativas para <strong><?php echo htmlspecialchars($_GET['compartilhado']); ?></strong> compartilhadas com sucesso!
      </div>
    <?php elseif (isset($_GET['erro']) && $_GET['erro'] === 'usuario'): ?>
      <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0;">
        Usuário não encontrado. Verifique o nome digitado.
      </div>
    <?php endif; ?>

  </div>

  <div class="accordion" onclick="toggleAccordion()">
    <div class="accordion-header">
      <h2>Ajuda</h2>
      <span id="accordion-icon">⌄</span>
    </div>
    <div id="accordion-content" class="accordion-content hidden">
      <p>Para criar novas iniciativas, clique em "Criar Iniciativa". Você será levado a um formulário onde poderá cadastrar os dados iniciais.</p>
      <p>Em "Minhas Vistorias", você pode visualizar e editar as informações já cadastradas.</p>
    </div>
  </div>
</div>

<div id="modalCompartilhar" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
  <div style="background:white; padding:20px; margin:100px auto; width:300px; position:relative; border-radius:8px;">
    <h3>Compartilhar com outro usuário</h3>
    <form method="POST" action="salvar_compartilhamento.php">
      <input type="text" name="nome_usuario" placeholder="Digite o nome do usuário (rede)" required style="width:100%; padding:8px; margin-bottom:10px;">
      <button type="submit" style="background:green; color:white; padding:8px 12px;">Compartilhar</button>
      <button type="button" onclick="document.getElementById('modalCompartilhar').style.display='none'" style="margin-left:10px;">Cancelar</button>
    </form>
  </div>
</div>

<script src="js/home.js"></script>