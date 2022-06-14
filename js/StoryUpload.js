/**
 * loads stories if page loads
 */
window.onload = function () {
    getAllStories();
};

/**
 * updates sortorder and refreshes page with this information
 * @param {bool} sortDownState if sorted up or down depends on timestamp of story
 */
function updateSortType(sortDownState) {
    sortdown = sortDownState;
    sortAndDisplay();
}

/**
 * is a wrapper around sortAndDisplay, for making effects of checkbox visibile
 */
function FilterStorys() {
    sortAndDisplay();
}

/**
 * saves POI-Story-Linking
 */
function saveLinkedPoi() {
    var intCounter = document.getElementById('LinkPoiStoryToken').value;
    var poi_ID = document.getElementById('LinkPoiStorySelect').value;
    sendApiRequest({type: "aps", poiid: poi_ID, storytoken: stories[intCounter].token}, false).data;
    showPoiLinks(intCounter);
}

/**
 * sets cookies for focus poi on map and redirect to map
 * @param {number} lat latitude
 * @param {number} lng longitude
 */
function focusPoiOfPicture(lat, lng)
{
    setCookie('LatPoi', lat, 5);
    setCookie('LngPoi', lng, 5);
    window.location.href = 'map.php';
}
