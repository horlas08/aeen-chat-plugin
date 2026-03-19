jQuery(document).ready(function($) {

    $(document).on('click', '.ware_login_btn[data-button=login_w_wa]', function(){
        $('.woocommerce-form.login .form-row:not(.ware):not(.ware-login-otp-submit)').slideUp();
        $('.woocommerce-form.login .form-row.ware').slideDown();
        $(this).hide();
        $('.ware_login_btn[data-button=login_w_email]').show();
        $('.woocommerce-form-login__submit[type=submit]').addClass('ware-ajax-login');
        $('button.ware-ajax-login').prop('disabled', true);
        if($('#login_otp').val() > 0){
            $('button.ware-ajax-login').prop('disabled', false);
        }
        if($('#login_your_whatsapp').val().length > 0){
            $('.woocommerce-form.login .ware-input').slideDown();
        }
    });

    $(document).on('click', '.ware_login_btn[data-button=login_w_email]', function(){
        $('.woocommerce-form.login .form-row:not(.ware-login-otp-submit)').slideDown();
        $('.woocommerce-form.login .form-row.ware').slideUp();
        $('.woocommerce-form.login .ware-input').hide();
        $(this).hide();
        $('.ware_login_btn[data-button=login_w_wa]').show();
        $('button.ware-ajax-login').prop('disabled', false);
        $('.woocommerce-form-login__submit[type=submit]').removeClass('ware-ajax-login');
    });

});
