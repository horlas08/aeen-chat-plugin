jQuery(document).ready(function($) {
    var otpSent = false;
    var resendInterval;
    var resendTimer = 5;
    var initialResendTimer = 5;

    var $wareResendOtpBtn = $('#ware_resend_otp_btn');
    var $wareResendTimer = $('#ware_resend_timer');
    var $billingPhoneField = $('#billing_phone_field');
    var $wareOtpPopup = $('#ware_otp_popup');
    var $userPhoneNumber = $('#user_phone_number');
    var $checkoutForm = $('form.woocommerce-checkout');
    var otpInputs = $('.otp-input');
    var $verificationMessage = $('#verification_message');

    // Consolidated Functions
    function startResendTimer() {
        $wareResendOtpBtn.prop('disabled', true);
        updateResendTimerText();
        resendInterval = setInterval(function() {
            resendTimer--;
            updateResendTimerText();
            if (resendTimer <= 0) {
                clearInterval(resendInterval);
                $wareResendOtpBtn.prop('disabled', false);
                $wareResendTimer.text('');
                resendTimer = initialResendTimer;
            }
        }, 1000);
    }

    function updateResendTimerText() {
        $wareResendTimer.text(' (' + resendTimer + 's)');
    }

    function showPopupMessage(message, isSuccess) {
        var messageClass = isSuccess ? 'custom-message-success' : 'custom-message-error';

        var messageElement = $billingPhoneField.next('.woocommerce-message.custom-message');
        if (messageElement.length === 0) {
            messageElement = $('<div class="woocommerce-message custom-message"></div>');
            $billingPhoneField.after(messageElement);
        }
        messageElement
            .removeClass('custom-message-success custom-message-error')
            .addClass(messageClass)
            .text(message)
            .show();

        var popupMessageElement = $wareOtpPopup.find('.ware-popup-message');
        if (popupMessageElement.length === 0) {
            popupMessageElement = $('<div class="ware-popup-message"></div>');
            $wareOtpPopup.find('.ware-otp-content').prepend(popupMessageElement);
        }
        popupMessageElement
            .removeClass('custom-message-success custom-message-error')
            .addClass(messageClass)
            .text(message)
            .show();
    }

    async function sendOtp(phoneNumber, firstName) {
        try {
            const response = await $.post({
                url: otpAjax.ajaxurl,
                data: {
                    action: 'send_otp',
                    phone_number: phoneNumber,
                    first_name: firstName,
                    security: otpAjax.nonce
                }
            });
            if (response.success) {
                console.log("OTP sent successfully");
                showPopupMessage(ware_translations.otp_sent_success, true);
                $wareOtpPopup.show();
                otpSent = true;
                startResendTimer();
            } else {
                console.log("Failed to send OTP");
                showPopupMessage(ware_translations.otp_sent_failure, false);
            }
        } catch (error) {
            console.log("Error in sending OTP", error);
            showPopupMessage(ware_translations.otp_sent_failure, false);
        }
    }

    async function verifyOtp() {
        var otp = '';
        otpInputs.each(function() {
            otp += $(this).val();
        });

        try {
            const response = await $.post({
                url: otpAjax.ajaxurl,
                dataType: 'json',
                data: {
                    action: 'verify_otp',
                    otp: otp,
                    security: otpAjax.nonce
                }
            });

            if (response.success) {
                console.log("OTP verified successfully");
                showPopupMessage(ware_translations.otp_verified_success, true);

                // Hide the OTP popup after successful verification
                $wareOtpPopup.hide();

                // Submit the checkout form or continue with the process
                $checkoutForm.submit();
            } else {
                console.log("Incorrect OTP");
                showPopupMessage(ware_translations.otp_incorrect, false);
            }
        } catch (error) {
            console.log("Error in verifying OTP", error);
            showPopupMessage(ware_translations.otp_incorrect, false);
        }
    }

    function clearOtpInputs() {
        otpInputs.val('').prop('disabled', true).css({ 'background-color': '#cacaca', 'border': '#cacaca' });
        otpInputs.first().prop('disabled', false).css('background-color', 'white');
    }

    // Event Handlers
    $(document).on('click', '#place_order', async function(e) {
        if (!otpSent && $wareOtpPopup.is(':hidden')) {
            e.preventDefault();
            var phoneNumber = $('#billing_phone').val();
            var firstName = $('#billing_first_name').val();
            $userPhoneNumber.text(phoneNumber);
            sendOtp(phoneNumber, firstName);
            $wareOtpPopup.show();
            otpInputs.first().focus(); // Always focus on the first input initially
        }
    });

    $(document).on('click', '.ware-otp-popup-close', function() {
        $wareOtpPopup.hide();
        otpSent = false;
        clearOtpInputs();
    });

    // Auto Verify OTP After All Inputs are Filled
    otpInputs.on('input', function() {
        if ($(this).val().length === this.maxLength) {
            $(this).next('.otp-input').prop('disabled', false).css('background-color', 'white').focus();
        }

        // Style the current and unfilled fields accordingly
        otpInputs.each(function() {
            if ($(this).val() === '') {
                $(this).css({ 'background-color': '#cacaca', 'border': '#cacaca' });
            } else {
                $(this).css({ 'background-color': 'white', 'border': '1px solid #ccc' });
            }
        });

        // Check if all OTP inputs are filled
        var allFilled = otpInputs.filter(function() {
            return $(this).val() === '';
        }).length === 0;

        if (allFilled) {
            verifyOtp();
        }
    });

    otpInputs.on('keydown', function(e) {
        if (e.key === 'Backspace') {
            if ($(this).val().length === 0) {
                $(this).prev('.otp-input').focus();
            }
        }
    });

    otpInputs.on('paste', function(e) {
        var clipboardData = e.originalEvent.clipboardData.getData('text');
        otpInputs.each(function(index) {
            $(this).val(clipboardData[index] || '');
        });

        // Automatically verify OTP if all fields are filled
        var allFilled = otpInputs.filter(function() {
            return $(this).val() === '';
        }).length === 0;

        if (allFilled) {
            verifyOtp();
        }
    });

    $(document).on('click', '#ware_verify_otp_btn', verifyOtp);

    $(document).on('click', '#ware_resend_otp_btn', function() {
        var phoneNumber = $('#billing_phone').val();
        var firstName = $('#billing_first_name').val();
        sendOtp(phoneNumber, firstName);
        resendTimer += 5;
        updateResendTimerText();
    });

    $(document).on('click', '#ware_edit_phone_btn', function() {
        $wareOtpPopup.hide();
        otpSent = false;
        $('#billing_phone').focus();
    });

    // Initialize OTP Inputs State
    clearOtpInputs(); // Ensures that the first field is ready for entry when the page is loaded
});
