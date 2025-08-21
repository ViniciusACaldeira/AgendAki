document.addEventListener("DOMContentLoaded", ( ) => 
{
  const dropdowns = document.querySelectorAll( ".dropdown" );

  dropdowns.forEach( dropdown => {
        const toggle = dropdown.querySelector( ".dropdown-toggle" );
        const menu = dropdown.querySelector( ".dropdown-menu" );

        toggle.addEventListener( "click", (e) => {
            e.stopPropagation( );
            dropdowns.forEach( d => d.classList.remove( "open" ) );
            dropdown.classList.toggle( "open" );
        });

        menu.addEventListener( "change", ( ) => {
            const checked = menu.querySelectorAll("input:checked");
            if( checked.length === 0 )
                toggle.textContent = "Selecionar serviços ▾";
            else 
            {
                const labels = Array.from( checked ).map( cb => cb.parentNode.textContent.trim( ) );
                toggle.textContent = labels.join( ", " ) + " ▾";
            }
        });
    });

    document.addEventListener( "click", ( ) => {
        dropdowns.forEach(d => d.classList.remove("open"));
    });
});