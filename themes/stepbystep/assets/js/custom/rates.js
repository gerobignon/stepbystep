/* by Light Innovation */

"use strict";

var LightRates = function() {
    var form;
    var submitButton;
    var validator;

    var handleForm = function(e) {
        validator = FormValidation.formValidation(
			form,
			{
				fields: {
                    'currency': {
                        validators: {
                            notEmpty: {
                                message: 'Currency is required'
                            }
                        }
                    } ,
                    'rates': {
                        validators: {
                            notEmpty: {
                                message: 'Rate value is required'
                            }
                        }
                    } 
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.row > div'
                    })
				}
			}
		);		

        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            validator.validate().then(function (status) {
                if (status == 'Valid') {
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;
     
                    $('#rates_form').request('site::onChangeRate  ', {
                        success: function() {
                            
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            Swal.fire({
                                title: "Yeah !",
                                text: "Rates has been updated.",
                                icon: "success",
                                allowEscapeKey: false,
                                allowOutsideClick: false,
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function (result) {
                                if (result.isConfirmed) {        
                                    location.href = "/admin/buys";
                                }
                            });

                        },
                        error : function(message){
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            
                            console.log(message);

                            let data = message.responseJson;
                            if(typeof data != "string") this.data = this.data.X_OCTOBER_ERROR_MESSAGE;

                            Swal.fire({
                                title: "Unable to update rates.",
                                text: data,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, I'll check.",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    });

                } else {
                    // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    Swal.fire({
                        title: "Ooops !",
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });
		});
    }

    // Public functions
    return {
        // Initialization
        init: function() {
            form = document.querySelector('#rates_form');
            submitButton = document.querySelector('#rates_submit');
            
            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    LightRates.init();
});
