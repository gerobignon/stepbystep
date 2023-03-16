"use strict";

var LightLogin = function() {
    var form;
    var submitButton;
    var validator;

    var handleForm = function(e) {
        validator = FormValidation.formValidation(
			form,
			{
				fields: {					
					'login': {
                        validators: {
							notEmpty: {
								message: 'Email address is required'
							},
                            emailAddress: {
								message: 'The value is not a valid email address'
							}
						}
					},
                    'password': {
                        validators: {
                            notEmpty: {
                                message: 'The password is required'
                            }
                        }
                    } 
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row'
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
                    
                    $('#login_form').request('onSignin', {
                        success: function() {
                            
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            Swal.fire({
                                title: "Yeah !",
                                text: "You have successfully logged in!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function (result) {
                                if (result.isConfirmed) { 
                                    form.querySelector('[name="login"]').value= "";
                                    form.querySelector('[name="password"]').value= "";  
                                                                
                                    location.href = "/dashboard";
                                }
                            });
                        },
                        error : function(message){
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            
                            let data = message.responseText;
                            if(typeof data != "string") this.data = this.data.X_OCTOBER_ERROR_MESSAGE;

                            Swal.fire({
                                title: "Unable to log in",
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
            form = document.querySelector('#login_form');
            submitButton = document.querySelector('#login_submit');
            
            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    LightLogin.init();
});
