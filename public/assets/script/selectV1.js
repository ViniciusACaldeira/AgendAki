let focusedIndex = -1;

function criarSelectDropdown( containerId, placeholder, opcoesArray ) {
    const container = document.getElementById(containerId);
    if (!container) return console.error(`Container "${containerId}" não encontrado`);

    const select = document.createElement('div');
    select.className = 'select-dropdown';

    select.innerHTML = `
        <div class="select-trigger">${placeholder}</div>
        <input type="text" class="select-input" placeholder="Pesquisar...">
        <ul class="select-opcoes">
            ${opcoesArray.map(op => `<li data-valor="${op.valor}">${op.texto}</li>`).join('')}
        </ul>
    `;

    container.appendChild(select);
}

function filtrarOpcoes(dropdown) {
    const input = dropdown.querySelector('.select-input');
    const filtro = input.value.toLowerCase();
    const opcoes = dropdown.querySelectorAll('.select-opcoes li');
    opcoes.forEach(li => {
        li.style.display = li.textContent.toLowerCase().includes(filtro) ? '' : 'none';
        li.classList.remove('focused');
    });
    focusedIndex = -1;
}

function fecharTodosDropdowns(exceto = null) {
    document.querySelectorAll('.select-dropdown.ativo').forEach(dd => {
        if (dd !== exceto) {
            dd.classList.remove('ativo');
            dd.querySelector('.select-input').value = '';
            filtrarOpcoes(dd);
        }
    });
}

document.addEventListener('click', e => {
    const dropdown = e.target.closest('.select-dropdown');
    const trigger = e.target.closest('.select-trigger');
    const opcao = e.target.closest('.select-opcoes li');

    if (!dropdown) {
        fecharTodosDropdowns();
        return;
    }

    if (trigger) {
        dropdown.classList.toggle('ativo');
        dropdown.querySelector('.select-input').focus();
    }

    if (opcao) {
        dropdown.querySelector('.select-trigger').textContent = opcao.textContent;
        dropdown.dataset.valorSelecionado = opcao.getAttribute('data-valor');
        dropdown.classList.remove('ativo');
        dropdown.querySelector('.select-input').value = '';
        filtrarOpcoes(dropdown);
    }
});

// Input para filtro
document.addEventListener('input', e => {
    if (!e.target.classList.contains('select-input')) return;
    const dropdown = e.target.closest('.select-dropdown');
    filtrarOpcoes(dropdown);
});

// Navegação com teclado
document.addEventListener('keydown', e => {
    const ativoDropdown = document.querySelector('.select-dropdown.ativo');
    if (!ativoDropdown) return;

    const opcoes = Array.from(ativoDropdown.querySelectorAll('.select-opcoes li'))
        .filter(li => li.style.display !== 'none');

    if (opcoes.length === 0) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        focusedIndex = (focusedIndex + 1) % opcoes.length;
        opcoes.forEach(li => li.classList.remove('focused'));
        opcoes[focusedIndex].classList.add('focused');
        opcoes[focusedIndex].scrollIntoView({block: 'nearest'});
    }

    if (e.key === 'ArrowUp') {
        e.preventDefault();
        focusedIndex = (focusedIndex - 1 + opcoes.length) % opcoes.length;
        opcoes.forEach(li => li.classList.remove('focused'));
        opcoes[focusedIndex].classList.add('focused');
        opcoes[focusedIndex].scrollIntoView({block: 'nearest'});
    }

    if (e.key === 'Enter' && focusedIndex >= 0) {
        e.preventDefault();
        opcoes[focusedIndex].click();
        focusedIndex = -1;
    }

    if (e.key === 'Escape') {
        fecharTodosDropdowns();
    }
});