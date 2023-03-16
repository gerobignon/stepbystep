"use strict";

// Class definition
var LightAccount = function() {
    // Elements
    var form;
    var submitButton;
    var validator;

    // Handle form
    var handleForm  = function(e) {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
			form,
			{
				fields: {
					'name': {
						validators: {
							notEmpty: {
								message: 'First Name is required'
							}
						}
                    },
					'surname': {
						validators: {
							notEmpty: {
								message: 'Last Name is required'
							}
						}
                    },
					'email': {
                        validators: {
							notEmpty: {
								message: 'Email address is required'
							},
                            emailAddress: {
								message: 'The value is not a valid email address'
							}
						}
					},
					'type': {
						validators: {
							notEmpty: {
								message: 'Type is required'
							}
						}
					},
					'idnumber': {
						validators: {
							notEmpty: {
								message: 'Number is required'
							}
						}
					},
					'idexp': {
						validators: {
							notEmpty: {
								message: 'Expiration date is required'
							}
						}
					},
                    'address': {
                        validators: {
                            notEmpty: {
                                message: 'Address is required'
                            }
                        }
                    },
                    'phone': {
                        validators: {
                            notEmpty: {
                                message: 'Whatsapp number is required'
                            }
                        }
                    },
                    'city': {
                        validators: {
                            notEmpty: {
                                message: 'City is required'
                            }
                        }
                    },
                    'country': {
                        validators: {
                            notEmpty: {
                                message: 'Country is required'
                            }
                        }
                    }
				},
				plugins: {
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

            validator.validate().then(function(status) {
		        if (status == 'Valid') {
                    submitButton.setAttribute('data-kt-indicator', 'on'); 
                    submitButton.disabled = true;
                    
                    $('#account_form').request('onUpdate', {
                        success: function() {
                            submitButton.removeAttribute('data-kt-indicator');

                            submitButton.disabled = false;

                            Swal.fire({
                                title: "Yeah !",
                                text: "New settings successfully transmitted.",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function (result) {
                                if (result.isConfirmed) { 
                                    form.reset();  // reset form
                                                                
                                    location.href = "/account";
                                }
                            });
                        },
                        error : function(message){
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            
                            let data = message.responseText;
                            if(typeof data != "string") this.data = this.data.X_OCTOBER_ERROR_MESSAGE;

                            Swal.fire({
                                title: "Unable to update your account now.",
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
            // Elements
            form = document.querySelector('#account_form');
            submitButton = document.querySelector('#account_submit');

            handleForm ();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    LightAccount.init();
});
