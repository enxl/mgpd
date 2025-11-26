/*
    Clase para el cronómetro.
    Autor: Enol Monte Soto
    Versión: 1
*/
class Cronometro {

    #tiempo;
    #inicio;
    #corriendo;

    constructor() {
        this.#tiempo = 0;
    }

    asociarEventosBotones() {
        const botonArrancar = document.querySelector("main button:nth-of-type(1)");
        const botonParar = document.querySelector("main button:nth-of-type(2)");
        const botonReiniciar = document.querySelector("main button:nth-of-type(3)");

        botonArrancar.addEventListener("click", function () {
            this.arrancar();
        }.bind(this));

        botonParar.addEventListener("click", function () {
            this.parar();
        }.bind(this));

        botonReiniciar.addEventListener("click", function () {
            this.reiniciar();
        }.bind(this));
    }

    arrancar() {
        this.reiniciar();
        try {
            if (typeof Temporal === 'undefined') {
                throw new Error("Objeto Temporal no disponible");
            }
            this.#inicio = Temporal.Now.instant();
        } catch (error) {
            this.#inicio = new Date();
        }
        this.#corriendo = setInterval(this.#actualizar.bind(this), 100);
    }

    #actualizar() {
        let actual;
        try {
            if (typeof Temporal === 'undefined') {
                throw new Error("Objeto Temporal no disponible");
            }
            actual = Temporal.Now.instant();
            this.#tiempo = actual.epochMilliseconds - this.#inicio.epochMilliseconds;
        } catch (error) {
            actual = new Date();
            this.#tiempo = actual.getTime() - this.#inicio.getTime();
        }
        this.#mostrar();
    }

    parar() {
        clearInterval(this.#corriendo);
    }

    reiniciar() {
        clearInterval(this.#corriendo);
        this.#tiempo = 0;
        this.#mostrar();
    }

    #mostrar() {
        const parrafo = document.querySelector('main p');
        const msTotales = this.#tiempo;
        const minutos = parseInt(msTotales / 60000);
        const segundos = parseInt((msTotales % 60000) / 1000);
        const decimas = parseInt((msTotales % 1000) / 100);
        const minDosDigitos = String(minutos).padStart(2, '0');
        const secDosDigitos = String(segundos).padStart(2, '0');
        parrafo.textContent = minDosDigitos + ":" + secDosDigitos + "." + decimas;
    }
}
