function adicionarLinha() {
    const tabela = document.getElementById('medicoes').getElementsByTagName('tbody')[0];
    const novaLinha = tabela.insertRow();

    const campos = [
        { name: 'ordem[]', type: 'text' },
        { name: 'etapa[]', type: 'text' },
        { name: 'responsavel[]', type: 'text' },
        { name: 'inicio_previsto[]', type: 'date' },
        { name: 'termino_previsto[]', type: 'date' },
        { name: 'inicio_real[]', type: 'date' },
        { name: 'termino_real[]', type: 'date' },
        { name: 'status[]', type: 'text' },
        { name: 'observacao[]', type: 'text' }
    ];

    campos.forEach(campo => {
        const celula = novaLinha.insertCell();
        const input = document.createElement('input');
        input.name = campo.name;
        input.type = campo.type;
        input.required = false;
        celula.appendChild(input);
    });
}

function deletarLinha(id) {
    if (!confirm('Tem certeza que deseja excluir esta linha?')) return;

    fetch('templates/deletar_linha.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(response => response.text())
    .then(data => {
        if (data.includes('sucesso')) {
            const linha = document.querySelector(`tr[data-id='${id}']`);
            if (linha) linha.remove();
        } else {
            alert('Erro ao excluir: ' + data);
        }
    })
    .catch(error => {
        alert('Erro de rede: ' + error);
    });
}


