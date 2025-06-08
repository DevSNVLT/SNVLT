var officeLayer;
var pefData = [];
var osmMap = L.tileLayer.provider('OpenStreetMap.Mapnik');
var stamenMap = L.tileLayer.provider('Stamen.Watercolor');
var imageryMap = L.tileLayer.provider('Esri.WorldImagery');

var topoMap = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
	maxZoom: 17,
	attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
});

var Thunderforest_Pioneer = L.tileLayer('https://{s}.tile.thunderforest.com/pioneer/{z}/{x}/{y}.png?apikey={apikey}', {
	attribution: '&copy; <a href="http://www.thunderforest.com/">Thunderforest</a>, &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
	apikey: '<your apikey>',
	maxZoom: 22
});

var baseMaps = {
    'OSM': osmMap,
    'stamen Wtaercolor' : stamenMap,
    'World Imagery' : imageryMap,
    'Topographie' : topoMap,
    'Thunderforest_Pioneer' : Thunderforest_Pioneer

}



var geoserverIPPort = 'localhost:8080';
var geoserverWorkspace = 'SNVLT';
var pefLayerName = 'SNVLT:pef';
var foretLayerName = 'SNVLT:foret';
var aireLayerName = 'SNVLT:aire';
var regionLayerName = 'SNVLT:region';

var regionLayer = L.tileLayer.wms(
    "http://" + geoserverIPPort + '/geoserver/'+geoserverWorkspace + '/wms',
    {
        layers : regionLayerName,
        format : 'image/png',
        version : '1.1.0',
        tiled : true,
        transparent : true,
        isVisible: true
    }
)

var pefLayer = L.tileLayer.wms(
    "http://" + geoserverIPPort + '/geoserver/'+geoserverWorkspace + '/wms',
    {
        layers : pefLayerName,
        format : 'image/png',
        version : '1.1.0',
        tiled : true,
        transparent : true,
        isVisible: true
    }
)
var foretLayer = L.tileLayer.wms(
    "http://" + geoserverIPPort + '/geoserver/'+geoserverWorkspace + '/wms',
    {
        layers : foretLayerName,
        format : 'image/png',
        version : '1.1.0',
        tiled : true,
        transparent : true,
        isVisible: true
    }
)
var aireLayer = L.tileLayer.wms(
    "http://" + geoserverIPPort + '/geoserver/'+geoserverWorkspace + '/wms',
    {
        layers : aireLayerName,
        format : 'image/png',
        version : '1.1.0',
        tiled : true,
        transparent : true,
        isVisible: true
    }
)



var overlayMaps = {
    "REGIONS" : regionLayer,
    "PEF" : pefLayer,
    "FORETS CLASSEES" : foretLayer,
    "AIRES PROTEGEES" : aireLayer,
}

var map = L.map("map", {
    center : [6.2, -5.5],
    zoom: 7,
    layers:[osmMap, aireLayer, regionLayer, foretLayer, pefLayer]
});
let polygonStyle = {
    color: '#FFF',
    fillColor: '#000',
    weight: 0.5,
    fillOpacity: 0.5
}
/*L.polygon(
    [
        [6.35, -4.2],
        [6.40, -4.1],
        [6.12, -4.35]
    ]
).addTo(map).bindPopup("LOT 114");*/
/*var zoButton = document.createElement('button');
zoButton.className = 'myButton';
zoButton.id = 'zoButton';
zoButton.title = 'Dézoomer';
document.appendChild(zoButton);*/

var ctlMeasure = L.control.polylineMeasure({
    position : "topleft",
    measureControlTitle : "Mesure de distances"
}).addTo(map);



var myIcon = L.icon({
    iconUrl:'cartographie/resources/images/depot.png',
    iconSize : [32,32]
});


function showPopup(feature, layer){
    layer.bindPopup(makePopupContent(feature), {closeButton:false});
}

function makePopupContent(office) {
    var num_pef = office.properties.numero_pef;
    var aire = office.properties.aire ;
	var lien = "http://localhost:84/agdeb/upload/" + office.properties.photo1;
    //alert(num_dossier);

        return '<div><h4 style="font-size:16px;">  ' + ' - N° TITRE : ' + num_pef +   '</h4><p style="font-size:14px;"><b>' + office.properties.nom +' ' + office.properties.prenoms + '<br><a href="#">' + office.properties.nationalit + '</a><br><br>Etat Activité : <span style="color:red">' + office.properties.etat_activ + '</span></b></p><img src="'+lien+'" style="float:center;height:250px;border:none;border-size:0px;"><br><div class="phone-number"><br><span><a href="#">détails...</a></span></div></div>';

    
}

function style(){
    return {
        fillColor : '#ff7800',
        stroke : false,
        color: "#000",
        weight : 1,
        opacity : 1,
        fillOpacity : 0.8
    };
}
var url = "http://localhost:8080/geoserver/SNVLT/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=SNVLT%3Apef&outputFormat=application%2Fjson";
$.getJSON(url, function (data) {
    pefData = data;
    officeLayer = L.geoJSON(data, {
    onEachFeature : showPopup,
    pointToLayer : function(feature, latlng){
        return L.marker(latlng, {style: style()});
    }
})
});

/*var marker1 = L.marker([5.285, -4.254]).addTo(map);
var polygon1 = L.polygon([
    [6.245, -3.253],
    [6.278, -4.245],
    [5.289, -4.145]
]).addTo(map).bindPopup('Ceci est un polygon'+ L.latlng);*/

var mapLayers =  L.control.layers(baseMaps, overlayMaps, officeLayer).addTo(map);
//L.control.geocoder().addTo(map);
function populateOffice(){
    const ul = document.querySelector('.list');
    
    $.getJSON(url, function (data) {
        pefData =data.features;
        pefData.forEach((office) => {

        const li = document.createElement('li');
        const div = document.createElement('div');
        const a = document.createElement('a');
        const p = document.createElement('p');
            a.addEventListener('click', () => {
                recherche(office.properties.numero_pef);
            })
        div.classList.add('office-item');
        a.innerText = office.properties.numero_pef + "\n" +
        " => Zone " + office.properties.zone_ + "\n"  ;
          a.href = "#";
        //p.innerText = p.innerText +  "      Identifiant : " + office.properties.code ;


        div.appendChild(a);
        div.appendChild(p);
        li.appendChild(div);
        ul.appendChild(li);
});
});
  
    
}

populateOffice();

function flyToStore(office){

    map.flyToBounds(office, 10)

}
L.control.scale().addTo(map);


var mapId = document.getElementById("map");
function fullScreenView(){
    mapId.requestFullscreen();
}

L.control.browserPrint({position: 'topleft', title: 'Impression ...'}).addTo(map);
function rechercheValeurs(){

    var valeurRaw = document.getElementById('txt-critere').value;
    var valeur_titre = "%25" + valeurRaw + "%25";

    var requete = "";
    var value_attribute = "numero_pef";
    var value_operator = "=";
    var urlFilter = "http://" + geoserverIPPort + "/geoserver/" + geoserverWorkspace + "/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=" + pefLayerName + "&CQL_FILTER=" + value_attribute + "+" + value_operator + "+'" + valeurRaw + "'&outputFormat=application/json";
    //alert(urlFilter);
    officeLayer.remove();
   
            $.getJSON(urlFilter, function (data) {
                pefData = data;
                officeLayer = L.geoJSON(data, {
                pointToLayer : function(feature, latlng){

                    return L.marker(latlng, {style: style(feature)});
                },
                onEachFeature : showPopup
            }).addTo(map);
                map.flyToBounds(officeLayer, 10)
            });
    
}
function recherche(valeur){


   /* var valeur_titre = "%25" + valeurRaw + "%25";*/

    var requete = "";
    var value_attribute = "numero_pef";
    var value_operator = "=";
    var urlFilter = "http://" + geoserverIPPort + "/geoserver/" + geoserverWorkspace + "/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=" + pefLayerName + "&CQL_FILTER=" + value_attribute + "+" + value_operator + "+'" + valeur + "'&outputFormat=application/json";
    //alert(urlFilter);
    officeLayer.remove();

    $.getJSON(urlFilter, function (data) {
        pefData = data;
        officeLayer = L.geoJSON(data, {
            pointToLayer : function(feature, latlng){

                return L.marker(latlng, {style: style(feature)});
            },
            onEachFeature : showPopup
        }).addTo(map);
        map.flyToBounds(officeLayer, 10)
    });

}

