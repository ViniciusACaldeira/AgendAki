function validarTempo( e )
{
    let input = e.target ? e.target : e;

    let [h, m] = input.value.split(":");
    h = Math.min(parseInt(h || 0), 23).toString().padStart(2, "0");
    m = Math.min(parseInt(m || 0), 59).toString().padStart(2, "0");
    if(input.value.includes(":"))
        input.value = `${h}:${m}`;
}

function completarTempo( e )
{
    let input = e.target ? e.target : e;

    let valor = input.value;
    if (!valor) return;

    let [h, m] = valor.split(":");

    h = h ? h.padStart(2, "0") : "00";
    m = m ? m.padStart(2, "0") : "00";

    input.value = `${h}:${m}`;

    validarTempo( e );
}