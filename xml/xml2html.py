import xml.etree.ElementTree as ET

class Html(object):

    def __init__(self):
        # Generación de la declaración html, cabecera y etiqueta body.
        self.html= ET.Element('html', attrib={'lang': 'es'})
        self.head = ET.SubElement(self.html, 'head')
        ET.SubElement(self.head, 'meta', charset='UTF-8')
        ET.SubElement(self.head, 'meta', name='viewport', content='width=device-width, initial-scale=1.0')
        ET.SubElement(self.head, 'title').text = 'Circuito de Sachsenring'
        ET.SubElement(self.head, 'link', rel='stylesheet', href='../estilo/estilo.css')
        self.body = ET.SubElement(self.html, 'body')
        self.main = ET.SubElement(self.body, 'main')

    # Añade al main un título h1-h6 según se indique.
    def addTitle(self, content, level):
        title = ET.SubElement(self.main, f'h{level}')
        title.text = content

    # Añade un parrafo <p> al bloque main.
    def addParagraph(self, content):
        p = ET.SubElement(self.main, 'p')
        p.text = content;

    # Añade una lista con elementos dados.
    def addList(self, items):
        ul = ET.SubElement(self.main, 'ul')
        for item in items:
            ET.SubElement(ul, 'li').text = item

    # Añade un enlace, indicando la URL.
    def addLink(self, href, titleAttr):
        p = ET.SubElement(self.main, 'p')
        a = ET.SubElement(p, 'a', href=href.strip(), target="_blank", title=titleAttr)
        a.text = titleAttr.strip()

    # Añade una imagen. Se pasa la ruta y el texto alternativo.
    def addImage(self, src, alt_text):
        ruta = f"{src.strip()}"
        ET.SubElement(self.main, 'img', src=ruta, alt=alt_text)

    # Añade un video
    def addVideo(self, src):
        ruta = f"{src.strip()}"
        ET.SubElement(self.main, 'video', src=ruta, controls='controls')


    def escribir(self, nombreArchivoHTML):
        arbol = ET.ElementTree(self.html)
        ET.indent(arbol)
        with open(nombreArchivoHTML, "wb") as f:
            f.write(b"<!DOCTYPE html>\n")
            arbol.write(f, encoding='utf-8', method='html')

def parseDuration(duration):
    duration = duration.replace('PT', '')
    hours, minutes, seconds = 0, 0, 0
    if 'H' in duration:
        hours, duration = duration.split('H')
        hours = int(hours)
    if 'M' in duration:
        minutes, duration = duration.split('M')
        minutes = int(minutes)
    if 'S' in duration:
        seconds = float(duration.replace('S',''))
    return f"{hours}h, {minutes}m, {seconds}s"


# Sección que transforma el código de circuitoEsquema.xml en documento HTML.
def toHtml(archivoXML):
    try:
        tree = ET.parse(archivoXML)
    except IOError:
        print('No se encuentra el archivo', archivoXML)
        exit()
    except ET.ParseError:
        print("Error procesando el archivo XML:", archivoXML)
        exit()

    root = tree.getroot()
    ns = '{http://www.uniovi.es}'
    html = Html()

    nombre = root.find(f'.//{ns}nombre')
    longitud = root.find(f'.//{ns}longitud-circuito')
    anchura = root.find(f'.//{ns}anchura-media')
    fecha = root.find(f'.//{ns}fecha')
    hora = root.find(f'.//{ns}hora')
    vueltas = root.find(f'.//{ns}vueltas')
    localidad = root.find(f'.//{ns}localidad')
    pais = root.find(f'.//{ns}pais')
    patrocinador = root.find(f'.//{ns}patrocinador')

    referencias = root.findall(f'.//{ns}referencias/{ns}referencia')
    fotos = root.findall(f'.//{ns}fotografias/{ns}fotografia')
    video = root.find(f'.//{ns}videos/{ns}video')
    clasificados = root.findall(f'.//{ns}clasificados/{ns}clasificado')

    html.addTitle(nombre.text, 2)

    html.addTitle("Datos generales", 3)
    html.addParagraph(
        f"Longitud del circuito: {longitud.text} {longitud.get('unidades')}"
    )
    html.addParagraph(
        f"Anchura media: {anchura.text} {anchura.get('unidades')}"
    )
    html.addParagraph(f"Fecha: {fecha.text}")
    html.addParagraph(f"Hora española: {hora.text}")
    html.addParagraph(f"Número de vueltas: {vueltas.text}")
    html.addParagraph(f"Localidad: {localidad.text}")
    html.addParagraph(f"País: {pais.text}")
    html.addParagraph(f"Patrocinador principal: {patrocinador.text}")

    html.addTitle("Galería de imágenes", 3)
    for foto in fotos:
        html.addImage(foto.text, foto.get('alt'))

    html.addTitle("Videos del circuito", 3)
    if video is not None:
        html.addVideo(video.text)

    html.addTitle("Tres primeros clasificados tras la carrera (2025)", 3)
    lista_clasificados = []
    for c in clasificados:
        posicion = c.get('posicion')
        piloto = c.text
        lista_clasificados.append(f"{posicion}º {piloto}")
    html.addList(lista_clasificados)

    vencedor = root.find(f'.//{ns}vencedor')
    if vencedor is not None:
        html.addTitle("Vencedor", 3)
        html.addParagraph(
            f"{vencedor.text} (tiempo: {parseDuration(vencedor.get('tiempo'))})"
        )

    html.addTitle("Referencias", 3)
    for ref in referencias:
        html.addLink(ref.text.strip(), ref.get('title'))

    html.escribir("InfoCircuito.html")
    print("Operación exitosa!")


toHtml(input("Introduzca nombre fichero XML: "))