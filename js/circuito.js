/*
    Clase para cargar información, altimetría y planimetría del circuito.
    Autor: Enol Monte Soto
    Versión: 1
*/
class Circuito {

    constructor() {
        this.#comprobarAPIFile;
        this.#asociarEventoInput();
    }

    #comprobarAPIFile() {
        if (!(window.File && window.FileReader && window.FileList && window.Blob)) {
            const parrafo = document.createElement("p");
            parrafo.textContent = "Este navegador no soporta API File;";
            document.body.appendChild(parrafo);
        }
    }

    #asociarEventoInput() {
        const input = document.querySelector("main input:nth-of-type(1)");
        input.addEventListener("change", function () {
            const archivo = input.files[0];
            this.leerArchivoHTML(archivo);
        }.bind(this));
    }

    leerArchivoHTML(archivo) {
        var tipoTexto = /text.*/;
        if (archivo && archivo.type.match(tipoTexto)) {
            var lector = new FileReader();
            lector.onload = function () {

                var resultado = lector.result;
                var parser = new DOMParser();
                var docExt = parser.parseFromString(resultado, "text/html");
                var main = document.getElementsByTagName("main")[0];
                var section = document.createElement("section");
                var contentMain = docExt.getElementsByTagName("main")[0];

                while (contentMain.firstChild) {
                    section.appendChild(contentMain.firstChild);
                }

                //main.appendChild(section);
                var input = document.querySelector("main input:nth-of-type(1)");
                input.after(section);
            };
            lector.readAsText(archivo);
        } else {
            console.error("Archivo inválido.");
        }
    }

}

class CargadorSVG {

    constructor() {
        this.#asociarEventoInput();
    }

    #asociarEventoInput() {
        const input = document.querySelector("main input:nth-of-type(2)");
        input.addEventListener("change", function () {
            const archivo = input.files[0];
            this.leerArchivoSVG(archivo);
        }.bind(this));
    }

    leerArchivoSVG(archivo) {
        if (archivo && archivo.type === "image/svg+xml") {
            const lector = new FileReader();
            lector.onload = function () {
                this.#insertarArchivoSVG(lector.result);
            }.bind(this);
            lector.readAsText(archivo);
        } else {
            console.error("Archivo inválido.");
        }
    }

    #insertarArchivoSVG(svg) {
        const parser = new DOMParser();
        var documentoSVG = parser.parseFromString(svg, 'image/svg+xml');
        var main = document.getElementsByTagName("main")[0];
        var input = document.querySelector("main input:nth-of-type(2)");
        var elementoSVG = documentoSVG.documentElement;
        input.after(elementoSVG);
    }

}

class CargadorKML {

    #coordenadas = [];
    #mapa = null;

    constructor() {
        this.#asociarEventoInput();
    }

    #asociarEventoInput() {
        const input = document.querySelector("main input:nth-of-type(3)");
        input.addEventListener("change", function () {
            const archivo = input.files[0];
            this.leerArchivoKML(archivo);
        }.bind(this));
    }

    leerArchivoKML(archivo) {

        if (archivo) {

            const lector = new FileReader();

            lector.onload = function () {
                const parser = new DOMParser();
                let doc = parser.parseFromString(lector.result, "text/xml");

                let coordenadasNode = doc.getElementsByTagName("coordinates")[0];
                if (!coordenadasNode) {
                    console.error("No se encontraron coordenadas en el KML.");
                    return;
                }

                let lineas = coordenadasNode.textContent.trim().split(/\s+/);

                lineas.forEach(function (linea) {
                    linea = linea.trim();
                    if (!linea)
                        return;
                    let [longitud, latitud, altitud] = linea.split(",").map(Number);
                    this.#coordenadas.push({ longitud, latitud, altitud });
                }.bind(this));

                console.log(this.#coordenadas);
                this.#insertarCapaKML();
            }.bind(this);

            lector.readAsText(archivo);
        } else {
            console.error("Archivo KML inválido.");
        }
    }

    #insertarCapaKML() {
        const token = "pk.eyJ1IjoiZW5vbG1vbnRlc290byIsImEiOiJjbWk1dXEyZmMyOWt0MmxzY28wNGEybG52In0.qriOMlbm6ItUFfCva6IArg";
        mapboxgl.accessToken = token;

        if (this.#coordenadas.length === 0) {
            console.error("No hay coordenadas para mostrar el mapa.");
            return;
        }
        let centro = [12.688689977147785, 50.79229545253801];

        const contenedor = document.querySelector('main > div');
        this.#mapa = new mapboxgl.Map({
            container: contenedor,
            style: 'mapbox://styles/mapbox/streets-v11',
            center: centro,
            zoom: 14
        });

        const geojson = {
            'type': 'Feature',
            'properties': {},
            'geometry': {
                'type': 'LineString',
                'coordinates': this.#coordenadas.map(c => [c.longitud, c.latitud])
            }
        };

        this.#mapa.on('load', () => {
            this.#mapa.addSource('circuito', {
                'type': 'geojson',
                'data': geojson
            });
            this.#mapa.addLayer({
                'id': 'circuito-linea',
                'type': 'line',
                'source': 'circuito',
                'layout': {
                    'line-join': 'round',
                    'line-cap': 'round'
                },
                'paint': {
                    'line-color': '#ff0000',
                    'line-width': 4
                }
            });
        });
    }

}