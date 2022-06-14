/**
 * file with function which are only used on map.php
 */

/**
 * sets cookies for focus poi on map and redirect to map
 * @param {number} lat latitude
 * @param {number} lng longitude
 */
function focusPoiOfPicture(lat:number, lng:number) :void
{
    setCookie('LatPoi', lat, 5);
    setCookie('LngPoi', lng, 5);
    window.location.href = 'map.php';
}