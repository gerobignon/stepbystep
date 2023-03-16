/* by Light Innovation */

"use strict";

var LightHometext = function() {
    var form;
    var submitButton;
    var validator;

    var handleForm = function(e) {
        validator = FormValidation.formValidation(
			form,
			{
				fields: {					
					'hometext': {
                        validators: {
							notEmpty: {
								message: 'The text is required'
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

                    $('#hometext_form').request('site::onChangeHomeText', {
                        success: function() {
                            
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            Swal.fire({
                                title: "Yeah !",
                                text: "The warning text has been changed.",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function (result) {
                                if (result.isConfirmed) {     
                                    location.href = "/admin/options";
                                }
                            });
                        },
                        error : function(message){
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            if(message.responseJSON) var data = message.responseJSON.result;
							else{
								var data = message.responseText;
                            	if(typeof data != "string") data = this.data.X_OCTOBER_ERROR_MESSAGE.result;
							}

                            Swal.fire({
                                title: "Unable to change this text",
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
            form = document.querySelector('#hometext_form');
            submitButton = document.querySelector('#hometext_submit');
            
            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    LightHometext.init();
});
