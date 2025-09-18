class Select{

    constructor( id, placeholder, opcoes )
    {
        this.id = id;
        this.placeholder = placeholder;
        this.opcoes = opcoes;
        this.select = document.getElementById( id );
        this.selecionado = new Set( );
        this.acoes = [];
        this.focusedIndex = -1;

        this.adicionaEventos( );
    }

    setOpcoes( opcoes )
    {
        this.opcoes = opcoes;
    }

    addAcao( acao )
    {
        this.acoes.push( acao );
    }

    setAcao( acao )
    {
        this.acoes.length = 0;
        this.acoes.push( acao );
    }
    
    filtrarOpcoes( dropdown ) 
    {
        const input = dropdown.querySelector('.select-input');
        const filtro = input.value.toLowerCase();
        const opcoes = dropdown.querySelectorAll('.select-opcoes li');
        opcoes.forEach(li => {
            li.style.display = li.textContent.toLowerCase().includes(filtro) ? '' : 'none';
            li.classList.remove('focused');
        });
        this.focusedIndex = -1;
    }

    fecharTodosDropdowns( exceto = null ) 
    {
        document.querySelectorAll('.select-dropdown.ativo').forEach( dd => {
            if (dd !== exceto) {
                dd.classList.remove('ativo');
                dd.querySelector('.select-input').value = '';
                this.filtrarOpcoes(dd);
            }
        });
    }

    addSelecionado( elemento )
    {
        this.selecionado.clear( );

        const opcao = { "valor": elemento.dataset.valor, "texto": elemento.textContent };

        this.selecionado.add( opcao );
    }

    getSelecionado( )
    {
        return Array.from(this.selecionado);
    }

    getElemento( )
    {
        return this.select;
    }

    limpar( )
    {
        this.selecionado.clear( );
        this.select.querySelector('.select-input').value = '';
    }

    adicionaEventos( )
    {
        this.select.addEventListener('click', e => {
            const dropdown = e.target.closest('.select-dropdown');
            const trigger = e.target.closest('.select-trigger');
            const opcao = e.target.closest('.select-opcoes li');

            if (!dropdown) {
                this.fecharTodosDropdowns();
                return;
            }

            if (trigger) {
                dropdown.classList.toggle('ativo');
                dropdown.querySelector('.select-input').focus();
            }

            if (opcao) {
                dropdown.querySelector('.select-trigger').textContent = opcao.textContent;
                this.addSelecionado( opcao );

                dropdown.classList.remove('ativo');
                dropdown.querySelector('.select-input').value = '';
                this.filtrarOpcoes(dropdown);

                this.acoes.forEach( a => a( this ) );
            }
        });

        this.select.addEventListener('input', e => {
            if (!e.target.classList.contains('select-input')) return;
            const dropdown = e.target.closest('.select-dropdown');
            this.filtrarOpcoes(dropdown);
        });

        this.select.addEventListener('keydown', e => {
            const ativoDropdown = this.select.querySelector('.select-dropdown.ativo');
            if (!ativoDropdown) return;

            const opcoes = Array.from(ativoDropdown.querySelectorAll('.select-opcoes li'))
                .filter(li => li.style.display !== 'none');

            if (opcoes.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.focusedIndex = (this.focusedIndex + 1) % opcoes.length;
                opcoes.forEach(li => li.classList.remove('focused'));
                opcoes[this.focusedIndex].classList.add('focused');
                opcoes[this.focusedIndex].scrollIntoView({block: 'nearest'});
            }

            if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.focusedIndex = (this.focusedIndex - 1 + opcoes.length) % opcoes.length;
                opcoes.forEach(li => li.classList.remove('focused'));
                opcoes[this.focusedIndex].classList.add('focused');
                opcoes[this.focusedIndex].scrollIntoView({block: 'nearest'});
            }

            if (e.key === 'Enter' && this.focusedIndex >= 0) {
                e.preventDefault();
                opcoes[this.focusedIndex].click();
                this.focusedIndex = -1;
            }

            if (e.key === 'Escape') {
                this.fecharTodosDropdowns();
            }
        });
    }

    build( )
    {
        if( !this.select ) 
            return console.error( `Container "${this.id}" não encontrado` );

        this.select.innerHTML = "";

        const select = document.createElement( 'div' );
        select.className = 'select-dropdown';

        select.innerHTML = `
            <div class="select-trigger">${this.placeholder}</div>
            <input type="text" class="select-input" placeholder="Pesquisar...">
            <ul class="select-opcoes">
                ${this.opcoes.map(op => `<li data-valor="${op.valor}">${op.texto}</li>`).join('')}
            </ul>
        `;

        this.select.appendChild(select);
    }
}