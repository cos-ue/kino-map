operators = {};
Seats = {};
Cinemas = {};
names = {};
histAddress = {};
Karte;
Karte2;
Sources = {};
SourceRelations = sendApiRequest({type: 'grs'}, false).data;
SourceTypes = sendApiRequest({type: 'gts'}, false).data;

color = redIcon;
mark;

Spielstaette = L.layerGroup();
latlng = [0, 0];

mark2;
data = sendApiRequest({type: "gpu"}, false).data;
minimap = false;
guestmode = sendApiRequest({type: "gue"}, false).data;
deletedPOI = false;

/**
 * loads map when called
 */
function loadMap() {
    Karte = L.map('Kartenframe').setView([52.0763, 11.1618], 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19, minZoom: 7,
        'attribution': 'Kartendaten &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> Mitwirkende',
        'useCache': true
    }).addTo(Karte);
    L.Control.geocoder().addTo(Karte);
    loadData();
    if (guestmode === false) {
        Karte.on('click', onClick);
    }
    $('.leaflet-control-geocoder-form').children().attr('placeholder', 'Adresse oder Kino suchen ...');
    //activate scroll event on list
    $('.leaflet-control-geocoder-alternatives').on('mousewheel wheel', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const $t = $(this);
        $t.stop().animate({
            scrollTop: $t.scrollTop() + e.originalEvent.deltaY
        }, 100);
    });
    $(function () {
        $('[data-toggle="popover"]').popover()
    })
}

/**
 * loads minimap if required
 */
function loadMinimap() {
    Karte2 = new L.Map('Kartemini', {center: [52.0763, 11.1618], zoom: 7});
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19, minZoom: 7,
        'attribution': 'Kartendaten &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> Mitwirkende',
        'useCache': true
    }).addTo(Karte2);
    Karte2.on('click', onClick);
    minimap = true;
}

/**
 * loads data into main map
 */
function loadData() {
    for (var i = 0; i < data.length; i++) {
        addMarker(data[i]);
    }
    Spielstaette.addTo(Karte);
}

/**
 * places marker if map is clicked
 * @param {Map} e map on which marker should be displayed afterwards
 */
function onClick(e) {
    placeMarker(e.latlng);
}

/**
 * places a marker on map after data for markers is loaded
 * @param {array} data required intel to place marker with small pop-up
 */
function addMarker(data) {
    var category;
    var place = true;
    if (typeof (data.lat) != 'undefined') {
        var color = null;
        switch (data.category) {
            case '0':
                if (data.validated == false && data.validatedByUser == false && data.deleted) {
                    if (document.getElementById('unvalidatedOnMapShow').checked == false) {
                        place = false;
                    }
                    color = deletedRedIcon;
                } else if (data.validated == false && data.validatedByUser == true && data.deleted) {
                    if (document.getElementById('partValidatedOnMapShow').checked == false) {
                        place = false;
                    }
                    color = deletedBlueIcon;
                } else if (data.validated && data.deleted) {
                    if (document.getElementById('validatedOnMapShow').checked == false) {
                        place = false;
                    }
                    color = deletedGreenIcon;
                } else if (data.validated == false && data.validatedByUser == false) {
                    if (document.getElementById('unvalidatedOnMapShow').checked == false) {
                        place = false;
                    }
                    color = redIcon;
                    if (data.blog) {
                        color = redIconBlog;
                    }
                } else if (data.validated == false && data.validatedByUser == true) {
                    if (document.getElementById('partValidatedOnMapShow').checked == false) {
                        place = false;
                    }
                    color = blueIcon;
                    if (data.blog) {
                        color = blueIconBlog;
                    }
                } else if (data.validated) {
                    if (document.getElementById('validatedOnMapShow').checked == false) {
                        place = false;
                    }
                    color = greenIcon;
                    if (data.blog) {
                        color = greenIconBlog;
                    }
                }
                if (place) {
                    color = new L.Icon(Object.assign({}, color.options, {className: 'marker-data-id-' + data.poi_id}));
                    var marker = L.marker([data.lat, data.lng], {icon: color});
                    Spielstaette.addLayer(marker);
                    category = "Spielstätte";
                }
                break;
            default:
                break;
        }
        if (place) {
            var address;
            if (data.current_address == null) {
                address = '-';
            } else {
                address = data.current_address;
            }
            var datestr = "";
            if (data.start == null) {

            } else {
                datestr = data.start;
            }
            if (data.end == null) {
                if (data.duty) {
                    datestr += ' bis heute';
                } else {
                    datestr += ' bis k.A.';
                }
            } else {
                datestr += ' bis ' + data.end;
            }
            var popup = '<h4 style="font-weight: bold">' + data.name + '</h4>';
            if (data.deleted) {
                popup += '<hr><span style="font-size: 0.9rem;" class="deleted">Dieser Interessenpunkt ist gelöscht.</span><hr>';
            }
            popup += '<span style="font-size: 0.9rem">' + datestr + '</br>' + address + '</br>' + category;
            if (data.blog) {
                popup += '</br><a class="btn btn-sm btn-primary mt-2" href="' + data.bloglink + '" style="align-content: center; font-size: 0.9rem" target="_blank">Blogeintrag</a>'
            }
            popup += '</span><form name="mehr" method="POST" enctype="multipart/form-data" accept-charset= "utf-8" >'
                + '<button type="button" onclick= "showMorePOI(' + data.poi_id + ')" class="btn btn-sm btn-primary btn-important mt-2" style="align-content: center; font-size: 0.9rem">Mehr Anzeigen</button>'
                + '</form>';
            marker.bindPopup(popup);
        }
    }
}

/**
 * refreshes Map with data within certain timerange
 */
function refreshMap() {
    Spielstaette.clearLayers();
    data = sendApiRequest({type: "gpu"}).data;
    for (var i = 0; i < data.length; i++) {
        var start = data[i]["start"];
        var end = data[i]["end"];
        if (yearsSelected.end === yearsSelected.maxYear && yearsSelected.start === yearsSelected.minYear) {
            addMarker(data[i]);
        } else if (yearsSelected.end >= new Date().getFullYear() && data[i].duty) {
            addMarker(data[i]);
        } else if (start <= yearsSelected.end && start >= yearsSelected.start && start !== null || end <= yearsSelected.end && end >= yearsSelected.start && end !== null ||
            start <= yearsSelected.start && end >= yearsSelected.end) {
            addMarker(data[i]);
        }
    }
}


/**
 * opens a large modal and shows all data of POI
 * @param {int} poi_id poi whose extended information should be displayed
 */
function showMorePOI(poi_id) {
    var category = loadBasePoi(poi_id);
    deletedPOI = category.del;
    loadComments(poi_id);
    if (category.del) {
        document.getElementById('addMorePictureDiv').style.display = 'none';
        document.getElementById('validateTimespanDiv').style.display = 'none';
        document.getElementById('validateCurrentAddressDiv').style.display = 'none';
        document.getElementById('validateCinematypeDiv').style.display = 'none';
        document.getElementById('validateHistoryDiv').style.display = 'none';

        document.getElementById('addNamesDiv').style.display = 'none';
        document.getElementById('addOperatorDiv').style.display = 'none';
        document.getElementById('addSeatsDiv').style.display = 'none';
        document.getElementById('addCinemaDiv').style.display = 'none';
        document.getElementById('addHistAddressDiv').style.display = 'none';
        document.getElementById('addSourceDiv').style.display = 'none';
        if (document.getElementById('addStoryDiv') != null) {
            document.getElementById('addStoryDiv').style.display = 'none';
        }
        document.getElementById('addCommentDiv').style.display = 'none';
    } else if (guestmode === false) {
        document.getElementById('addMorePictureDiv').style.display = '';
        document.getElementById('validateTimespanDiv').style.display = '';
        document.getElementById('validateCurrentAddressDiv').style.display = '';
        document.getElementById('validateCinematypeDiv').style.display = '';
        document.getElementById('validateHistoryDiv').style.display = '';

        document.getElementById('addNamesDiv').style.display = '';
        document.getElementById('addOperatorDiv').style.display = '';
        document.getElementById('addSeatsDiv').style.display = '';
        document.getElementById('addCinemaDiv').style.display = '';
        document.getElementById('addHistAddressDiv').style.display = '';
        document.getElementById('addSourceDiv').style.display = '';
        if (document.getElementById('addStoryDiv') != null) {
            document.getElementById('addStoryDiv').style.display = '';
        }
        document.getElementById('addCommentDiv').style.display = '';
    }
    switch (category.cat) {
        case 'sp':	//Spielstätte
            ShowMoreNames(poi_id);
            ShowMoreOperators(poi_id);
            ShowMoreSeats(poi_id);
            ShowMoreCinemas(poi_id);
            ShowMoreHistoricalAddresses(poi_id);
            ShowMoreSources(poi_id);
            if (sendApiRequest({type: "cse"}, false).data) {
                ShowMoreStories(poi_id);
                SetShowMoreStoryLinkOptions(poi_id);
            }
            ShowMoreMainPic(poi_id);
            ShowMoreAdditionalPictures(poi_id);
            break;
    }
    deleteCookie('OpenComment');
    deleteCookie('OpenPoi');
}

/**
 * loads basedata of poi
 * @param {int} poiid id of poi
 * @return {array} category ('cat') and state of deletion of poi ('del')
 */
function loadBasePoi(poiid) {
    var response = sendApiRequest({poi_id: poiid, type: "smd"}, false).data;
    document.getElementById('showMoreTitle').innerHTML = '';
    if (response.deleted) {
        document.getElementById('showMoreTitle').innerHTML += '<span class="deleted">Gelöscht: </span>'
    }
    document.getElementById('showMoreTitle').innerHTML += response.poi_name;
    document.getElementById('showMoreTitle').innerHTML += " (Spielstätte)";
    if (response.blog != null) {
        document.getElementById('ModalShowMoreBlogEntry').innerHTML = '<a class="btn btn-important" href="' + response.blog + '" target="_blank">Blogeintrag</a>';
    } else {
        document.getElementById('ModalShowMoreBlogEntry').innerHTML = '';
    }
    var Btn = "";
    if (response.deleted === false && !guestmode) {
        if (response.validatable) {
            if (response.validated) {
                Btn += '<button class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top" title="Validiert">\n' +
                    '                                        <img src="images/check-solid-green.svg" width="15px" style="margin-top: -2px">\n' +
                    '                                    </button>';
            } else {
                Btn += '<button onclick="validatePoi(' + poiid + ');" class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top" title="Validieren">\n' +
                    '                                        <img src="images/check-solid.svg" width="15px" style="margin-top: -2px">\n' +
                    '                                    </button>';
            }
        }
        if (response.editable) {
            Btn += '<button onclick="location.href=\'' + response.editLink + '\';" class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top" title="Bearbeiten">\n' +
                '                                        <img src="images/pencil-alt-solid.svg" width="15px" style="margin-top: -2px">\n' +
                '                                    </button>';
        }
        if (response.deletable) {
            Btn += '<button onclick="deletePoiMap(' + poiid + ');" class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top" title="Löschen">\n' +
                '                                        <img src="images/trash-alt-solid.svg" width="15px" style="margin-top: -2px">\n' +
                '                                    </button>';
        }
        document.getElementById('showMoreTitle').innerHTML += " " + Btn;
    } else if (response.finalDelete && response.deleted && !guestmode) {
        Btn += '<button onclick="finalDeletePoiMap(' + poiid + ');" class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top" title="Löschen">\n' +
            '                                    <img src="images/trash-alt-solid-red.svg" width="15px" style="margin-top: -2px">\n' +
            '                                </button>';
        Btn += '<button onclick="restorePoiMap(' + poiid + ');" class="btn btn-sq btn-secondary m-1" data-toggle="tooltip" data-placement="top" title="Wiederherstellen">\n' +
            '                                    <img src="images/trash-restore-solid-dark-green.svg" width="15px" style="margin-top: -2px">\n' +
            '                                </button>';
        document.getElementById('showMoreTitle').innerHTML += " " + Btn;
    }
    if (document.getElementById('ValidateBtnTimeSpanMap') !== null) {
        document.getElementById('ValidateBtnTimeSpanMap').innerHTML = '<img src="images/check-solid.svg" width="15px" style="margin-top: -2px">';
        document.getElementById('ValidateBtnTimeSpanMap').setAttribute('onclick', '');
    }
    if (document.getElementById('ValidateBtnHistoryMap') !== null) {
        document.getElementById('ValidateBtnHistoryMap').innerHTML = '<img src="images/check-solid.svg" width="15px" style="margin-top: -2px">';
        document.getElementById('ValidateBtnHistoryMap').setAttribute('onclick', '');
    }
    if (document.getElementById('ValidateBtnCurrentAddressMap') !== null) {
        document.getElementById('ValidateBtnCurrentAddressMap').innerHTML = '<img src="images/check-solid.svg" width="15px" style="margin-top: -2px">';
        document.getElementById('ValidateBtnCurrentAddressMap').setAttribute('onclick', '');
    }
    if (document.getElementById('ValidateBtnCinemaType') !== null) {
        document.getElementById('ValidateBtnCinemaType').innerHTML = '<img src="images/check-solid.svg" width="15px" style="margin-top: -2px">';
        document.getElementById('ValidateBtnCinemaType').setAttribute('onclick', '');
    }
    var timespanText = "";
    if ((response.start == null || response.start === '') && (response.end == null || response.end === '')) {
        if (response.duty) {
            timespanText = "bis heute";
        } else {
            timespanText = "";
        }
    } else if (response.end == null || response.end === '') {
        if (response.duty) {
            timespanText = response.start + " bis heute";
        } else {
            timespanText = response.start + " bis k.A.";
        }
    } else if ((response.start == null || response.start === '') && (response.end != null && response.end !== '')) {
        timespanText = "bis " + response.end;
    } else {
        timespanText = response.start + " bis " + response.end;
    }
    if (document.getElementById('ValidateBtnTimeSpanMap') !== null) {
        if (response.timespan_validate === true) {
            document.getElementById('ValidateBtnTimeSpanMap').innerHTML = '<img src="images/check-solid-green.svg" width="15px" style="margin-top: -2px">';
        } else {
            document.getElementById('ValidateBtnTimeSpanMap').setAttribute('onclick', 'validateTimeSpanPOI(' + poiid + ')');
        }
    }
    document.getElementById('ModalShowMoreTimespan').innerHTML = timespanText;
    if (response.City == null) {
        response.City = ""
    }
    if (response.Postalcode == null) {
        response.Postalcode = ""
    }

    if (response.Housenumber == null) {
        response.Housenumber = ""
    }

    if (response.Streetname == null) {
        response.Streetname = ""
    }
    if ((response.Streetname === "") || (response.Housenumber === "")) {
        document.getElementById('ModalShowMoreCurrentAddress').innerHTML = response.Postalcode + " " + response.City;
    } else if ((response.Postalcode === "") || (response.City === "")) {
        document.getElementById('ModalShowMoreCurrentAddress').innerHTML = response.Streetname + " " + response.Housenumber;
    } else if ((response.Postalcode === "") && (response.City === "") && (response.Streetname === "") && (response.Housenumber === "")) {
        document.getElementById('ModalShowMoreCurrentAddress').innerHTML = "";
    } else {
        document.getElementById('ModalShowMoreCurrentAddress').innerHTML = response.Streetname + " " + response.Housenumber + ", " + response.Postalcode + " " + response.City;
    }
    if (document.getElementById('ValidateBtnCurrentAddressMap') !== null) {
        if (response.currAddr_validate == true) {
            document.getElementById('ValidateBtnCurrentAddressMap').innerHTML = '<img src="images/check-solid-green.svg" width="15px" style="margin-top: -2px">';
        } else {
            document.getElementById('ValidateBtnCurrentAddressMap').setAttribute('onclick', 'validateCurrentAddressPOI(' + poiid + ')');
        }
    }
    document.getElementById('ModalShowMoreHistoyEntry').innerHTML = response.history;
    if (document.getElementById('ValidateBtnHistoryMap') !== null) {
        if (response.history_validate == true) {
            document.getElementById('ValidateBtnHistoryMap').innerHTML = '<img src="images/check-solid-green.svg" width="15px" style="margin-top: -2px">';
        } else {
            document.getElementById('ValidateBtnHistoryMap').setAttribute('onclick', 'validateHistoryPOI(' + poiid + ')');
        }
    }
    document.getElementById('ModalShowMoreCinemaType').innerHTML = response.type;
    if (document.getElementById('ValidateBtnCinemaType') !== null) {
        if (response.type_validate == true) {
            document.getElementById('ValidateBtnCinemaType').innerHTML = '<img src="images/check-solid-green.svg" width="15px" style="margin-top: -2px">';
        } else {
            document.getElementById('ValidateBtnCinemaType').setAttribute('onclick', 'validateTypePOI(' + poiid + ')');
        }
    }
    $('#MarkerModalBig').modal();
    switch (response.category) {
        case '0':
            return {'cat': 'sp', 'del': response.deleted};
    }
    return "";
}

/**
 * loads comments for certain poi
 * @param {int} poiid id of poi
 */
function loadComments(poiid) {
    var response = sendApiRequest({poi_id: poiid, type: "lcp"}, false).data;
    if (!guestmode) {
        document.getElementById('poi_id_comment_map').setAttribute("value", poiid);
        document.getElementById('poi_content_comment_map').innerHTML = "";
        document.getElementById('comments').innerHTML = '';
        if (Object.keys(response.comments).length >= 1) {
            document.getElementById('comments').innerHTML = '<div>'
                + '<h5 style="color: #d2d2d2; margin-bottom: -40px; margin-top: 10px">Kommentare</h5>'
                + '<div style="display: inline-block" class="col-12">';
            for (var i = 0; i < Object.keys(response.comments).length; i++) {
                comments[i] = response.comments[i];
                comments[i].kinoname = response.poi_name;
                comments[i].poiid = poiid;
                var long = false;
                var text = "";
                if (comments[i].content.length < 396) {
                    text = comments[i].content;
                } else {
                    text = comments[i].content.substring(0, 396) + " ...";
                    long = true;
                }
                var str = '<div id="comment_' + comments[i].comment_id + '" class="';
                if (comments[i].deleted) {
                    str += 'deleted-div';
                }
                str += '">';
                str += '<p class="col-11" style="color: white; margin-top: 30px">' + text + '</p>'
                    + '<p class="ml-4" style="font-size: 0.8em; color: #c2c2c2">' + comments[i].name + ' – ' + comments[i].timestamp + '</p>'
                    + '<div class="float-right">';
                if (long) {
                    str += '<button class="btn btn-secondary " data-toggle="tooltip" data-placement="top" title="Vollständig anzeigen" onclick="showLongComment(' + i + ')" style="align-content: center; margin-left: -100px; margin-top: -100px; margin-right: 60px;"><img src="images/expand-solid-white.svg" width="15px"></button>';
                }
                if ((response.deleteComments || comments[i].deletable) && !deletedPOI && !comments[i].deleted) {
                    str += '<button onclick="$(this).tooltip(\'hide\'); this.blur(); if(confirm(\'Kommentar wirklich löschen?\')){deleteCommentMap(\'' + comments[i].comment_id + '\', ' + poiid + ');}" ' +
                        'type="submit" class="btn btn-secondary delete-button" data-toggle="tooltip" data-placement="top" ' +
                        'title="Löschen" style="align-content: center; margin-left: -50px; margin-top: -100px">' +
                        '<img src="images/trash-alt-solid.svg" alt="Löschen" width="15px">' +
                        '</button>';
                } else if ((response.deleteComments || comments[i].deletable) && !deletedPOI && comments[i].deleted) {
                    str += "<button onclick=\"$(this).tooltip('hide'); this.blur();finalDeleteCommentPOI(" + comments[i].comment_id + "," + poiid + ");\" class=\"btn btn-secondary delete-button\" style=\"align-content: center; margin-left: -50px; margin-top: -100px\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Endgültig Löschen\"><img src=\"images/trash-alt-solid-red.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
                    str += "<button onclick=\"$(this).tooltip('hide'); this.blur();RestoreCommentPOI(" + comments[i].comment_id + "," + poiid + ");\" class=\"btn btn-secondary delete-button\" style=\"align-content: center; margin-left: 10px; margin-right: 10px; margin-top: -100px\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Wiederherstellen\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
                }
                str += '</div></div>';
                document.getElementById('comments').innerHTML += str;
                if (i < Object.keys(response.comments).length - 1) {
                    document.getElementById('comments').innerHTML += '<hr>';
                }
            }
            document.getElementById('comments').innerHTML += "</div></div>";
        }
    }
    if (focusComment >= 0) {
        var delayInMilliseconds = 300;
        setTimeout(function () {
            var elementname = 'comment_' + focusComment;
            var elmnt = document.getElementById(elementname);
            elmnt.scrollIntoView();
            focusComment = -1;
        }, delayInMilliseconds);
    }
}

/**
 * final deletion of a comment
 * @param {int} id identifier of comment
 * @param {int} poiid identifier of poi
 */
function finalDeleteCommentPOI(id, poiid) {
    if (confirm('Kommentar wirklich löschen?') === false) {
        return;
    }
    sendApiRequest({type: 'fcp', IDent: id}, false);
    loadComments(poiid);
}

/**
 * restore a comment of poi
 * @param {int} id identifier of comment
 * @param {int} poiid identifier of poi
 */
function RestoreCommentPOI(id, poiid) {
    if (confirm('Kommentar wirklich wiederherstellen?') === false) {
        return;
    }
    sendApiRequest({type: 'rcp', IDent: id}, false);
    loadComments(poiid);
}

/**
 * loads additional pictures for poi
 * @param {int} poiid id of poi
 */
function ShowMoreAdditionalPictures(poiid) {
    var response = sendApiRequest({poi_id: poiid, type: "apl"}, false).data;
    if (Object.keys(response).length > 0) {
        document.getElementById('slideshowShowMore').setAttribute('class', 'carousel slide');
        var html2 = "";
        var inc = 2;
        if (guestmode || deletedPOI) {
            inc = 3;
        }
        for (var i2 = 0; i2 < response.length; i2 = i2 + inc) {
            html2 += '<div class="carousel-item';
            if (i2 === 0) {
                html2 += ' active';
            }
            html2 += '">'
            if (guestmode || deletedPOI) {
                html2 += '<div class="d-flex flex-row justify-content-center">'
                for (var j = 0; j < inc; j++) {
                    if (i2 + j < response.length) {
                        var title = "";
                        if (response[i2 + j].description !== "" && response[i2 + j].description !== null) {
                            title = '<b>' + response[i2 + j].title + '</b>' + ' ' + response[i2 + j].description;
                        }
                        if (response[i2 + j].source !== "" && response[i2 + j].source !== null) {
                            title += ' Quelle: ' + response[i2 + j].source + ' (' + response[i2 + j].sourcename + ')';
                        }
                        html2 += '<div class="p-2"><a href="' + response[i2 + j].fullsize + '" data-lightbox="ShowMore" data-title="' + title + '" title="Größer anzeigen" data-toggle="tooltip" data-placement:"top">';
                        html2 += '<img class="d-block align-content-center mx-auto';
                        if (deletedPOI) {
                            html2 += ' deleted-pic';
                        }
                        html2 += '" style="height: 100px;" src="' + response[i2 + j].preview + '" alt="slide ' + (i2 + j) + '"></a></div>';
                    }
                }
            } else {
                html2 += '<div class="d-flex flex-row justify-content-center">'
                for (var j = 0; j < inc; j++) {
                    if (i2 + j < response.length) {
                        var title = "";
                        if (response[i2 + j].description !== "" && response[i2 + j].description !== null) {
                            title = '<b>' + response[i2 + j].title + '</b>' + ' ' + response[i2 + j].description;
                        }
                        if (response[i2 + j].source !== "" && response[i2 + j].source !== null) {
                            title += ' Quelle: ' + response[i2 + j].source + ' (' + response[i2 + j].sourcename + ')';
                        }
                        html2 += '<div class="p-2">';
                        html2 += '<div class="card text-center card-poi no-border';
                        if (response[i2 + j].deleted) {
                            html2 += ' deleted-div'
                        }
                        html2 += '">';
                        var IdAddPictureLink = "poiAdditionalPic" + response[i2 + j].ppid;
                        html2 += '<a class="poi-card-link" id="' + IdAddPictureLink + '" href="' + response[i2 + j].fullsize + '" data-lightbox="ShowMore" data-title="' + title + '" title="Größer anzeigen" data-toggle="tooltip" style="height: 20vh" data-placement:"top"><img class="card-img-top img-carousel" src="' + response[i2 + j].preview + '" alt="slide ' + (i2 + j) + '">';
                        html2 += '<div class="card-img-overlay poi-card-overlay d-flex align-items-end justify-content-center">';
                        if (!response[i2 + j].deleted) {
                            if (response[i2 + j].validated) {
                                html2 += '<button class="card-link btn btn-secondary btn-sq btn-poi-card" onmouseenter="disableCardLinks(\'' + IdAddPictureLink + '\');" onmouseleave="enableCardLinks(\'' + IdAddPictureLink + '\')" data-toggle="tooltip" data-placement="top" data-trigger="hover" title="Validiert" id="validatedBtn' + response[i2 + j].ppid + '" onclick="blurButton(\'validatedBtn' + response[i2 + j].ppid + '\');"><img src="images/check-solid-green.svg" width="15px" style="margin-top: -2px"></button>';
                            } else {
                                html2 += '<button class="card-link btn btn-secondary btn-sq btn-poi-card" onmouseenter="disableCardLinks(\'' + IdAddPictureLink + '\');" onmouseleave="enableCardLinks(\'' + IdAddPictureLink + '\')" data-toggle="tooltip" data-placement="top" data-trigger="hover" title="Validieren" id="validateBtn' + response[i2 + j].ppid + '" onclick="blurButton(\'validateBtn' + response[i2 + j].ppid + '\'); verifyPicPoiLink(' + response[i2 + j].ppid + ',' + poiid + ')"><img src="images/check-solid.svg" width="15px" style="margin-top: -2px"></button>';
                            }
                            if (response[i2 + j].deletable) {
                                html2 += '<button class="card-link btn btn-secondary btn-sq btn-poi-card" onmouseenter="disableCardLinks(\'' + IdAddPictureLink + '\');" onmouseleave="enableCardLinks(\'' + IdAddPictureLink + '\')" data-toggle="tooltip" data-placement="top" data-trigger="hover" title="Verknüpfung Löschen" id="deleteBtn' + response[i2 + j].ppid + '" onclick="blurButton(\'deleteBtn' + response[i2 + j].ppid + '\'); deletePicPoiLink(' + response[i2 + j].ppid + ',' + poiid + ');"><img src="images/trash-alt-solid.svg" width="15px"></button>';
                            }
                        } else if (response[i2 + j].deleted && response[i2 + j].deletable && !deletedPOI) {
                            html2 += "<button onclick=\"finalDeleteLinkPoiPicMapWrapper(" + response[i2 + j].ppid + "," + poiid + ")\" class=\"card-link btn btn-secondary btn-sq btn-poi-card\" onmouseenter=\"disableCardLinks('" + IdAddPictureLink + "');\" onmouseleave=\"enableCardLinks('" + IdAddPictureLink + "')\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Endgültig Löschen\"><img src=\"images/trash-alt-solid-red.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
                            if (!response[i2 + j].restrictions) {
                                html2 += "<button onclick=\"RestoreLinkPoiPicMapWrapper(" + response[i2 + j].ppid + "," + poiid + ")\" class=\"card-link btn btn-secondary btn-sq btn-poi-card\" onmouseenter=\"disableCardLinks('" + IdAddPictureLink + "');\" onmouseleave=\"enableCardLinks('" + IdAddPictureLink + "')\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Wiederherstellen\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
                            } else {
                                html2 += "<button class=\"card-link btn btn-secondary btn-sq btn-poi-card disabled\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Abhängigkeiten gelten als gelöscht.\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
                            }
                        }
                        html2 += '</div></a></div></div>';
                    }
                }
            }
            html2 += '</div></div></div>';
            document.getElementById('slideshowShowMore').setAttribute('class', 'carousel slide');
        }
        document.getElementById('ShowMoreCarouselItems').innerHTML = html2;
    } else {
        document.getElementById('ShowMoreCarouselItems').innerHTML = "";
        document.getElementById('slideshowShowMore').setAttribute('class', 'carousel slide hidden');
    }
    if (!guestmode) {
        document.getElementById('SelectMorePicturesShowMore').setAttribute('onclick', 'openSelectMorePicturesOnMap(' + poiid + ')');
    }
}

/**
 * a wrapper around finalDeleteLinkPoiPic() to reload part of show more modal
 * @param {int} id Identifier of Link
 * @param {int} poiid Identifier of poi
 */
function finalDeleteLinkPoiPicMapWrapper(id, poiid) {
    if (confirm('Verknüpfung wirklich löschen?') === false) {
        return;
    }
    finalDeleteLinkPoiPic(id);
    ShowMoreAdditionalPictures(poiid);
}

/**
 * a wrapper around RestoreLinkPoiPic() to reload part of show more modal
 * @param {int} id Identifier of Link
 * @param {int} poiid Identifier of poi
 */
function RestoreLinkPoiPicMapWrapper(id, poiid) {
    if (confirm('Verknüpfung wirklich wiederherstellen?') === false) {
        return;
    }
    RestoreLinkPoiPic(id);
    ShowMoreAdditionalPictures(poiid);
}

/**
 * loads main picture for poi
 * @param {int} poiid id of poi
 */
function ShowMoreMainPic(poiid) {
    var response = sendApiRequest({poi_id: poiid, type: "plp"}, false);
    if (response.data != null && response.data != "") {
        document.getElementById("pic").src = response.data;
    } else {
        document.getElementById("pic").src = "";
    }
    if (response.source !== "" && response.source !== null) {
        document.getElementById('MainPictureCaptionShowMore').innerText = response.sourceType + ": " + response.source;
    }
    var cssClass = document.getElementById("pic").getAttribute('class');
    if (response.deleted || deletedPOI) {
        if (response.data != null && response.data != "") {
            cssClass = 'deleted-pic ' + cssClass;
        }
    } else if (cssClass.includes('deleted-pic ') && !response.deleted && !deletedPOI) {
        cssClass.replace('deleted-pic ', '');
    }
    document.getElementById("pic").setAttribute('class', cssClass);
}

/**
 * shows story links in show more modal
 * @param {int} poiid id of poi
 */
function ShowMoreStories(poiid) {
    var response = sendApiRequest({poi_id: poiid, type: "slp"}, false).data;
    var stories = response;
    var tableBodyLinks = "";
    for (i = 0; i < Object.keys(stories).length; i++) {
        storiesMap[i] = stories[i];
        tableBodyLinks += '<tr';
        if (storiesMap[i].deleted) {
            tableBodyLinks += ' class="deleted-row"';
        }
        tableBodyLinks += '>';
        tableBodyLinks += '<td>' + stories[i].title + '</td>';
        tableBodyLinks += '<td>';
        if (!guestmode && !deletedPOI && !storiesMap[i].deleted) {
            if (stories[i].LinkDeletable) {
                tableBodyLinks += '<button onclick="$(this).tooltip(\'hide\'); this.blur(); if (confirm(\'Verlinkung wirklich löschen?\')){deletePoiStoryLinkOnPoi(' + stories[i].id + ',' + poiid + ');}" class="btn btn-sq btn-secondary mr-2" data-toggle="tooltip" data-placement="top" title="Löschen"><img src="images/trash-alt-solid.svg" width="15px" style="margin-top: -2px"></button>';
            }
            if (stories[i].LinkValidated === false) {
                tableBodyLinks += '<button onclick="$(this).tooltip(\'hide\'); this.blur(); validatePoiStoryLinkOnPoi(' + stories[i].id + ',' + poiid + ')" class="btn btn-sq btn-secondary mr-2" data-toggle="tooltip" data-placement="top" title="Validieren"><img src="images/check-solid.svg" width="15px" style="margin-top: -2px"></button>';
            } else {
                tableBodyLinks += '<button onclick="$(this).tooltip(\'hide\'); this.blur();" class="btn btn-sq btn-secondary disabled-ng disabled mr-2" data-toggle="tooltip" data-placement="top" title="Validiert"><img src="images/check-solid-green.svg" width="15px" style="margin-top: -2px"></button>';
            }
        } else if (storiesMap[i].deleted && storiesMap[i].LinkDeletable && !guestmode && !deletedPOI) {
            tableBodyLinks += "<button onclick=\"$(this).tooltip('hide'); this.blur(); finalDeleteLinkPoiStoryMapWrapper(" + stories[i].id + "," + poiid + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Endgültig Löschen\"><img src=\"images/trash-alt-solid-red.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            if (!storiesMap[i].restrictions) {
                tableBodyLinks += "<button onclick=\"$(this).tooltip('hide'); this.blur(); RestoreLinkPoiStoryMapWrapper(" + stories[i].id + "," + poiid + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Wiederherstellen\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            } else {
                tableBodyLinks += "<button class=\"btn btn-sq btn-secondary mr-2 disabled\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Abhängigkeiten gelten als gelöscht.\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            }
        }
        tableBodyLinks += '<button onclick="showMoreStoryMap(' + i + ',' + poiid + ')" class="btn btn-sq btn-secondary mr-2" data-toggle="tooltip" data-placement="top" title="Geschichte anzeigen"><img src="images/expand-solid-white.svg" width="15px" style="margin-top: -2px"></button></span>';
        tableBodyLinks += '</td></tr>';
    }
    document.getElementById('ModalShowMoreStoryTable').innerHTML = tableBodyLinks;
}

/**
 * a wrapper around finalDeleteLinkPoiPic() to reload part of show more modal
 * @param {int} id Identifier of Link
 * @param {int} poiid Identifier of poi
 */
function finalDeleteLinkPoiStoryMapWrapper(id, poiid) {
    if (confirm('Verknüpfung wirklich löschen?') === false) {
        return;
    }
    finalDeleteLinkPoiStory(id);
    ShowMoreStories(poiid);
    SetShowMoreStoryLinkOptions(poiid);
}

/**
 * a wrapper around RestoreLinkPoiPic() to reload part of show more modal
 * @param {int} id Identifier of Link
 * @param {int} poiid Identifier of poi
 */
function RestoreLinkPoiStoryMapWrapper(id, poiid) {
    if (confirm('Verknüpfung wirklich wiederherstellen?') === false) {
        return;
    }
    RestoreLinkPoiStory(id);
    ShowMoreStories(poiid);
    SetShowMoreStoryLinkOptions(poiid);
}

/**
 * sets options for Story link add options
 * @param {int} poiid id of poi
 */
function SetShowMoreStoryLinkOptions(poiid) {
    if (guestmode || deletedPOI) {
        return;
    }
    var storyTitles = sendApiRequest({poi_id: poiid, type: "gsp"}, false).data;
    var options = "";
    for (i = 0; i < Object.keys(storyTitles).length; i++) {
        options += '<option value="' + storyTitles[i].token + '">' + storyTitles[i].title + '</option>';
    }
    if (document.getElementById("LinkPoiStorySelectMap") !== null) {
        document.getElementById('LinkPoiStorySelectMap').innerHTML = options;
    }
    if (document.getElementById("LinkPoiStoryPoiId") !== null) {
        document.getElementById('LinkPoiStoryPoiId').value = poiid;
    }
}

/**
 * shows seat count for poi show more
 * @param {int} poiid
 */
function ShowMoreSeats(poiid) {
    var response = sendApiRequest({poi_id: poiid, type: "ssp"}, false).data;
    var html = "";
    for (i = 0; i < Object.keys(response).length; i++) {
        Seats[i] = response[i];
        if (Seats[i].start == null) {
            Seats[i].start = "–";
        }
        if (Seats[i].end == null) {
            Seats[i].end = "–";
        }
        html += "<tr class='tablerow";
        if (Seats[i].deleted) {
            html += " deleted-row";
        }
        html += "' id='seats_row_" + Seats[i].ID + "'><td>" + Seats[i].start + "</td><td>" + Seats[i].end + "</td><td>" + Seats[i].seats + "</td><td>";
        if (!guestmode && !deletedPOI && !Seats[i].deleted) {
            if (Seats[i].editable) {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); if (confirm('Eintrag wirklich löschen?')){deletePoiSeatsFromList(" + Seats[i].ID + "," + poiid + " )}\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Löschen\"><img src=\"images/trash-alt-solid.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); editSeats(" + i + ")\" class=\"btn btn-sq btn-secondary btn-edit mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Bearbeiten\"><img src=\"images/pencil-alt-solid.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            }
            if (Seats[i].validatable) {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); validatePoiSeats(" + Seats[i].ID + "," + poiid + ")\"\n" +
                    "                                            class=\"btn btn-sq btn-secondary\" data-toggle=\"tooltip\"\n" +
                    "                                            data-placement=\"top\" title=\"Validieren\" id=\"ValidateBtnHistory\">\n" +
                    "                                        <img src=\"images/check-solid.svg\" width=\"15px\" style=\"margin-top: -2px\">\n" +
                    "                                    </button>";
            } else {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur();\"" +
                    "                                            class=\"btn btn-sq btn-secondary\" data-toggle=\"tooltip\"\n" +
                    "                                            data-placement=\"top\" title=\"Validiert\" id=\"ValidateBtnHistory\">\n" +
                    "                                        <img src=\"images/check-solid-green.svg\" width=\"15px\" style=\"margin-top: -2px\">\n" +
                    "                                    </button>";
            }
        } else if (Seats[i].deleted && Seats[i].editable && !guestmode && !deletedPOI) {
            html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); finalDeleteSeatsPOI(" + Seats[i].ID + "," + poiid + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Endgültig Löschen\"><img src=\"images/trash-alt-solid-red.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); RestoreSeatsPOI(" + Seats[i].ID + "," + poiid + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Wiederherstellen\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
        }
        html += "</td></tr>";
    }
    document.getElementById('ModalShowMoreSeatsTable').innerHTML = html;
}

/**
 * final deletion of seats
 * @param {int} id identifier of seat count
 * @param {int} poiid identifier of poi
 */
function finalDeleteSeatsPOI(id, poiid) {
    if (confirm('Sitzplatzanzahl wirklich löschen?') === false) {
        return;
    }
    sendApiRequest({type: 'fsc', IDent: id}, false);
    ShowMoreSeats(poiid);
}

/**
 * restore seats of poi
 * @param {int} id identifier of seat count
 * @param {int} poiid identifier of poi
 */
function RestoreSeatsPOI(id, poiid) {
    if (confirm('Sitzplatzanzahl wirklich wiederherstellen?') === false) {
        return;
    }
    sendApiRequest({type: 'rsc', IDent: id}, false);
    ShowMoreSeats(poiid);
}

/**
 * shows cinema count for poi show more
 * @param {int} poiid id of poi
 */
function ShowMoreCinemas(poiid) {
    var response = sendApiRequest({poi_id: poiid, type: "scp"}, false).data;
    var html = "";
    for (i = 0; i < Object.keys(response).length; i++) {
        Cinemas[i] = response[i];
        if (Cinemas[i].start == null) {
            Cinemas[i].start = "–";
        }
        if (Cinemas[i].end == null) {
            Cinemas[i].end = "–";
        }
        html += "<tr class='tablerow";
        if (Cinemas[i].deleted) {
            html += " deleted-row";
        }
        html += "' id='cinemas_row_" + Cinemas[i].ID + "'><td>" + Cinemas[i].start + "</td><td>" + Cinemas[i].end + "</td><td>" + Cinemas[i].cinemas + "</td><td>";
        if (!guestmode && !deletedPOI && !Cinemas[i].deleted) {
            if (Cinemas[i].editable) {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); if (confirm('Eintrag wirklich löschen?')){deletePoiCinemasFromList(" + Cinemas[i].ID + "," + poiid + " )}\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Löschen\"><img src=\"images/trash-alt-solid.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); editCinemas(" + i + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Bearbeiten\"><img src=\"images/pencil-alt-solid.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            }
            if (Cinemas[i].validatable) {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); validatePoiCinemas(" + Cinemas[i].ID + "," + poiid + ")\"\n" +
                    "                                            class=\"btn btn-sq btn-secondary\" data-toggle=\"tooltip\"\n" +
                    "                                            data-placement=\"top\" title=\"Validieren\" id=\"ValidateBtnHistory\">\n" +
                    "                                        <img src=\"images/check-solid.svg\" width=\"15px\" style=\"margin-top: -2px\">\n" +
                    "                                    </button>";
            } else {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur();\"" +
                    "                                            class=\"btn btn-sq btn-secondary\" data-toggle=\"tooltip\"\n" +
                    "                                            data-placement=\"top\" title=\"Validieren\" id=\"ValidateBtnHistory\">\n" +
                    "                                        <img src=\"images/check-solid-green.svg\" width=\"15px\" style=\"margin-top: -2px\">\n" +
                    "                                    </button>";
            }
        } else if (Cinemas[i].deleted && Cinemas[i].editable && !guestmode && !deletedPOI) {
            html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); finalDeleteCinemasPOI(" + Cinemas[i].ID + "," + poiid + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Endgültig Löschen\"><img src=\"images/trash-alt-solid-red.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); RestoreCinemasPOI(" + Cinemas[i].ID + "," + poiid + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Wiederherstellen\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
        }
        html += "</td></tr>";
    }
    document.getElementById('ModalShowMoreCinemasTable').innerHTML = html;
}

/**
 * final cinemas of seats
 * @param {int} id identifier of cinema count
 * @param {int} poiid identifier of poi
 */
function finalDeleteCinemasPOI(id, poiid) {
    if (confirm('Saalanzahl wirklich löschen?') === false) {
        return;
    }
    sendApiRequest({type: 'fcc', IDent: id}, false);
    ShowMoreCinemas(poiid);
}

/**
 * restore cinemas of poi
 * @param {int} id identifier of cinema count
 * @param {int} poiid identifier of poi
 */
function RestoreCinemasPOI(id, poiid) {
    if (confirm('Saalanzahl wirklich wiederherstellen?') === false) {
        return;
    }
    sendApiRequest({type: 'rcc', IDent: id}, false);
    ShowMoreCinemas(poiid);
}

/**
 * shows historical Addresses for poi show more
 * @param {int} poiid id of poi
 */
function ShowMoreHistoricalAddresses(poiid) {
    var response = sendApiRequest({poi_id: poiid, type: "shp"}, false).data;
    var html = "";
    for (i = 0; i < Object.keys(response).length; i++) {
        histAddress[i] = response[i];
        html += "<tr class='tablerow";
        if (histAddress[i].deleted) {
            html += " deleted-row";
        }
        html += "' id='histAddress_row_" + histAddress[i].ID + "'><td>";
        if (histAddress[i].start !== null) {
            html += histAddress[i].start;
        } else {
            html += "–";
        }
        html += "</td><td>";
        if (histAddress[i].end !== null) {
            html += histAddress[i].end;
        } else {
            html += "–";
        }
        html += "</td><td>";
        var numberOfAddressFields = 0;
        if (histAddress[i].Streetname !== null) {
            html += histAddress[i].Streetname;
            numberOfAddressFields++;
        }
        if (histAddress[i].Streetname !== null && histAddress[i].Housenumber !== null) {
            html += " ";
        }
        if (histAddress[i].Housenumber !== null) {
            html += histAddress[i].Housenumber;
            numberOfAddressFields++;
        }
        if ((histAddress[i].Streetname !== null || histAddress[i].Housenumber !== null) && (histAddress[i].City !== null || histAddress[i].Postalcode !== null)) {
            html += ", ";
        }
        if (histAddress[i].Postalcode !== null) {
            html += histAddress[i].Postalcode;
            numberOfAddressFields++;
        }
        if (histAddress[i].Postalcode !== null && histAddress[i].City !== null) {
            html += " ";
        }
        if (histAddress[i].City !== null) {
            html += histAddress[i].City;
            numberOfAddressFields++;
        }
        if (numberOfAddressFields === 0) {
            html += "–";
        }
        html += "</td><td>";
        if (!guestmode && !deletedPOI && !histAddress[i].deleted) {
            if (histAddress[i].editable) {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); if (confirm('Eintrag wirklich löschen?')){deletePoiAddressFromList(" + histAddress[i].ID + "," + poiid + " )}\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Löschen\"><img src=\"images/trash-alt-solid.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); editHistAddress(" + i + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Bearbeiten\"><img src=\"images/pencil-alt-solid.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            }
            if (histAddress[i].validatable) {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); validatePoiAddress(" + histAddress[i].ID + "," + poiid + ")\"\n" +
                    "                                            class=\"btn btn-sq btn-secondary\" data-toggle=\"tooltip\"\n" +
                    "                                            data-placement=\"top\" title=\"Validieren\" id=\"ValidateBtnHistory\">\n" +
                    "                                        <img src=\"images/check-solid.svg\" width=\"15px\" style=\"margin-top: -2px\">\n" +
                    "                                    </button>";
            } else {
                {
                    html += "<button onclick=\"$(this).tooltip('hide'); this.blur();\"" +
                        "                                            class=\"btn btn-sq btn-secondary\" data-toggle=\"tooltip\"\n" +
                        "                                            data-placement=\"top\" title=\"Validieren\" id=\"ValidateBtnHistory\">\n" +
                        "                                        <img src=\"images/check-solid-green.svg\" width=\"15px\" style=\"margin-top: -2px\">\n" +
                        "                                    </button>";
                }
            }
        } else if (histAddress[i].deleted && histAddress[i].editable && !guestmode && !deletedPOI) {
            html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); finalDeleteHistAddrPOI(" + histAddress[i].ID + "," + poiid + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Endgültig Löschen\"><img src=\"images/trash-alt-solid-red.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); RestoreHistAddrPOI(" + histAddress[i].ID + "," + poiid + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Wiederherstellen\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
        }
        html += "</td></tr>";
    }
    document.getElementById('ModalShowMoreHistAddressTable').innerHTML = html;
}

/**
 * final deletion of historical address
 * @param {int} id identifier of historical address
 * @param {int} poiid identifier of poi
 */
function finalDeleteHistAddrPOI(id, poiid) {
    if (confirm('Historische Adresse wirklich löschen?') === false) {
        return;
    }
    sendApiRequest({type: 'fha', IDent: id}, false);
    ShowMoreHistoricalAddresses(poiid);
}

/**
 * restore historical address of poi
 * @param {int} id identifier of historical address
 * @param {int} poiid identifier of poi
 */
function RestoreHistAddrPOI(id, poiid) {
    if (confirm('Historische Adresse wirklich wiederherstellen?') === false) {
        return;
    }
    sendApiRequest({type: 'rha', IDent: id}, false);
    ShowMoreHistoricalAddresses(poiid);
}

/**
 * displays operators on show more from poi
 * @param {int} poiid id of poi
 */
function ShowMoreOperators(poiid) {
    var response = sendApiRequest({poi_id: poiid, type: "sop"}, false).data;
    var html = "";
    for (i = 0; i < Object.keys(response).length; i++) {
        operators[i] = response[i];
        if (operators[i].start == null) {
            operators[i].start = "–";
        }
        if (operators[i].end == null) {
            operators[i].end = "–";
        }
        html += "<tr class='tablerow";
        if (operators[i].deleted) {
            html += " deleted-row";
        }
        html += "' id='operators_row_" + operators[i].ID + "'><td>" + operators[i].start + "</td><td>" + operators[i].end + "</td><td>" + operators[i].Operator + "</td><td>";
        if (!guestmode && !deletedPOI && !operators[i].deleted) {
            if (operators[i].editable) {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); if (confirm('Eintrag wirklich löschen?')){deletePoiOperatorFromList(" + operators[i].ID + "," + poiid + " )}\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Löschen\"><img src=\"images/trash-alt-solid.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); editOperator(" + i + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Bearbeiten\"><img src=\"images/pencil-alt-solid.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            }
            if (operators[i].validatable) {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); validatePoiOperator(" + operators[i].ID + "," + poiid + ")\"\n" +
                    "                                            class=\"btn btn-sq btn-secondary\" data-toggle=\"tooltip\"\n" +
                    "                                            data-placement=\"top\" title=\"Validieren\" id=\"ValidateBtnHistory\">\n" +
                    "                                        <img src=\"images/check-solid.svg\" width=\"15px\" style=\"margin-top: -2px\">\n" +
                    "                                    </button>";
            } else {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur();\"" +
                    "                                            class=\"btn btn-sq btn-secondary\" data-toggle=\"tooltip\"\n" +
                    "                                            data-placement=\"top\" title=\"Validieren\" id=\"ValidateBtnHistory\">\n" +
                    "                                        <img src=\"images/check-solid-green.svg\" width=\"15px\" style=\"margin-top: -2px\">\n" +
                    "                                    </button>";
            }
        } else if (operators[i].deleted && operators[i].editable && !guestmode && !deletedPOI) {
            html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); finalDeleteOperatorPOI(" + operators[i].ID + "," + poiid + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Endgültig Löschen\"><img src=\"images/trash-alt-solid-red.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); RestoreOperatorPOI(" + operators[i].ID + "," + poiid + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Wiederherstellen\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
        }
        html += "</td></tr>";
    }
    document.getElementById('ModalShowMoreOperatorTable').innerHTML = html;
}

/**
 * final deletion of operator
 * @param {int} id identifier of operator
 * @param {int} poiid identifier of poi
 */
function finalDeleteOperatorPOI(id, poiid) {
    if (confirm('Betreiber wirklich löschen?') === false) {
        return;
    }
    sendApiRequest({type: 'fop', IDent: id}, false);
    ShowMoreOperators(poiid);
}

/**
 * restore operator of poi
 * @param {int} id identifier of operator
 * @param {int} poiid identifier of poi
 */
function RestoreOperatorPOI(id, poiid) {
    if (confirm('Betreiber wirklich wiederherstellen?') === false) {
        return;
    }
    sendApiRequest({type: 'rop', IDent: id}, false);
    ShowMoreOperators(poiid);
}

/**
 * displays names on show more
 * @param {int} poiid id of poi
 */
function ShowMoreNames(poiid) {
    var response = sendApiRequest({poi_id: poiid, type: "snp"}, false).data;
    var html = "";
    for (i = 0; i < Object.keys(response).length; i++) {
        names[i] = response[i];
        if (names[i].start == null) {
            names[i].start = "–";
        }
        if (names[i].end == null) {
            names[i].end = "–";
        }
        html += "<tr class='tablerow";
        if (names[i].deleted) {
            html += " deleted-row";
        }
        html += "' id='names_row_" + names[i].ID + "'>";
        html += "<td>" + names[i].start + "</td><td>" + names[i].end + "</td><td>" + names[i].name + "</td><td>";
        if (!guestmode && i > 0 && !deletedPOI && !names[i].deleted) {
            if (names[i].editable) {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); if (confirm('Eintrag wirklich löschen?')){deletePoiNameFromList(" + names[i].ID + "," + poiid + " )}\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Löschen\"><img src=\"images/trash-alt-solid.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); editName(" + i + ");\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Bearbeiten\"><img src=\"images/pencil-alt-solid.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            }
            if (names[i].validatable) {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); validatePoiName(" + names[i].ID + "," + poiid + ")\"\n" +
                    "                                            class=\"btn btn-sq btn-secondary\" data-toggle=\"tooltip\"\n" +
                    "                                            data-placement=\"top\" title=\"Validieren\" id=\"ValidateBtnHistory\">\n" +
                    "                                        <img src=\"images/check-solid.svg\" width=\"15px\" style=\"margin-top: -2px\">\n" +
                    "                                    </button>";
            } else {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur();\"" +
                    "                                            class=\"btn btn-sq btn-secondary\" data-toggle=\"tooltip\"\n" +
                    "                                            data-placement=\"top\" title=\"Validieren\" id=\"ValidateBtnHistory\">\n" +
                    "                                        <img src=\"images/check-solid-green.svg\" width=\"15px\" style=\"margin-top: -2px\">\n" +
                    "                                    </button>";
            }
        } else if (names[i].deleted && names[i].editable && !guestmode && !deletedPOI) {
            html += "<button onclick=\"$(this).tooltip('hide'); this.blur();finalDeleteNamePOI(" + names[i].ID + "," + poiid + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Endgültig Löschen\"><img src=\"images/trash-alt-solid-red.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            html += "<button onclick=\"$(this).tooltip('hide'); this.blur();restoreNamePOI(" + names[i].ID + "," + poiid + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Wiederherstellen\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
        }
        html += "</td></tr>";
    }
    document.getElementById('ModalShowMoreNameTitleTable').innerHTML = html;
}

/**
 * final deletes a name
 * @param {int} id Identifier of Name
 * @param {int} poiid Identifier of poi to reload part of show more modal
 */
function finalDeleteNamePOI(id, poiid) {
    if (confirm('Namen wirklich löschen?') === false) {
        return;
    }
    sendApiRequest({type: 'fna', IDent: id}, false);
    ShowMoreNames(poiid);
}

/**
 * restores a name
 * @param {int} id Identifier of Name
 * @param {int} poiid Identifier of poi to reload part of show more modal
 */
function restoreNamePOI(id, poiid) {
    if (confirm('Namen wirklich wiederherstellen?') === false) {
        return;
    }
    sendApiRequest({type: 'rna', IDent: id}, false);
    ShowMoreNames(poiid);
}

/**
 * disables link over picture
 * @param {string} identifer card on which button is placed
 */
function disableCardLinks(identifer) {
    var link = document.getElementById(identifer);

    for (var i = 0; i < link.length; i++) {
        link.eq(i).attr('oldhref', link.eq(i).attr('href'));
    }
    var btn = $('#' + identifer);
    btn.tooltip('hide');
    btn.removeAttr('data-lightbox');
    btn.attr('href', '#');
}

/**
 * enables link over picture
 * @param {string} identifer card on which button is placed
 */
function enableCardLinks(identifer) {
    var link = document.getElementById(identifer);

    for (var i = 0; i < link.length; i++) {
        link.eq(i).attr('href', link.eq(i).attr('oldhref'));
    }
    var btn = $('#' + identifer);
    btn.attr('data-lightbox', 'ShowMore');
    btn.attr('oldhref', '#');
}

/**
 * removes the focus from the button that has been clicked and hides remaining tooltips
 * @param {string} identifier identifier of button that has been clicked
 */
function blurButton(identifier) {
    var btn = $('#' + identifer);
    btn.tooltip('hide');
    btn.blur();
}

/**
 * saves POI-Story-Linking Map
 */
function saveLinkedPoiMap() {
    var token = document.getElementById('LinkPoiStorySelectMap').value;
    var poi_ID = document.getElementById('LinkPoiStoryPoiId').value;
    sendApiRequest({type: "aps", poiid: poi_ID, storytoken: token}, false).data;
    ShowMoreStories(poi_ID);
    SetShowMoreStoryLinkOptions(poi_ID);
}

/**
 * shows complete Story on Map
 * @param {int} IntCounter story poisiton in array
 * @param {int} poi_id id of calling poi
 */
function showMoreStoryMap(IntCounter, poi_id) {
    $('#MarkerModalBig').modal('hide');
    document.getElementById('StoryFullTitle').innerHTML = storiesMap[IntCounter].title;
    document.getElementById('StoryTextMap').innerHTML = storiesMap[IntCounter].story;
    document.getElementById('StoryLongNameDateMap').innerHTML = storiesMap[IntCounter].name + ' – ' + storiesMap[IntCounter].date;
    document.getElementById('closeModalFullStoryMap').setAttribute('onclick', 'showMorePOI(' + poi_id + ')');
    document.getElementById('showFullStoryMapBack').setAttribute('onclick', 'closeStoryModalShowMorePoi(' + poi_id + ')');
    $('#StoryFullMap').modal();
}

/**
 * toogles display of Story Modal on Map
 * @param {int} poi_id id of POI to open after
 */
function closeStoryModalShowMorePoi(poi_id) {
    $('#StoryFullMap').modal('hide');
    showMorePOI(poi_id);
}

/**
 * sets focus to a map position
 * @param {float} lat latitude
 * @param {float} lng longitude
 */
function setfocus2(lat, lng) {
    Karte.setView([lat, lng], zoom = 17.5);
}

/**
 * toggles add poi button
 * @param {boolean} enabled sets enabele state of poi button
 */
function toggleAddPOIButton(enabled) {
    if (enabled === false) {
        document.getElementById("addPOIButton").setAttribute("data-toggle", "tooltip");
        document.getElementById("addPOIButton").setAttribute("class", "btn btn-dark disabled");
        //document.getElementById("addPOIButton").setAttribute("onclick", "");
        document.getElementById("addPOIButton").setAttribute("data-original-title", "Bitte zuerst einen Ort auf der Karte auswählen.");
    } else {
        document.getElementById("addPOIButton").setAttribute("data-toggle", "modal");
        document.getElementById("addPOIButton").setAttribute("class", "btn btn-dark");
        //document.getElementById("addPOIButton").setAttribute("onclick", "insertCoordinates(false);");
        document.getElementById("addPOIButton").setAttribute("data-original-title", "Eintrag hinzufügen");
    }
}

/**
 * adds comment
 * @returns {boolean} result always false to inhibit page reloading
 */
function getCommentFromFormular() {
    var comment = document.getElementById('poi_content_comment_map').value;
    var poi_id = document.getElementById('poi_id_comment_map').value;
    AddCommentAPI(comment, poi_id);
    document.getElementById('poi_content_comment_map').value = "";
    showMorePOI(poi_id);
}

/**
 * writes name to database, gets information from input fields and reloads modal
 */
function saveNameShowMore() {
    var name = document.getElementById('stringNameShowMore').value;

    if (name === "") {
        alert("Bitte geben Sie den Namen an.");
    } else {
        var poiid = document.getElementById('poi_id_comment_map').value;
        var from = document.getElementById('fromNameShowMore').value;
        var till = document.getElementById('tillNameShowMore').value;
        sendApiRequest({
            type: 'adn',
            from: from,
            till: till,
            name: name,
            poi_id: poiid
        }, false);
        document.getElementById('fromNameShowMore').value = "";
        document.getElementById('tillNameShowMore').value = "";
        document.getElementById('stringNameShowMore').value = "";
        ShowMoreNames(poiid);
    }
}

/**
 * updates operator in database, gets information from input fields and reloads modal
 */
function updateNameShowMore(nameId) {
    var dataOfRow = document.getElementById('names_row_' + nameId).children;
    var name = dataOfRow[2].firstChild.value;

    if (name === "") {
        alert("Bitte geben Sie den Namen an.");
    } else {
        var from = dataOfRow[0].firstChild.value;
        var till = dataOfRow[1].firstChild.value;
        var poiid = document.getElementById('poi_id_comment_map').value;
        sendApiRequest({
            type: 'una',
            id: nameId,
            name: name,
            start: from,
            end: till
        }, false);
        ShowMoreNames(poiid);
    }
}

/**
 * load data for name in input fields
 * @param {int} id identificiator of name in names array
 */
function editName(id) {
    document.getElementById('names_row_' + names[id].ID).innerHTML =
        '<td>' +
        '<input type="number" class="form-control textinput-formular" ' +
        'style="width: 250px; background-color: #3b3b3b; color: #ffffff" ' +
        'name="fromNameShowMore" value="' + names[id].start + '">' +
        '</td>' +
        '<td>' +
        '<input type="number" class="form-control textinput-formular" ' +
        'style="width: 250px; background-color: #3b3b3b; color: #ffffff" ' +
        'name="tillNameShowMore" value="' + names[id].end + '">' +
        '</td>' +
        '<td>' +
        '<input type="text" class="form-control textinput-formular" required="required" ' +
        'style="width: 250px; background-color: #3b3b3b; color: #ffffff" ' +
        'name="stringNameShowMore" value="' + names[id].name + '">' +
        '</td>' +
        '<td>' +
        '<button onclick="$(this).tooltip(\'hide\'); this.blur(); updateNameShowMore(' + names[id].ID + ');" ' +
        'class="btn btn-sq btn-secondary" data-toggle="tooltip" ' +
        'data-placement="top" title="Speichern" id="ValidateBtnHistory">' +
        '<img src="images/save-solid-white.svg" width="15px" ' +
        'style="margin-top: -2px">' +
        '</button>' +
        '</td>';
}

/**
 * writes operator to database, gets information from input fields and reloads modal
 */
function saveOperatorShowMore() {
    var operator = document.getElementById('stringOperatorShowMore').value;

    if (operator === "") {
        alert("Bitte geben Sie einen Betreiber an.");
    } else {
        var poiid = document.getElementById('poi_id_comment_map').value;
        var from = document.getElementById('fromOperatorShowMore').value;
        var till = document.getElementById('tillOperatorShowMore').value;
        sendApiRequest({
            type: 'ado',
            from: from,
            till: till,
            operator: operator,
            poi_id: poiid
        }, false);
        document.getElementById('fromOperatorShowMore').value = "";
        document.getElementById('tillOperatorShowMore').value = "";
        document.getElementById('stringOperatorShowMore').value = "";
    }
    ShowMoreOperators(poiid);
}

/**
 * updates operator in database, gets information from input fields and reloads modal
 */
function updateOperatorShowMore(operatorId) {
    var dataOfRow = document.getElementById('operators_row_' + operatorId).children;
    var operator = dataOfRow[2].firstChild.value;

    if (operator === "") {
        alert("Bitte geben Sie einen Betreiber an.");
    } else {
        var poiid = document.getElementById('poi_id_comment_map').value;
        var from = dataOfRow[0].firstChild.value;
        var till = dataOfRow[1].firstChild.value;
        sendApiRequest({
            type: 'uop',
            id: operatorId,
            operator: operator,
            start: from,
            end: till
        }, false);
        ShowMoreOperators(poiid);
    }
}

/**
 * load data for operator in input fields
 * @param {int} id identificiator of Operator in operators array
 */
function editOperator(id) {

    document.getElementById('operators_row_' + operators[id].ID).innerHTML =
        '<td>' +
        '<input type="number" class="form-control textinput-formular" ' +
        'style="width: 250px; background-color: #3b3b3b; color: #ffffff" ' +
        'name="fromOperatorShowMore" id="fromOperatorShowMore" value="' + operators[id].start + '">' +
        '</td>' +
        '<td>' +
        '<input type="number" class="form-control textinput-formular" ' +
        'style="width: 250px; background-color: #3b3b3b; color: #ffffff" ' +
        'name="tillOperatorShowMore" id="tillOperatorShowMore" value="' + operators[id].end + '">' +
        '</td>' +
        '<td>' +
        '<input type="text" class="form-control textinput-formular" required="required" ' +
        'style="width: 250px; background-color: #3b3b3b; color: #ffffff" name="stringOperatorShowMore" ' +
        'id="stringOperatorShowMore" value="' + operators[id].Operator + '">' +
        '</td>' +
        '<td>' +
        '<button onclick="$(this).tooltip(\'hide\'); this.blur(); updateOperatorShowMore(' + operators[id].ID + ');" ' +
        'class="btn btn-sq btn-secondary" data-toggle="tooltip" ' +
        'data-placement="top" title="Speichern" id="ValidateBtnHistory">' +
        '<img src="images/save-solid-white.svg" width="15px" ' +
        'style="margin-top: -2px" alt="Speichern">' +
        '</button>' +
        '</td>';
}

/**
 * writes historical address to database, gets information from input fields and reloads modal
 */
function saveHistoricalShowMore() {
    var streetname = document.getElementById('StreetnameShowMore').value;
    var housenumber = document.getElementById('HousenumberShowMore').value;
    var city = document.getElementById('CityShowMore').value;
    var postalcode = document.getElementById('PostalcodeShowMore').value;

    if (streetname === "" && housenumber === "" && city === "" && postalcode === "") {
        alert("Bitte füllen Sie mindestens eins der Adressfelder aus.");
    } else {
        var test = checkAddressExists(streetname, housenumber, city, postalcode);
        var quest = false;
        if (test) {
            quest = confirm("Diese Adresse ist bereits vorhanden. Möchten Sie trotzdem fortfahren?");
        }
        if (!test || quest) {
            var poiid = document.getElementById('poi_id_comment_map').value;
            var from = document.getElementById('fromDateShowMore').value;
            var till = document.getElementById('tillDateShowMore').value;

            sendApiRequest({
                type: 'aha',
                from: from,
                till: till,
                streetname: streetname,
                housenumber: housenumber,
                city: city,
                postalcode: postalcode,
                poi_id: poiid
            }, false);
            document.getElementById('fromDateShowMore').value = "";
            document.getElementById('tillDateShowMore').value = "";
            document.getElementById('StreetnameShowMore').value = "";
            document.getElementById('HousenumberShowMore').value = "";
            document.getElementById('CityShowMore').value = "";
            document.getElementById('PostalcodeShowMore').value = "";
            document.getElementById('toggleHideShowMoreAddress').style.display = 'none';
            document.getElementById('toggleHideShowMoreAddressSaveBtn').style.display = 'none';
            ShowMoreHistoricalAddresses(poiid);
        }
    }
}

/**
 * updates historical address in database, gets information from input fields and reloads modal
 */
function updateHistoricalShowMore(addressId) {
    var dataOfRow = document.getElementById('histAddress_row_' + addressId).children;
    var streetname = dataOfRow[2].children[1].firstChild.firstChild.value;
    var housenumber = dataOfRow[2].children[1].lastChild.firstChild.value;
    var postalcode = dataOfRow[2].lastChild.firstChild.firstChild.value;
    var city = dataOfRow[2].lastChild.lastChild.firstChild.value;

    if (streetname === "" && housenumber === "" && postalcode === "" && city === "") {
        alert("Bitte füllen Sie mindestens eins der Adressfelder aus.");
    } else {
        var from = dataOfRow[0].firstChild.value;
        var till = dataOfRow[1].firstChild.value;
        var poiid = document.getElementById('poi_id_comment_map').value;
        sendApiRequest({
            type: 'uha',
            id: addressId,
            start: from,
            end: till,
            streetname: streetname,
            housenumber: housenumber,
            city: city,
            postalcode: postalcode
        }, false);
        ShowMoreHistoricalAddresses(poiid);
    }
}

/**
 * enable edit historical address
 * @param {int} id identificiator of historical address in array
 */
function editHistAddress(id) {

    var streetname = histAddress[id].Streetname;
    var housenumber = histAddress[id].Housenumber;
    var city = histAddress[id].City;
    if (streetname == null) {
        streetname = "";
    }
    if (housenumber == null) {
        housenumber = "";
    }
    if (city == null) {
        city = "";
    }

    document.getElementById('histAddress_row_' + histAddress[id].ID).innerHTML =
        '<td>' +
        '<input type="number" class="form-control textinput-formular" name="fromDateShowMore" ' +
        'style="width: 250px; background-color: #3b3b3b; color: #ffffff" ' +
        'value="' + histAddress[id].start + '">' +
        '</td>' +
        '<td>' +
        '<input type="number" class="form-control textinput-formular" name="tillDateShowMore" ' +
        'style="width: 250px; background-color: #3b3b3b; color: #ffffff" ' +
        'value="' + histAddress[id].end + '">' +
        '<td>' +
        '<label class="col-form-label">Adresszeile 1</label>' +
        '<div class="form-group form-row">' +
        '<div class="col">' +
        '<input type="text" class="form-control textinput-formular" name="StreetnameShowMore" ' +
        'style="background-color: #3b3b3b; color: #ffffff" ' +
        'placeholder="Straßenname" value="' + streetname + '">' +
        '</div>' +
        '<div class="col">' +
        '<input type="text" class="form-control textinput-formular" name="HousenumberShowMore" ' +
        'style=" background-color: #3b3b3b; color: #ffffff" ' +
        'placeholder="Hausnummer" value="' + housenumber + '">' +
        '</div>' +
        '</div>' +
        '<label class="col-form-label">Adresszeile 2</label>' +
        '<div class="form-group form-row">' +
        '<div class="col">' +
        '<input type="number" class="form-control textinput-formular" name="PostalcodeShowMore" ' +
        'style="background-color: #3b3b3b; color: #ffffff" ' +
        'placeholder="Postleitzahl" value="' + histAddress[id].Postalcode + '">' +
        '</div>' +
        '<div class="col">' +
        '<input type="text" class="form-control textinput-formular" name="CityShowMore" ' +
        'style="background-color: #3b3b3b; color: #ffffff" ' +
        'placeholder="Ortsname" value="' + city + '">' +
        '</div>' +
        '</div>' +
        '</td>' +
        '<td>' +
        '<button onclick="$(this).tooltip(\'hide\'); this.blur(); updateHistoricalShowMore(' + histAddress[id].ID + ')" ' +
        'class="btn btn-sq btn-secondary" data-toggle="tooltip" data-placement="top" title="Speichern" ' +
        'id="ValidateBtnHistory">' +
        '<img src="images/save-solid-white.svg" width="15px" style="margin-top: -2px" alt="Speichern">' +
        '</button>' +
        '</td>';
}

/**
 * Checks if checkNStep is set aktive and shows modal
 */
function checkNStep() {
    var metas = document.getElementsByTagName('meta');
    if (metas.length > 0) {
        for (var i = 0; i < metas.length; i++) {
            if (metas[i].getAttribute('name') === 'n-step' && metas[i].getAttribute('content') === 'show') {
                console.log(metas[i].getAttribute('content'));
                $('#NextStepMap').modal();
            }
        }
    }
}

/**
 * checks if a Modal has to be opened
 */
function CheckCommentShow() {
    if (testCookie('OpenPoi') && testCookie('OpenComment')) {
        focusComment = getCookie('OpenComment');
        var poiid = getCookie('OpenPoi');
        showMorePOI(poiid);
    }
    deleteCookie('OpenPoi');
    deleteCookie('OpenComment');
}

/**
 * checks if a certain poi should be focused
 */
function CheckFocus() {
    if (testCookie('LatPoi') && testCookie('LngPoi')) {
        var lat = getCookie('LatPoi');
        var lng = getCookie('LngPoi');
        setfocus2(lat, lng);
    }
    deleteCookie('LatPoi');
    deleteCookie('LngPoi');
}

/**
 * opens select pictures modal and sets it's parameters
 * @param {int} poiid id of POI
 */
function openSelectMorePicturesOnMap(poiid) {
    $('#MarkerModalBig').modal('hide');
    setPictureSelect_MultiSelect();
    document.getElementById('MainPictureSelectCloseButton').setAttribute('onclick', 'abortSelectMorePicturesOnMap(' + poiid + ')');
    document.getElementById('MainPictureSelectAbortButton').setAttribute('onclick', 'abortSelectMorePicturesOnMap(' + poiid + ')');
    document.getElementById('MainPictureSelectSaveButton').setAttribute('onclick', 'saveSelectMorePicturesOnMap(' + poiid + ')');
    showSinglePicSelect();
}

/**
 * saves selected pictures to database
 * @param {int} poiid id of poi
 */
function saveSelectMorePicturesOnMap(poiid) {
    var pictures = document.getElementById('MainPictureSelectSelected').value;
    pictures = pictures.split('$');
    var json = {type: "app", data: pictures, poi: poiid};
    sendApiRequest(json, false).data;
    $('#PictureSelectModal').modal('hide');
    showMorePOI(poiid)
}

/**
 * closes select picture modal
 * @param {int} poiid id of poi to open after
 */
function abortSelectMorePicturesOnMap(poiid) {
    $('#PictureSelectModal').modal('hide');
    showMorePOI(poiid);
}

/**
 * writes seat counter to database, gets information from input fields and reloads modal
 */
function saveSeatCount() {
    var count = document.getElementById('CountSeatsShowMore').value;

    if (count === "") {
        alert("Bitte geben Sie die Anzahl der Sitzplätze an.");
    } else {
        var from = document.getElementById('fromSeatsShowMore').value;
        var till = document.getElementById('tillSeatsShowMore').value;
        var poiid = document.getElementById('poi_id_comment_map').value;
        sendApiRequest({
            type: 'asc',
            from: from,
            till: till,
            seats: count,
            poi_id: poiid
        }, false);
        document.getElementById('fromSeatsShowMore').value = "";
        document.getElementById('tillSeatsShowMore').value = "";
        document.getElementById('CountSeatsShowMore').value = "";
        ShowMoreSeats(poiid);
    }
}

/**
 * updates seat counter in database, gets information from input fields and reloads modal
 */
function updateSeatCount(seatId) {
    var dataOfRow = document.getElementById('seats_row_' + seatId).children;
    var count = dataOfRow[2].firstChild.value;

    if (count === "") {
        alert("Bitte geben Sie die Anzahl der Sitzplätze an.");
    } else {
        var from = dataOfRow[0].firstChild.value;
        var till = dataOfRow[1].firstChild.value;
        var poiid = document.getElementById('poi_id_comment_map').value;
        sendApiRequest({
            type: 'usc',
            id: seatId,
            seats: count,
            start: from,
            end: till
        }, false);
        ShowMoreSeats(poiid);
    }
}

/**
 * load data for seat count in input fields
 * @param {int} id identificiator of seats in seats array
 */
function editSeats(id) {

    document.getElementById('seats_row_' + Seats[id].ID).innerHTML =
        '<td>' +
        '<input type="number" class="form-control textinput-formular" ' +
        'style="width: 250px; background-color: #3b3b3b; color: #ffffff" ' +
        'name="fromSeatsShowMore" value="' + Seats[id].start + '">' +
        '</td>' +
        '<td>' +
        '<input type="number" class="form-control textinput-formular" ' +
        'style="width: 250px; background-color: #3b3b3b; color: #ffffff" ' +
        'name="tillSeatsShowMore" value="' + Seats[id].end + '">' +
        '</td>' +
        '<td>' +
        '<input type="number" class="form-control textinput-formular" required="required" ' +
        'style="width: 250px; background-color: #3b3b3b; color: #ffffff" ' +
        'name="CountSeatsShowMore" value="' + Seats[id].seats + '">' +
        '</td>' +
        '<td>' +
        '<button onclick="$(this).tooltip(\'hide\'); this.blur(); updateSeatCount(' + Seats[id].ID + ');" class="btn btn-sq btn-secondary" data-toggle="tooltip" ' +
        'data-placement="top" title="Speichern" id="ValidateBtnSeats">' +
        '<img src="images/save-solid-white.svg" width="15px" style="margin-top: -2px" ' +
        'alt="Speichern">' +
        '</button>' +
        '</td>';
}

/**
 * writes cinema counter to database, gets information from input fields and reloads modal
 */
function saveCinemaCount() {
    var count = document.getElementById('countCinemasShowMore').value;

    if (count === "") {
        alert("Bitte geben Sie die Anzahl der Kinosäle an.");
    } else {
        var from = document.getElementById('fromCinemasShowMore').value;
        var till = document.getElementById('tillCinemasShowMore').value;
        var poiid = document.getElementById('poi_id_comment_map').value;
        sendApiRequest({
            type: 'acc',
            from: from,
            till: till,
            cinemas: count,
            poi_id: poiid
        }, false);
        document.getElementById('fromCinemasShowMore').value = "";
        document.getElementById('tillCinemasShowMore').value = "";
        document.getElementById('countCinemasShowMore').value = "";
        ShowMoreCinemas(poiid);
    }
}

/**
 * updates cinema counter in database, gets information from input fields and reloads modal
 */
function updateCinemaCount(cinemaId) {
    var dataOfRow = document.getElementById('cinemas_row_' + cinemaId).children;
    var count = dataOfRow[2].firstChild.value;

    if (count === "") {
        alert("Bitte geben Sie die Anzahl der Kinosäle an.");
    } else {
        var from = dataOfRow[0].firstChild.value;
        var till = dataOfRow[1].firstChild.value;
        var poiid = document.getElementById('poi_id_comment_map').value;
        sendApiRequest({
            type: 'ucc',
            id: cinemaId,
            cinemas: count,
            start: from,
            end: till
        }, false);
        ShowMoreCinemas(poiid);
    }
}

/**
 * load data for cinema count in input fields
 * @param {int} id identifier of cinemas in cinemas array
 */
function editCinemas(id) {
    document.getElementById('cinemas_row_' + Cinemas[id].ID).innerHTML =
        '<td>' +
        '<input type="number" class="form-control textinput-formular" ' +
        'style="width: 250px; background-color: #3b3b3b; color: #ffffff" ' +
        'name="fromCinemasShowMore" value="' + Cinemas[id].start + '">' +
        '</td>' +
        '<td>' +
        '<input type="number" class="form-control textinput-formular" ' +
        'style="width: 250px; background-color: #3b3b3b; color: #ffffff" ' +
        'name="tillCinemasShowMore" value="' + Cinemas[id].end + '">' +
        '</td>' +
        '<td>' +
        '<input type="number" class="form-control textinput-formular" required="required" ' +
        'style="width: 250px; background-color: #3b3b3b; color: #ffffff" ' +
        'name="countCinemasShowMore" value="' + Cinemas[id].cinemas + '">' +
        '</td>' +
        '<td>' +
        '<button onclick="$(this).tooltip(\'hide\'); this.blur(); updateCinemaCount(' + Cinemas[id].ID + ');" ' +
        'class="btn btn-sq btn-secondary" data-toggle="tooltip" ' +
        'data-placement="top" title="Speichern">' +
        '<img src="images/save-solid-white.svg" width="15px" style="margin-top: -2px" ' +
        'alt="Speichern">' +
        '</button>' +
        '</td>';
}

/**
 * blends in custom popover and enables hovering it
 * @param {string} popoverId identifier of popover to show
 */
function showPopover(popoverId) {
    var popover = document.getElementById(popoverId);
    popover.style.pointerEvents = "all";
    popover.className = popover.className.replace(/\bblend-out-animation\b/g, "blend-in-animation");
}

/**
 * blends out custom popover and disables hovering it
 * @param {string} popoverId identifier of popover to hide
 */
function hidePopover(popoverId) {
    var popover = document.getElementById(popoverId);
    popover.style.pointerEvents = "none";
    popover.className = popover.className.replace(/\bblend-in-animation\b/g, "blend-out-animation");
}

/**
 * sets the cinema type selected in the dropdown to the hidden input field
 * @param {int} typeId identifier of selected type
 * @param {string} typeName name of selected type
 */
function setCinemaType(typeId, typeName) {
    document.getElementById("CinemaTypeSelect").setAttribute("value", typeId);
    document.getElementById("cinemaTypeButton").innerHTML = typeName;
}

/**
 * Adds new source to poi
 */
function saveAddNewSourceShowMore() {
    var typeSource = document.getElementById('SourceAddTypeSelect').value;
    var source = document.getElementById('SourceAddDescriptionInput').value;
    var relation = document.getElementById('SourceAddRelationSelect').value;
    var poiid = document.getElementById('poi_id_comment_map').value;
    var json = {type: "asp", typeSource: typeSource, source: source, relation: relation, poiid: poiid};
    if (sendApiRequest(json, false).code !== 0) {
        return;
    }
    document.getElementById('SourceAddTypeSelect').selectedIndex = 0;
    document.getElementById('SourceAddDescriptionInput').value = "";
    document.getElementById('SourceAddRelationSelect').selectedIndex = 0;
    ShowMoreSources(poiid);
}

/**
 * displays source information of poi
 * @param {int} poiid identifier of point of interest
 */
function ShowMoreSources(poiid) {
    var json = {type: 'grp', poiid: poiid};
    var result = sendApiRequest(json, false).data;
    var html = ""
    for (var i = 0; i < result.length; i++) {
        var cssClass = "tablerow";
        if (result[i].deleted) {
            cssClass += " deleted-row";
        }
        html += "<tr id='showMoreSourceRow" + result[i].id + "' class='" + cssClass + "'>";
        html += "<td>" + result[i].type + "</td>";
        html += "<td>" + result[i].source + "</td>";
        html += "<td>" + result[i].relation + "</td>";
        html += "<td>";
        if (!guestmode && !deletedPOI && !result[i].deleted) {
            if (result[i].editable) {
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); deleteSourceEntryShowMore(" + result[i].id + "," + poiid + ");\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Löschen\"><img src=\"images/trash-alt-solid.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
                html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); enableEditSourceShowMore(" + result[i].id + ");\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Bearbeiten\"><img src=\"images/pencil-alt-solid.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            }
        } else if (result[i].deleted && result[i].editable && !guestmode && !deletedPOI) {
            html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); finalDeleteSourceShowMore(" + result[i].id + "," + poiid + ");\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Endgültig Löschen\"><img src=\"images/trash-alt-solid-red.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
            html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); restoreSourceShowMore(" + result[i].id + "," + poiid + ");\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Wiederherstellen\"><img src=\"images/trash-restore-solid-dark-green.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
        }
        html += "</td>";
        html += "</tr>";
        Sources[result[i].id] = result[i];
    }
    document.getElementById('ModalShowMoreSourcesTable').innerHTML = html;
}

/**
 * enables editing of sources
 * @param {int} id identifier of source
 */
function enableEditSourceShowMore(id) {
    var html = "";
    html += "<td>";
    html += '<select name="SourceEditTypeSelect' + id + '" id="SourceEditTypeSelect' + id + '" class="form-control dropdown-list">'
    var selectedType = false;
    for (var i = 0; i < SourceTypes.length; i++) {
        if (SourceTypes[i].id == Sources[id].typeid && selectedType === false) {
            html += '<option value="' + SourceTypes[i].id + '" selected>' + SourceTypes[i].name + '</option>';
            selectedType = true;
        } else {
            html += '<option value="' + SourceTypes[i].id + '">' + SourceTypes[i].name + '</option>';
        }
    }
    html += '</select>';
    html += "</td>";
    html += "<td>";
    html += '<input type="text" class="form-control textinput-formular" value="' + Sources[id].source + '" required="required" style="background-color: #3b3b3b; color: #ffffff" name="SourceEditDescriptionInput' + id + '" id="SourceEditDescriptionInput' + id + '">';
    html += "</td>";
    html += "<td>"
    html += '<select name="SourceEditRelationSelect' + id + '" id="SourceEditRelationSelect' + id + '" class="form-control dropdown-list">'
    var selectedRelation = false;
    for (var i = 0; i < SourceRelations.length; i++) {
        if (SourceRelations[i].id == Sources[id].relationid && selectedRelation === false) {
            html += '<option value="' + SourceRelations[i].id + '" selected>' + SourceRelations[i].name + '</option>';
            selectedRelation = true;
        } else {
            html += '<option value="' + SourceRelations[i].id + '">' + SourceRelations[i].name + '</option>';
        }
    }
    html += '</select>';
    html += "</td>";
    html += "<td>";
    html += '<button onclick="$(this).tooltip(\'hide\'); this.blur(); saveEditSourceShowMore(' + id + ')" class="btn btn-sq btn-secondary" data-toggle="tooltip" data-placement="top" title="Speichern" id="SourceAddButton"> <img src="images/save-solid-white.svg" width="15px" style="margin-top: -2px"> </button>';
    html += "</td>";
    document.getElementById('showMoreSourceRow' + id).innerHTML = html;
}

/**
 * saves changed values of a source of a point of interest
 * @param {int} id identifier of source
 */
function saveEditSourceShowMore(id) {
    var typeid = document.getElementById('SourceEditTypeSelect' + id).value;
    var source = document.getElementById('SourceEditDescriptionInput' + id).value;
    var relationid = document.getElementById('SourceEditRelationSelect' + id).value;
    var json = {type: 'usp', id: id, typeSource: typeid, source: source, relation: relationid}
    if (sendApiRequest(json, false).code != 0) {
        return;
    }
    var poiid = document.getElementById('poi_id_comment_map').value;
    var html = "<td>";
    var sourcetype = "";
    var reltype = "";
    for (var i = 0; i < SourceTypes.length; i++) {
        if (SourceTypes[i].id == typeid) {
            sourcetype = SourceTypes[i].name;
            break;
        }
    }
    for (var j = 0; j < SourceRelations.length; j++) {
        if (SourceRelations[j].id == relationid) {
            reltype = SourceRelations[j].name;
            break;
        }
    }
    html += sourcetype;
    html += '</td><td>';
    html += source;
    html += '</td><td>';
    html += reltype;
    html += '</td><td>';
    html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); deleteSourceEntryShowMore(" + id + "," + poiid + ")\" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Löschen\"><img src=\"images/trash-alt-solid.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
    html += "<button onclick=\"$(this).tooltip('hide'); this.blur(); enableEditSourceShowMore(" + id + ") \" class=\"btn btn-sq btn-secondary mr-2\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Bearbeiten\"><img src=\"images/pencil-alt-solid.svg\" width=\"15px\" style=\"margin-top: -2px\"></button>";
    html += '</td>';
    document.getElementById('showMoreSourceRow' + id).innerHTML = html;
}

/**
 * deletes a source entry or marks it as deleted
 * @param {int} id identifier of source
 * @param {int} poiid identifier of current point of interest
 */
function deleteSourceEntryShowMore(id, poiid) {
    if (confirm('Quelle wirklich löschen?') === false) {
        return;
    }
    var json = {type: 'des', id: id};
    sendApiRequest(json, false);
    ShowMoreSources(poiid);
}

/**
 * deletes a source finally
 * @param {int} id identifier of source
 * @param {int} poiid identifier of point of interest
 */
function finalDeleteSourceShowMore(id, poiid) {
    if (confirm('Quelle wirklich final löschen?') === false) {
        return;
    }
    var json = {type: 'fds', id: id};
    sendApiRequest(json, false);
    ShowMoreSources(poiid);
}

/**
 * restores a certain source entry
 * @param {int} id identifier of source
 * @param {int} poiid identifier of current point of interest
 */
function restoreSourceShowMore(id, poiid) {
    if (confirm('Quelle qirklich wiederherstellen?') === false) {
        return;
    }
    var json = {type: 'rso', id: id};
    sendApiRequest(json, false);
    ShowMoreSources(poiid);
}

/**
 * validates a certain point of interest
 * @param {int} id identifier of point of interest
 */
function validatePoi(id) {
    if (confirm('Interessenpunkt wirklich validieren?') === false) {
        return;
    }
    var json = {type: 'vpi', id: id};
    if (sendApiRequest(json, false).code == 0) {
        refreshMap();
        showMorePOI(id);
    }
}

/**
 * deletes a point of interest or marks it as deleted
 * @param {int} id identifier of point of interest
 */
function deletePoiMap(id) {
    if (confirm('Interessenpunkt wirklich löschen?') === false) {
        return;
    }
    var json = {
        type: "dpi",
        poiid: id
    };
    var directDelete = sendApiRequest({type: 'ddl'}).data;
    sendApiRequest(json, false);
    if (directDelete) {
        $('#MarkerModalBig').modal('hide');
    } else {
        showMorePOI(id);
    }
    refreshMap();
}

/**
 * restores data of a certain point of interest
 * @param {int} id identifier of point of interest
 */
function restorePoiMap(id) {
    if (confirm('Interessenpunkt wirklich wiederherstellen?') === false) {
        return;
    }
    sendApiRequest({type: 'rpi', IDent: id}, false);
    showMorePOI(id);
    refreshMap();
}

/**
 * deletes a certain point of interest finally
 * @param {int} id identifier of point of interest
 */
function finalDeletePoiMap(id) {
    if (confirm('Interessenpunkt wirklich endgültig löschen?') === false) {
        return;
    }
    sendApiRequest({type: 'fpi', IDent: id}, false);
    $('#MarkerModalBig').modal('hide');
    refreshMap();
}

/**
 * changes selection markers on slider-bar
 */
function changeIconSelect() {
    if (document.getElementById('unvalidatedOnMapShow').checked) {
        document.getElementById('unvalidatedBtnOnMapPic').src = 'images/map-marker-red.svg';
    } else {
        document.getElementById('unvalidatedBtnOnMapPic').src = 'images/map-marker-red-del.svg';
    }
    if (document.getElementById('partValidatedOnMapShow').checked) {
        document.getElementById('partvalidatedBtnOnMapPic').src = 'images/map-marker-blue.svg';
    } else {
        document.getElementById('partvalidatedBtnOnMapPic').src = 'images/map-marker-blue-del.svg';
    }
    if (document.getElementById('validatedOnMapShow').checked) {
        document.getElementById('validatedBtnOnMapPic').src = 'images/map-marker-green.svg';
    } else {
        document.getElementById('validatedBtnOnMapPic').src = 'images/map-marker-green-del.svg';
    }
}

/**
 * sets focus on large map from map.php
 * @param {float} lng longitude
 * @param {float} lat latitude
 */
function setfocus(lng, lat) {
    Karte.setView([lat, lng], zoom = 17.5);
}

/**
 * draw statistic to slider
 * @param {{}} data of pois
 * @param {string} sliderWrapperId - Element to build timeline and steps in
 * @param {int} minElementSize - min step size in px
 * @param {string} mode timeline - maxResolution|prettySteps|scaledTimeline
 *
 * @param {int} fallbackCount - under this step count use fallback mode
 * @param {string} fallbackMode timeline - maxResolution|prettySteps|scaledTimeline
 */
function mapShowSliderStats(data, sliderWrapperId, minElementSize, mode, fallbackMode, fallbackCount) {
    let out = {};
    let min_date = 999999999;
    let max_date = 0;
    let max_val = 0;

    if (typeof minElementSize != 'number' || minElementSize < 1) {
        minElementSize = 50;
    }

    const modes = ["maxResolution", "prettySteps", "scaledTimeline"]
    if (modes.indexOf(mode) == -1) {
        mode = "maxResolution";
    }
    if (modes.indexOf(fallbackMode) == -1) {
        fallbackMode = "maxResolution";
        fallbackCount = 0;
    }
    if (typeof fallbackCount != "number" && fallbackCount <= 1) {
        fallbackCount = 0;
    }
    /* prepare data */
    let second_map = [];
    for (let i = 0; i < data.length; i++) {
        let d = data[i];

        if (typeof d.start != 'undefined' && d.start && typeof d.end != 'undefined' && d.end) {
            let s = parseInt(d.start);
            let e = parseInt(d.end);

            min_date = Math.min(min_date, s);
            max_date = Math.max(max_date, e);

            for (let j = 0; j <= e - s; j++) {
                let k = j + s;
                if (typeof out[k] === 'undefined') {
                    out[k] = [];
                }
                out[k].push(i);
                max_val = Math.max(max_val, out[k].length)
            }
        } else if ((typeof d.start != 'undefined' && d.start) || (typeof d.end != 'undefined' && d.end) || d.duty) {
            second_map.push(i);
        }
        if (d.duty) {
            max_date = Math.max(max_date, new Date().getFullYear());
        }
    }
    for (let i of second_map) {
        let d = data[i];

        let s, e;

        if (typeof d.start != 'undefined' && d.start) {
            s = parseInt(d.start);
            e = d.duty ? new Date().getFullYear() : parseInt(d.start);
        } else if (typeof d.end != 'undefined' && d.end) {
            s = min_date;
            e = parseInt(d.end);
        } else if (d.duty) {
            s = e = new Date().getFullYear();
        } else {
            continue;
        }

        for (let j = 0; j <= e - s; j++) {
            let k = j + s;
            if (typeof out[k] === 'undefined') {
                out[k] = [];
            }
            out[k].push(i);
            max_val = Math.max(max_val, out[k].length)
        }
    }
    let r_min = min_date - min_date % 10;
    let r_max = max_date + 10 - max_date % 10;
    /* draw legend */
    let tl = document.getElementById(sliderWrapperId);

    let d_diff = r_max - r_min;
    let tlStat = document.createElement("div");
    tlStat.className = "timeline-stat-wrapper";
    tl.prepend(tlStat);
    // draw line diagram
    for (const p in out) {
        let a = document.createElement("span");
        a.className = "timeline-stat-el";
        a.title = "" + p + ": " + out[p].length;
        a.style.left = (100 / d_diff * (p - r_min)) + "%";
        a.style.height = (out[p].length * 100 / max_val) + "%";
        a.style.width = (100 / d_diff) + "%";
        tlStat.appendChild(a);
    }

    let resizeSlider = function (fallback_mode) {
        let scale_mode = mode;
        let fallback_count = fallbackCount;

        if (typeof fallback_mode != "undefined" && modes.indexOf(fallback_mode) != -1) {
            scale_mode = fallback_mode;
            fallback_count = 0;
        }
        // calc min size of elements
        let m_width = Math.max(tl.getBoundingClientRect().width, minElementSize * 2, 100);

        // maxResolution     || prettySteps
        if (scale_mode == modes[0] || scale_mode == modes[1]) {
            let d_step_count = parseInt(m_width / minElementSize - 1, 10);
            // pretty correction for prettyStep size
            if (scale_mode == modes[1]) {
                for (let i = d_step_count; i >= 1; i--) {
                    let t_1 = (d_diff / 10);
                    let t_2 = t_1 % i;
                    if ((d_diff / 10) % i == 0) {
                        d_step_count = i;
                        break;
                    }
                }
            }

            if (fallback_count > 0 && fallback_count >= d_step_count) {
                return resizeSlider(fallbackMode);
            }

            let d_step_size = d_diff / d_step_count;

            //remove old nodes if nessesary
            for (let i = 0; i < tl.childNodes.length; i++) {
                let child = tl.childNodes[i];
                if (/(^|\s)(timeline-sep-wrapper)($|\s)/.test(child.className)) {
                    tl.removeChild(child);
                }
            }

            let tlSep = document.createElement("div");
            tlSep.className = "timeline-sep-wrapper";
            if (min_date > max_date || (min_date == 999999999 && max_date == 0)) {
                if (!document.getElementById('yearSliderSpan').classList.contains('notime')) {
                    document.getElementById('yearSliderSpan').classList.add('notime');
                }
                return;
            } else {
                document.getElementById('yearSliderSpan').classList.remove('notime');
            }
            for (let i = 0; i <= d_step_count; i++) {
                let a = document.createElement("span");
                a.className = i % 5 == 0 ? 'timeline-seperator high' : 'timeline-seperator';
                a.style.left = (i * 100 / d_step_count) + '%'
                a.innerHTML = "<span class=\"timeline-year\">" + parseInt(r_min + d_step_size * i) + "</span>";
                tlSep.appendChild(a);
            }
            tl.insertBefore(tlSep, tl.firstElementChild.nextElementSibling);
        }
        //scaledTimeline
        else if (scale_mode == modes[2]) { //
            let d_step_count = m_width / minElementSize - 1;
            let d_step_size = d_diff / d_step_count;

            //remove old nodes if nessesary
            for (let i = 0; i < tl.childNodes.length; i++) {
                let child = tl.childNodes[i];
                if (/(^|\s)(timeline-sep-wrapper)($|\s)/.test(child.className)) {
                    tl.removeChild(child);
                }
            }

            let tlSep = document.createElement("div");
            tlSep.className = "timeline-sep-wrapper";

            const allowed_steps = [1, 2, 5];
            let curr_res = 1;
            let curr_mul = 1;
            outer:
                for (let i = 0; i <= 6; i++) {
                    let res = Math.pow(10, i)
                    for (let j of allowed_steps) {
                        if (d_diff / (j * res) <= d_step_count) {
                            d_step_count = parseInt(d_diff / (j * res));
                            d_step_size = (j * res);
                            curr_res = res;
                            curr_mul = j;
                            break outer;
                        }
                    }
                }
            if (fallback_count > 0 && fallback_count >= d_step_count) {
                return resizeSlider(fallbackMode);
            }
            let d_pos_mod = r_min % d_step_size;
            d_pos_mod = d_pos_mod == 0 ? d_pos_mod : d_step_size - d_pos_mod

            let d_start_pos = d_pos_mod * 100 / d_diff
            for (let i = 0; i <= d_step_count; i++) {
                let a = document.createElement("span");
                a.className = (r_min + d_pos_mod + d_step_size * i) % (curr_res * 10) ? 'timeline-seperator' : 'timeline-seperator high';
                a.style.left = (d_start_pos + (i * d_step_size * 100 / d_diff)) + '%';
                a.innerHTML = "<span class=\"timeline-year\">" + (r_min + d_pos_mod + d_step_size * i) + "</span>";
                if (d_start_pos + (i * d_step_size * 100 / d_diff) > 100) {
                    continue;
                }
                tlSep.appendChild(a);
            }
            tl.insertBefore(tlSep, tl.firstElementChild.nextElementSibling);
        }
    };
    let _debounce;
    let _last_width = -1;
    window.addEventListener("resize", function () {
        if (_debounce != null) {
            clearTimeout(_debounce);
            _debounce = null;
        }
        _debounce = setTimeout(function () {
            _debounce = null;
            let w = tl.getBoundingClientRect().width;
            if (_last_width != w) {
                _last_width = w;
                resizeSlider();
            }
        }, 200);
    });
    //init timeline on site loaded
    if (/complete|interactive|loaded/.test(document.readyState)) {
        resizeSlider();
    }
    document.addEventListener("DOMContentLoaded", resizeSlider, false);
}

/**
 * toggle shown pois via state
 * @param el button
 * @param id id of checkbox to toggle
 */
function toggleShowPoiState(el, id) {
    this.blur();
    document.getElementById(id).checked = !document.getElementById(id).checked;
    changeIconSelect();
    refreshMap();
}

jQuery(function ($) {
    mapShowSliderStats(data, 'yearSliderSpan', 50, "scaledTimeline", "maxResolution", 2);

    //select theatres via validation state
    $('#checkboxesStateShow > button').each(function () {
        if ($(this).data('id')) {
            $(this).on('click', function () {
                toggleShowPoiState(this, $(this).data('id'));
            })
        }
    });
});
