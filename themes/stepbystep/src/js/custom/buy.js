/* by Light Innovation */

"use strict";

var LightBuy = function() {
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
								message: 'Crypto-currency is required'
							}
						}
					},
                    'want': {
                        validators: {
                            notEmpty: {
                                message: 'The amount is required'
                            },
                            numeric: {
                                message: 'The value is not a number',
                            },
                            greaterThan: {
                                message: 'The value must be greater than or equal to $2',
                                min: 2,
                            },
                        }
                    } ,
                    'give': {
                        validators: {
                            notEmpty: {
                                message: 'The amount is required'
                            },
                            numeric: {
                                message: 'The value is not a number',
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

                    Swal.fire({
                        title: "Please check the information and confirm !",
                        html: "Do you want to buy <span class='badge badge-danger fs-6'>" + $("#buy_form input[name='currency']:checked").val().toUpperCase() + "</span> for <strong class='fw-boldest text-info fs-4'>" + $("#buy_form input[name='give']").val() + "</strong> XOF ?",
                        icon: "question",
                        buttonsStyling: false,
                        showCancelButton: true,
                        confirmButtonText: "Ok, got it!",
                        cancelButtonText: 'Nope, cancel it',
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: 'btn btn-danger'
                        }
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            
                            $('#buy_form').request('crypto::onBuy', {
                                success: function() {
                                    
                                    submitButton.removeAttribute('data-kt-indicator');
                                    submitButton.disabled = false;

                                    Swal.fire({
                                        title: "Yeah !",
                                        text: "Your transaction has been initiated. You will receive a confirmation email as soon as the purchase is completed.",
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
                                            form.querySelector('[name="give"]').value= "";  
                                            form.querySelector('[name="want"]').value= "";  
                                                                        
                                            location.href = "/buy";
                                        }
                                    });

                                },
                                error : function(message){
                                    submitButton.removeAttribute('data-kt-indicator');
                                    submitButton.disabled = false;
                                    
                                    console.log(message);

                                    let data;

                                    if(message.responseJSON.result)  data = message.responseJSON.result;
                                    else {
                                        data = message.responseJson;
                                        if(typeof data != "string") data = data.X_OCTOBER_ERROR_MESSAGE;
                                    }

                                    Swal.fire({
                                        title: "Unable to make purchases",
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
                        } else{ 
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
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
            form = document.querySelector('#buy_form');
            submitButton = document.querySelector('#buy_submit');
            
            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    LightBuy.init();
});
