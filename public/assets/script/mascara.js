function mascaraTelefone(e)
{
    let value = e.target.value.replace(/\D/g, '');
    
    if( value.length > 11 ) 
        value = value.slice( 0, 11 );

    let formatted = value;

    if( value.length > 2 ) 
    {
        const ddd = value.slice(0,2);
        const numero = value.slice(2);

        if(numero.length > 5) 
            formatted = `(${ddd}) ${numero.slice(0,5)}-${numero.slice(5)}`;
        else if( numero.length > 4 )
            formatted = `(${ddd}) ${numero.slice(0,4)}-${numero.slice(4)}`;
        else
            formatted = `(${ddd}) ${numero}`;
    } 
    else if( value.length > 0 )  
        formatted = `(${value}`;

    e.target.value = formatted;
}

function desmascararTelefone( telefone )
{
    return telefone.replace(/\D/g, '' );
}