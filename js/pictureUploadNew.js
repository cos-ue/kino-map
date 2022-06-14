jQuery(function ($) {
    $(document).on('change', '.btn-file :file', function () {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function (event, label) {
        console.log('used');
        var input = $(this).parents('.input-group').find(':text');
            log = label;
            console.log(log);
        if (input.length) {
            input.val(log);
        } else {
            if (log) alert(log);
        }

    });

    /**
     * displays picture preview
     */
    $("#bild, #imgInp").on('change', function (e) {
       // readURL(this);
        var filename = this.value.split(String.fromCharCode(92));
        if (document.getElementById("formularImageName") !== null){
            document.getElementById("formularImageName").value = filename[filename.length-1];
        }
        if (document.getElementById("imageName") !== null){
            document.getElementById("imageName").value = filename[filename.length-1];
        }

        //img to show image
        let obj = null;
        if (document.getElementsByClassName('new-img-preview').length){
            obj = document.getElementsByClassName('new-img-preview')[0];
        }else if (document.getElementById('img-upload')){
            obj = document.getElementById('img-upload');
        }
        //read as createObjectURL
        if (obj !== null){
            if (e.target.files.length){
                obj.src = URL.createObjectURL(e.target.files[0]);
                obj.classList.remove('hide');
            }else{
                obj.src = '';
                obj.classList.add('hide');
            }
            obj.onload = function() {
                URL.revokeObjectURL(obj.src) // free memory
            }
        }
    });
});