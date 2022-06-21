var stories = {};
var comments = {};
var storiesMap = {};
var guest = false;
var approver = false;
var admin = false;
var pois = null;
var GCMarker;
var anz = 0;
var announcements;

var operators = {};
var Seats = {};
var Cinemas = {};
var names = {};
var histAddress = {};
var Karte;
var Karte2;
var Karte3;
var Sources = {};
var SourceRelations;
var SourceTypes;

var mark;
var Spielstaette = L.layerGroup();
var latlng = [0, 0];
var mark2;
var data;
var minimap = false;
var guestmode;
var deletedPOI = false;
var anzEditMap = 0;
var focusComment = -1;
var StatData = {};
var sortdown = true;

var yearsSelected = new Array();
var poiidedit;
/**
 *  Needed to make the tooltips work, just don't question it
 */
jQuery(function ($) {
    $("body").tooltip({selector: '[data-toggle=tooltip]'});
    $('[data-toggle="tooltip"]').tooltip();
    var OpenPersonalArea = getCookie("personalArea");
    if (OpenPersonalArea === "1") {
        var delayInMilliseconds = 300;
        setTimeout(function () {
            deleteCookie("personalArea");
            loadPersonalArea();
        }, delayInMilliseconds);
    }
});

/**
 * sends API-Request to Kinomap-API
 * @param {json} json data to transmit
 * @param {boolean} reload enables or disables page reload
 * @returns {array} is in json form and is already parsed
 */
function sendApiRequest(json, reload) {
    reload = reload || false;
    var csrfToken = document.getElementById('TokenScriptCSRF').value;
    json.csrf = csrfToken;
    var otherReq = new XMLHttpRequest();
    otherReq.open("POST", "Formular/api.php", false);
    otherReq.withCredentials = true;
    otherReq.setRequestHeader("Content-Type", "application/json");
    otherReq.send(JSON.stringify(json));
    var resp = otherReq.responseText;
    var result = JSON.parse(resp);

    if (result.code === 1) {
        throw new Error("Something went badly wrong!");
    }
    if (reload) {
        location.reload();
    }
    return result;
}

/**
 * sends API-Request to Kinomap-API asynchronous
 * @param {{}} json data to transmit
 * @param {Function} callback callback with request result (as object if possible)
 * @returns {array} is in json form and is already parsed
 */
function asyncApiRequest(json, callback) {
    var csrfToken = document.getElementById('TokenScriptCSRF').value;
    json.csrf = csrfToken;
    sendRequestPromise(json).then(function (result) {
        if (typeof callback == "function"){
            callback(result);
        }
    }).catch(function (result) {
        console.error('Something went badly wrong!');
        if (typeof callback == "function"){
            callback(result);
        }
    });
}

/**
 * sends API-Request to Kinomap-API asynchronous as promise
 * @param {json} json data to transmit
 * @param {"POST"|"GET"|String} [method] request method, default: "POST"
 * @param {String} [url] request url, default: "Formular/api.php"
 * @returns {array} is in json form and is already parsed
 */
function sendRequestPromise(json, method, url) {
    if (typeof json == 'undefined'){
        json = null;
    }
    if (typeof method == 'undefined'){
        method = "POST";
    }
    if (typeof url == 'undefined'){
        url = "Formular/api.php";
    }
    return new Promise(function (resolve, reject) {
        let xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4){
                let response = JSON.parse(xhr.responseText);
                if (typeof response == 'object') {
                    if (!Array.isArray(response) && typeof response.code != 'undefined' && response.code === 1) {
                        reject({
                            status: this.status,
                            statusText: xhr.statusText,
                            response: xhr.response
                        });
                        return;
                    }
                }
                resolve(response);
            }
        }
        xhr.onerror = function () {
            reject({
                status: this.status,
                statusText: xhr.statusText,
                response: xhr.response
            });
        };
        xhr.overrideMimeType('application/json');
        xhr.withCredentials = true;
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.send((json !== null)? JSON.stringify(json) : null);
    });
}


/**
 * sends request to background platform
 * @param {string} url speciefies which request is done and has all data in it
 * @returns {array} result already parsed JSON
 */
function sendCoseRequest(url) {
    var Req = new XMLHttpRequest();
    Req.open("GET", url, false);
    Req.withCredentials = false;
    Req.setRequestHeader("Content-Type", "application/json");
    Req.send();
    var resp = Req.responseText;
    var result = JSON.parse(resp);
    if (result.code > 0) {
        throw new Error("Something went badly wrong!");
    }
    return result;
}

/**
 * sends request to background platform
 * @param {string} url speciefies which request is done and has all data in it
 * @param {array} content data to send to cosp
 * @returns {array} result already parsed JSON
 */
function sendCoseRequestPost(url, content) {
    var Req = new XMLHttpRequest();
    Req.open("POST", url, false);
    Req.withCredentials = true;
    Req.setRequestHeader("Content-Type", "application/json");
    Req.send(JSON.stringify(content));
    var resp = Req.responseText;
    var result = JSON.parse(resp);
    if (result.code > 0) {
        throw new Error("Something went badly wrong!");
    }
    return result;
}

/**
 * selects if Stories are sorted up or down
 */
function SortStoriesByDate() {
    if (sortdown) {
        SortStoriesByDateDown();
    } else if (sortdown === false) {
        SortStoriesByDateUp();
    }
}

/**
 * Sort strories by date, descending
 */
function SortStoriesByDateDown() {
    var date1 = new Date();
    var date2 = new Date();
    var maxVal;
    var CountOfStories = Object.keys(stories).length;
    var change = true;
    while (change) {
        change = false;
        for (i = 1; i < CountOfStories; i++) {
            date1 = Date.parse(stories[i - 1].date.replace(" ", "T"));
            date2 = Date.parse(stories[i].date.replace(" ", "T"));
            if (date1 < date2) {
                maxVal = i
                change = true
            }
        }
        if (change === true) {
            var bucket = stories [maxVal - 1];
            stories[maxVal - 1] = stories[maxVal];
            stories[maxVal] = bucket;
        }
    }
}

/**
 * Sort strories by date, ascending
 */
function SortStoriesByDateUp() {
    var date1 = new Date();
    var date2 = new Date();
    var maxVal;
    var CountOfStories = Object.keys(stories).length;
    var change = true;
    while (change) {
        change = false;
        for (i = 1; i < CountOfStories; i++) {
            date1 = Date.parse(stories[i - 1].date.replace(" ", "T"));
            date2 = Date.parse(stories[i].date.replace(" ", "T"));
            if (date1 > date2) {
                maxVal = i
                change = true
            }
        }
        if (change === true) {
            var bucket = stories [maxVal - 1];
            stories[maxVal - 1] = stories[maxVal];
            stories[maxVal] = bucket;
        }
    }
}

/**
 * loads all Stories for this projekt from Cosp
 */
function getAllStories() {
    var result = sendApiRequest({type: "gas"}, false).data;
    if (Object.keys(result).length === 4 && Object.keys(result['result']).length > 0) {
        guest = result['guest'];
        approver = result['approver'];
        admin = result['admin'];
        var result2 = result.result;
        stories = {};
        var Story = sendCoseRequestPost(result2.url, {
            type: result2.type,
            data: result2.data,
            seccode: result2.seccode,
            time: result2.time
        });
        for (i = 0; i < Story.length; i++) {
            loadStory(Story[i], i);
        }
    }
    sortAndDisplay();
}

/**
 * sorts and displays stories
 */
function sortAndDisplay() {
    SortStoriesByDate();
    document.getElementById("stories").innerHTML = "";
    var notOnlyApprovable = true;
    var notOnlyValidated = true;
    if (document.getElementById('approved_story').checked) {
        notOnlyApprovable = false;
    }
    if (document.getElementById('unvalidated_story').checked) {
        notOnlyValidated = false;
    }
    for (var keys in stories) {
        if (notOnlyValidated || (stories[keys].validate === false)) {
            if (notOnlyApprovable || (stories[keys].approval === false)) {
                loadStoryText(stories[keys], keys);
            }
        }
    }
}

/**
 * load a single storie to global dict and shows it on website
 * @param {string} Link to storiedownload via api
 * @param {int} IntCounter is Position of Story in global dict
 */
function loadStorySingle(Link, IntCounter) {
    var Story = sendCoseRequest(Link).data;
    stories[IntCounter] = Story;
}

/**
 * loads a story only to global dict
 * @param {array} Story is full Story
 * @param {int} IntCounter Position of entity in global dict
 * @returns {array} Story complete Storie
 */
function loadStory(Story, IntCounter) {
    stories[IntCounter] = Story;
}

/**
 * displays Story on website
 * @param {array} Story parsed JSON with all data for generating html sourcecode for story
 * @param {int} IntCounter Position of Story in global array
 */
function loadStoryText(Story, IntCounter) {
    var text = "";
    var long = false;
    var str = "";
    var validated = Story.validate || Story.validatedByUser;
    var editable = Story.editable;
    if (Story.story.length < 396) {
        text = Story.story;
    } else {
        text = Story.story.substring(0, 396) + " ...";
        long = true;
    }
    str += '<div class="px-3 mx-3 col-12">'
        + '<div style="display: inline-block" class="col-12';
    if (Story.deleted) {
        str += ' deleted-div';
    }
    str += '">'
        + '<h5 style="color: #d2d2d2;">' + Story.title + '</h5>'
        + '<p class="col-11" style="color: white; margin-top: 30px">' + text + '</p>'
        + '<div class="d-flex align-items-center">'
        + '<div class="ml-4" style="font-size: 0.8em; color: #c2c2c2">' + Story.name + ' – ' + Story.date + '</div>'
        + '<div class="ml-auto">';
    if (long) {
        str += '<button class="btn m-1 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Vollständig anzeigen" onclick="showMoreStory(' + IntCounter + ')"><img src="images/expand-solid.svg" width="15px" style="margin-top: -2px"></button></span>';
    } else {
        str += '<button class="btn m-1 btn-sq-sm disabled invisible"></button>';
    }
    if (!Story.deleted) {
        if (guest === false) {
            if (validated) {
                str += '<span class="btn m-1 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Validiert"><img src="images/check-solid-dark-green.svg" width="15px" style="margin-top: 3px"></span>'
            } else {
                str += '<button class="btn m-1 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Validieren" style="align-content: center;" onclick="if (confirm(\'Geschichte wirklich validieren?\')){validateStory(' + IntCounter + ')}"><img src="images/check-solid-black.svg" width="15px" style="margin-top: -2px"></button>';
            }
            if (editable) {
                str += '<button class="btn m-1 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Bearbeiten" onclick="openEditStorie(' + IntCounter + ')"><img src="images/pencil-alt-solid-dark.svg" width="15px" style="margin-top: -2px"></button></span>';
            } else {
                str += '<button class="btn m-1 btn-sq-sm btn-success disabled invisible"><img src="images/pencil-alt-solid-dark.svg" width="15px" style="margin-top: -2px"></button></span>';
            }
        }
        str += '<button class="btn m-1 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Verknüpfte Einträge anzeigen" onclick="showPoiLinks(' + IntCounter + ')"><img src="images/map-marker-alt-solid-black.svg" width="13px" style="margin-top: -3px"></button>';
        if (guest === false) {
            if (editable) {
                str += '<button onclick="if (confirm(\'Geschichte wirklich löschen?\')){deleteStory(' + IntCounter + ')}" class="btn m-1 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Löschen"><img src="images/trash-alt-solid-black.svg" width="15px" style="margin-top: -2px"></button>';
            }
        }
        if (approver) {
            if (Story.approval) {
                str += '<button onclick="DisapproveStory(' + IntCounter + ')" class="btn m-1 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Sperren"><img src="images/lock-open-solid-dark-green.svg" width="15px" style="margin-top: -2px"></button>';
            } else {
                str += '<button onclick="ApproveStory(' + IntCounter + ')" class="btn m-1 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Freigeben"><img src="images/lock-open-solid.svg" width="15px" style="margin-top: -2px"></button>';
            }
        }
    } else if (guest === false && Story.deleted && admin) {
        str += "<button onclick=\"finalDeleteStory(" + IntCounter + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Endgültig Löschen\"><img src=\"images/trash-alt-solid-red.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
        str += "<button onclick=\"restoreStory(" + IntCounter + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Wiederherstellen\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
    }
    str += '</div></div><hr>'
        + '</div></div>';
    document.getElementById("stories").innerHTML += str;
}

/**
 * finally deletes a story
 * @param {int} intCounter position of story in stories array
 */
function finalDeleteStory(intCounter) {
    if (confirm('Geschichte wirklich löschen?')) {
        sendApiRequest({type: "fst", IDent: stories[intCounter].token}, true);
    }
}

/**
 * restores a story
 * @param {int} intCounter position of story in stories array
 */
function restoreStory(intCounter) {
    if (confirm('Geschichte wirklich wiederherstellen?')) {
        sendApiRequest({type: "rst", IDent: stories[intCounter].token}, true);
    }
}


/**
 * approve story via api
 * @param {int} intCounter position of story in stories array
 */
function ApproveStory(intCounter) {
    if (confirm('Geschichte wirklich Freigeben?')) {
        sendApiRequest({type: "asa", story_token: stories[intCounter].token}, true);
    }
}

/**
 * disapprove story via api
 * @param {int} intCounter position of story in stories array
 */
function DisapproveStory(intCounter) {
    if (confirm('Geschichte wirklich Sperren?')) {
        sendApiRequest({type: "das", story_token: stories[intCounter].token}, true);
    }
}

/**
 * Displays full story, if story is longer than 396 characters
 * @param {int} intCounter Position of Storie in global dict
 */
function showMoreStory(intCounter) {
    document.getElementById("StoryTitle").innerHTML = stories[intCounter].title;
    document.getElementById("StoryLongText").innerHTML = stories[intCounter].story;
    document.getElementById("StoryLongNameDate").innerHTML = stories[intCounter].name + ' – ' + stories[intCounter].date;
    var str = "";
    var notGuest = !checkIfGuest();
    if (notGuest) {
        if (stories[intCounter].validate || stories[intCounter].validatedByUser) {
            str += '<span class="btn ml-3 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Validiert"><img src="images/check-solid-dark-green.svg" width="15px" style="margin-top: 3px"></span>'
        } else {
            str += '<button class="btn ml-3 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Validieren" style="align-content: center;" onclick="if (confirm(\'Geschichte wirklich validieren?\'){validateStory(' + intCounter + ')}"><img src="images/check-solid-black.svg" width="15px" style="margin-top: -2px"></button>';
        }
        if (stories[intCounter].editable) {
            str += '<button class="btn ml-3 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Bearbeiten" onclick="openEditStorie(' + intCounter + ')"><img src="images/pencil-alt-solid-dark.svg" width="15px" style="margin-top: -2px"></button></span>';
        } else {
            str += '<button class="btn ml-3 btn-sq-sm btn-success disabled invisible"><img src="images/pencil-alt-solid-dark.svg" width="15px" style="margin-top: -2px"></button></span>';
        }
    }
    str += '<button class="btn ml-3 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Verknüpfte Einträge anzeigen" onclick="showLinksLongStory(' + intCounter + ')"><img src="images/map-marker-alt-solid-black.svg" width="13px" style="margin-top: -3px"></button>';
    if (stories[intCounter].editable && notGuest) {
        str += '<button onclick="if (confirm(\'Geschichte wirklich löschen?\')){deleteStory(' + intCounter + ')}" class="btn ml-3 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Löschen"><img src="images/trash-alt-solid-black.svg" width="15px" style="margin-top: -2px"></button>';
    }
    if (approver) {
        if (stories[intCounter].approval) {
            str += '<button onclick="DisapproveStory(' + intCounter + ')" class="btn ml-3 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Sperren"><img src="images/lock-open-solid-dark-green.svg" width="15px" style="margin-top: -2px"></button>';
        } else {
            str += '<button onclick="ApproveStory(' + intCounter + ')" class="btn ml-3 btn-sq-sm btn-success" data-toggle="tooltip" data-placement="top" title="Freigeben"><img src="images/lock-open-solid.svg" width="15px" style="margin-top: -2px"></button>';
        }
    }
    document.getElementById('ButtonsLongStoryShowMoreUl').innerHTML = str;
    $("#LongStory").modal();
}

/**
 * wrapper to close long story modal
 * @param {int} intCounter position of story in array
 */
function showLinksLongStory(intCounter) {
    $("#LongStory").modal('hide');
    showPoiLinks(intCounter);
}

/**
 * asks api if current user is guest
 */
function checkIfGuest() {
    return sendApiRequest({type: "asg"}, false).data;
}

/**
 * Loads Storydata in EditModal for Editing text and open Edit-Modal
 * @param {int} intCounter Position of Story in stories-array which should be edited
 */
function openEditStorie(intCounter) {
    $('#EditStories').modal();
    document.getElementById('StorieEditTitelField').value = stories[intCounter].title;
    document.getElementById("StorieTBcommentField").value = stories[intCounter].story;
    document.getElementById("StorieEditTokenField").value = stories[intCounter].token;
}

/**
 * saves Storie back to Database, reads data from Inputfields
 */
function saveEditStorie() {
    var title = document.getElementById('StorieEditTitelField').value;
    var description = document.getElementById('StorieTBcommentField').value;
    var token = document.getElementById("StorieEditTokenField").value;
    var json = {
        type: "eus",
        storytoken: token,
        title: title,
        story: description,
    };
    $('#EditStories').modal('hide');
    return sendApiRequest(json, true);
}

/**
 * opens modal for pois linked to story
 * @param {int} intCounter Position of story in global dict
 */
function showPoiLinks(intCounter) {
    var pois = getPoiTitle(stories[intCounter].token);
    var str = "";
    var table = "";
    var request = loadKnownStoriesPoiLinks(stories[intCounter].token);
    var result = request.pois;
    var guest = request.guest;
    var admin = request.admin;
    for (var key in result) {
        table += '<tr class="';
        if (result[key].deleted) {
            table += 'deleted-row';
        }
        table += '"><td class="align-middle">' + result[key].name + '</td><td class="align-middle">';
        if (!guest && !result[key].deleted) {
            if (result[key].deletable) {
                table += '<button onclick="if (confirm(\'Verlinkung wirklich löschen?\')){deletePoiStoryLink(' + result[key].id + ',' + intCounter + ');}" class="btn btn-sq btn-secondary mr-2" data-toggle="tooltip" data-placement="top" title="Löschen"><img src="images/trash-alt-solid.svg" width="15px" style="margin-top: -2px"></button>';
            }
            if (request.valpos) {
                if (result[key].validated === false) {
                    table += '<button onclick="validatePoiStoryLink(' + result[key].id + ',' + intCounter + ')" class="btn btn-sq btn-secondary mr-2" data-toggle="tooltip" data-placement="top" title="Validieren"><img src="images/check-solid.svg" width="15px" style="margin-top: -2px"></button>';
                } else {
                    table += '<button class="btn btn-sq btn-secondary disabled-ng disabled mr-2" data-toggle="tooltip" data-placement="top" title="Validiert"><img src="images/check-solid-green.svg" width="15px" style="margin-top: -2px"></button>';
                }
            }
        } else if (!guest && result[key].deleted && admin) {
            table += "<button onclick=\"FinalDeletePoiStoryLink(" + result[key].id + "," + intCounter + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Endgültig Löschen\"><img src=\"images/trash-alt-solid-red.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            if (!result[key].restrictions) {
                table += "<button onclick=\"RestorePoiStoryLink(" + result[key].id + "," + intCounter + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Wiederherstellen\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            } else {
                table += "<button class=\"btn btn-sq btn-secondary mr-2 disabled\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Abhängigkeiten gelten als gelöscht.\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            }
        }
        table += '<button onclick="$(this).tooltip(\'hide\'); this.blur(); focusPoiOfPicture(' + result[key].lat + ', ' + result[key].lng + ')" class="btn btn-sq btn-secondary mr-2" data-toggle="tooltip" data-placement="top" title="Auf Karte Anzeigen"><img src="images/map-marker-alt-solid.svg" width="15px" style="margin-top: -2px"></button>';
        table += '</td></tr>'
    }
    if (!guest) {
        for (var key2 in pois) {
            str += '<option value="' + pois[key2].poi_id + '">' + pois[key2].name + '</option>';
        }
        document.getElementById('LinkPoiStorySelect').innerHTML = str;
        document.getElementById('LinkPoiStoryToken').value = intCounter;
    }
    document.getElementById('poiLinkTabelBody').innerHTML = table;
    document.getElementById("StoryTitleLinks").innerHTML = stories[intCounter].title + " - Verknüpfungen mit Einträgen";
    $("#poiLinks").modal();
}

/**
 * final deletion link and updates modal
 * @param {int} id Id of Poi Story Link
 * @param {int} IntCounter Position of Story after finishing
 */
function FinalDeletePoiStoryLink(id, IntCounter) {
    if (confirm('Verknüpfung wirklich löschen?') === false) {
        return;
    }
    sendApiRequest({type: 'fsp', IDent: id}, false);
    showPoiLinks(IntCounter);
}

/**
 * restore Link and updates modal
 * @param {int} id Id of Poi Story Link
 * @param {int} IntCounter Position of Story after finishing
 */
function RestorePoiStoryLink(id, IntCounter) {
    if (confirm('Verknüpfung wirklich wiederherstellen?') === false) {
        return;
    }
    sendApiRequest({type: 'rsp', IDent: id}, false);
    showPoiLinks(IntCounter);
}

/**
 * triggers deletion of Link and updates modal
 * @param {int} IdPoiStory Id of Poi Story Link
 * @param {int} IntCounter Position of Story after finishing
 */
function deletePoiStoryLink(IdPoiStory, IntCounter) {
    ApiRequestDeletePoiStoryLink(IdPoiStory);
    showPoiLinks(IntCounter);
}

/**
 * sends Request for deleting Poi Story Link to API
 * @param {int} IdPoiStory Id of Poi Story Link
 */
function ApiRequestDeletePoiStoryLink(IdPoiStory) {
    sendApiRequest({type: "dps", poiStoryId: IdPoiStory}, false)
}

/**
 * triggers api request to validate Poi Story Link
 * @param {int} IdPoiStory Id of Poi Story Link
 * @param {int} IntCounter Position of Story after finishing
 */
function validatePoiStoryLink(IdPoiStory, IntCounter) {
    if (confirm("Verlinkung zwischen Interessenpunkt und Geschichte wirklich validieren?")) {
        ApiRequestValidatePoiStoryLink(IdPoiStory);
        showPoiLinks(IntCounter);
    }
}

/**
 * sends Request for validating Poi Story Link to API
 * @param {int} IdPoiStory Id of Poi Story Link
 */
function ApiRequestValidatePoiStoryLink(IdPoiStory) {
    sendApiRequest({type: "vps", poiStoryId: IdPoiStory}, false)
}

/**
 * gets all POI Titles
 * @param {int} token identifier of story
 * @returns {Array} gethered Information
 */
function getPoiTitle(token) {
    return sendApiRequest({type: "gpt", storytoken: token}, false).data;
}

/**
 * loads data from database for selected story
 * @param {string} token token of current selected story
 * @returns {array} result of request
 */
function loadKnownStoriesPoiLinks(token) {
    return sendApiRequest({type: "gps", storytoken: token}, false).data;
}

/**
 * Sends new story to kinomap-platform for uploading to cosp
 * @param {array} e All elements of form to input new story
 * @returns {boolean} Always false to prevent reload of page
 */
function addStory() {
    testCheckBox = document.getElementById("storyAddRightsCheckbox").checked;
    if (testCheckBox) {
        var json = {
            type: "aus",
            title: document.getElementById('storyAddStoryTitle').value,
            story: document.getElementById('storyAddStoryInput').value,
            rights: testCheckBox,
        };
        sendApiRequest(json, false);
        $("#AddStoryModal").modal('hide');
        getAllStories();
    }
    document.getElementById("storyAddRightsCheckbox").checked = false;
    document.getElementById('storyAddStoryTitle').value = "";
    document.getElementById('storyAddStoryInput').value = "";
}

/**
 * Executed if somebody wants to validate a story
 * @param {int} IntCounter Position of story in global dict
 */
function validateStory(IntCounter) {
    var result = sendApiRequest({type: 'ccp'}, false).data;
    sendCoseRequestPost(result,
        {
            seccode: stories[IntCounter].valLink.seccode,
            time: stories[IntCounter].valLink.time,
            data: stories[IntCounter].valLink.token,
            type: 'vas'
        });
    location.reload();
}

/**
 * Executed if somebody wants to validate a story
 * @param {int} IntCounter Position of story in global dict
 */
function deleteStory(IntCounter) {
    var token = stories[IntCounter].token;
    var json = {
        type: "dus",
        story_token: token,
    };
    sendApiRequest(json, true);
}

/**
 * Executed if somebody wants to validate a picture
 * @param {string} url URL to send cosp request for validating a picture to
 */
function validatePicture(url) {
    sendCoseRequest(url);
    location.reload();
}

/**
 * deletes poi and referenced data via api
 * @param {int} poiid Id of poi which should be deleted
 */
function deletePOI(poiid) {
    var json = {
        type: "dpi",
        poiid: poiid
    };
    setCookie("personalArea", 1);
    sendApiRequest(json, true);
}

/**
 * deletes a given comment with its commentid
 * @param {int} commentid ID of given comment
 * @param {boolean} personalArea must be set to true if done from personal area
 */
function deleteComment(commentid, personalArea) {
    var json = {
        type: "duc",
        commentid: commentid,
    };
    if (personalArea) {
        setCookie("personalArea", 1);
        sendApiRequest(json, true);
        return;
    }
    sendApiRequest(json, false);
}

/**
 * deletes a comment from a certain poi on show more modal
 * @param {int} commentid id of comment
 * @param {int} poi_id id of poi to load afterwards
 */
function deleteCommentMap(commentid, poi_id) {
    deleteComment(commentid, false);
    loadComments(poi_id);
}

/**
 * requests data of a given comment with its commentid
 * @param {int} commentid ID of given comment
 */
function getCommentByID(commentid) {
    var json = {
        type: "gcs",
        commentid: commentid,
    };
    return sendApiRequest(json, false);
}

/**
 * Saves a edited Comment via Apirequest
 * @param {int} commentID comment ID of comment which should be edited saved
 * @param {string} commentContent new Content of edited Comment
 */
function saveCommentByID(commentID, commentContent) {
    var json = {
        type: "sec",
        commentid: commentID,
        commentContent: commentContent
    };
    return sendApiRequest(json, false);
}

/**
 * Adds Comment to POI using API
 * @param {string} comment Content of Content
 * @param {int} poiid ID of Point od Interest to which comment should be addeded
 * @returns {boolean} always true
 */
function AddCommentAPI(comment, poiid) {
    sendApiRequest({type: 'auc', comment: comment, poi_id: poiid}, false);
    return true;
}

/**
 * Loads data for a single Material
 * @param {string} token unique Identifier of material
 * @returns {Array} data gathered from kinomap
 */
function LoadSingleMaterialData(token) {
    return sendApiRequest({type: 'dsm', token: token});
}

/**
 * transmits edited Data of Material back to kinomap-platform-API
 * @param {string} title title of material
 * @param {string} description description of material
 * @param {string} token unique Identifier of material
 */
function sendEditedMaterialData(title, description, token) {
    var json = {
        type: "ssm",
        title: title,
        token: token,
        description: description
    };
    sendApiRequest(json, true);
}

/**
 * transmits edited Data of Material back to kinomap-platform-API
 * @param {string} title title of material
 * @param {string} description description of material
 * @param {string} token unique Identifier of material
 * @param {string} source source of picture
 * @param {int} sourcetype type of source
 */
function sendEditedMaterialDataSource(title, description, token, source, sourcetype) {
    var json = {
        type: "ssm",
        title: title,
        token: token,
        description: description,
        source: source,
        sourcetype: sourcetype
    };
    sendApiRequest(json, true);
}

/**
 * transmits Data for new name of Poi in Name Table
 * @param {int} from start date of new name
 * @param {int} till end date of new name
 * @param {string} name name to add to poi
 * @param {int} poiid id of affected POI
 */
function AddNameOfPoiAPI(from, till, name, poiid) {
    sendApiRequest({type: 'adn', from: from, till: till, name: name, poi_id: poiid}, false);
}

/**
 * Deletes Name of POI from Database
 * @param {int} ID Unique identification of name in Table
 * @param {int} POiid ID of POI
 */
function deletePoiNameFromList(ID, POiid) {
    sendApiRequest({type: 'dna', IDent: ID}, false);
    ShowMoreNames(POiid);
}

/**
 * Deletes Operator of POI from Database
 * @param {int} ID Unique identification of operator in Table
 * @param {int} POiid ID of POI
 */
function deletePoiOperatorFromList(ID, POiid) {
    sendApiRequest({type: 'dop', IDent: ID}, false);
    ShowMoreOperators(POiid);
}

/**
 * Deletes Address of POI from Database
 * @param {int} ID Unique identification of address in Table
 * @param {int} POiid ID of POI
 */
function deletePoiAddressFromList(ID, POiid) {
    sendApiRequest({type: 'dha', IDent: ID}, false);
    ShowMoreHistoricalAddresses(POiid);
}

/**
 * Validates Time span of an POI
 * @param {int} POi_id ID of POI to which the Time span is belonging
 */
function validateTimeSpanPOI(POi_id) {
    if (confirm("Zeitraum wirklich validieren?")) {
        sendApiRequest({type: 'vts', POIID: POi_id}, false);
        showMorePOI(POi_id);
    }
}

/**
 * Validates current address of an POI
 * @param {int} POi_id ID of POI to which the current address is belonging
 */
function validateCurrentAddressPOI(POi_id) {
    if (confirm("Aktuelle Addresse wirklich validieren?")) {
        sendApiRequest({type: 'vca', POIID: POi_id}, false);
        showMorePOI(POi_id);
    }
}

/**
 * Validates history of an POI
 * @param {int} POi_id ID of POI to which the history is belonging
 */
function validateHistoryPOI(POi_id) {
    if (confirm("Historie wirklich validieren?")) {
        sendApiRequest({type: 'vhi', POIID: POi_id}, false);
        showMorePOI(POi_id);
    }
}

/**
 * Validates type of an POI
 * @param {int} POi_id ID of POI to which the type is belonging
 */
function validateTypePOI(POi_id) {
    if (confirm("Typ des Kinos wirklich validieren?")) {
        sendApiRequest({type: 'vty', POIID: POi_id}, false);
        showMorePOI(POi_id);
    }
}

/**
 * Validates one name of an POI
 * @param {int} name_id ID of name to be validated
 * @param {int} POi_id nessecary to reload POI
 */
function validatePoiName(name_id, POi_id) {
    if (confirm("Name des Kinos wirklich validieren?")) {
        sendApiRequest({type: 'vna', NAMEID: name_id}, false);
        ShowMoreNames(POi_id);
    }
}

/**
 * Validates one operator of an POI
 * @param {int} operator_id ID of operator to be validated
 * @param {int} POi_id nessecary to reload POI
 */
function validatePoiOperator(operator_id, POi_id) {
    if (confirm("Betreiber des Kinos wirklich validieren?")) {
        sendApiRequest({type: 'vop', OPERATORID: operator_id}, false);
        ShowMoreOperators(POi_id);
    }
}

/**
 * Validates one address of an POI
 * @param {int} Address_id ID of address to be validated
 * @param {int} POi_id nessecary to reload POI
 */
function validatePoiAddress(Address_id, POi_id) {
    if (confirm("Historische Addresse des Kinos wirklich validieren?")) {
        sendApiRequest({type: 'vha', ADDRESSID: Address_id}, false);
        ShowMoreHistoricalAddresses(POi_id);
    }
}

/**
 * shows very long comment
 * @param {int} commentID Id of comment in comments array
 */
function showLongComment(commentID) {
    $('#MarkerModalBig').modal('toggle');
    document.getElementById('MainLongComment').innerText = comments[commentID].content;
    document.getElementById('LongCommentTitel').innerText = "Kommentar zu " + comments[commentID].kinoname;
    document.getElementById('LongCommentNameDate').innerText = comments[commentID].name + ' – ' + comments[commentID].timestamp;
    document.getElementById('LongComment').setAttribute("style", document.getElementById('LongComment').getAttribute("style") + "; overflow-y: overlay;")
    document.getElementById('CloseBtnLongComment').setAttribute('onclick', 'closeLongComment(' + comments[commentID].poiid + ')');
    $('#LongComment').modal();
}

/**
 * closes long comment and opens show more modal
 * @param {int} id ID of Poi to which shown comment belongs
 */
function closeLongComment(id) {
    $('#LongComment').modal('toggle');
    document.getElementById('MarkerModalBig').setAttribute("style", document.getElementById('LongComment').getAttribute("style") + "; overflow-y: overlay;")
    showMorePOI(id);
}

/**
 * triggers deletion of Link and updates modal
 * @param {int} IdPoiStory Id of Poi Story Link
 * @param {int} PoiId PoiId from which call came
 */
function deletePoiStoryLinkOnPoi(IdPoiStory, PoiId) {
    ApiRequestDeletePoiStoryLink(IdPoiStory);
    ShowMoreStories(PoiId);
    SetShowMoreStoryLinkOptions(PoiId);
}

/**
 * triggers api request to validate Poi Story Link
 * @param {int} IdPoiStory Id of Poi Story Link
 * @param {int} PoiId PoiId from which call came
 */
function validatePoiStoryLinkOnPoi(IdPoiStory, PoiId) {
    if (confirm("Verknüpfung zwischen Kino und Geschichte wirklich validieren?")) {
        ApiRequestValidatePoiStoryLink(IdPoiStory);
        ShowMoreStories(PoiId);
        SetShowMoreStoryLinkOptions(PoiId);
    }
}

/**
 * checks if Database already knows Address
 * @param {string} Streetname Streetname of address to check
 * @param {string} Housenumber Housenumber of address to check
 * @param {string} City City of address to check
 * @param {int} Postalcode Postalcode of address to check
 * @return {boolean} true if address exists
 */
function checkAddressExists(Streetname, Housenumber, City, Postalcode) {
    var result = sendApiRequest({st: Streetname, hn: Housenumber, ct: City, pc: Postalcode, type: "cha"}, false).data;
    return result;
}

/**
 * checks if Address already Exists
 * @return {boolean} true if user wanted or address doesn't exists
 */
function CheckAddress() {
    var st = document.getElementById('streetname').value;
    var hn = document.getElementById('housenumber').value;
    var pc = document.getElementById('postalcode').value;
    var ct = document.getElementById('city').value;
    if (st != "" && st != null && hn != "" && hn != null && ((pc != "" && pc != null) || (ct != "" && ct != null))) {
        if (checkAddressExists(st, hn, ct, pc)) {
            return confirm("Diese Addresse ist bereits vorhanden. Trotzdem Fortfahren?");
        }
    }
    return true;
}

/**
 * gets Pictures as List
 * @return {Array} structured answer data
 */
function getPicturesAsList() {
    return sendApiRequest({type: 'gpf'}, false).data;
}

/**
 * loads Single Picture Select
 */
function showSinglePicSelect() {
    document.getElementById('MainPictureSelectSelected').value = "";
    var pictureList = getPicturesAsList();
    var html = "";
    for (var i = 0; i < pictureList.length; i++) {
        html += '<div class="card card-pic-select mt-2" onclick="onclick_picture(\'' + pictureList[i].identifier + '\');" id="card_' + pictureList[i].identifier + '">';
        html += '<img class="card-img pictureSelectPic" src="' + pictureList[i].preview + '" alt="Card image cap">';
        html += '<div class="card-img-overlay card-pic-select-overlay" id="card_' + pictureList[i].identifier + '_O">';
        html += '<h5 class="card-title" id="card-title">' + pictureList[i].title + '</h5>';
        html += '</div>';
        html += '</div>';
    }
    document.getElementById('MainPictureSelectorCards').innerHTML = html;
    $('#PictureSelectModal').modal();
}

/**
 * saves name to inpu field, mechanism depends on multi or single select toogle checkbox
 * @param {string} e Identifier of Picture
 */
function onclick_picture(e) {
    var cssClasses = document.getElementById('card_' + e + '_O').getAttribute('class');
    if (cssClasses.includes('card-pic-select-overlay-selected')) {
        if (document.getElementById('MainPictureSelectSingleToggle').checked === false) {
            var selected = document.getElementById('MainPictureSelectSelected').value;
            document.getElementById('MainPictureSelectSelected').value = "";
            selected = selected.split('$');
            for (var i = 0; i < selected.length; i++) {
                if (selected[i] !== e) {
                    if (document.getElementById('MainPictureSelectSelected').value === "") {
                        document.getElementById('MainPictureSelectSelected').value = selected[i];
                    } else {
                        document.getElementById('MainPictureSelectSelected').value += "$" + selected[i];
                    }
                }
            }
        } else {
            document.getElementById('MainPictureSelectSelected').value = "";
        }
        document.getElementById('card_' + e + '_O').setAttribute('class', 'card-img-overlay card-pic-select-overlay');
    } else {
        if (document.getElementById('MainPictureSelectSingleToggle').checked === false) {
            if (document.getElementById('MainPictureSelectSelected').value === "") {
                document.getElementById('MainPictureSelectSelected').value = e;
            } else {
                document.getElementById('MainPictureSelectSelected').value += "$" + e;
            }
        } else {
            if (document.getElementById('MainPictureSelectSelected').value !== "") {
                var old = document.getElementById('MainPictureSelectSelected').value;
                document.getElementById('card_' + old + '_O').setAttribute('class', 'card-img-overlay card-pic-select-overlay');
            }
            document.getElementById('MainPictureSelectSelected').value = e;
        }
        document.getElementById('card_' + e + '_O').setAttribute('class', 'card-img-overlay card-pic-select-overlay card-pic-select-overlay-selected');
    }
}

/**
 * Sets picture select Modal to Single Select
 */
function setPictureSelect_SingleSelect() {
    document.getElementById('MainPictureSelectSingleToggle').checked = true;
}

/**
 * Sets picture select Modal to Multi Select
 */
function setPictureSelect_MultiSelect() {
    document.getElementById('MainPictureSelectSingleToggle').checked = false;
}

/**
 * send request to verify a certain poi pic link
 * @param {int} id id of link
 * @param {int} poiid piid to reload modal
 */
function verifyPicPoiLink(id, poiid) {
    if (confirm("Verknüpfung zwischen Bild und Interessenpunkt wirklich validieren?")) {
        sendApiRequest({type: 'vpp', id: id}, false);
        ShowMoreAdditionalPictures(poiid);
    }
}

/**
 * send request to delete a certain poi pic link
 * @param {int} id id of link
 * @param {int} poiid piid to reload modal
 */
function deletePicPoiLink(id, poiid) {
    if (confirm("Verknüpfung zwischen Bild und Interessenpunkt wirklich aufheben?")) {
        sendApiRequest({type: 'dpp', id: id}, false);
        ShowMoreAdditionalPictures(poiid);
    }
}

/**
 * opens modal for pois linked to story
 * @param {int} intCounter Position of story in global dict
 */
function showPoiPicLinks(picToken, title) {
    var str = "";
    var data = getPoisForPicture(picToken).data;
    console.log(data);
    var table = "";
    if (data.guest === false) {
        for (var key in data.options) {
            str += '<option value="' + data.options[key].poi_id + '">' + data.options[key].name + '</option>';
        }
    }
    for (var key2 in data.linked) {
        table += '<tr class="';
        if (data.linked[key2].deleted) {
            table += 'deleted-row';
        }
        table += '"><td class="align-middle">' + data.linked[key2].name + '</td><td class="align-middle">';
        if (data.guest === false && !data.linked[key2].deleted) {
            if (data.linked[key2].deletable) {
                table += '<button class="btn btn-sq btn-secondary mr-2" data-toggle="tooltip" data-placement="top" title="Löschen" onclick="$(this).tooltip(\'hide\'); this.blur(); deletePicPoiListMaterialLink(' + data.linked[key2].lid + ', \'' + picToken + '\', \'' + title + '\')"><img src="images/trash-alt-solid.svg" width="15px" style="margin-top: -2px"></button>';
            }
            if (data.valpos) {
                if (data.linked[key2].validated === false) {
                    table += '<button class="btn btn-sq btn-secondary mr-2" data-toggle="tooltip" data-placement="top" title="Validieren" onclick="$(this).tooltip(\'hide\'); this.blur(); verifyPicPoiLinkMaterial(' + data.linked[key2].lid + ', \'' + picToken + '\', \'' + title + '\')"><img src="images/check-solid.svg" width="15px" style="margin-top: -2px"></button>';
                } else {
                    table += '<button class="btn btn-sq btn-secondary disabled-ng disabled mr-2" data-toggle="tooltip" data-placement="top" title="Validiert"><img src="images/check-solid-green.svg" width="15px" style="margin-top: -2px"></button>';
                }
            }
        } else {
            table += '<button onclick="$(this).tooltip(\'hide\'); this.blur(); ListMaterialWrapperFinalDeletePictureLink(' + data.linked[key2].lid + ', \'' + picToken + '\', \'' + title + '\')" class="btn btn-sq btn-secondary mr-2" data-toggle="tooltip" data-placement="top" title="Endgültig Löschen"><img src="images/trash-alt-solid-red.svg" width="15px" style="margin-top: -2px"></button>';
            if (!data.linked[key2].restrictions) {
                table += '<button onclick="$(this).tooltip(\'hide\'); this.blur(); ListMaterialWrapperRestorePictureLink(' + data.linked[key2].lid + ', \'' + picToken + '\', \'' + title + '\')" class="btn btn-sq btn-secondary mr-2" data-toggle="tooltip" data-placement="top" title="Wiederherstellen"><img src="images/trash-restore-solid-dark-green.svg" width="15px" style="margin-top: -2px"></button>';
            } else {
                table += "<button class=\"btn btn-sq btn-secondary mr-2 disabled\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Abhängigkeiten gelten als gelöscht.\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            }
        }
        table += '<button onclick="$(this).tooltip(\'hide\'); this.blur(); focusPoiOfPicture(' + data.linked[key2].lat + ', ' + data.linked[key2].lng + ')" class="btn btn-sq btn-secondary mr-2" data-toggle="tooltip" data-placement="top" title="Auf Karte Anzeigen"><img src="images/map-marker-alt-solid.svg" width="15px" style="margin-top: -2px"></button>';
        table += '</td></tr>'
    }
    document.getElementById('PicPoiLinksTitle').innerHTML = title + ' - Verknüpfte Interessenpunkte';
    document.getElementById('poiPicLinkTabelBody').innerHTML = table;
    if (data.guest === false) {
        document.getElementById('LinkPoiPicSelect').innerHTML = str;
        document.getElementById('LinkPoiPicSelectSavebtn').setAttribute('onclick', 'addPicPoiLinkMaterialList(\'' + picToken + '\', \'' + title + '\');');
    }
    $("#poiPicLinks").modal();
}

/**
 * finally deletes a link between a picture and a poi
 * @param {int} id Identifier of LinkS
 * @param {string} pictoken Identifier of picture
 * @param {string} title title of picture
 */
function ListMaterialWrapperFinalDeletePictureLink(id, pictoken, title) {
    if (confirm("Verknüpfung zwischen Bild und Interessenpunkt wirklich endgültig löschen?")) {
        finalDeleteLinkPoiPic(id);
        showPoiPicLinks(pictoken, title);
    }
}

/**
 * restores a link between a picture and a poi
 * @param {int} id Identifier of LinkS
 * @param {string} pictoken Identifier of picture
 * @param {string} title title of picture
 */
function ListMaterialWrapperRestorePictureLink(id, pictoken, title) {
    if (confirm("Verknüpfung zwischen Bild und Interessenpunkt wirklich wiederherstellen?")) {
        RestoreLinkPoiPic(id);
        showPoiPicLinks(pictoken, title);
    }
}

/**
 * gets all pois for certain pictures
 * @param {string} pictoken picture identifier
 * @return {Array} structured request result
 */
function getPoisForPicture(pictoken) {
    return sendApiRequest({type: 'lpp', pictoken: pictoken}, false);
}

/**
 * send request to delete a certain poi pic link
 * @param {int} id id of link
 * @param {string} pictoken pictoken of pic whose story is validated
 * @param {string} title pic title
 */
function deletePicPoiListMaterialLink(id, pictoken, title) {
    if (confirm("Verknüpfung zwischen Bild und Interessenpunkt wirklich aufheben?")) {
        sendApiRequest({type: 'dpp', id: id}, false);
        showPoiPicLinks(pictoken, title);
    }
}

/**
 * send request to verify a certain poi pic link
 * @param {int} id id of link
 * @param {string} title pic title
 * @param {string} pictoken pictoken of pic whose story is validated
 */
function verifyPicPoiLinkMaterial(id, pictoken, title) {
    if (confirm("Verknüpfung zwischen Bild und Interessenpunkt wirklich validieren?")) {
        sendApiRequest({type: 'vpp', id: id}, false);
        showPoiPicLinks(pictoken, title);
    }
}

/**
 * sends data for new poi pic link to backend
 * @param {string} pictoken identifier of picture
 * @param {string} title title of picture
 */
function addPicPoiLinkMaterialList(pictoken, title) {
    var pics = new Array(pictoken)
    var poi = document.getElementById('LinkPoiPicSelect').value;
    sendApiRequest({type: 'app', data: pics, poi: poi}, false);
    showPoiPicLinks(pictoken, title);
}

/**
 * validates poi seat counter
 * @param {int} seat_id id of seat count
 * @param {int} POi_id poi to show afterwards
 */
function validatePoiSeats(seat_id, POi_id) {
    if (confirm("Sitzplatzanzahl des Kinos wirklich validieren?")) {
        sendApiRequest({type: 'vsc', SEATID: seat_id}, false);
        ShowMoreSeats(POi_id);
    }
}

/**
 * Deletes Seats of POI from Database
 * @param {int} ID Unique identification of Seats in Table
 * @param {int} POiid ID of POI
 */
function deletePoiSeatsFromList(ID, POiid) {
    sendApiRequest({type: 'dsc', IDent: ID}, false);
    ShowMoreSeats(POiid);
}

/**
 * validates poi seat counter
 * @param {int} cinema_id id of seat count
 * @param {int} POi_id poi to show afterwards
 */
function validatePoiCinemas(cinema_id, POi_id) {
    if (confirm("Saalanzahl des Kinos wirklich validieren?")) {
        sendApiRequest({type: 'vcc', CINEMAID: cinema_id}, false);
        ShowMoreCinemas(POi_id);
    }
}

/**
 * Deletes Seats of POI from Database
 * @param {int} ID Unique identification of Seats in Table
 * @param {int} POiid ID of POI
 */
function deletePoiCinemasFromList(ID, POiid) {
    sendApiRequest({type: 'dcc', IDent: ID}, false);
    ShowMoreCinemas(POiid);
}

/**
 * loads a captcha code from API
 */
function loadCaptchaContact() {
    var json = {
        type: "cpa"
    };
    var image = sendApiRequest(json, false).data;
    document.getElementById('Captcha').src = image;
}

/**
 * sends a user defined contact message to the api
 */
function submitContact() {
    var captcha = document.getElementById('captchaReturn').value;
    var title = document.getElementById('ContactTitle').value;
    var message = document.getElementById('ContactMessage').value;
    var mail = "";
    var json;
    var ErrorCheckbox = document.getElementById('errorCheckbox').checked;
    if (document.getElementById('ContactMail') !== null) {
        mail = document.getElementById('ContactMail').value;
        json = {
            type: "cmg",
            cap: captcha,
            title: title,
            msg: message,
            email: mail
        };
    } else {
        json = {
            type: "cmg",
            cap: captcha,
            title: title,
            msg: message
        };
    }
    if (ErrorCheckbox) {
        json.title = "[Fehlermeldung] " + json.title;
    }
    var result = sendApiRequest(json, false);
    if (result.code === 2) {
        if (result.msg.cap){
            var cssclass = document.getElementById('captchaReturn').getAttribute("class");
            document.getElementById('captchaReturn').setAttribute("class", cssclass + " border-danger bg-danger");
            document.getElementById('captchaReturn').setAttribute('data-toggle', 'tooltip');
            document.getElementById('captchaReturn').setAttribute('data-placement', 'top');
            document.getElementById('captchaReturn').setAttribute('title', 'Captcha ist falsch');
        } else {
            document.getElementById('captchaReturn').setAttribute("class", "form-control textinput float-left ml-2");
            document.getElementById('captchaReturn').removeAttribute('data-toggle');
            document.getElementById('captchaReturn').removeAttribute('data-placement');
            document.getElementById('captchaReturn').removeAttribute('title');
        }
        setErrorOnInputContact("ContactTitle", result.msg.title, "Titel bitte ausfüllen.");
        setErrorOnInputContact("ContactMessage", result.msg.msg, "Nachrichteninhalt bitte ausfüllen.");
        if (document.getElementById('ContactMail') !== null) {
            setErrorOnInputContact("ContactMail", result.msg.mail, "Mailadresse bitte angeben.");
        }
    } else if (result.code === 0) {
        document.getElementById('contactSuccessMessage').removeAttribute("class");
        var CssClassMain = "form-control textinput border-success";
        var CssClassCap = "form-control textinput float-left ml-2 border-success";
        document.getElementById('ContactTitle').setAttribute("class", CssClassMain);
        document.getElementById('ContactMessage').setAttribute("class", CssClassMain);
        document.getElementById('captchaReturn').setAttribute("class", CssClassCap);
        if (document.getElementById('ContactMail') !== null) {
            document.getElementById('ContactMail').setAttribute("class", CssClassMain);
        }
        document.getElementById('captchaReturn').value = "";
        document.getElementById('ContactTitle').value = "";
        document.getElementById('ContactMessage').value = "";
        if (document.getElementById('ContactMail') !== null) {
            document.getElementById('ContactMail').value = "";
        }
        document.getElementById('errorCheckbox').checked = false;
        loadCaptchaContact();
    }
}

/**
 *
 * @param {string} elementid identifier of html-element
 * @param {boolean} state state of error
 * @param {string} tootip text of tooltip
 */
function setErrorOnInputContact(elementid, state, tootip){
    if(state) {
        var cssclass = document.getElementById(elementid).getAttribute("class");
        document.getElementById(elementid).setAttribute("class", cssclass + " border-danger bg-danger");
        document.getElementById(elementid).setAttribute('data-toggle', 'tooltip');
        document.getElementById(elementid).setAttribute('data-placement', 'top');
        document.getElementById(elementid).setAttribute('title', tootip);
    } else {
        document.getElementById(elementid).setAttribute("class", "form-control textinput");
        document.getElementById(elementid).removeAttribute('data-toggle');
        document.getElementById(elementid).removeAttribute('data-placement');
        document.getElementById(elementid).removeAttribute('title');
    }
}

/**
 * final deletion of picture poi link
 * @param {int} id Identifier of Link
 */
function finalDeleteLinkPoiPic(id) {
    sendApiRequest({type: 'fdp', IDent: id}, false);
}

/**
 * restores a link between a poi and a pic
 * @param {int} id Identifier of Link
 */
function RestoreLinkPoiPic(id) {
    sendApiRequest({type: 'rdp', IDent: id}, false);
}

/**
 * final deletion of picture poi link
 * @param {int} id Identifier of Link
 */
function finalDeleteLinkPoiStory(id) {
    sendApiRequest({type: 'fsp', IDent: id}, false);
}

/**
 * restores a link between a poi and a pic
 * @param {int} id Identifier of Link
 */
function RestoreLinkPoiStory(id) {
    sendApiRequest({type: 'rsp', IDent: id}, false);
}

/**
 * finally deletes a picture
 * @param {string} token identifier of picture
 */
function finalDeletePicture(token) {
    if (confirm('Bild wirklich löschen?')) {
        sendApiRequest({type: "fpc", IDent: token}, true);
    }
}

/**
 * restores a story
 * @param {string} token identifier of picture
 */
function restorePicture(token) {
    if (confirm('Bild wirklich wiederherstellen?')) {
        sendApiRequest({type: "rpc", IDent: token}, true);
    }
}

/**
 * gets the value of a cookie
 * @param {string} name name of the cookie
 * @return {string} value of the cookie
 */
function getCookie(name) {
    var cookies = document.cookie;
    var cookies_ar = cookies.split(";");
    for (var i = 0; i < cookies_ar.length; i++) {
        if (cookies_ar[i].startsWith(" ")) {
            cookies_ar[i] = cookies_ar[i].substring(1);
        }
        if (cookies_ar[i].startsWith(name)) {
            var index = cookies_ar[i].indexOf("=")
            return cookies_ar[i].substring(index + 1);
        }
    }
}

/**
 * checks if a certain cookie is set
 * @param {string} name name of cookie to check
 * @return {boolean} true if cookie is availabel
 */
function testCookie(name) {
    return document.cookie.includes(name);
}

/**
 * sets a cookie
 * @param {string} name name of cookie
 * @param {int|string} value data of cookie
 * @param {int} exdays days till expiration
 */
function setCookie(name, value, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

/**
 * deletes a cookie
 * @param {string} name name of cookie
 */
function deleteCookie(name) {
    document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
}

jQuery(function($){
    $('table.do-reflow:not(.reflow-ratio)').reflowTable({thead:'tr:first-child'});
    $('table.do-reflow.reflow-ratio.reflow-20').reflowTable({widthRatio:'20',thead:'tr:first-child'});
    $('table.do-reflow.reflow-ratio.reflow-30').reflowTable({widthRatio:'30',thead:'tr:first-child'});
    $('table.do-reflow.reflow-ratio.reflow-40').reflowTable({widthRatio:'40',thead:'tr:first-child'});
    $('table.do-reflow.reflow-ratio.reflow-60').reflowTable({widthRatio:'60',thead:'tr:first-child'});
    $('table.do-reflow.reflow-ratio.reflow-70').reflowTable({widthRatio:'70',thead:'tr:first-child'});
    $('table.do-reflow.reflow-ratio.reflow-80').reflowTable({widthRatio:'80',thead:'tr:first-child'});
})
