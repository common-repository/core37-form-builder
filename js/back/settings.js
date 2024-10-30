/**
 * Created by luis on 9/27/16.
 */
(function(){
    jQuery(function() {

        jQuery("#tabs").tabs();

        var saveCaptchaButton = jQuery('#save_captcha_settings');
        var saveReceivingEmailButton = jQuery('#save-receiving-email');
        var saveGmailSettings = jQuery('#save-gmail-settings');
        var saveSMTPSettings = jQuery('#save-smtp-settings');
        var useDefaultMail = jQuery('#use-default-mail');

        var gmailOption = jQuery('#gmail-sender');
        var smtpOption = jQuery('#smtp-sender');
        var defaultOption = jQuery('#default-sender');

        jQuery('[name=sender]').on('change', function(){
            gmailOption.hide();
            smtpOption.hide();
            defaultOption.hide();

            switch (jQuery(this).val())
            {
                case 'default':
                    defaultOption.show();
                    defaultOption.removeClass('hidden');
                    break;
                case 'gmail':
                    gmailOption.show();
                    gmailOption.removeClass('hidden');
                    break;

                case 'smtp':
                    smtpOption.show();
                    smtpOption.removeClass('hidden');
                    break;
                default:
                    defaultOption.show();
                    defaultOption.removeClass('hidden');
                    break;
            }

        });



        saveCaptchaButton.on('click', function () {
            var recaptchaSiteKey = jQuery('#site_key').val();
            var recaptchaSecretKey = jQuery('#secret_key').val();
            jQuery.post(
                ajaxurl,

                {
                    action: 'core37_form_admin_save_settings',
                    type: 'recaptcha',
                    recaptcha_site_key: recaptchaSiteKey,
                    recaptcha_secret_key: recaptchaSecretKey
                },

                function(response)
                {
                    toastr.info(SUCCESS_CODE_SAVED);
                }
            );

        });

        saveReceivingEmailButton.on('click', function(){
            var receivingEmail = jQuery('#receiving-email').val();

            jQuery.post(
                ajaxurl,
                {
                    action: 'core37_form_admin_save_settings',
                    type: 'receiving-email',
                    email: receivingEmail
                },

                function(response)
                {
                    toastr.info(SUCCESS_EMAIL_SAVED);
                }

            )
        });

        useDefaultMail.on('click', function(){
            jQuery.post(ajaxurl,
                {
                    action: 'core37_form_admin_save_settings',
                    type: 'use-default-mail',
                },
                function(response)
                {
                    toastr.info('Settings saved!');
                }
            )
        });

        saveGmailSettings.on('click', function(){

            var gmailUsername = jQuery('#gmail-username').val().trim();
            var gmailPassword = jQuery('#gmail-password').val().trim();
            var gmailSenderName = jQuery('#gmail-sender-name').val().trim();

            jQuery.post(ajaxurl,
                {
                    action: 'core37_form_admin_save_settings',
                    type: 'gmail',
                    username: gmailUsername,
                    password: gmailPassword,
                    senderName: gmailSenderName
                },
                function(response)
                {
                    toastr.info('Gmail settings saved!');
                }
            )

        });

        saveSMTPSettings.on('click', function(){

            var smtpUsername = jQuery('#smtp-username').val().trim();
            var smtpPassword = jQuery('#smtp-password').val().trim();
            var smtpSenderName = jQuery('#smtp-sender-name').val().trim();
            var smtpHost = jQuery('#smtp-host').val().trim();
            var smtpPort = jQuery('#smtp-port').val().trim();


            jQuery.post(
                ajaxurl,
                {
                    action: 'core37_form_admin_save_settings',
                    username: smtpUsername,
                    password: smtpPassword,
                    senderName: smtpSenderName,
                    host: smtpHost,
                    port: smtpPort,
                    type: 'smtp'
                }, function(response)
                {
                    toastr.info('SMTP Settings saved!')
                }
            );
        });
    });

})();
