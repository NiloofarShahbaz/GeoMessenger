// initialize map
let map = L.map('mapId');
L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 40,
    id: 'mapbox/streets-v11',
    accessToken: 'pk.eyJ1Ijoibmlsb29mYXI3NiIsImEiOiJjazV5N3V2enQwMHJuM29sazM3NTBkMmZoIn0.yiledL6f1PVk1i8ZS6CIbQ'
}).addTo(map);
mapControl = L.control.locate({
    icon: 'fas fa-map-marker-alt show-loc-icon',
    locateOptions: {
        maxZoom: 17
    }
}).addTo(map);
mapControl.start();

let markers = new L.LayerGroup([]).addTo(map);

let myPosIcon = L.icon({
    iconUrl: '/static/images/leaflet-markers/marker-icon-yellow.png',
    shadowUrl: '/static/images/leaflet-markers/marker-shadow.png',

    iconSize:[25,41],
    iconAnchor:[12,41],
    popupAnchor:[1,-34],
    tooltipAnchor:[16,-28],
    shadowSize:[41,41]
});

let myFriendsPosIcon = L.icon({
    iconUrl: '/static/images/leaflet-markers/marker-icon-red.png',
    shadowUrl: '/static/images/leaflet-markers/marker-shadow.png',

    iconSize:[25,41],
    iconAnchor:[12,41],
    popupAnchor:[1,-34],
    tooltipAnchor:[16,-28],
    shadowSize:[41,41]
});





// initialize dropdown map
let DropdownMap = new L.map('dropdownMapId');
L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 40,
    id: 'mapbox/streets-v11',
    accessToken: 'pk.eyJ1Ijoibmlsb29mYXI3NiIsImEiOiJjazV5N3V2enQwMHJuM29sazM3NTBkMmZoIn0.yiledL6f1PVk1i8ZS6CIbQ'
}).addTo(DropdownMap);


// dropdown map parameters
function showOnMap() {
    navigator.geolocation.getCurrentPosition(showPosition);
}

function showPosition(position) {
    let latitude = position.coords.latitude;
    let longitude = position.coords.longitude;

    document.getElementById('lat').value = latitude;
    document.getElementById('lng').value = longitude;

    DropdownMap.setView([latitude, longitude], 17);

    let myLocationMarkerInForm = new L.marker([latitude, longitude], {draggable: true})
        .addTo(DropdownMap)
        .on('dragend', function () {
            document.getElementById('lat').value = myLocationMarkerInForm.getLatLng().lat;
            document.getElementById('lng').value = myLocationMarkerInForm.getLatLng().lng;
        });
}

