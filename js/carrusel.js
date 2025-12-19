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

    getFotografias(callback) {

        const url = this.#url;
        const parametros = {
            tags: "motogp,sachsenring",
            tagmode: "any",
            format: "json"
        };

        $.ajax({
            dataType: "jsonp",
            url: url,
            method: "GET",
            data: parametros,
            success: function (data) {
                this.#fotosJSON = data;
                this.procesarJSONFotografias();
                if (callback) {
                    callback(this.#jsonProcesado);
                }
            }.bind(this),
            error: function () {
                console.error("Error al obtener las imágenes de Flickr.");
            }
        });
    }

    procesarJSONFotografias() {
        if (!this.#fotosJSON) {
            console.error("No se ha obtenido el JSON.");
            return;
        }
        var resultado = { imagenes: [] };
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
        if (
            !this.#jsonProcesado ||
            !this.#jsonProcesado.imagenes ||
            this.#jsonProcesado.imagenes.length === 0
        ) {
            console.error("No hay imágenes para mostrar.");
            return;
        }

        const section = $("section:nth-of-type(1)");
        const primeraImagen = this.#jsonProcesado.imagenes[0];

        const h3 = $("<h3>").text("Carrusel de fotos");

        const imagen = $("<img>").attr({
            src: primeraImagen.url,
            alt: primeraImagen.titulo
        });

        const article = $("<article>").append(h3, imagen);
        section.append(article);

        if (this.intervalo) {
            clearInterval(this.intervalo);
        }

        this.intervalo = setInterval(
            this.cambiarFotografia.bind(this),
            3000
        );
    }


    cambiarFotografia() {
        if (!this.#jsonProcesado || this.#jsonProcesado.imagenes.length === 0) {
            return;
        }

        // Al acabar volver a primera foto.
        this.#actual = (this.#actual + 1) % this.#jsonProcesado.imagenes.length;
        var siguiente = this.#jsonProcesado.imagenes[this.#actual];

        $("main article img").attr({
            src: siguiente.url,
            alt: siguiente.titulo
        });
    }
}