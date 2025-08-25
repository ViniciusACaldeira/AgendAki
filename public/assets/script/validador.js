function validarTempo( e )
{
    let [h, m] = e.target.value.split(":");
    h = Math.min(parseInt(h || 0), 23).toString().padStart(2, "0");
    m = Math.min(parseInt(m || 0), 59).toString().padStart(2, "0");
    if(e.target.value.includes(":"))
        e.target.value = `${h}:${m}`;
}

function completarTempo( e )
{
    let valor = e.target.value;
    if (!valor) return;

    let [h, m] = valor.split(":");

    h = h ? h.padStart(2, "0") : "00";
    m = m ? m.padStart(2, "0") : "00";

    e.target.value = `${h}:${m}`;

    validarTempo( e );
}