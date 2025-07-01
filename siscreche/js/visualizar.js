// Script accordion (deve estar aí já)
const accordions = document.querySelectorAll(".accordion");
accordions.forEach((acc) => {
  acc.addEventListener("click", function () {
    this.classList.toggle("active");
    const panel = this.nextElementSibling;
    panel.style.display = panel.style.display === "block" ? "none" : "block";
  });
});

// Script para ativar o drag-and-drop
new Sortable(document.getElementById('sortable'), {
  animation: 150,
  handle: '.accordion',
  ghostClass: 'drag-ghost'
});

document.addEventListener('DOMContentLoaded', () => {
  // 1. Abrir o painel salvo
  const painelAberto = localStorage.getItem('painelAberto');
  if (painelAberto) {
    const acc = document.querySelector(`.accordion[data-id="${painelAberto}"]`);
    const panel = document.querySelector(`#panel-${painelAberto}`);
    if (acc && panel) {
      acc.classList.add('active');
      panel.style.maxHeight = panel.scrollHeight + "px";
    }
  }

  // 2. Comportamento do accordion
  document.querySelectorAll('.accordion').forEach(btn => {
    btn.addEventListener('click', () => {
      const panel = btn.nextElementSibling;

      const isOpen = panel.style.maxHeight;

      // Fecha todos os outros
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
