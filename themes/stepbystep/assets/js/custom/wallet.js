/* by Light Innovation */

"use strict";

var LightWallet = function() {
    var form;
    var submitButton;
    var validator;

    var handleForm = function(e) {
        validator = FormValidation.formValidation(
			form,
			{
				fields: {					
					'name': {
                        validators: {
							notEmpty: {
								message: 'Name is required'
							}
						}
					},
                    'currency': {
                        validators: {
                            notEmpty: {
                                message: 'Currency is required'
                            }
                        }
                    } ,
                    'wallet': {
                        validators: {
                            notEmpty: {
                                message: 'Wallet address is required'
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
     
                    $('#wallet_form').request('site::onAddWallet  ', {
                        success: function() {
                            
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            Swal.fire({
                                title: "Yeah !",
                                text: "Your wallet address has been added.",
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
                                    location.href = "/buy";
                                }
                            });

                        },
                        error : function(message){
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            console.log(message);
                            let data = message.responseText;
                            if(typeof data != "string") this.data = this.data.X_OCTOBER_ERROR_MESSAGE;

                            Swal.fire({
                                title: "Unable to add wallet address.",
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
            form = document.querySelector('#wallet_form');
            submitButton = document.querySelector('#wallet_submit');
            
            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    LightWallet.init();
});
