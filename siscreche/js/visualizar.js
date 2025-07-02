
const accordions = document.querySelectorAll(".accordion");
accordions.forEach((acc) => {
  acc.addEventListener("click", function () {
    this.classList.toggle("active");
    const panel = this.nextElementSibling;
    panel.style.display = panel.style.display === "block" ? "none" : "block";
  });
});


new Sortable(document.getElementById('sortable'), {
  animation: 150,
  handle: '.accordion',
  ghostClass: 'drag-ghost',
  onEnd: function () {
    const novaOrdem = [];

    document.querySelectorAll('#sortable .item .accordion').forEach((el, index) => {
      novaOrdem.push({ id: el.dataset.id, ordem: index });
    });

    fetch('templates/salvar_ordem.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(novaOrdem)
    }).then(res => res.json())
      .then(data => {
        if (data.status === 'sucesso') {
          console.log('Ordem atualizada com sucesso!');
        } else {
          console.error('Erro ao salvar ordem:', data.erro);
        }
      });
  }
});

document.addEventListener('DOMContentLoaded', () => {

  const painelAberto = localStorage.getItem('painelAberto');
  if (painelAberto) {
    const acc = document.querySelector(`.accordion[data-id="${painelAberto}"]`);
    const panel = document.querySelector(`#panel-${painelAberto}`);
    if (acc && panel) {
      acc.classList.add('active');
      panel.style.maxHeight = panel.scrollHeight + "px";
    }
  }

  document.querySelectorAll('.accordion').forEach(btn => {
    btn.addEventListener('click', () => {
      const panel = btn.nextElementSibling;

      const isOpen = panel.style.maxHeight;

      document.querySelectorAll('.accordion').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.panel').forEach(p => p.style.maxHeight = null);

      if (!isOpen) {
        btn.classList.add('active');
        panel.style.maxHeight = panel.scrollHeight + "px";
        localStorage.setItem('painelAberto', btn.dataset.id);
      } else {
        localStorage.removeItem('painelAberto');
      }
    });
  });
});