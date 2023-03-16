"use strict";

// Class definition
var LightChangePass = function() {
    // Elements
    var form;
    var submitButton;
    var validator;
    var passwordMeter;

    var initSettings = function () {

        // UI elements
        var passwordMainEl = document.getElementById('signin_password');
        var passwordEditEl = document.getElementById('signin_password_edit');

        // button elements
        var passwordChange = document.getElementById('signin_password_button');
        var passwordCancel = document.getElementById('password_cancel');

        // toggle UI

        passwordChange.querySelector('button').addEventListener('click', function () {
            toggleChangePassword();
        });

        passwordCancel.addEventListener('click', function () {
            toggleChangePassword();
        });

        var toggleChangePassword = function () {
            passwordMainEl.classList.toggle('d-none');
            passwordChange.classList.toggle('d-none');
            passwordEditEl.classList.toggle('d-none');
        }
    }

    // Handle form
    var handleForm  = function(e) {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
			form,
			{
				fields: {
                    'password': {
                        validators: {
                            notEmpty: {
                                message: 'The new password is required'
                            },
                            callback: {
                                message: 'Please enter valid password',
                                callback: function(input) {
                                    if (input.value.length > 0) {
                                        return validatePassword();
                                    }
                                }
                            }
                        }
                    },
                    'password_current': {
                        validators: {
                            notEmpty: {
                                message: 'The current password is required'
                            }
                        }
                    },
                    'password_confirmation': {
                        validators: {
                            notEmpty: {
                                message: 'The password confirmation is required'
                            },
                            identical: {
                                compare: function() {
                                    return form.querySelector('[name="password"]').value;
                                },
                                message: 'The password and its confirm are not the same'
                            }
                        }
                    }
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger({
                        event: {
                            password: false
                        }  
                    }),
					bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
				}
			}
		);

        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            validator.revalidateField('password');

            validator.validate().then(function(status) {
		        if (status == 'Valid') {
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;
                    
                    $('#changepass_form').request('onUpdate', {
                        success: function() {
                            submitButton.removeAttribute('data-kt-indicator');

                            submitButton.disabled = false;

                            Swal.fire({
                                title: "Yeah !",
                                text: "Your password has been successfully changed.",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function (result) {
                                if (result.isConfirmed) { 
                                    form.reset();  // reset form                    
                                    passwordMeter.reset();  // reset password meter
                                                                
                                    location.href = "/account";
                                }
                            });
                        },
                        error : function(message){
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            console.log(message);

                            Swal.fire({
                                title: "Unable to update your password",
                                text: message.responseJSON.X_OCTOBER_ERROR_MESSAGE,
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

        // Handle password input
        form.querySelector('input[name="password"]').addEventListener('input', function() {
            if (this.value.length > 0) {
                validator.updateFieldStatus('password', 'NotValidated');
            }
        });
    }

    // Password input validation
    var validatePassword = function() {
        return  (passwordMeter.getScore() === 100);
    }

    // Public functions
    return {
        // Initialization
        init: function() {
            // Elements
            form = document.querySelector('#changepass_form');
            submitButton = document.querySelector('#changepass_submit');
            passwordMeter = KTPasswordMeter.getInstance(form.querySelector('[data-kt-password-meter="true"]'));

            initSettings();
            handleForm ();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    LightChangePass.init();
});
