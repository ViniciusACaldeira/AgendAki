const TOAST_SUCESSO = "sucesso";
const TOAST_ERRO = "erro";
const TOAST_AVISO = "aviso";
const TOAST_INFORMACAO = "informacao";

const TOAST_ESTILOS = [ TOAST_SUCESSO, TOAST_ERRO, TOAST_AVISO, TOAST_INFORMACAO ];

function mostrarToast( mensagem, tipo = TOAST_INFORMACAO ) 
{
    if( !TOAST_ESTILOS.includes(tipo) )
        tipo = TOAST_INFORMACAO;

    const container = document.getElementById("toast-container");

    const toast = document.createElement( "div" );
    toast.classList.add( "toast", tipo );
    toast.textContent = mensagem;

    container.appendChild( toast );

    setTimeout( ( ) => {
        toast.style.animation = "fadeOut 0.5s forwards";
        toast.addEventListener( "animationend", ( ) => toast.remove( ) );
    }, 3000 );
}