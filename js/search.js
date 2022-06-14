/**
 * generates modal for search results and displays it
 * @param {array} json gethered intel of search request
 */
function mergedSearchModal(json) {
    let data = json.data || [];
    let maxDisplayedResults = 5;
    let results = [];
    let resultMoreBtn = document.createElement("li");
    let first = null;
    resultMoreBtn.className = 'search-theatres-found search-theatres-more';
    resultMoreBtn.innerHTML = "<span class='result-icon fas fa-plus' title='Mehr Ergebnisse anzeigen'></span>";
    resultMoreBtn.addEventListener('mousedown', function(e){
        e.stopPropagation();
        e.preventDefault();
        e.stopImmediatePropagation();
        for (let i=0; i< results.length; i++){
            resultMoreBtn.parentNode.insertBefore(results[i], resultMoreBtn.nextSibling);
        }
        return false;
    }, true)
    resultMoreBtn.addEventListener('touchstart', function(e){
        e.stopPropagation();
        e.preventDefault();
        e.stopImmediatePropagation();
        for (let i=0; i< results.length; i++){
            resultMoreBtn.parentNode.insertBefore(results[i], resultMoreBtn.nextSibling);
        }
        return false;
    }, true)

    $(resultMoreBtn).on('mouseup touchend click ', function(e){
        L.DomEvent.stop(e);
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        resultMoreBtn.parentNode.removeChild(resultMoreBtn);
        return false;

    })

    let appendCounter = 0;
    let hit = false;

    const searchstring = $(".leaflet-control-geocoder-form > input").val().toLowerCase();
    let parent_ul = $('ul.leaflet-control-geocoder-alternatives')[0];
    for(let p in Spielstaette._layers){
        if (Spielstaette._layers.hasOwnProperty(p) && typeof Spielstaette._layers[p]._popup._closeButton != 'undefined'){
            Spielstaette._layers[p]._popup._closeButton.click();
        }
    }
    for (let i = 0; i < data.length; i++) {
        let s = '' +
            (data[i].name? data[i].name + ' ': '') +
            (data[i].current_address? data[i].current_address + ' ': '') +
            (data[i].hist_address? data[i].hist_address + ' ': '') +

            (data[i].Streetname? data[i].Streetname + ' ': '') +
            (data[i].Housenumber? data[i].Housenumber + ' ': '') +
            (data[i].Postalcode? data[i].Postalcode + ' ': '') +
            (data[i].City? data[i].City + ' ': '') +

            (data[i].operator? data[i].operator + ' ': '') +
            (data[i].history? data[i].history + ' ': '');
        if (s.toLowerCase().includes(searchstring)) {
            appendCounter++;
            let el = document.createElement("li");
            el.className = 'search-theatres-found';
            el.innerHTML = "<span class='result-icon fas fa-video'></span><span class='result-name'>" + data[i].name + "</span>";
            let $el = $(el);
            let $hov = $('.leaflet-marker-icon.marker-data-id-' + data[i].poi_id)
            $el.on('mousedown ',function(e){
                e.stopPropagation();
                e.preventDefault();
                e.stopImmediatePropagation();
                L.DomEvent.stop(e);
                focusPOI(data[i].lng , data[i].lat);
                setTimeout(function (){
                    $hov.trigger('click');
                },200);
                return true;
            });
            $el.on('mouseup click',function(e){
                L.DomEvent.stop(e);
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                parent_ul.classList.add('leaflet-control-geocoder-alternatives-minimized');
                $('.leaflet-control-geocoder.leaflet-bar.leaflet-control').removeClass('leaflet-control-geocoder-expanded');
            });

            $el.on('mouseenter mouseleave', function(){
                $hov.trigger('click');
            });
            if (appendCounter === 1){
                first = el;
            }
            if (appendCounter <= maxDisplayedResults ){
                parent_ul.prepend(el);
            } else if (appendCounter === maxDisplayedResults +1){
                first.parentNode.insertBefore(resultMoreBtn, first.nextSibling);
                results.push(el);
            }else{
                results.push(el);
            }

            hit = true;
        }
    }
    if (hit === false) {
        let el = document.createElement("li");
        el.className = 'search-theatres-found';
        el.innerHTML = "<span class='result-name'>Kein Kino gefunden.</span>";

        parent_ul.prepend(el);
    }
}

/**
 * set focus on point of interest
 * @param {float} lng longitutde
 * @param {float} lat latitude
 */
function focusPOI(lng, lat) {
    setfocus(lng, lat);
}
