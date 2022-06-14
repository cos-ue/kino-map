/**
 * prepares Pic Select modal and shows it
 */
function preparePicSelectModal(): void {
    setPictureSelect_SingleSelect();
    showSinglePicSelect();
    document.getElementById('MainPictureSelectCloseButton').setAttribute('onclick', 'abortSelectMorePicturesEditPoi();');
    document.getElementById('MainPictureSelectAbortButton').setAttribute('onclick', 'abortSelectMorePicturesEditPoi();');
    document.getElementById('MainPictureSelectSaveButton').setAttribute('onclick', 'saveSelectMorePicturesEditPoi(' + poiidedit + ');');
    $('#ChangeMainPicEditModal').modal('hide')
}

/**
 * aborts picture select
 */
function abortSelectMorePicturesEditPoi(): void {
    $('#PictureSelectModal').modal('hide');
}

/**
 * saves new main picture for poi
 * @param {int} poi_id identifier of poi
 */
function saveSelectMorePicturesEditPoi(poi_id: number): void {
    var token : string;
    var tokenInput = document.getElementById('MainPictureSelectSelected') as HTMLInputElement;
    token = tokenInput.value;
    var json = {
        type: 'emp',
        poiid: poi_id,
        token: token
    };
    sendApiRequest(json, true);
    $('#PictureSelectModal').modal('hide');
}