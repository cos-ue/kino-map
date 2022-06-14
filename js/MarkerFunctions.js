/**
 * deletes a placed marker on the selected map(s)
 */
function delMark() {
    if (typeof GCMarker != 'undefined' && GCMarker){
        GCMarker.remove();
    }
    if (typeof mark2 != 'undefined' && mark2){
        mark2.remove();
    }
    anz = 0;
    toggleAddPOIButton(false);
}

/**
 * updates coordinates in form if marker is moved
 * @param {GCMarker} e
 * @param {boolean} editMap true if POI is being edited
 * @param {boolean} Minimap true if marker is on Minimap
 */
function onMove(e, editMap, Minimap) {
    if (editMap) {
        insertCoordinates(true);
    } else {
        var latLng = e.target._latlng;
        GCMarker.setLatLng(latLng);
        if (typeof mark2 != 'undefined' && mark2){
            mark2.setLatLng(latLng);
        }
        if (Minimap) {
            onMoveMM(latLng)
        }
    }
}

/**
 * updates fields if marker is moved on minimap
 * @param {array} latLng latitude and longitude og POI
 */
function onMoveMM(latLng) {
    document.getElementById('lat').value = Math.round(latLng['lat'] * 10000000000) / 10000000000;
    document.getElementById('lng').value = Math.round(latLng['lng'] * 10000000000) / 10000000000;
}

/**
 * places marker in map
 * @param {array} coordinates coordinates where marker is going to be placed
 * @param {bool} editMap if true: marker is replaced in editmap.php
 *                       if false: marker is (re)placed in map.php on the big map and on the minimap
 */
function placeMarker(coordinates, editMap) {
    editMap = editMap || false;
    if (editMap) {
        mark3.setLatLng(coordinates);
        insertCoordinates(true);
    } else {
        if (anz === 1) {
            GCMarker.setLatLng(coordinates);
            if (typeof mark2 != 'undefined' && mark2){
                mark2.setLatLng(coordinates);
            }
        } else {
            GCMarker = L.marker(coordinates, {draggable: 'true', icon: blackIcon}).addTo(Karte);
            if (minimap) {
                mark2 = L.marker(coordinates, {draggable: 'true', icon: blackIcon}).addTo(Karte2);
            }
            anz = 1;
            if (minimap) {
                toggleAddPOIButton(true);
            }
            GCMarker.on('click', delMark);
            GCMarker.on('dragend', onMove);
            if (minimap) {
                mark2.on('dragend', function(e) {
                    onMove(e, false, true);
                });
            }
            anz = 1;
        }
    }
}

/**
 * sets coordinates into input-fields if called
 */
function insertCoordinates(editMap) {
    var latlng = [0, 0];
    if (editMap) {
        latlng[0] = Math.round(mark3.getLatLng()['lat'] * 10000000000) / 10000000000;
        latlng[1] = Math.round(mark3.getLatLng()['lng'] * 10000000000) / 10000000000;
    } else if (anz === 0) {
        document.getElementById('lat').value = "";
        document.getElementById('lng').value = "";
    } else {
        latlng[0] = Math.round(GCMarker.getLatLng()['lat'] * 10000000000) / 10000000000;
        latlng[1] = Math.round(GCMarker.getLatLng()['lng'] * 10000000000) / 10000000000;
        resizeMap(Karte2, latlng);
    }
    document.getElementById('lat').value = latlng[0];
    document.getElementById('lng').value = latlng[1];
}

/**
 * resistes Minimap after Modal has opened
 */
function resizeMap(map, coordinates) {
    var delayInMilliseconds = 300;
    setTimeout(function () {
        map.invalidateSize();
        map.setView(coordinates, 16);
    }, delayInMilliseconds);
}