<div class="formulario-container" style="max-width: 500px; margin: 40px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); font-family: Poppins, sans-serif;">
  <h2 style="text-align: center; margin-bottom: 20px;">Solicitação de Acesso</h2>
  <form method="POST" action="salvar_solicitacao.php">
    <label for="nome">Nome Completo</label>
    <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px;" />

    <label for="telefone">Telefone</label>
    <input type="text" id="telefone" name="telefone" placeholder="(xx) xxxxx-xxxx" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px;" />

    <button type="submit" style="width: 100%; background-color: #28a745; color: white; padding: 12px; border: none; border-radius: 5px; font-weight: bold;">Solicitar Acesso</button>
  </form>
</div>
