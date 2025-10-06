<script>
document.querySelector("#gerar_lote_automatico form").addEventListener("submit", async function(e) {
    e.preventDefault(); // impede reload

    const form = e.target;
    const dados = new FormData(form);

    try {
        const resp = await fetch(form.action, {
            method: "POST",
            body: dados
        });
        const resultado = await resp.json();

        const container = document.getElementById("relatorio-unico-container");

        if (resultado.status === "ok") {
            container.innerHTML = `
                <div class="relatorio">
                    <h3>Relatório do Lote Gerado</h3>
                    <p><strong>ID do Lote:</strong> ${resultado.lote.id_lote}</p>
                    <p><strong>Data:</strong> ${resultado.lote.data}</p>
                    <p><strong>Tanque:</strong> ${resultado.lote.localizacao}</p>
                </div>
            `;
        } else {
            container.innerHTML = `<p style="color:red;">Erro: ${resultado.mensagem}</p>`;
        }
    } catch (err) {
        alert("Erro ao gerar lote: " + err.message);
    }
});
</script>
