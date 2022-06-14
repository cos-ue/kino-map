/**
 * file with function which are only used on map.php
 */

/**
 * checks input data when creating new poi
 */
function checkInputDataAddPOI() {
    var address = CheckAddress();
    var startEnd = checkYearInputAddPoi();
    var result = address && startEnd;
    var form = document.getElementById('formCinema') as HTMLFormElement;
    if (result) {
        form.submit();
    }
}

/**
 * checks length of given years
 */
function checkYearInputAddPoi(): boolean {
    var startInput = document.getElementById('start') as HTMLInputElement;
    var endInput = document.getElementById('end') as HTMLInputElement;
    var start = startInput.value as String;
    var end = endInput.value as String;
    var outEnd = end.length == 0 || end.length == 4;
    var outStart = start.length == 0 || start.length == 4;
    var result = outEnd && outStart;
    if (result == false) {
        alert('Bitte verwenden Sie 4-Stellige Jahreszahlen.');
    }
    return result;
}

/**
 * toggles view of historical adress add fields
 */
function toggleHistoricalAdressAdd() {
    var image = document.getElementById('showMoreHistAdressDropOutBtn') as HTMLImageElement;
    if (document.getElementById('toggleHideShowMoreAddress').style.display === 'none') {
        document.getElementById('toggleHideShowMoreAddressSaveBtn').style.display = 'block';
        document.getElementById('toggleHideShowMoreAddress').style.display = 'block';
        image.src = 'images/caret-square-up-regular-white.svg';
    } else {
        document.getElementById('toggleHideShowMoreAddress').style.display = 'none';
        document.getElementById('toggleHideShowMoreAddressSaveBtn').style.display = 'none';
        image.src = 'images/caret-square-down-regular-white.svg';
    }
}
