/**
 * generates html sourcecode for content of personal area from kinomap-API request
 */
function loadPersonalArea() {
    var resp = sendApiRequest({type: "pac"}, false).data;
    var m = $('#MeineInfos');
    var str = '';
    str += '<br /><h5 class="text-light">Ihre Daten</h5>'
        + '<div><table class="table table-dark do-reflow">'
        + '<tr><th scope="row" style="width: 20%">Nutzername</th><th scope="row" style="width: 20%">Vorname</th><th scope="row" style="width: 20%">Nachname</th><th scope="row" style="width: 20%">E-Mail-Addresse</th></tr>'
        + '<tr><td scope="row" class="align-middle">' + resp.User.username + '</td><td scope="row" class="align-middle">' + resp.User.firstname + '</td><td scope="row" class="align-middle">' + resp.User.lastname + '</td><td scope="row" class="align-middle">' + resp.User.email + '</td></tr>'
        + '</table></div>'
        + '<br /><h5 class="text-light">Ihre erstellten Einträge</h5>'
        + '<div><table class="table table-dark do-reflow">'
        + '<tr><th scope="col" style="width: 25%">Name</th><th scope="col" style="width: 25%">Adresse</th><th scope="col" style="width: 25%"></th><th scope="col" style="width: 25%"></th></tr>';
    for (i = 0; i < resp.pois.length; i++) {
        str += '<tr';
        if (resp.pois[i].deleted){
            str += ' class="deleted-row"';
        }
        str += '><td scope="row" class="align-middle">' + resp.pois[i].name + '</td><td scope="row" class="align-middle">' + resp.pois[i].address + '</td><td></td><td>';
        if (window.location.href.includes("/map.php")) {
            str += '<button type="submit" class="btn btn-secondary btn-sq m-1" onclick="setfocus2(' + resp.pois[i].lat + ',' + resp.pois[i].lng + ')" data-dismiss="modal" data-toggle="tooltip" data-placement="top" ' +
                'title="Auf der Karte anzeigen"><img src="images/map-marker-alt-solid.svg" width="15px"' +
                'style="margin-top: -2px"></button>';
        } else {
            str += '<button type="submit" class="btn btn-secondary btn-sq m-1" onclick="setCookie(\'LatPoi\', ' +resp.pois[i].lat + ', 5);setCookie(\'LngPoi\', ' +resp.pois[i].lng + ', 5); window.location.href = \'map.php\'" data-dismiss="modal" data-toggle="tooltip" data-placement="top" ' +
                'title="Auf der Karte anzeigen"><img src="images/map-marker-alt-solid.svg" width="15px"' +
                'style="margin-top: -2px"></button>';
        }
        if (resp.pois[i].edit_enable && !resp.pois[i].deleted) {
            str += '<button onclick="location.href=\'editPoi.php?poi=' + resp.pois[i].poi_id + '\'" class="btn btn-sq btn-secondary m-1"' +
                'data-dismiss="modal" data-toggle="tooltip" data-placement="top" title="Bearbeiten">' +
                '<img src="images/pencil-alt-solid.svg" width="15px" style="margin-top: -2px"></button>'
                + '<button class="btn btn-secondary btn-sq m-1"' +
                'onclick="if(confirm(\'POI wirklich löschen?\')){deletePOI( \'' + resp.pois[i].poi_id + '\' );}">' +
                '<img src="images/trash-alt-solid.svg" width="15px" style="margin-top: -2px"></button>';
        }
        if (resp.pois[i].deleted){
            str += '<button type="submit" class="btn btn-sq btn-secondary btn-delete m-1" onClick="deletePoiFinalPersonalArea(' + resp.pois[i].poi_id + ')" data-toggle="tooltip" data-placement="top" title="Endgültig Löschen"><img src="images/trash-alt-solid-red.svg" width="15px" style="margin-top: -2px"></button>'
            str += '<button type="submit" class="btn btn-sq btn-secondary btn-delete m-1" onClick="restorePoiPersonalArea(' + resp.pois[i].poi_id + ')" data-toggle="tooltip" data-placement="top" title="Wiederherstellen"><img src="images/trash-restore-solid-dark-green.svg" width="15px" style="margin-top: -2px"></button>'
        }
        str += '</td></tr>';
    }
    str += '</table> </div>';
    str += '<br /><h5 class="text-light">Ihre Kommentare</h5>'
        + '<div><table class="table table-dark do-reflow">'
        + '<tr><th scope="col" style="width: 25%">Datum</th><th scope="col" style="width: 25%">Eintrag</th><th scope="col" style="width: 25%">Kommentar</th><th scope="col"></th></tr>';
    for (i = 0; i < resp.comments.length; i++) {
        var commentContent = resp.comments[i].content;
        if (commentContent.length > 100) {
            commentContent = commentContent.substring(0, 100)
        }
        str += '<tr';
        if (resp.comments[i].deleted){
            str += ' class="deleted-row"';
        }
        str +='><td scope="row" class="align-middle">' + resp.comments[i].date + '</td><td scope="row" class="align-middle">' + resp.comments[i].poiname + '</td><td scope="row" class="align-middle">' + commentContent + '</td><td scope="row" class="align-middle">';
        if (window.location.href.includes("/map.php")) {
            str += '<button type="submit" class="btn btn-sq btn-secondary m-1" onClick="focusComment = ' + resp.comments[i].cid + ';showMorePOI(' + resp.comments[i].poiid + ')" data-dismiss="modal"><img src="images/map-marker-alt-solid.svg" width="15px" style="margin-top: -2px"></button>';
        } else {
            str += '<button type="submit" class="btn btn-sq btn-secondary m-1" onClick="setCookie(\'OpenComment\', ' +resp.comments[i].cid + ', 5);setCookie(\'OpenPoi\', ' + resp.comments[i].poiid +', 5);window.location.href=\'map.php\'" data-dismiss="modal"><img src="images/map-marker-alt-solid.svg" width="15px" style="margin-top: -2px"></button>';
        }
        if (!resp.comments[i].deleted) {
            str += '<button onclick="editComment(\'' + resp.comments[i].cid + '\');" class="btn btn-secondary btn-sq m-1" data-dismiss="modal"><img src="images/pencil-alt-solid.svg" width="15px"></button>'
                + '<button class="btn btn-secondary btn-sq m-1" onclick="if(confirm(\'Kommentar wirklich löschen?\')){deleteComment(\'' + resp.comments[i].cid
                + '\', true);}"><img src="images/trash-alt-solid.svg" width="15px"></button>';
        } else {
            str += '<button type="submit" class="btn btn-sq btn-secondary btn-delete m-1" onClick="deleteCommentFinalPersonalArea(' + resp.comments[i].cid + ')" data-toggle="tooltip" data-placement="top" title="Endgültig Löschen"><img src="images/trash-alt-solid-red.svg" width="15px" style="margin-top: -2px"></button>'
            str += '<button type="submit" class="btn btn-sq btn-secondary btn-delete m-1" onClick="restoreCommentPersonalArea(' + resp.comments[i].cid + ')" data-toggle="tooltip" data-placement="top" title="Wiederherstellen"><img src="images/trash-restore-solid-dark-green.svg" width="15px" style="margin-top: -2px"></button>'
        }
        str += "</td></tr>";
    }
    str += '</table> </div>';
    document.getElementById('wichtig').innerHTML = str;
    m.modal();
    m.find('table.do-reflow').reflowTable({thead:'tr:first-child'});
}

/**
 * deletes data of a poi final
 * @param {int} id identifier of poi
 */
function deletePoiFinalPersonalArea(id){
    if (confirm('Interessenpunkt wirklich löschen?') === false){
        return ;
    }
    var res = sendApiRequest({type: 'fpi', IDent: id}, false);
    console.log(res);
    loadPersonalArea();
}

/**
 * restores data of a poi
 * @param {int} id identifier of poi
 */
function restorePoiPersonalArea(id){
    if (confirm('Interessenpunkt wirklich Wiederherstellen?') === false){
        return ;
    }
    var res = sendApiRequest({type: 'rpi', IDent: id}, false);
    loadPersonalArea();
}

/**
 * deleted a comment finally
 * @param {int} id identifier of comment
 */
function deleteCommentFinalPersonalArea(id){
    if (confirm('Kommentar wirklich löschen?') === false){
        return ;
    }
    sendApiRequest({type: 'fcp', IDent: id}, false);
    loadPersonalArea();
}

/**
 * restore a comment
 * @param {int} id identifier of comment
 */
function restoreCommentPersonalArea(id){
    if (confirm('Kommentar wirklich wiederherstellen?') === false){
        return ;
    }
    sendApiRequest({type: 'rcp', IDent: id}, false);
    loadPersonalArea();
}

/**
 * opens Edit comment-Modal and perpares Modal
 * @param {int} commentID id of choosen comment
 */
function editComment(commentID) {
    var m = $('#EditComment');
    var comment = getCommentByID(commentID);
    document.getElementById('cidEditComment').value = comment[0].cid;
    document.getElementById('commentEditTBfield').value = comment[0].content;
    m.modal();
}

/**
 * saves Edited Comment with API-Request
 */
function saveEditedComment() {
    var commentID = document.getElementById('cidEditComment').value;
    var commentContent = document.getElementById('commentEditTBfield').value;
    saveCommentByID(commentID, commentContent);
    var delayInMilliseconds = 500;
    setTimeout(function () {
        loadPersonalArea();
    }, delayInMilliseconds);
    $('#EditComment').modal('hide');
}