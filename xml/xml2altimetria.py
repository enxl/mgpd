# 02030-SVG.py
# # -*- coding: utf-8 -*-
""""
Crea archivos SVG con rectángulos, círculos, líneas, polilíneas y texto

@version 1.0 18/Octubre/2024
@author: Juan Manuel Cueva Lovelle. Universidad de Oviedo
"""

import xml.etree.ElementTree as ET

class Svg(object):
    """
    Genera archivos SVG con rectángulos, círculos, líneas, polilíneas y texto
    @version 1.0 18/Octubre/2024
    @author: Juan Manuel Cueva Lovelle. Universidad de Oviedo
    """
    def __init__(self):
        """
        Crea el elemento raíz, el espacio de nombres y la versión
        """
        self.raiz = ET.Element('svg',
                       xmlns="http://www.w3.org/2000/svg",
                       version="2.0",
                       width="500",
                       height="400")


    def addRect(self,x,y,width,height,fill, strokeWidth,stroke):
        """
        Añade un elemento rect
        """
        ET.SubElement(self.raiz,'rect',
                      x=x,
                      y=y,
                      width=width,
                      height=height,
                      fill=fill,
                      attrib={'stroke-width': str(strokeWidth)},
                      stroke=stroke)

    def addCircle(self,cx,cy,r,fill):
        """
        Añade un elemento circle
        """
        ET.SubElement(self.raiz,'circle',
                      cx=cx,
                      cy=cy,
                      r=r,
                      fill=fill)

    def addLine(self,x1,y1,x2,y2,stroke,strokeWith):
        """
        Añade un elemento line
        """
        ET.SubElement(self.raiz,'line',
                      x1=x1,
                      y1=y1,
                      x2=x2,
                      y2=y2,
                      stroke=stroke,
                      attrib={'stroke-width': str(strokeWith)})

    def addPolyline(self,points,stroke,strokeWith,fill):
        """
        Añade un elemento polyline
        """
        ET.SubElement(self.raiz,'polyline',
                      points=points,
                      stroke=stroke,
                      attrib={'stroke-width': str(strokeWith)},
                      fill=fill)

    def addText(self,texto,x,y,fontFamily,fontSize,style):
        """
        Añade un elemento texto
        """
        ET.SubElement(self.raiz,'text',
                      x=x,
                      y=y,
                      fontFamily=fontFamily,
                      fontSize=fontSize,
                      style=style).text=texto

    def escribir(self,nombreArchivoSVG):
        """ de
        Escribe el archivo SVG con declaración y codificación
        """
        arbol = ET.ElementTree(self.raiz)

        """
        Introduce indentacióon y saltos de línea
        para generar XML en modo texto
        """
        ET.indent(arbol)

        arbol.write(nombreArchivoSVG,
                    encoding='utf-8',
                    xml_declaration=True
                    )

    def ver(self):
        """
        Muestra el archivo SVG. Se utiliza para depurar
        """
        print("\nElemento raiz = ", self.raiz.tag)

        if self.raiz.text != None:
            print("Contenido = "    , self.raiz.text.strip('\n')) #strip() elimina los '\n' del string
        else:
            print("Contenido = "    , self.raiz.text)

        print("Atributos = "    , self.raiz.attrib)

        # Recorrido de los elementos del árbol
        for hijo in self.raiz.findall('.//'): # Expresión XPath
            print("\nElemento = " , hijo.tag)
            if hijo.text != None:
                print("Contenido = ", hijo.text.strip('\n')) #strip() elimina los '\n' del string
            else:
                print("Contenido = ", hijo.text)
            print("Atributos = ", hijo.attrib)

# Sección de código para contruir el SVG de la altimetría

def toSVG(archivoXML):

    try:
        tree = ET.parse(archivoXML)
    except IOError:
        print('No se encuentra el archivo ', archivoXML)
        exit()
    except ET.ParseError:
        print("Error procesando en el archivo XML = ", archivoXML)
        exit()

    root = tree.getroot()
    ns = '{http://www.uniovi.es}'

    puntos = root.findall(f'.//{ns}punto')

    puntos_svg = []
    distancia_recorrida = 0
    escala = 0.1
    puntos_string = ""

    for punto in puntos:
        distancia_punto = float(
            punto.find(f'{ns}distancia').text
        )

        altitud_punto = float(
            punto.find(f'{ns}coordenadas/{ns}altitud').text
        )

        distancia_recorrida += distancia_punto
        puntos_svg.append((distancia_recorrida * escala, altitud_punto))

    for x, y in puntos_svg:
        puntos_string += f"{int(x)},{int(y)} "

    altura_base = 400
    x_inicio = int(puntos_svg[0][0])
    x_final = int(puntos_svg[-1][0])

    puntos_string += (
        f"{x_final},{altura_base} "
        f"{x_inicio},{altura_base} "
        f"{x_inicio},{int(puntos_svg[0][1])}"
    )

    altimetria_svg = Svg()
    altimetria_svg.addPolyline(
        puntos_string.strip(),
        stroke="red",
        strokeWith="3",
        fill="#EAFF00"
    )

    altimetria_svg.escribir("altimetria.svg")
    print("Operación Exitosa!")


toSVG(input("Introduzca nombre fichero XML: "))




