/**
 * adds a new announcement to the tool
 */
function addAnnouncement() {
    var title = document.getElementById('titleAnnouncementAddInput').value;
    var content = document.getElementById('contentAnnouncementAddInput').value;
    var start = document.getElementById('startAnnouncementAddInput').value;
    var end = document.getElementById('EndAnnouncementAddInput').value;
    var missing = false;
    if (title === "" || title === null) {
        missing = true;
        setFaultAnnouncement('titleAnnouncementAddInput');
    }
    if (content === "" || content === null) {
        missing = true;
        setFaultAnnouncement('contentAnnouncementAddInput');
    }
    if (start === "" || start === null) {
        missing = true;
        setFaultAnnouncement('startAnnouncementAddInput');
    }
    if (end === "" || end === null) {
        missing = true;
        setFaultAnnouncement('EndAnnouncementAddInput');
    }
    var startDate = new Date(start);
    var endDate = new Date(end);
    if (endDate.getTime() < startDate.getTime()) {
        setFaultAnnouncement('startAnnouncementAddInput');
        setFaultAnnouncement('EndAnnouncementAddInput');
        missing = true;
    }
    if (missing === true) {
        return;
    } else {
        resetFaultAnnouncement('titleAnnouncementAddInput');
        resetFaultAnnouncement('contentAnnouncementAddInput');
        resetFaultAnnouncement('startAnnouncementAddInput');
        resetFaultAnnouncement('EndAnnouncementAddInput');
    }
    var jsonReq = {type: "aan", title: title, content: content, start: start, end: end};
    sendApiRequest(jsonReq, true);
    document.getElementById('titleAnnouncementAddInput').value = "";
    document.getElementById('contentAnnouncementAddInput').value = "";
    document.getElementById('startAnnouncementAddInput').value = "";
    document.getElementById('EndAnnouncementAddInput').value = "";
    $("#AddAnnouncementModal").modal('hide');
}

/**
 * marks foulty input
 * @param {string} fieldName Identifier of field
 */
function setFaultAnnouncement(fieldName) {
    document.getElementById(fieldName).setAttribute("class", "form-control textinput-danger");
    document.getElementById(fieldName).setAttribute("data-toggle", "tooltip");
    document.getElementById(fieldName).setAttribute("title", "Pflichtfeld");
}

/**
 * unmarks inputs as faulty
 * @param {string} fieldName Identifier of field
 */
function resetFaultAnnouncement(fieldName) {
    document.getElementById(fieldName).setAttribute("class", "form-control border textinput-formular");
    document.getElementById(fieldName).removeAttribute("data-toggle");
    document.getElementById(fieldName).removeAttribute("title");
}

/**
 * opens the edit announcement modal and fills it with data
 * @param id
 */
function openEditAnnouncement(id) {
    var jsonReq = {type: "gan", id: id};
    var req = sendApiRequest(jsonReq, false).data;
    document.getElementById('titleAnnouncementEditInput').value = req.title;
    document.getElementById('IdAnnouncementEditInput').value = req.id;
    document.getElementById('contentAnnouncementEditInput').value = req.content;
    document.getElementById('startAnnouncementEditInput').value = req.start;
    document.getElementById('EndAnnouncementEditInput').value = req.end;
    $("#EditAnnouncementModal").modal();
}

/**
 * saves an edited announcement
 */
function saveEditAnnouncement() {
    var title = document.getElementById('titleAnnouncementEditInput').value;
    var id = document.getElementById('IdAnnouncementEditInput').value;
    var content = document.getElementById('contentAnnouncementEditInput').value;
    var start = document.getElementById('startAnnouncementEditInput').value;
    var end = document.getElementById('EndAnnouncementEditInput').value;
    var jsonReq = {type: "uan", id: id, end: end, start: start, content: content, title: title};
    sendApiRequest(jsonReq, true);
}

/**
 * deletes a certain announcement
 * @param {int} id identifier of announcement
 */
function deleteAnnouncement(id, target) {
    dynamicModal.confirm('Wollen sie die Ankündigung wirklich löschen?', (r) => {
        if (r){
            var jsonReq = {type: "dan", id: id};
            asyncApiRequest(jsonReq, () => {
                $(target).closest('tr').animate({height:0,opacity:0}, 400, 'swing', () => {
                    $(target).closest('tr').remove();
                });
            })
        }
    }, {title:"Ankündigung Löschen", titleClass:'bg-danger'});
}

/**
 * activates preview for announcement content on announcement add modal
 */
function addPreview() {
    document.getElementById('previewAddText').innerHTML = document.getElementById('contentAnnouncementAddInput').value;
    document.getElementById('previewAddTextDiv').hidden = false;
}

/**
 * activates preview for announcement content on announcement edit modal
 */
function editPreview() {
    document.getElementById('previewEditText').innerHTML = document.getElementById('contentAnnouncementEditInput').value;
    document.getElementById('previewEditTextDiv').hidden = false;
}

/**
 * sets the activation status of a certain announcement
 * @param {int} id identifier of announcement
 * @param {boolean} state activation state of announcement
 */
function setAktivionStateAnnouncement(id, state){
    var jsonReq = {type: "saa", id: id, state: state};
    sendApiRequest(jsonReq, true);
}

/**
 * activates a certain announcement
 * @param {int} id identifier of announcement
 */
function aktivateAnnouncement(id) {
    setAktivionStateAnnouncement(id, true);
}

/**
 * deactivates a certain announcement
 * @param {int} id identifier of announcement
 */
function deaktivateAnnouncement(id) {
    setAktivionStateAnnouncement(id, false);
}

/**
 * opens announcement preview modal
 * @param {int} id identifier of announcement
 */
function openAnnouncementPreview(id) {
    var jsonReq = {type: "gan", id: id};
    var req = sendApiRequest(jsonReq, false).data;
    document.getElementById('PreviewAnnouncementModalMainContent').innerHTML = req.content;
    document.getElementById('PreviewAnnouncementModalMainTitle').innerHTML = req.title;
    $('#PreviewAnnouncementModal').modal();
}