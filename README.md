## Descripción de la prueba técnica
Desarrollar una aplicación utilizando el esqueleto de Symfony 5.3 de este repositorio, que obtenga ofertas aéreas de una API mock y las muestre en pantalla.
Por la naturaleza de la prueba y por no demorar la finalización de la misma, no es necesario realizar la validación de los datos obtenidos.
### Evaluación
Se evaluarán los siguientes aspectos técnicos:
- Conocimientos en GIT.
- Conocimientos en Symfony y el patrón MVC.
- Conocimientos en el consumo de APIs.
- Conocimientos del sector aéreo, comprensión de ofertas y asociación de datos.
### Depedencias
- PHP 8.0
- Librería cURL.
- Binario de Symfony `(#1)`.
- Cuenta en GitHub.
### Descripción técnica
Partiendo del proyecto esqueleto `gac-air-technical-interview-test` y teniendo en cuenta el patrón MVC _(exceptuando el modelado de datos, y por ende la gestión de base de datos)_.
1. Realizar un fork del repositorio público https://github.com/josecdv/gac-air-technical-interview-test.git.
2. Consumir la siguiente mock API con cURL `(#2)` o la librería HTTP Client de Symfony `(#3)` _(no incluida en el esqueleto)_. La API devolverá ofertas aéreas basadas en el esquema 17.2 de IATA NDC.
- Endpoint: https://airmockupapi.gac.travel
- Método: GET
- Autenticación. Authorization Basic `(#4)`
- Nombre usuario: demo
- Contraseña: demo
3. Interpretar la respuesta obtenida con `DOMDocument` o `SimpleXML` y cargar los datos sobre una estructura intermedia _(array u objeto)_.
4. Renderizar el array de ofertas en la vista `availability.html.twig` utilizando TWIG y la estructura intermedia generada en el punto anterior. La vista dispone del render de una oferta con datos estáticos sobre la que trabajar.

![Ejemplo de resultado.](./public/result.png "Ejemplo de resultado.")

5. Realizar push de los cambios y enviar el enlace del fork.
### Información de utilidad
A continuación se listan una serie de puntos que pueden ser de ayuda para agilizar la finalización de la prueba técnica.
#### Symfony Local Web Server
Symfony proporciona un servidor web para el entorno de desarrollo. 
Si tienes varias versiones de PHP instaladas en tu equipo, puedes decirle a Symfony cuál usar. Para ello, creaa un archivo llamado `.php-version` en el directorio raíz del proyecto con la versión a utilizar:
```
echo 8.0.6 > .php-version
```
Ejecute el siguiente comando si no recuerda todas las versiones de PHP instaladas en tu equipo:
```
symfony local:php:list

```
Una vez configurada la versión de PHP a utilizar, puede ejecutar el servidor web:
```
symfony server:start
```
#### Maker Bundle
Se permite el uso del paquete `maker` de Symfony, ya requerido como librería.
```
symfony console list make
```
#### Asociación datos API mockup (esquema NDC 17.2)
La API mockup devolverá una única oferta, con dos trayectos _(ida y vuelta)_. Cada trayecto esta compuesto por dos segmentos _(una escala)_. La oferta disponible es para cuatro pasajeros, 2 ADT _(ADULT_01 y ADULT_02)_, 1 CHD _(CHILD_01)_ y 1 INF _(INFANT_01)_. Las tarifas se agrupan por PTC, esto quiere decir que una tarifa puede hacer referencia a más de un pasajero.

Todos los identificadores a otros elementos, como los pasajeros, segmentos o detalles de tarifa, se identifican a nivel de etiqueta con el sufijo `Ref`, esto informa de que hay más información desglosada en otro elemento de la respuesta. Las referencias pueden darse para múltiples elementos, dado el caso los encontrarás separados con un espacio en blanco. Por ejemplo, los detalles de tarifa "TCLESEU" aplican a los segmentos "IB503920211210ALCBCN" y "IB559320211210BCNLGW". Los datos desglosados de las referencias los encontrarás como hijos de `DataLists`.
```
<FareComponent>
    <!-- Referencia a //DataLists/PriceClassList/PriceClass/@PriceClassID -->
    <PriceClassRef>TCLESEU</PriceClassRef>
    <!-- Referencia a //DataLists/FlightSegmentList/FlightSegment/@SegmentKey -->
    <SegmentRefs>IB503920211210ALCBCN IB559320211210BCNLGW</SegmentRefs>
</FareComponent>
```
Para facilitar la interpretación de la respuesta, a continuación se listan algunos XPATH para los elementos que componen una oferta aérea.
- Ofertas: `//Offer`
- PTC según identificador de PAX: `//DataLists/PassengerListPassenger`
- Trayectos: `//Offer/FlightsOverview/FlightRef` enlaza con `//DataLists/Flight/@FlightKey`
- Segmentos por trayecto: `//DataLists/Flight/SegmentReferences` enlaza con `//DataLists/FlightSegmentList/FlightSegment/@SegmentKey`
- Tarifas: `//Offer/OfferItem`
- Tarifa aplicada a un grupo de PTC: `//Offer/OfferItem/FareDetail/PassengerRefs`
- Detalles de tarifa _(equipaje, cabina, políticas de cancelación, etc.)_ por tarifa y segmento: `//Offer/OfferItem/FareDetail/FareComponent` enlaza con `//DataLists/PriceClassList/PriceClass/@PriceClassID`

La estructura más básica que describe una oferta aérea es:
- Oferta
    - Pasajero(s)
        - Tarifa
        - Detalles de tarifa _(asociación entre tarifa y segmento)_. Un detalle por cada segmento del itinerario.
    - Trayectos
        - Segmento(s)
### Notas
1. https://symfony.com/download
2. https://www.php.net/manual/es/book.curl.php
3. https://symfony.com/doc/current/http_client.html
4. https://developer.mozilla.org/es/docs/Web/HTTP/Headers/Authorization