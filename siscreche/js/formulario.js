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
      window.location.href = 'index.php?page=home';
    }
  }

  function calcularVariacao() {
    const exec = parseFloat(execucaoInput.value.replace(',', '.')) || 0;
    const prev = parseFloat(previstoInput.value.replace(',', '.')) || 0;
    const variacao = (exec - prev).toFixed(2);
    variacaoInput.value = variacao.replace('.', ',');
  }

document.getElementById('btn-sim').addEventListener('click', function() {
  window.location.href = 'index.php';
});

document.getElementById('btn-nao').addEventListener('click', function() {
  document.getElementById('modal-cancelar').classList.add('hidden');
});

document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('modal');
  const mensagem = modal.getAttribute('data-mensagem');

  if (mensagem) {
    showModal(mensagem);
  }
});

execucaoInput.addEventListener('input', calcularVariacao);
previstoInput.addEventListener('input', calcularVariacao);
