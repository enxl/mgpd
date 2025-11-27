/*
    Clase que representa a una ciudad.
    Autor: Enol Monte Soto
    Versión: 2
*/
class Ciudad {

    #nombre;
    #pais;
    #gentilicio;
    #poblacion;
    #longitud;
    #latitud;

    #jsonAPI;
    #jsonProcesado;
    #jsonAPIEntrenos;
    #jsonProcesadoEntrenos;

    constructor(nombre, pais, gentilicio) {
        this.#nombre = nombre;
        this.#pais = pais;
        this.#gentilicio = gentilicio;
    }

    rellenarDatos(poblacion, longitud, latitud) {
        this.#poblacion = poblacion;
        this.#longitud = longitud;
        this.#latitud = latitud;
    }

    getNombreCiudad() {
        return this.#nombre;
    }

    getPais() {
        return this.#pais;
    }

    getInfoSecundaria() {
        const main = document.querySelector("main");
        const lista = document.createElement("ul");
        const liGentilicio = document.createElement("li");
        liGentilicio.textContent = "Gentilicio: " + this.#gentilicio;
        const liPoblacion = document.createElement("li");
        liPoblacion.textContent = "Población: " + this.#poblacion + " habitantes";
        lista.appendChild(liGentilicio);
        lista.appendChild(liPoblacion);
        main.appendChild(lista);
    }

    getCoordenadas() {
        const main = document.querySelector("main");
        const parrafo = document.createElement("p");
        parrafo.textContent = "Coordenadas: (" + this.#latitud + ", " + this.#longitud + ")";
        main.appendChild(parrafo);
    }

    // Meteorología Open Meteo.
    // Obtener JSON de la API.
    getMeteorologiaCarrera() {

        const url = "https://archive-api.open-meteo.com/v1/archive";
        const lat = 50.789;
        const lon = 12.688;
        const fecha = "2024-07-07";

        const parametros = {
            latitude: lat,
            longitude: lon,
            start_date: fecha,
            end_date: fecha,
            hourly: "temperature_2m,apparent_temperature,rain,relative_humidity_2m,wind_speed_10m,wind_direction_10m",
            daily: "sunrise,sunset",
            timezone: "auto"
        };

        $.ajax({
            dataType: "json",
            url: url,
            method: "GET",
            data: parametros,
            success: function (datos) {
                this.#jsonAPI = datos;
                this.procesarJSONCarrera();
                this.verMeteoCarrera();
                this.getMeteorologiaEntrenos();
            }.bind(this),
            error: function () {
                console.error("Se ha producido un error obteniendo meteorología.");
            }
        });
    }

    procesarJSONCarrera() {
        if (!this.#jsonAPI) {
            console.error("No hay datos meteorológicos para procesar.");
            return;
        }
        let datos = this.#jsonAPI;

        var indiceHoraCarrera = datos.hourly.time.findIndex(function (hora) {
            return hora.endsWith("T14:00");
        });

        let res = {
            hora: datos.hourly.time[indiceHoraCarrera],
            temperatura: datos.hourly.temperature_2m[indiceHoraCarrera],
            sensacion_termica: datos.hourly.apparent_temperature[indiceHoraCarrera],
            lluvia: datos.hourly.rain[indiceHoraCarrera],
            humedad: datos.hourly.relative_humidity_2m[indiceHoraCarrera],
            viento_velocidad: datos.hourly.wind_speed_10m[indiceHoraCarrera],
            viento_direccion: datos.hourly.wind_direction_10m[indiceHoraCarrera],
            salida_sol: datos.daily.sunrise[0],
            puesta_sol: datos.daily.sunset[0]
        }

        this.#jsonProcesado = res;
    }

    verMeteoCarrera() {
        const datos = this.#jsonProcesado;

        if (!datos) {
            console.error("No hay datos meteorológicos para procesar.");
            return;
        }

        let meteoDiaCarrera = $("<section>");
        meteoDiaCarrera.append($("<h3>").text("Meteorología del día de la carrera"));
        meteoDiaCarrera.append($("<p>").text("Fecha de la carrera: 7 de julio de 2024"));
        meteoDiaCarrera.append($("<p>").text("Salida del sol: " + this.#formatearHora(datos.salida_sol)));
        meteoDiaCarrera.append($("<p>").text("Puesta del sol: " + this.#formatearHora(datos.puesta_sol)));
        meteoDiaCarrera.append($("<h4>").text("Meteorología a la hora de la carrera"));
        meteoDiaCarrera.append($("<p>").text("Hora de la carrera: " + this.#formatearHora(datos.hora)));
        meteoDiaCarrera.append($("<p>").text("Temperatura: " + datos.temperatura + " ºC"));
        meteoDiaCarrera.append($("<p>").text("Sensación térmica: " + datos.sensacion_termica + " ºC"));
        meteoDiaCarrera.append($("<p>").text("Lluvia: " + datos.lluvia + " mm"));
        meteoDiaCarrera.append($("<p>").text("Humedad: " + datos.humedad + " %"));
        meteoDiaCarrera.append($("<p>").text("Viento: " + datos.viento_velocidad + " km/h (" + datos.viento_direccion + "º)"));

        $("main").append(meteoDiaCarrera);
    }

    getMeteorologiaEntrenos() {
        const url = "https://archive-api.open-meteo.com/v1/archive";
        const lat = 50.789;
        const lon = 12.688;
        const fechaInicio = "2024-07-04";
        const fechaFin = "2024-07-06";

        $.ajax({
            url: url,
            method: "GET",
            data: {
                latitude: lat,
                longitude: lon,
                start_date: fechaInicio,
                end_date: fechaFin,
                hourly: "temperature_2m,rain,relative_humidity_2m,wind_speed_10m",
                timezone: "Europe/Madrid"
            },
            success: function (datos) {
                this.#jsonAPIEntrenos = datos;
                this.procesarJSONEntrenos();
                this.verMeteoEntrenos();
            }.bind(this),
            error: function () {
                console.error("Error al obtener la meteorología de entrenamientos.");
            }
        });
    }

    procesarJSONEntrenos() {
        if (!this.#jsonAPIEntrenos) {
            console.error("No hay datos meteorológicos de entrenamientos para procesar.");
            return;
        }

        const datos = this.#jsonAPIEntrenos;
        const dias = {};

        for (let i = 0; i < datos.hourly.time.length; i++) {
            const fechaHora = datos.hourly.time[i].split("T")[0];
            if (!dias[fechaHora]) {
                dias[fechaHora] = { temperatura: [], lluvia: [], humedad: [], viento: [] };
            }
            dias[fechaHora].temperatura.push(datos.hourly.temperature_2m[i]);
            dias[fechaHora].lluvia.push(datos.hourly.rain[i]);
            dias[fechaHora].humedad.push(datos.hourly.relative_humidity_2m[i]);
            dias[fechaHora].viento.push(datos.hourly.wind_speed_10m[i]);
        }

        const resultado = {};
        for (const dia in dias) {
            let sumaTemperatura = 0;
            let sumaLluvia = 0;
            let sumaHumedad = 0;
            let sumaViento = 0;

            for (let i = 0; i < dias[dia].temperatura.length; i++) {
                sumaTemperatura += dias[dia].temperatura[i];
                sumaLluvia += dias[dia].lluvia[i];
                sumaHumedad += dias[dia].humedad[i];
                sumaViento += dias[dia].viento[i];
            }

            resultado[dia] = {
                temperatura: (sumaTemperatura / dias[dia].temperatura.length).toFixed(2),
                lluvia: (sumaLluvia / dias[dia].lluvia.length).toFixed(2),
                humedad: (sumaHumedad / dias[dia].humedad.length).toFixed(2),
                viento: (sumaViento / dias[dia].viento.length).toFixed(2)
            };
        }

        this.#jsonProcesadoEntrenos = resultado;
    }

    verMeteoEntrenos() {
        const datos = this.#jsonProcesadoEntrenos;
        if (!datos) return;

        let meteoEntrenosSection = $("<section>");
        meteoEntrenosSection.append($("<h3>").text("Meteorología de los días de entrenamientos (promedio)"));

        for (const dia in datos) {
            meteoEntrenosSection.append($("<h4>").text("Día: " + dia));
            meteoEntrenosSection.append($("<p>").text("Temperatura: " + datos[dia].temperatura + " °C"));
            meteoEntrenosSection.append($("<p>").text("Lluvia: " + datos[dia].lluvia + " mm"));
            meteoEntrenosSection.append($("<p>").text("Humedad: " + datos[dia].humedad + " %"));
            meteoEntrenosSection.append($("<p>").text("Viento: " + datos[dia].viento + " km/h"));
        }
        $("main").append(meteoEntrenosSection);
    }

    #formatearHora(fecha) {
        return fecha.split("T")[1].slice(0,5)
    }
    
}
