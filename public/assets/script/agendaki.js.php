<?php
    header("Content-Type: application/javascript");
    $config = require_once __DIR__ . '/../../../config/config.php';

    $baseUrl = $config['base_url'];
?>
const BASE_URL = "<?= $baseUrl ?>";

const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('nav-menu');

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
