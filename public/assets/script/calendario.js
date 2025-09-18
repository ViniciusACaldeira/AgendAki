class Calendario{
    static TipoBotao = {
        ANTERIOR: "anterior",
        PROXIMO: "proximo",
        ATUAL: "atual"
    };

    static TipoSemana = {
        SEGUNDA: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb','Dom'],
        DOMINGO: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb']
    }

    constructor( id, permiteMultiplasDatas )
    {
        this.id = id;
        this.calendario = document.getElementById( this.id );
        this.diasSelecionados = new Set( );
        this.diasInvalidos = new Set( );
        this.diasValidos = new Set( );
        this.semana = Calendario.TipoSemana.DOMINGO;
        this.mes = this.primeiroDiaMes( new Date( ) );
        this.dataMinima = null;
        this.dataMaxima = null;
        this.acoesSelecionar = [];
        this.permiteMultiplasDatas = permiteMultiplasDatas !== undefined ? permiteMultiplasDatas : true;
        this.validarDiaValidoVazio = false;
        this.build( );
    }

    setPermiteMultiplasDatas( permite )
    {
        this.permiteMultiplasDatas = permite;
    }

    setDataMinima( data )
    {
        this.dataMinima = data;
    }

    setDataMaxima( data )
    {
        this.dataMaxima = data;
    }

    setDiasSelecionados( dias )
    {
        dias.forEach( d => { this.diasSelecionados.add( d ) } );
    }

    setDiasValidos( dias )
    {
        this.diasValidos.clear( );
        dias.forEach( d => { this.diasValidos.add( d ) } );
    }

    setValidaDiasValidosVazio( validar )
    {
        this.validarDiaValidoVazio = validar;
    }

    coletarDiasSelecionados( )
    {
        return this.diasSelecionados;
    }

    setDiasInvalidos( dias )
    {
        dias.forEach( d => {this.diasInvalidos.add( d ) } );
    }

    coletarDiasInvalidos( )
    {
        return this.diasInvalidos;
    }

    gerarMatrix( )
    {
        const celulas = [];

        const primeiroDia = this.primeiroDiaMes( this.mes );
        const ultimoDia = this.ultimoDiaMes( this.mes );
        const primeiroDiaSemana = (primeiroDia.getDay( ) - (this.semana == Calendario.TipoSemana.DOMINGO ? 0 : 1) + 7 ) % 7;

        for( let i = 0; i < primeiroDiaSemana; i++ )
            celulas.push( null );

        const ano = this.mes.getFullYear( );
        const mes = this.mes.getMonth( );

        for( let i = 1; i <= ultimoDia.getDate( ); i++ )
            celulas.push( new Date( ano, mes, i ) );
        
        while( celulas.length % 7 !== 0 )
            celulas.push( null );

        return celulas;
    }

    gerarSemana( )
    {
        this.calendario.querySelector("#semanas").innerHTML = this.semana.map( s => `<div class='semana'>${s}</div>`).join( '' );
    }

    primeiroDiaMes( data )
    {
        return new Date( data.getFullYear( ), data.getMonth( ), 1 );
    }

    ultimoDiaMes( data )
    {
        return new Date( data.getFullYear( ), data.getMonth( ) + 1, 0 );
    }

    diaMes( data )
    {
        return new Date( data.getFullYear( ), data.getMonth( ), data.getDay( ) );
    }

    addAcoesAoSelecionar( acoes )
    {
        acoes.forEach( a => this.acoesSelecionar.push(a) );
    }

    setAcoesAoSelecionar( acoes )
    {
        this.acoesSelecionar = [];
        this.addAcoesAoSelecionar( acoes );
    }

    toISO( data )
    {
        return [data.getFullYear(), String(data.getMonth()+1).padStart(2,'0'), String(data.getDate()).padStart(2,'0')].join('-');
    }

    addMes( data, quantidade )
    {
        return new Date( data.getFullYear( ), data.getMonth( ) + quantidade, 1 );
    }

    limpar( )
    {
        this.diasSelecionados.clear( );
    }

    alterarMes( tipo )
    {
        switch( tipo )
        {
            case Calendario.TipoBotao.ANTERIOR:
                this.mes = this.addMes( this.mes, -1 );
                break;
            case Calendario.TipoBotao.PROXIMO:
                this.mes = this.addMes( this.mes, +1 );
                break;
            default:
                this.mes = this.primeiroDiaMes( new Date( ) );
                break;
        }

        this.render( );
    }

    build( )
    {
        this.calendario.innerHTML = 
        `   <div class="toolbar">
                <div class="title">
                    <span>🗓️</span>
                    <span id="cabecalho"></span>
                </div>
                <div class="spacer"></div>
                <div>
                    <button type="button" class="btn" data-act="${Calendario.TipoBotao.ANTERIOR}">◀</button>
                    <button type="button" class="btn ghost" data-act="${Calendario.TipoBotao.ATUAL}">Hoje</button>
                    <button type="button" class="btn" data-act="${Calendario.TipoBotao.PROXIMO}">▶</button>
                </div>
            </div>

            <div class="seg" id="semanas"></div>
            <div class="grid" id="grid"></div>

            <div class="legend">
                <span class="dot"></span>
                <span>Dia marcado</span>
                <span style="margin-left:auto"></span>
                <label>Semana começa:
                    <select id="comeco_semana">
                        <option value="0">Domingo</option>
                        <option value="1">Segunda</option>
                    </select>
                </label>
            </div>
            
            <div class="card panel">
                <div class="row">
                    ${ this.permiteMultiplasDatas ? 
                        `<button type="button" class="btn" data-bulk="diasUtil">Marcar dias úteis</button>
                        <button type="button" class="btn" data-bulk="fimSemana">Marcar fins de semana</button>
                        <button type="button" class="btn" data-bulk="todos">Marcar mês inteiro</button>`: "" }
                    <button type="button" class="btn danger" data-bulk="limpar">Limpar</button>
                </div>
            </div>`;

        this.calendario.querySelector( "#comeco_semana" ).addEventListener( "change", e => {
            this.semana = e.target.value == 0 ? Calendario.TipoSemana.DOMINGO : Calendario.TipoSemana.SEGUNDA;
            this.render( );
        } );

        this.calendario.addEventListener( "click", e => {
            let botao = e.target.closest( "[data-act]");

            if( botao )
                return this.alterarMes( botao.dataset.act );

            if( e.target.tagName.toLowerCase( ) == "label" )
                return;

            botao = e.target.closest( ".dia" );
            if( botao )
            {
                const data = botao.dataset.data;

                if( !data )
                    return;
                
                if( this.diasInvalidos.has( data ) || !this.validarDiaValido( data ) )
                    return;

                if( this.diasSelecionados.has( data ) )
                    this.diasSelecionados.delete( data );
                else
                {
                    if( !this.permiteMultiplasDatas )
                        this.diasSelecionados.clear( );

                    this.diasSelecionados.add( data );
                }

                if( this.acoesSelecionar.length > 0 )
                    this.acoesSelecionar.forEach( (acao) => acao(data) );

                this.render( );
                return;
            }

            const bulk = e.target.closest('[data-bulk]');

            if( bulk )
            {
                const mode = bulk.getAttribute('data-bulk');
                if( mode === 'limpar' )
                    this.diasSelecionados.clear( ); 
                else
                {
                    const celulas = this.gerarMatrix( );

                    celulas.forEach( ( d ) => {
                        if( !d ) 
                            return;

                        const iso = this.toISO( d );
                        
                        if( this.diasInvalidos.has( iso ) || !this.dataValida( d ) || !this.validarDiaValido( iso ) )
                            return;

                        const dia = d.getDay( );
                        if( mode === 'todos' ) 
                            this.diasSelecionados.add( iso );
                        else if( mode==='diasUtil' && dia>=1 && dia<=5)
                            this.diasSelecionados.add( iso );
                        else if( mode==='fimSemana' && (dia===0 || dia===6))
                            this.diasSelecionados.add( iso );
                    });
                }

                this.render( );
                return;
            }
        });
    }

    validarDiaValido( d )
    {
        if( this.diasValidos.size > 0 )
            return this.diasValidos.has( d );
        else if( this.validarDiaValidoVazio )
            return false;
        
        return true;
    }

    render( )
    {
        this.gerarCabecalho( );
        this.gerarSemana( );
        this.gerarGrid( );
    }

    gerarCabecalho( )
    {
        this.calendario.querySelector( "#cabecalho" ).textContent = this.mes.toLocaleDateString( "pt-BR", {month:'long', year:'numeric'});
    }

    gerarGrid(  )
    {
        const hoje    = this.toISO( new Date( ) );
        const celulas = this.gerarMatrix( );

        this.calendario.querySelector( "#grid" ).innerHTML = celulas.map( d => {
            if( !d )
                return `<div></div>`;
            
            const iso = this.toISO( d );
            const desabilitado = this.diasInvalidos.has( iso ) || !this.dataValida( d ) || !this.validarDiaValido( iso );
            const checked = this.diasSelecionados.has( iso );
            const eHoje = hoje === iso;

            return `
                <label class="dia ${desabilitado ? 'disabled' : '' } ${eHoje ? 'hoje' : '' }" data-data="${iso}">
                    <div class="top">
                        <span class="num">${d.getDate( )}</span>
                        <input id="data_${iso}" type="checkbox" class="check" ${checked ? "checked" : "" } ${desabilitado ? "disabled" : "" } aria-label="Selecionar ${d.toDateString("pt-BR")}" />
                    </div>
                    ${checked ? '<span class="selecionado">Selecionado</span>' : "" }
                </label>
            `;
        }).join( "" );
    }

    dataValida( data )
    {
        if( !data )
            return false;

        if( (this.dataMinima && data < this.dataMinima ) ||
            (this.dataMaxima && data > this.dataMaxima ) )
            return false;

        return true;
    }
}