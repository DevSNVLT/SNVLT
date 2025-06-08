var valcr = "";
var officeLayer;
var valeur = "";
var depotsData = [];
var osmMap = L.tileLayer.provider('OpenStreetMap.Mapnik');
var stamenMap = L.tileLayer.provider('Stamen.Watercolor');
var imageryMap = L.tileLayer.provider('Esri.WorldImagery');

var topoMap = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
	maxZoom: 22,
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



var geoserverIPPort = 'localhost:8085';
var geoserverWorkspace = 'DEPOTS_VENTE';
var depotsLayerName = 'DEPOTS_VENTE:DEPOTS';
var usinesLayerName = 'DEPOTS_VENTE:USINES';
var equipementsLayerName = 'DEPOTS_VENTE:EQUIPEMENTS';
var quartiersLayerName = 'DEPOTS_VENTE:QUARTIERS';

var depotsLayer = L.tileLayer.wms(
    "http://" + geoserverIPPort + '/geoserver/'+geoserverWorkspace + '/wms',
    {
        layers : depotsLayerName,
        format : 'image/png',
        version : '1.1.0',
        tiled : true,
        transparent : true
    }
)

var usinesLayer = L.tileLayer.wms(
    "http://" + geoserverIPPort + '/geoserver/'+geoserverWorkspace + '/wms',
    {
        layers : usinesLayerName,
        format : 'image/png',
        version : '1.1.0',
        tiled : true,
        transparent : true
    }
)


var equipementsLayer = L.tileLayer.wms(
    "http://" + geoserverIPPort + '/geoserver/'+geoserverWorkspace + '/wms',
    {
        layers : equipementsLayerName,
        format : 'image/png',
        version : '1.1.0',
        tiled : true,
        transparent : true
    }
)

var quartiersLayer = L.tileLayer.wms(
    "http://" + geoserverIPPort + '/geoserver/'+geoserverWorkspace + '/wms',
    {
        layers : quartiersLayerName,
        format : 'image/png',
        version : '1.1.0',
        tiled : true,
        transparent : true
    }
)

var overlayMaps = {
    "QUARTIERS" : quartiersLayer,
	"USINES" : usinesLayer,
    "EQUIPEMENTS " : equipementsLayer
}

var map = L.map("map", {
    center : [5.33, -4.1],
    zoom: 13,
    layers:[osmMap]
});


var ctlMeasure = L.control.polylineMeasure({
    position : "topleft",
    measureControlTitle : "Mesure de distances"
});



var myIcon = L.icon({
    iconUrl:'cartographie/resources/images/depot.png',
    iconSize : [32,32]
});


function showPopup(feature, layer){
    layer.bindPopup(makePopupContent(feature), {closeButton:false});
}

function makePopupContent(office) {
    var num_dossier = office.properties.numero_depot;
    var etat = office.properties.etat_activ ;
	var lien = "http://localhost:84/agdeb/upload/" + office.properties.photo1;
    //alert(num_dossier);
    if (etat == 'Actif') {
        return '<div><h4 style="font-size:16px;"> ID ' + office.properties.code + ' - N° DOSSIER : ' + num_dossier +   '</h4><p style="font-size:14px;"><b>' + office.properties.nom +' ' + office.properties.prenoms + '<br><a href="#">' + office.properties.nationalit + '</a><br><br>Etat Activité : <span style="color:green">' + office.properties.etat_activ + '</span></b></p><img src="'+lien+'" style="float:center;height:250px;border:none;border-size:0px;"><br><div class="phone-number"><span><a href="#">détails...</a></span></div></div>';
    } else {
        return '<div><h4 style="font-size:16px;"> ID ' + office.properties.code + ' - N° DOSSIER : ' + num_dossier +   '</h4><p style="font-size:14px;"><b>' + office.properties.nom +' ' + office.properties.prenoms + '<br><a href="#">' + office.properties.nationalit + '</a><br><br>Etat Activité : <span style="color:red">' + office.properties.etat_activ + '</span></b></p><img src="'+lien+'" style="float:center;height:250px;border:none;border-size:0px;"><br><div class="phone-number"><br><span><a href="#">détails...</a></span></div></div>';
    }
    
}

function style(feature){
    return {
        radius: Math.round(feature.properties.numero_depot / 10),
        fillColor : '#ff7800',
        stroke : false,
        color: "#000",
        weight : 1,
        opacity : 1,
        fillOpacity : 0.8
    };
}
var url = "http://localhost:8085/geoserver/DEPOTS_VENTE/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=DEPOTS_VENTE%3ADEPOTS&outputFormat=application%2Fjson";
$.getJSON(url, function (data) {
    depotsData = data;
    officeLayer = L.geoJSON(data, {
    onEachFeature : showPopup,
    pointToLayer : function(feature, latlng){
        return L.marker(latlng, {icon: myIcon});
    }
});
});



var mapLayers =  L.control.layers(baseMaps, overlayMaps, officeLayer).addTo(map);
//L.control.geocoder().addTo(map);
function populateOffice(){
    const ul = document.querySelector('.list');
    
    $.getJSON(url, function (data) {
        depotsData =data.features;
        depotsData.forEach((office) => {

        const li = document.createElement('li');
        const div = document.createElement('div');
        const a = document.createElement('a');
        const p = document.createElement('p');
            a.addEventListener('click', () => {
                flyToStore(office);
            })
        div.classList.add('office-item');
        a.innerText = "DOSSIER " + office.properties.numero_depot + "\n" +
        " => " + office.properties.prenoms + " " + office.properties.nom + "\n" +
          "      Identifiant : " + office.properties.code ;
          a.href = "#";
        //p.innerText = p.innerText +  "      Identifiant : " + office.properties.code ;


        div.appendChild(a);
        div.appendChild(p);
        li.appendChild(div);
        ul.appendChild(li);
});
});
  
    
}

//populateOffice();

function flyToStore(office){
    const lat = office.geometry.coordinates[1];
    const lng = office.geometry.coordinates[0];

    map.flyTo([lat, lng], 18, {
        duration: 3
    });
    setTimeout(() => {
        L.popup({closeButton: false, offset: L.point(0, -1)})
            .setLatLng([lat, lng])
            .setContent(makePopupContent(office))
            .openOn(map);
    }, 3000);
}
L.control.scale().addTo(map);
//L.control.browserPrint().addTo(map);
//L.Control.geocoder().addTo(map);

var mapId = document.getElementById("map");
function fullScreenView(){
    mapId.requestFullscreen();
}

//L.control.browserPrint({position: 'topleft', title: 'Impression ...'}).addTo(map);

function rechercheValeurs(id){
    
    var value_attribute = "code";
    var value_operator = "=";
    var urlFilter = "http://" + geoserverIPPort + "/geoserver/" + geoserverWorkspace + "/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=" + depotsLayerName + "&CQL_FILTER=code+=+" + id + "&outputFormat=application/json";
    console.log(urlFilter);
    //officeLayer.remove();
    //alert(urlFilter);
            $.getJSON(urlFilter, function (data) {
                depotsData = data;
                    depotsD =data.features;
                    depotsD.forEach((office) => {
                    flyToStore(office);
                });
                officeLayer = L.geoJSON(data, {
                pointToLayer : function(feature, latlng){
                    return L.marker(latlng, {icon: myIcon});
                },
                onEachFeature : showPopup
            }).addTo(map);
            
            });
            
}


   
// Function to create the cookie
function createCookie(name, value, days) {
    var expires;
      
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    }
    else {
        expires = "";
    }
      
    document.cookie = escape(name) + "=" + 
        escape(value) + expires + "; path=/";
}

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }