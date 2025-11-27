/*
    Clase para el carrusel de fotos del circuito.
    Autor: Enol Monte Soto
    Versión: 1
*/
class Carrusel {

    #fotosJSON;
    #jsonProcesado;
    #maximo;
    #url;
    #actual;

    constructor() {
        this.#fotosJSON = null;
        this.#jsonProcesado = null;
        this.#maximo = 5;
        this.#url = "https://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?";
        this.#actual = 0;
    }

    // Obtener fotos desde la API y guardarlas en #fotosJSON
    getFotografias(callback) {
        $.ajax({
            url: this.#url,
            dataType: "jsonp",
            data: {
                tags: "sachsenring,motogp",
                tagmode: "any",
                format: "json"
            },
            success: (data) => {
                this.#fotosJSON = data;
                this.procesarJSONFotografias();
                if (callback) callback(this.#jsonProcesado);
            },
            error: () => {
                console.error("Error al obtener las imágenes de Flickr.");
            }
        });
    }

    procesarJSONFotografias() {
        if (!this.#fotosJSON) {
            console.error("No se ha obtenido el JSON.");
            return;
        }
        let resultado = { imagenes: [] };
        for (let i = 0; i < this.#fotosJSON.items.length && i < this.#maximo; i++) {
            let item = this.#fotosJSON.items[i];
            resultado.imagenes.push({
                url: item.media.m.replace("_m.", "_z."),
                titulo: item.title
            });
        }

        this.#jsonProcesado = resultado;
    }

    mostrarFotografias() {
        if (!this.#jsonProcesado || this.#jsonProcesado.imagenes.length === 0) {
            console.error("No hay imágenes para mostrar.");
            return;
        }

        let primeraImagen = this.#jsonProcesado.imagenes[0];

        let titulo = $("<h2>").text("Imágenes del circuito de Sachsenring");
        let imagen = $("<img>").attr({
            src: primeraImagen.url,
            alt: primeraImagen.titulo
        });

        let elementos = titulo.add(imagen);
        let article = $("<article>").append(elementos);
        $("main").append(article);

        // Pasar galería fotos.
        setInterval(this.cambiarFotografia.bind(this), 3000);
    }

    cambiarFotografia() {
        if (!this.#jsonProcesado || this.#jsonProcesado.imagenes.length === 0) {
            return;
        }

        // Al acabar volver a primera foto.
        this.#actual = (this.#actual + 1) % this.#jsonProcesado.imagenes.length;
        let siguiente = this.#jsonProcesado.imagenes[this.#actual];

        $("main article img").attr({
            src: siguiente.url,
            alt: siguiente.titulo
        });
    }
}