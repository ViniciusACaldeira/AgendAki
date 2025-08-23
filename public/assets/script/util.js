function getParametros( )
{
    var params = window.location.search.substring(1).split('&');
    var paramArray = {};
    for(var i=0; i<params.length; i++) 
    {
        var param = params[i].split('=');
        paramArray[param[0]] = param[1];
    }

    return paramArray;
}

function transformarParaInputs() {
  form.querySelectorAll( "span[data-field]" ).forEach( span => {

    if( span.dataset.editable === "false" )
      return;

    const id = span.id;
    const valor = span.textContent;
    const field = span.dataset.field;
    const tipo = span.dataset.type || "text";
    let input;

    if(tipo === "textarea")
      input = document.createElement("textarea");
    else 
    {
        input = document.createElement("input");
        input.type = tipo;

        for( const attr of ["step", "min", "max", "placeholder"] )
            if (span.dataset[attr]) 
                input[attr] = span.dataset[attr];
    }

    input.id = id;
    input.name = field;
    input.value = valor.trim() ;
    input.dataset.field = field;
    span.replaceWith( input );
  });
}

function transformarParaSpans( ) {
    form.querySelectorAll( "input[data-field], textarea[data-field]" ).forEach(input => {
        const id = input.id;
        const valor = input.value;
        const field = input.dataset.field;
        const tipo = input.tagName.toLowerCase( ) === "textarea" ? "textarea" : input.type;

        const span = document.createElement( "span" );
        span.id = id;
        span.dataset.field = field;
        span.dataset.type = tipo;
        span.textContent = valor;

        input.replaceWith( span );
    });
}

function formataData( data )
{
  if( data === null )
    return "00/00/0000";

  const date = new Date( data );  
  return date.toLocaleDateString( "pt-BR" );
}

function redireciona( url )
{
  window.location = url;
}