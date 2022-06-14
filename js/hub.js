announcements = sendApiRequest({type: 'gca'}, false).data;

window.onload = function () {
    loadAnnouncement();
}

/**
 * loads announcement modal
 */
function loadAnnouncement() {
    if (announcements.length > 0) {
        var test = false;
        while (!test) {
            if (announcements.length <= 0) {
                test = true
            } else {
                if (testCookie("Announcement" + announcements[0].id) === false && test === false) {
                    document.getElementById('announcementModalMainContent').innerHTML = announcements[0].content;
                    document.getElementById('announcementModalMainTitle').innerHTML = announcements[0].title;
                    $('#AnnouncementModal').modal();
                    test = true
                } else {
                    announcements.splice(0, 1);
                }
            }
        }
    }
}

/**
 * loads following announcements or closes modal
 */
function closeAnnouncement(){
    setCookie("Announcement" + announcements[0].id, true, announcements[0].end);
    announcements.splice(0,1);
    if (announcements.length > 0) {
        loadAnnouncement();
    } else {
        $('#AnnouncementModal').modal('hide');
    }
}