/**
 * opens Edit material data modal
 * @param {string} token Token of material which should be edited
 */
function openEditMaterial(token) {
    var data = LoadSingleMaterialData(token);
    document.getElementById('MaterialTitelField').value = data.title;
    document.getElementById('MaterialTBcommentField').value = data.description;
    document.getElementById('ImageEditMaterialModal').src = data.picture;
    document.getElementById('MaterialTokenField').value = data.token;
    if (data.source !== "" && data.source !== null) {
        document.getElementById('MaterialEditSourceTypeField').value = data.sourcetypeid;
        document.getElementById('MaterialEditSourceField').value = data.source;
    } else {
        document.getElementById('MaterialEditSourceTypeField').selectedIndex = 0;
        document.getElementById('MaterialEditSourceField').value = "";
    }
    $('#EditMaterial').modal();
}

/**
 * gathered changed Information from Formular and transmits it to somewehere else
 */
function saveEditedMaterial() {
    var token = document.getElementById('MaterialTokenField').value;
    var discription = document.getElementById('MaterialTBcommentField').value;
    var title = document.getElementById('MaterialTitelField').value;
    var source = document.getElementById('MaterialEditSourceField').value;
    var srcType = document.getElementById('MaterialEditSourceTypeField').value;
    if (source === "" || source === null ) {
        sendEditedMaterialData(title, discription, token);
    } else {
        sendEditedMaterialDataSource(title, discription, token, source, srcType);
    }
    $('#EditMaterial').modal('hide');
}

/**
 * delete material
 * @param {string} token Token of material which should be deleted
 */
function deleteMaterial(token) {
    sendApiRequest({type: 'dsp', token: token});
    location.href = 'ListMaterial.php?';
}