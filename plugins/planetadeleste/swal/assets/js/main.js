jQuery(function ($) {
    $(window).on('ajaxErrorMessage', function(event, message){
        if (!message) return;

        swal({
            title: message,
            // type: 'error',
            confirmButtonClass: 'btn-default'
        });

        // Prevent the default alert() message
        event.preventDefault()
    });

    $(window).on('ajaxError', function (event, obj, context, status) {
        var message;
        var showfm = function (message) {
            if ($.isArray(message) || $.isPlainObject(message)) {
                message = message[key][0];
            }
            if (document.swalFlash) {
                document.swalOptions.title = message;
                swal(document.swalOptions);
            } else {
                swal({
                    title: message,
                    type: 'error',
                    timer: 10000
                });
            }
        };
        if ($.isPlainObject(status)) {
            if (typeof status.responseJSON === 'undefined') {
                message = status.responseText;
                var responseText = status.responseText.match(/^\"(.*)\"\s/);
                if (responseText && typeof responseText == 'object') {
                    message = responseText[1];
                }
            } else {
                message = (status.responseJSON['X_OCTOBER_ERROR_MESSAGE']) ? status.responseJSON.X_OCTOBER_ERROR_MESSAGE : status.responseJSON.X_OCTOBER_ERROR_FIELDS;
            }
            if (typeof message === 'object') {
                for (var key in message) {
                    showfm(message);
                }
            } else {
                showfm(message);
            }
        }
    });

    $(document).ready(function () {
        $.request('onShowFlashMsg', {
            success: function (data) {
                if (data != '') {
                    $.each(data.msgs, function (type, msg) {
                        var t = type == 'error' ? 'danger' : type;
                        data.options.title = msg;
                        swal(data.options);
                    });
                }
            }
        });

        $('[data-control="swal"]').on('click', function () {
            var $el = $(this),
                options = $el.data();

            if(options['title'] || options['originalTitle']){
                options.title = options['title'] || options['originalTitle'];
            }

            if(options['modalTarget']){
                options.text = $(options.modalTarget).html();
                options.html = true;
            }

            if(options['title']){
                swal(options);
            }

        });
    });

});
