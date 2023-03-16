"use strict";

// Class definition
var LightValidation = function () {
	// Elements
	var modal;	
	var modalEl;

	var stepper;
	var form;
	var formSubmitButton;
	var formContinueButton;

	// Variables
	var stepperObj;
	var validations = [];

	// Private Functions
	var initStepper = function () {
		// Initialize Stepper
		stepperObj = new KTStepper(stepper);

		// Stepper change event
		stepperObj.on('kt.stepper.changed', function (stepper) {
			if (stepperObj.getCurrentStepIndex() === 3) {
				formSubmitButton.classList.remove('d-none');
				formSubmitButton.classList.add('d-inline-block');
				formContinueButton.classList.add('d-none');
			} else if (stepperObj.getCurrentStepIndex() === 4) {
				formSubmitButton.classList.add('d-none');
				formContinueButton.classList.add('d-none');
			} else {
				formSubmitButton.classList.remove('d-inline-block');
				formSubmitButton.classList.remove('d-none');
				formContinueButton.classList.remove('d-none');
			}
		});

		// Validation before going to next page
		stepperObj.on('kt.stepper.next', function (stepper) {
			console.log('stepper.next');

			// Validate form before change stepper step
			var validator = validations[stepper.getCurrentStepIndex() - 1]; // get validator for currnt step

			if (validator) {
				validator.validate().then(function (status) {
					console.log('validated!');

					if (status == 'Valid') {
						stepper.goNext();

						//KTUtil.scrollTop();
					} else {
						Swal.fire({
							text: "Sorry, looks like there are some errors detected, please try again.",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Ok, got it!",
							customClass: {
								confirmButton: "btn btn-light"
							}
						}).then(function () {
							//KTUtil.scrollTop();
						});
					}
				});
			} else {
				stepper.goNext();

				//KTUtil.scrollTop();
			}
		});

		// Prev event
		stepperObj.on('kt.stepper.previous', function (stepper) {
			console.log('stepper.previous');

			stepper.goPrevious();
			//KTUtil.scrollTop();
		});

        // Init select2 country options
        // Format options
        const optionFormat = (item) => {
            if ( !item.id ) {
                return item.text;
            }

            var span = document.createElement('span');
            var template = '';

            template += '<img src="' + item.element.getAttribute('data-kt-select2-country') + '" class="rounded-circle h-20px me-2" alt="image"/>';
            template += item.text;

            span.innerHTML = template;

            return $(span);
        }


		$('#country').select2({
            placeholder: "Select a country",
            minimumResultsForSearch: Infinity,
            templateSelection: optionFormat,
            templateResult: optionFormat
        });

	}

	var handleForm = function() {
		formSubmitButton.addEventListener('click', function (e) {
			// Validate form before change stepper step
			var validator = validations[2]; // get validator for last form

			validator.validate().then(function (status) {
				console.log('validated!');

				if (status == 'Valid') {
					// Prevent default button action
					e.preventDefault();

					// Disable button to avoid multiple click 
					formSubmitButton.disabled = true;

					// Show loading indication
					formSubmitButton.setAttribute('data-kt-indicator', 'on');

					$('#kt_create_account_form').request('validator::onValidate', {
						files: true,
                        success: function() {
							console.log( $('#kt_create_account_form').serializeArray(FormData));
							$('#kt_create_account_form').request('onUpdate', {
								success: function() {

									formSubmitButton.removeAttribute('data-kt-indicator');
									formSubmitButton.disabled = false;
									stepperObj.goNext();

									$('#disc').addClass('d-none');

									Swal.fire({
										title: "Yeah !",
										text: "Your validation request is sent with success.",
										icon: "success",
										buttonsStyling: false,
										confirmButtonText: "Ok, got it!",
										customClass: {
											confirmButton: "btn btn-primary"
										}
									});
								},
								error : function(message){
									formSubmitButton.removeAttribute('data-kt-indicator');
									formSubmitButton.disabled = false;
									
									let data = message.responseText;
									if(typeof data != "string") this.data = this.data.X_OCTOBER_ERROR_MESSAGE;
		
									$.request('validator::onReset');

									Swal.fire({
										title: "Unable to submit",
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
                        },
                        error : function(message){
                            formSubmitButton.removeAttribute('data-kt-indicator');
                            formSubmitButton.disabled = false;

							if(message.responseJSON.result) var data = message.responseJSON.result;
							else{
								var data = message.responseText;
                            	if(typeof data != "string") data = this.data.X_OCTOBER_ERROR_MESSAGE.result;
							}

                            console.log(data);
                            Swal.fire({
                                title: "Unable to submit",
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
					Swal.fire({
						text: "Sorry, looks like there are some errors detected, please try again.",
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Ok, got it!",
						customClass: {
							confirmButton: "btn btn-light"
						}
					}).then(function () {
						//KTUtil.scrollTop();
					});
				}
			});
		});
	}

	var initValidation = function () {
		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		// Step 1
		validations.push(FormValidation.formValidation(
			form,
			{
				fields: {
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
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap5({
						rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
					})
				}
			}
		));

		// Step 2
		validations.push(FormValidation.formValidation(
			form,
			{
				fields: {
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
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap5({
						rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
					})
				}
			}
		));

		// Step 3
		validations.push(FormValidation.formValidation(
			form,
			{
				fields: {
					'file': {
						validators: {
							notEmpty: {
								message: 'Please select an image'
							},
							file: {
								extension: 'jpg,jpeg,png',
								type: 'image/jpeg,image/png',
								message: 'The selected file is not valid'
							}
						}
					},
					'selfie': {
						validators: {
							notEmpty: {
								message: 'Please select an image'
							},
							file: {
								extension: 'jpg,jpeg,png',
								type: 'image/jpeg,image/png',
								message: 'The selected file is not valid'
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap5({
						rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
					})
				}
			}
		));
	}

	var handleFormSubmit = function() {
		console.log("sending...");
	}

	return {
		// Public Functions
		init: function () {
			// Elements
			modalEl = document.querySelector('#kt_modal_create_account');
			if (modalEl) {
				modal = new bootstrap.Modal(modalEl);	
			}					

			stepper = document.querySelector('#kt_create_account_stepper');
			form = stepper.querySelector('#kt_create_account_form');
			formSubmitButton = stepper.querySelector('[data-kt-stepper-action="submit"]');
			formContinueButton = stepper.querySelector('[data-kt-stepper-action="next"]');

			initStepper();
			initValidation();
			handleForm();
		}
	};
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    LightValidation.init();
});