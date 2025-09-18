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

  let [ano, mes, dia] = data.split("-");

  const date = new Date( ano, mes-1, dia );  
  return date.toLocaleDateString( "pt-BR", { timeZone: "America/Sao_Paulo" });
}

function formatarTelefone( telefone )
{
  if( telefone == null )
    return "(00) 00000-0000";

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

function formatarTempo( tempo )
{
  const [hora, minuto] = tempo.split(":"); 
  return `${hora}:${minuto}`;
}

function redireciona( url )
{
  window.location = url;
}

function formatarPreco( texto )
{
  return new Intl.NumberFormat( "pt-BR", { style: "currency", currency: "BRL" } ).format( texto ) ; 
}

function dateToString( date )
{
  const ano = date.getFullYear( );
  const mes = String( date.getMonth( ) + 1 ).padStart( 2, "0" );
  const dia = String( date.getDate( ) ).padStart( 2, "0" );

  return `${ano}-${mes}-${dia}`;
}

function stringToDate( data )
{
  const [ano, mes, dia] = data.split("-");
  const dataLocal = new Date(ano, mes - 1, dia);

  return dataLocal;
}

function toMinutes( horaStr ) 
{
  const [h, m] = horaStr.split(":").map(Number);
  return h * 60 + m;
}

function filtrarHorarios( horarios, min, max ) 
{
  const minMin = toMinutes(min);
  const maxMin = toMinutes(max);

  return horarios.filter(hora => {
    const totalMin = toMinutes(hora);
    return totalMin >= minMin && totalMin <= maxMin;
  });
}