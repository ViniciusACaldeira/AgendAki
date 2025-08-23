let modal_element_instance = null;

function modal_criar( ) 
{
    const modal_element = document.createElement("div");
    modal_element.id = "modal_container";
    Object.assign(modal_element.style, {
        display: "none",
        position: "fixed",
        top: "0", left: "0", right: "0", bottom: "0",
        background: "rgba(0,0,0,0.5)",
        justifyContent: "center",
        alignItems: "center",
        zIndex: "9999",
        opacity: "0",
        transition: "opacity 0.3s"
    });

    const modal_content = document.createElement("div");
    modal_content.id = "modal_content";
    Object.assign(modal_content.style, {
        background: "#fff",
        borderRadius: "12px",
        minWidth: "300px",
        maxWidth: "500px",
        boxShadow: "0 2px 12px rgba(0,0,0,0.3)",
        display: "flex",
        flexDirection: "column",
        overflow: "hidden",
        transform: "translateY(-30px)",
        transition: "transform 0.3s"
    });

    const modal_header = document.createElement("div");
    modal_header.id = "modal_header";
    Object.assign(modal_header.style, {
        padding: "15px",
        borderBottom: "1px solid #ddd",
        fontWeight: "bold",
        fontSize: "18px"
    });

    const modal_body = document.createElement("div");
    modal_body.id = "modal_body";
    Object.assign(modal_body.style, {
        padding: "15px",
        flex: "1",
        display: "none"
    });

    const modal_footer = document.createElement("div");
    modal_footer.id = "modal_footer";
    Object.assign(modal_footer.style, {
        padding: "10px 15px",
        borderTop: "1px solid #ddd",
        display: "flex",
        justifyContent: "flex-end",
        gap: "10px",
        flexWrap: "wrap",
        display: "none"
    });

    modal_content.appendChild(modal_header);
    modal_content.appendChild(modal_body);
    modal_content.appendChild(modal_footer);
    modal_element.appendChild(modal_content);
    document.body.appendChild(modal_element);

    modal_element.addEventListener('click', (e) => {
        if (e.target === modal_element) modal_fechar();
    });

    return { modal_element, modal_header, modal_body, modal_footer, modal_content };
}

function modal_abrir({ titulo = '', body = '', botoes = [] }) 
{
    if(!modal_element_instance) modal_element_instance = modal_criar();

    modal_element_instance.modal_header.innerHTML = titulo;
    
    if(body)
    {
        modal_element_instance.modal_body.innerHTML = body;
        modal_element_instance.modal_body.style.display = "block";
    } 
    else
        modal_element_instance.modal_body.style.display = "none";

    if (botoes.length > 0) 
    {
        modal_element_instance.modal_footer.innerHTML = "";
        modal_element_instance.modal_footer.style.display = "flex";
        botoes.forEach(btn => {
            const button = document.createElement("button");
            button.textContent = btn.texto;
            Object.assign(button.style, {
                padding: "6px 12px",
                border: "none",
                borderRadius: "6px",
                cursor: "pointer",
                backgroundColor: btn.cor || "#3498db",
                color: "white",
                transition: "transform 0.1s"
            });
            button.addEventListener("mouseover", () => button.style.transform = "scale(1.05)");
            button.addEventListener("mouseout", () => button.style.transform = "scale(1)");
            button.addEventListener("click", (e) => {
                e.stopPropagation();
                if (btn.acao) btn.acao(modal_element_instance.modal_element);
            });
            modal_element_instance.modal_footer.appendChild(button);
        });
    }
    else
        modal_element_instance.modal_footer.style.display = "none";
    

    modal_element_instance.modal_element.style.display = "flex";
    setTimeout( ( ) => {
            modal_element_instance.modal_element.style.opacity = "1";
            modal_element_instance.modal_content.style.transform = "translateY(0)";
        }, 10 );
}

function modal_fechar( ) 
{
    if( !modal_element_instance ) 
        return;

    modal_element_instance.modal_element.style.opacity = "0";
    modal_element_instance.modal_content.style.transform = "translateY(-30px)";
    setTimeout( ( ) => {
            modal_element_instance.modal_element.style.display = "none";
        }, 300 );
}