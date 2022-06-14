jQuery(function ($) {
    var response = sendApiRequest({type: "mmy"}, false);
    if (response["MinYear"] == response["MaxYear"] || response["MaxYear"] == null || response["MinYear"] == null) {
        document.getElementById("BotRight").setAttribute("class", "hidden");
    } else {
        var maxYear = response["MaxYear"];
        var minYear = response["MinYear"];
        for (date in data) {
            if (data[date].duty) {
                maxYear = new Date().getFullYear();
                break;
            }
        }
        maxYear = parseInt(maxYear, 10) + 10 - parseInt(maxYear,10) % 10;
        minYear = parseInt(minYear,10) - parseInt(minYear,10) % 10;
        yearsSelected['maxYear'] = parseInt(maxYear, 10);
        yearsSelected['minYear'] = parseInt(minYear, 10);
        let _append = document.createElement('input');
        _append.id = 'yearSlider';
        _append.type='text';
        _append.className = 'span2';
        _append.dataset.sliderMin=minYear;
        _append.dataset.sliderMax=maxYear;
        _append.dataset.sliderStep=5;
        _append.dataset.sliderValue="[" + minYear + ',' + maxYear + "]";

        document.getElementById("yearSliderSpan").appendChild(_append);
        var slider = new Slider("#yearSlider", {});
        document.getElementById("minYearSpan").innerHTML = minYear;
        document.getElementById("maxYearSpan").innerHTML = maxYear;
        yearsSelected['end'] = slider.getValue()[1];
        yearsSelected['start'] = slider.getValue()[0];

        /**
         * sets minimal and maximal value of slider
         */
        slider.on("slide", function (sliderValues) {
            document.getElementById("minYearSpan").innerHTML = sliderValues[0];
            document.getElementById("maxYearSpan").innerHTML = sliderValues[1];
        });

        /**
         * refreshs the map if slider stops moving with selected values
         */
        slider.on("slideStop", function () {
            yearsSelected['end'] = slider.getValue()[1];
            yearsSelected['start'] = slider.getValue()[0];
            refreshMap();
        });
    }
});
