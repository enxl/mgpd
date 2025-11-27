class Noticias {
    
    busqueda;
    #url;
    #apiToken = "zU1AObywJGoylVOplHw0uOFONijhq9ng8KtMQexL"
    #jsonAPI;

    constructor() {
        this.busqueda = "";
        this.#url = "https://api.thenewsapi.com/v1/news/";
    }

    async buscar() {
        const busquedaAPI = this.busqueda.value.trim();
        if (!busquedaAPI) {
            // Incluir mensaje de error.
            return;
        }

        const url = `${this.#url}all?api_token=${this.#apiToken}&search=${encodeURIComponent(busquedaAPI)}&language=es`;

        try {
            const respuesta = await fetch(url);
            if (!respuesta.ok) {
                throw new Error('No se han obtenido noticias.');
            }
            this.#jsonAPI = await respuesta.json();

        } catch (error) {
            // Incluir mensaje de error.
        }
    }

    procesarInformacion() {
        if (!this.#jsonAPI || !this.#jsonAPI.data || this.#jsonAPI.data.length === 0) {
            console.error("No hay noticias para mostrar.");
            return;
        }

        let noticias = this.#jsonAPI.data;

        for (let noticia of noticias) {
            let article = $("<article>");
            let tituloNoticia = $("<h3>").text(noticia.title);
            let descripcion = $("<p>").text(noticia.description || "Sin descripción disponible.");
            let fuente = $("<p>").text(`Fuente: ${noticia.source || "Desconocida"}`);
            let fecha = $("<p>").text(`Fecha: ${new Date(noticia.published_at).toLocaleString()}`);
            let enlace = $("<a>").attr({ href: noticia.url, target: "_blank" }).text("Leer más");
            article.append(tituloNoticia, descripcion, fuente, fecha, enlace);
            $("main").append(article);
        }
    }
}