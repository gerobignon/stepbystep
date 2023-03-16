/* by Light Innovation */

"use strict";

var LightDeposit = function() {
    var form;
    var submitButton;
    var validator;

    var handleForm = function(e) {
        validator = FormValidation.formValidation(
			form,
			{
				fields: {					
					'via': {
                        validators: {
							notEmpty: {
								message: 'Deposit processor is required'
							}
						}
					},
                    'amount': {
                        validators: {
                            notEmpty: {
                                message: 'The amount is required'
                            },
                            numeric: {
                                message: 'The value is not a number',
                            },
                            greaterThan: {
                                message: 'The value must be greater than or equal to 3000 XOF',
                                min: 3000,
                            },
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
                        html: "Do you want to deposit <strong class='fw-boldest text-info fs-4'>" + $("#deposit_form input[name='amount']").val() + "</strong> XOF xith <span class='badge badge-danger fs-6'>" + $("#deposit_form input[name='via']:checked").val().toUpperCase() + "</span> ?",
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
                            
                            $('#deposit_form').request('deposit::onPay', {
                                success: function() {
                                    
                                    submitButton.removeAttribute('data-kt-indicator');
                                    submitButton.disabled = false;

                                    Swal.fire({
                                        title: "Yeah !",
                                        text: "Your transaction has been initiated. You will be redirected to the payment page for completion.",
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
                                            form.querySelector('[name="amount"]').value= "";  
                                                                        
                                            location.href = "/pay_with_"+$("#deposit_form input[name='via']:checked").val();
                                        }
                                    });
                                },
                                error : function(message){
                                    submitButton.removeAttribute('data-kt-indicator');
                                    submitButton.disabled = false;
                                    
                                    let data = message.responseJson;
                                    if(typeof data != "string") this.data = this.data.X_OCTOBER_ERROR_MESSAGE;

                                    Swal.fire({
                                        title: "Unable to make deposit",
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
            form = document.querySelector('#deposit_form');
            submitButton = document.querySelector('#deposit_submit');
            
            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    LightDeposit.init();
});
