window.dynamicModal = window.dynamicModal || (function () {
    "use strict";
    let out = {};

    out.alert = function(message, options){
        let settings = Object.assign({
            title:'',
            titleClass:'',
            backdrop: true,
            keyboard: true,
            focus: true,
            show: true,
        }, typeof options == "undefined"? {}:options);
        let modalHTML = document.createElement('div');
        modalHTML.className = 'modal';
        modalHTML.tabIndex=-1;
        modalHTML.setAttribute('role', 'dialog');
        modalHTML.innerHTML = '' +
            '<div class="modal-dialog modal-dialog-centered" role="document">' +
            '  <div class="modal-content text-light">' + (settings.title !== ''?
            '    <div class="modal-header border-top border-left border-right rounded-top-7' + (settings.titleClass !== '' ? ' ' + settings.titleClass: '') + '">' +
            '      <h5 class="modal-title"></h5>' +
            '    </div>' : '') +
            '    <div class="modal-body border-left border-right' + (settings.title !== ''?
                '' : ' border-top rounded-top-7') + '">' +
            '      <p></p>' +
            '    </div>' +
            '    <div class="modal-footer border-left border-right border-bottom rounded-bottom-7">' +
            '      <button type="button" class="btn btn-outline-success" data-dismiss="modal">OK</button>' +
            '    </div>' +
            '  </div>' +
            '</div>';
        if (settings.title !== ''){
            modalHTML.getElementsByClassName('modal-title')[0].textContent = settings.title;
        }
        modalHTML.getElementsByClassName('modal-body')[0].firstElementChild.textContent = message;
        document.body.append(modalHTML);
        $(modalHTML).modal(settings)
        .on('hidden.bs.modal', function (e) {
            $(modalHTML).modal("dispose")
                .remove();
            modalHTML = null;
        })
    };
    out.confirm = function(message, callback, options){
        let settings = Object.assign({
            title:'',
            titleClass:'',
            backdrop: false,
            keyboard: false,
            focus: true,
            show: true,
        }, typeof options == "undefined"? {}:options);
        let modalHTML = document.createElement('div');
        modalHTML.className = 'modal';
        modalHTML.tabIndex=-1;
        modalHTML.setAttribute('role', 'dialog');
        modalHTML.innerHTML = '' +
            '<div class="modal-backdrop h-100 w-100 position-fixed modal-dynamicBg"></div>' +
            '<div class="modal-dialog modal-dialog-centered mb-0 mt-0" role="document">' +
            '  <div class="modal-content text-light">' + (settings.title !== ''?
                '    <div class="modal-header border-top border-left border-right rounded-top-7' + (settings.titleClass !== '' ? ' ' + settings.titleClass: '') + '">' +
                '      <h5 class="modal-title"></h5>' +
                '    </div>' : '') +
            '    <div class="modal-body border-left border-right' + (settings.title !== ''?
                '' : ' border-top rounded-top-7') + '">' +
            '      <p></p>' +
            '    </div>' +
            '    <div class="modal-footer border-left border-right border-bottom rounded-bottom-7">' +
            '      <button type="button" class="btn btn-outline-success" data-dismiss="modal">OK</button>' +
            '      <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Abbrechen</button>' +
            '    </div>' +
            '  </div>' +
            '</div>';
        if (settings.title !== ''){
            modalHTML.getElementsByClassName('modal-title')[0].textContent = settings.title;
        }
        modalHTML.getElementsByClassName('modal-body')[0].firstElementChild.textContent = message;
        document.body.append(modalHTML);
        $(modalHTML).modal(settings)
        .on('hidden.bs.modal', function (e) {
            $(modalHTML).modal("dispose")
                .remove();
            modalHTML = null;
        });
        if(typeof callback == "function"){
            $(modalHTML).find(".modal-footer > button").on('click',function(e){
               callback($(this).index() === 0);
            });
        }


    };


    return out;
})();
