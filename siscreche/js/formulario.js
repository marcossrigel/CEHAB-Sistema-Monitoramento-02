document.querySelector('form.formulario').addEventListener('submit', function () {
  const p = (document.getElementById('numero_contrato_prefixo').value || '').padStart(3, '0');
  const a = document.getElementById('numero_contrato_ano').value || '';
  document.getElementById('numero_contrato').value = `${p}/${a}`;
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
      window.location.href = '../index.php?page=home';
    }
  }

  function calcularVariacao() {
    const exec = parseFloat(execucaoInput.value.replace(',', '.')) || 0;
    const prev = parseFloat(previstoInput.value.replace(',', '.')) || 0;
    const variacao = (exec - prev).toFixed(2);
    variacaoInput.value = variacao.replace('.', ',');
  }

document.getElementById('btn-sim').addEventListener('click', function() {
    window.location.href = '../index.php?page=home';
});

document.getElementById('btn-nao').addEventListener('click', function() {
  document.getElementById('modal-cancelar').classList.add('hidden');
});

document.addEventListener('DOMContentLoaded', () => {
  const form  = document.querySelector('form.formulario');
  if (!form) return;

  const pref   = document.getElementById('numero_contrato_prefixo');
  const ano    = document.getElementById('numero_contrato_ano');
  const hidden = document.getElementById('numero_contrato');

  function syncContrato() {
    const p = (pref?.value || '').replace(/\D+/g, '').padStart(3, '0'); // 3 dígitos
    const a = (ano?.value  || '').replace(/\D+/g, '');                  // 4 dígitos
    hidden.value = (p && a.length === 4) ? `${p}/${a}` : '';
  }

  // mantém sincronizado enquanto digita
  pref?.addEventListener('input', syncContrato);
  ano ?.addEventListener('input', syncContrato);

  // garante antes de enviar
  form.addEventListener('submit', () => {
    syncContrato();
    // (opcional) validação
    if (!hidden.value.match(/^\d{3}\/\d{4}$/)) {
      // Se quiser bloquear quando estiver inválido, descomente:
      // event.preventDefault();
      // alert('Preencha o nº do contrato no formato 000/2025');
    }
  });
});

execucaoInput.addEventListener('input', calcularVariacao);
previstoInput.addEventListener('input', calcularVariacao);
