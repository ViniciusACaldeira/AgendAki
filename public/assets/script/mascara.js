function mascaraTelefone(e)
{
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = mascaraTextoTelefone( value );
}

function mascaraTextoTelefone( telefone )
{
    if( telefone.length > 11 ) 
        telefone = telefone.slice( 0, 11 );

    let formatado = telefone;

    if( telefone.length > 2 ) 
    {
        const ddd = telefone.slice(0,2);
        const numero = telefone.slice(2);

        if(numero.length > 5) 
            formatado = `(${ddd}) ${numero.slice(0,5)}-${numero.slice(5)}`;
        else if( numero.length > 4 )
            formatado = `(${ddd}) ${numero.slice(0,4)}-${numero.slice(4)}`;
        else
            formatado = `(${ddd}) ${numero}`;
    } 
    else if( telefone.length > 0 )  
        formatado = `(${telefone}`;

    return formatado;
}

function desmascararTelefone( telefone )
{
    return telefone.replace(/\D/g, '' );
}
