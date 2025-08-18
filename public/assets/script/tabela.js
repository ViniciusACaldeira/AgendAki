class Tabela
{
    static TipoBotao = {
        PRIMEIRO: "primeiro",
        ANTERIOR: "anterior",
        PROXIMO: "proximo",
        ULTIMO: "ultimo"
    };
    
    constructor( tabela, acoes )
    {
        this.tabela = tabela;
        this.dados_coluna = [];
        this.dados = [];
        this.url = "";
        this.paginado = false;
        this.temFiltro = false;
        this.filtro = false;
        this.method = "GET";

        this.botaoTipos = [
            Tabela.TipoBotao.PRIMEIRO,
            Tabela.TipoBotao.ANTERIOR,
            Tabela.TipoBotao.PROXIMO,
            Tabela.TipoBotao.ULTIMO
        ];


        this.total_paginas = 1;

        if( acoes != undefined )
        {
            this.temAcao = true;
            this.acoes = acoes;
        }
        else
            this.temAcao = false;
    }

    setMethod( method )
    {
        this.method = method;
    }

    setFiltro( filtro )
    {
        const form = document.getElementById( filtro );

        if( form != undefined )
        {
            this.setMethod( form.method );

            form.addEventListener( 'submit', (event) =>{
                event.preventDefault( );
                this.build( );
            });

            this.filtro = form;
            this.temFiltro = true;
        }
    }

    setPaginado( paginado )
    {
        this.paginado = paginado;
    }

    getFiltro( )
    {
        if( this.paginado && this.filtro !== undefined )
            return this.filtro;

        return null;
    }

    setURL( url )
    {
        this.url = url;
    }

    setTemAcao( temAcao )
    {
        this.temAcao = temAcao;
    }
    
    addCampo( nome, campo, alternativo, formatacao )
    {
        this.dados_coluna.push( { nome, campo, alternativo, formatacao } );
    }

    setDados( data )
    {
        this.dados = data;
    }

    getTable( )
    {
        return document.getElementById( this.tabela );
    }

    setPaginacao( paginacao )
    {
        this.page = paginacao['page'];
        this.per_page = paginacao['per_pagina'];
        this.total = paginacao['total'];
        this.total_paginas = paginacao['paginas'];

        const table = this.getTable( );

        const total = table.querySelector( "#total_registro" );
        total.innerText = `Total de registros: ${this.total}`;

        table.querySelector( "#pagina" ).max = paginacao['paginas'];
        table.querySelector( "#pagina" ).min = 1;        
    }

    build( )
    {
        let url = this.url;

        if( this.paginado )
        {
            const page = document.getElementById( "pagina" ).value;
            const per_page = document.getElementById( "per_page" ).value;

            if( page == undefined )
                page = 1;
            
            if( per_page == undefined )
                per_page = 10;

            url = `${url}?page=${page}&per_page=${per_page}`;
        }

        let request = {};

        const filtro = this.getFiltro( );
        if( filtro )
        {
            const formData = new FormData( filtro );

            if( this.method == "get" )
            {
                const urlSearchParams = new URLSearchParams(formData);
                const queryString = urlSearchParams.toString();

                if( queryString !== "" )
                {
                    if( this.paginado )
                        url = `${url}&`;
                    else
                        url = `${url}?`;

                    url = `${url}${queryString}`;
                }
            }
            else
            {
                request = {
                    method: this.method,
                    body: formData
                }
            }
        }

        fetch( url, request )
        .then( response => response.json( ) )
        .then( data => {
            if( data['status'] == 200 )
            {
                this.setDados( data['data'] );

                if( this.paginado && data['paginacao'] !== undefined)
                    this.setPaginacao( data['paginacao'] );
            }
        })
        .finally( () => {
            const table = document.getElementById( this.tabela );
            this.montaTbody( table );
        })
        .catch( error => {
            console.log( error );
        });
    }

    render( )
    {
        const table = document.getElementById( this.tabela );
        table.innerText = "";

        this.montaTHead( table );
        table.appendChild( document.createElement( "tbody" ) );
        this.montaTfoot( table );

        if( !this.temFiltro )
            this.build( );
    }

    montaTfoot( table )
    {
        const tfoot = document.createElement( "tfoot" );
        const tr = document.createElement( "tr" );
        const td = document.createElement( "td" );
        td.colSpan = 4;

        const div = document.createElement( "div" );
        div.className = "paginacao";

        if( this.paginado )
        {
            const label_per_page = document.createElement( "label" );
            label_per_page.innerText = "Por página:";
            label_per_page.htmlFor = "per_page";

            const per_page = document.createElement( "input" );
            per_page.id = "per_page";
            per_page.type = "number";
            per_page.min = 1;
            per_page.max = 100;
            per_page.step = 1;
            per_page.value = 10;
            per_page.onchange = () => this.validaPerPage( per_page );

            label_per_page.appendChild( per_page );
            div.appendChild( label_per_page );

            
            const primeiro = document.createElement( "button" );
            primeiro.innerText = "«";
            primeiro.id = "pagina_primeira";
            primeiro.onclick = () => this.alteraPagina( Tabela.TipoBotao.PRIMEIRO );
            div.appendChild( primeiro );

            const anterior = document.createElement( "button" );
            anterior.innerText = "‹";
            anterior.id = "pagina_anterior";
            anterior.onclick = () => this.alteraPagina( Tabela.TipoBotao.ANTERIOR );
            div.appendChild( anterior );

            const label_pagina = document.createElement( "label" );
            label_pagina.innerText = "Página:";
            label_pagina.htmlFor = "pagina";

            const pagina = document.createElement( "input" );
            pagina.id = "pagina";
            pagina.type = "number";
            pagina.min = 1;
            pagina.step = 1;
            pagina.value = 1;
            pagina.onchange = () => this.validaPagina( pagina );

            label_pagina.appendChild( pagina );
            div.appendChild( label_pagina );


            const proxima = document.createElement( "button" );
            proxima.innerText = "›";
            proxima.id = "pagina_proxima";
            proxima.onclick = () => this.alteraPagina( Tabela.TipoBotao.PROXIMO );
            div.appendChild( proxima );

            const ultima = document.createElement( "button" );
            ultima.innerText = "»";
            ultima.id = "pagina_ultima";
            ultima.onclick = () => this.alteraPagina( Tabela.TipoBotao.ULTIMO );
            div.appendChild( ultima );

            const total = document.createElement( "small" );
            total.id = "total_registro";
            total.innerText = "";

            div.appendChild( total );
        }
        
        td.appendChild( div );
        tr.appendChild( td );
        tfoot.appendChild( tr );
        table.appendChild( tfoot );
    }

    alteraPagina( tipo )
    {
        if( !this.paginado )
            return;

        const pagina = document.getElementById( "pagina" );
        let pagina_atual = pagina.value;
        let pagina_old = pagina.value;

        switch( tipo )
        {
            case Tabela.TipoBotao.PRIMEIRO: pagina_atual = 1; break;
            case Tabela.TipoBotao.ANTERIOR: if( pagina_atual > 1 ) pagina_atual--; break;
            case Tabela.TipoBotao.PROXIMO: if( pagina_atual < this.total_paginas ) pagina_atual++; break;
            case Tabela.TipoBotao.ULTIMO: pagina_atual = this.total_paginas; break;
        }
        pagina.value = pagina_atual;

        if( pagina_atual != pagina_old )
            this.build( );
    }

    validaPerPage( element )
    {
        if( element.value < 1 )
            element.value = 1;
        
        this.build( );
    }

    validaPagina( element )
    {
        if( element.value < 1 )
            element.value = 1;
        
        this.build( );
    }

    montaTHead( table )
    {
        const thead = document.createElement( "thead" );

        this.dados_coluna.forEach( (dados) => {
            const th = document.createElement( "th" );
            th.innerText = dados['nome'];

            thead.appendChild( th );
        });

        if( this.temAcao )
        {
            const th = document.createElement( "th" );
            th.innerText = "Ações";

            thead.appendChild( th );
        }

        table.appendChild( thead );
    }

    montaTbody( table )
    {
        let tbody = table.querySelector( "tbody" );

        if( tbody == undefined )
            tbody = document.createElement( "tbody" );
        else
            tbody.innerText = "";

        let quantidade = this.dados_coluna.length;

        if( this.dados.length > 0 )
        {
            this.dados.forEach( (data) => {
                const tr = document.createElement( "tr" );

                for( let i = 0; i < quantidade; i++ )
                {
                    const td = document.createElement( "td" );
                    let texto = data[this.dados_coluna[i]['campo']];

                    if( texto == undefined )
                        texto = this.dados_coluna[i]['alternativo'];

                    if( this.dados_coluna[i].formatacao != undefined )
                        texto = this.dados_coluna[i].formatacao( texto );
                    
                    td.innerText = texto;

                    tr.appendChild( td );
                }

                if( this.temAcao )
                {
                    const td = document.createElement( "td" );
                    
                    this.acoes.forEach( (acao,index) => {
                        const botao = acao(data, index);
                        td.appendChild(botao);
                    });

                    tr.appendChild( td );
                }

                tbody.appendChild( tr );
            });
        }
        else
        {
            const tr = document.createElement( "tr" );
            const td = document.createElement( "td" );
            td.innerText = "Sem registro.";
            td.col = this.dados_coluna.length + ( this.temAcao ? 1 : 0 );

            tr.appendChild( td );
        }
    }
}