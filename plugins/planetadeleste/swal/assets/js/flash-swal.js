/**
 * Flash Swal ($.flashSwal)
 * http://octobercms.com/plugin/planetadeleste-swal
 * Author: Alvaro Canepa<info@planetadeleste.com>
 * Licensed under the MIT license
 */

+(function ($) {
    "use strict";
    var FlashSwal = function () {};

    FlashSwal.prototype = {
        types            : ["warning", "error", "success", "info"],
        swalFlash        : false,
        swalOptions      : [],
        swalGlobalOptions: [],
        init             : function () {
            // Do something
        },

        /**
         * Try to show ajax error messages
         *
         * @param event
         * @param message
         */
        ajaxErrorMessage: function (event, message) {
            if (!message) {return;}

            var options = {};

            if (this.swalGlobalOptions.hasOwnProperty('ajaxErrorMessage')) {
                options = this.swalGlobalOptions.ajaxErrorMessage;
            } else {
                options.showCancelButton = false;
            }

            options.title = message;

            $.flashSwal.swal(options);

            // Prevent the default alert() message
            event.preventDefault();
        },

        /**
         * Try to show ajax error
         *
         * @param event
         * @param obj
         * @param context
         * @param status
         */
        ajaxError: function (event, obj, context, status) {
            var message, options = {};

            if (this.swalGlobalOptions.hasOwnProperty('ajaxError')) {
                options = this.swalGlobalOptions.ajaxError;
            } else {
                options.showCancelButton = false;
            }

            if ($.isPlainObject(status)) {
                if (typeof status.responseJSON === 'undefined') {
                    message = status.responseText;
                    var responseText = status.responseText.match(/^\"(.*)\"\s/);
                    if (responseText && typeof responseText === 'object') {
                        message = responseText[1];
                    }
                } else {
                    message = (status.responseJSON['X_OCTOBER_ERROR_MESSAGE']) ? status.responseJSON.X_OCTOBER_ERROR_MESSAGE : status.responseJSON.X_OCTOBER_ERROR_FIELDS;
                }

                if ($.type(message) === 'string') {
                    options.title = message;
                } else {
                    $.extend(options, message);
                }

                $.flashSwal.swal(options);
            }
        },

        ajaxComplete: function (event, obj, context) {
            var self = this;

            if ($.isPlainObject(context)) {

                if (context.hasOwnProperty('headers')) {
                    if (context.headers.hasOwnProperty('X-OCTOBER-REQUEST-HANDLER')) {
                        return false;
                    }
                }

                if (context.hasOwnProperty('context')) {
                    if ($.isPlainObject(context.context)) {
                        // Prevent infinite loop
                        if (context.context.hasOwnProperty('handler')) {
                            if (context.context.handler === 'onShowFlashMsg') {
                                return false;
                            }
                        }
                        // Prevent messages on redirect from Ajax
                        if (context.context.hasOwnProperty('options')) {
                            if (context.context.options.hasOwnProperty('redirect')) {
                                return false;
                            }
                        }
                    }

                }
                // Prevent messages on redirect
                if (context.hasOwnProperty('responseJSON')) {
                    if ($.isPlainObject(context.responseJSON)) {
                        var response = context.responseJSON;
                        if (response.hasOwnProperty('X_OCTOBER_REDIRECT')) {
                            return false;
                        }

                        /*if (response.hasOwnProperty('X_OCTOBER_FLASH_MESSAGES')) {
                         response = response.X_OCTOBER_FLASH_MESSAGES;
                         }*/

                        if ($.inArray(response, this.types)) {
                            $.each(this.types, function (index, type) {
                                if (response.hasOwnProperty(type)) {
                                    self.swal({
                                        type : type,
                                        title: response[type]
                                    });
                                }
                            });
                            return false;
                        }
                    }
                }
            }

            //this.showFlashMessages();
        },

        ajaxConfirmMessage: function (event, message) {
            if (!message) {return;}
            var options = {};

            if (this.swalGlobalOptions.hasOwnProperty('ajaxConfirmMessage')) {
                options = this.swalGlobalOptions.ajaxConfirmMessage;
            } else {
                options = {
                    showCancelButton  : true,
                    confirmButtonClass: 'btn-primary'
                };
            }

            options.title = message;


            $.flashSwal.swal(options, function (isConfirm) {
                isConfirm ? event.promise.resolve() : event.promise.reject();
            });

            event.preventDefault();
            return true;
        },

        /**
         * Find for ajax messages
         */
        showFlashMessages: function () {
            var options = {},
                self = this
            ;

            if (this.swalGlobalOptions.hasOwnProperty('ajaxFlashMessages')) {
                options = this.swalGlobalOptions.ajaxFlashMessages;
            }

            $.request('onShowFlashMsg', {
                success: function (data) {
                    if (!$.isPlainObject(data)) {
                        return false;
                    }

                    if(!data.hasOwnProperty('msgs')) {
                        return false;
                    }

                    $.each(data.msgs, function (type, msg) {
                        var t = type === 'error' ? 'danger' : type;
                        data.options.title = msg;
                        data.options.type = type;

                        $.extend(options, data.options);
                        options.showCancelButton = false;

                        self.swal(options);
                    });
                }
            });
        },

        /**
         * Show the Flash message
         *
         * @param options
         * @param callback function
         */
        swal: function (options, callback) {
            if ($.type(options) === 'string') {
                options = { title: options };
            }

            if ($.type(this.swalFlash) !== 'undefined') {
                //document.swalOptions.title = message;
                var swalOptions = ($.type(this.swalOptions) === 'string') ? JSON.parse(this.swalOptions) : this.swalOptions;
                options = $.extend({}, swalOptions, options);
            }

            options = $.extend({ type: 'error', timer: 10000 }, options);
            if ($.type(callback) !== 'function') {callback = null;}

            if ($.isArray(options.title)) {
                options.title = options.title[options.title.length - 1];
            }

            swal(options, callback);
        },

        flashMessage: function (data, el) {
            var
                options = $.extend({}, {
                    class   : 'success',
                    text    : 'Default text',
                    interval: 5
                }, data),
                $element = $(el)
            ;

            this.swal({
                title: options.text,
                type : options.class,
                timer: options.interval * 1000
            });
        }
    };

    if ($.flashSwal === undefined) {
        $.flashSwal = {};
    }

    $.flashSwal = new FlashSwal();

    // Overide $.oc.flashMsg
    if ($.oc !== undefined) {
        if ($.oc.flashMsg !== undefined) {
            $.oc.flashMsg = $.proxy($.flashSwal.flashMessage, $.flashSwal);
        }
    }

    $(document).ready(function () {
        $.flashSwal.init();
    });

    $(window).on('load', function () {
        $.flashSwal.init();
    });


    $(window)
        .on('ajaxErrorMessage', $.proxy($.flashSwal.ajaxErrorMessage, $.flashSwal))
        .on('ajaxError', $.proxy($.flashSwal.ajaxError, $.flashSwal))
        .on('ajaxComplete', $.proxy($.flashSwal.ajaxComplete, $.flashSwal))
        .on('ajaxConfirmMessage', $.proxy($.flashSwal.ajaxConfirmMessage, $.flashSwal))
    ;

    $(document)
        .ready(function () {
            $.flashSwal.showFlashMessages();

            $('[data-control="swal"]').on('click', function () {
                var $el = $(this),
                    options = $el.data();

                if (options.hasOwnProperty('title') || options.hasOwnProperty('originalTitle')) {
                    options.title = options.title || options.originalTitle;
                }

                if (options.hasOwnProperty('modalTarget')) {
                    options.text = $(options.modalTarget).html();
                    options.html = true;
                }

                if (options.hasOwnProperty('title')) {
                    $.flashSwal.swal(options);
                }

            });
        });

})(jQuery);