poiLatLng = [document.getElementById('lat').value, document.getElementById('lng').value];
Karte3 = L.map('mapframepoi').setView(poiLatLng, 7);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19, minZoom: 7,
    'attribution': 'Kartendaten &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> Mitwirkende',
    'useCache': true

}).addTo(Karte3);
resizeMap(Karte3, poiLatLng);

Karte3.on('click', function(e) {
    placeMarker(e.latlng, true);
});

var mark3 = L.marker(poiLatLng, {draggable: 'true', icon: blackIcon}).addTo(Karte3);
mark3.on('dragend', function(e) {
    onMove(e, true);
});


