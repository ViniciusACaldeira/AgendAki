<?php
    header("Content-Type: application/javascript");
    $config = require_once __DIR__ . '/../../../config/config.php';

    $baseUrl = $config['base_url'];
    $logaErro = $config['loga_erro'] ?? false;
?>
const BASE_URL = "<?= $baseUrl ?>";
const LOGA_ERRO = "<?= $logaErro ?>";

const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('nav-menu');

if( hamburger != null )
    hamburger.addEventListener('click', () => {
        navMenu.classList.toggle('show');
    });

document.querySelectorAll('#nav-menu li.has-submenu > a').forEach(link => {
    let tapped = false;

    link.addEventListener('click', function(e){
        const li = this.parentElement;

        if(window.innerWidth <= 768)
        {
            if(!tapped)
            {
                e.preventDefault();
                li.classList.toggle('active');

                tapped = true;

                setTimeout(() => tapped = false, 500);
            }
        }
    });
});

document.body.addEventListener("focus", function (e) {
    if (e.target.matches('input[data-type="time"]')) {
        aplicarMascaraTempo(e.target);
    }
}, true);

function aplicarMascaraTempo( e )
{   
    if(e._mascaraAtiva) 
        return;
    
    e._mascaraAtiva = true;
    e.addEventListener( "input", mascararTempo );
    e.addEventListener( "blur", completarTempo );
}

function geraInit( method = "GET", body = null )
{
    const config = {
        method
    }

    if( body )
        config.body = body;

    return config;
}

function getAPI( url, callback, msgErro )
{
    return requestAPI( url, geraInit( ), callback, msgErro );
}

function postAPI( url, body, callback, msgErro )
{
    return requestAPI( url, geraInit( "post", body ), callback, msgErro );
}

function requestAPI( url, init, callback, msgErro )
{
    fetch( BASE_URL + url, init )
    .then( response => response.json( ) )
    .then( response => {
        const data = response['data'];
        const status = response['status'];
        const erros = data['erros'];

        if( erros !== undefined )
            erros.forEach( e => mostrarToast( e, TOAST_ERRO ) );
        else if( status == 200 )
            callback( data );
        else
            mostrarToast( msgErro, TOAST_ERRO );
    })
    .catch( (erro) => { if( LOGA_ERRO ) console.error( erro ) } );
}