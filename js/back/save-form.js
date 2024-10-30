/**
 * Created by luis on 6/22/16.
 */
(function (jQuery) {

    /*
    | Get form HTML, collect extra form information and send to server
     */

    //learn to use post with backbone instead
    //http://wordpress.stackexchange.com/questions/119765/using-backbone-with-the-wordpress-ajax-api

    jQuery(document).on('click', '#save-form', function(){

        console.log(core37Form);
        if (jQuery.trim(core37Form.formName) == '')
        {
            toastr.error(ERROR_MISSING_FORM_NAME);
            return;
        }

        /**
         * Compile content from individual steps, this is needed when we have multiple step forms
         * @type {string}
         */
        var formContent = '';
        var steps = jQuery('#construction-site .c37-step');

        _.each(steps, function(step){
            //console.log(step);
            formContent += encodeURIComponent(step.outerHTML);

        });

        jQuery.post(ajaxurl,
            {
                formContent: formContent,
                elementsActions: JSON.stringify(elementsActions),
                formID: core37Form.formID,
                formSettings: JSON.stringify(core37Form.formSettings),
                formName: core37Form.formName,
                action: 'core37_save_form',
                formValidation: JSON.stringify(validation),
                formCSSCode: encodeURIComponent(jQuery('#element-styles').text()),
                formCSSObject: JSON.stringify(elementsStyles)
            },
        function(response){
            //update current form ID to the generated ID
            core37Form.formID = parseInt(response);

            toastr.success(SUCCESS_FORM_SAVED);

        });


    });

    //load all available forms
    jQuery(document).on('click', '#get-forms', function(response){
        jQuery.post(
            ajaxurl,
            {action: 'core37_list_forms'},
            function(response)
            {
                jQuery('#options-window').html('');
                jQuery('#options-window').append('<div id="forms-list"></div>');
                var model = new C37ElementModel({
                });
                model.set('forms', JSON.parse(response));
                new FormsList({
                    model: model
                });
            }
        )

    });

    //load a single form based on form ID
    jQuery(document).on('click', '.form-edit i.fa-pencil', function(){

        var formID = jQuery(this).closest('li').attr('form-id');

        jQuery.post(
            ajaxurl,
            {
                action: 'core37_load_form',
                formID: formID
            },

            function(response)
            {
                var data = JSON.parse(response);
                //console.log(data);
                //update form code
                //data.formData.post_content contains only HTML of the steps
                //we need to construct the form also from the form settings

                //update element actions
                elementsActions = JSON.parse(data.elementsActions);
                core37Form.formSettings = JSON.parse(data.formSettings);
                validation = JSON.parse(data.formValidation);
                elementsStyles = JSON.parse(data.formCSSObject);
                jQuery('#element-styles').remove();
                jQuery('head').append('<style id="element-styles"></style>');
                jQuery('#element-styles').text(decodeURIComponent(data.formCSSCode));

                var formHTML =
                    '<form class="c37-form c37-container '+core37Form.formSettings.presetCSSStyle+' " method="'+
                    core37Form.formSettings.method
                    +'" action="'+
                    core37Form.formSettings.action
                    +'" enctype="application/x-www-form-urlencoded" id="'+
                    core37Form.formSettings.cssID
                    +'" style="width: '+core37Form.formSettings.width+'px;">'+
                        decodeURIComponent(data.formData.post_content)
                + '</form>';
                jQuery('#construction-site').html(formHTML);

                //update form name
                core37Form.formName = data.formData.post_title;

                //update form ID
                core37Form.formID = formID;


                /**
                 * On page load, make the current c37-box boxes droppable
                 */


                makeFromDroppable(jQuery);
                makeC37BoxDroppable(jQuery);
                makeC37StepDroppable(jQuery);

            }
        )

    });

    //delete a form based on form ID
    jQuery(document).on('click', '.form-edit i.fa-trash', function(){
        var formID = jQuery(this).closest('li').attr('form-id');
        var that = jQuery(this);

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this form!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false,
            html: false
        }, function(){

            jQuery.post(
                ajaxurl,
                {
                    action: 'core37_delete_form',
                    formID: formID
                },

                function()
                {
                    that.closest('.form-edit').hide('slide', {direction: 'up'}, 200);
                    //toastr.success('Your form was deleted');

                }

            );

            swal("Deleted!",
                "Your form has been deleted.",
                "success");
        });


    });


    jQuery(document).on('click', '.form-edit i.fa-code', function(){
        var formID = jQuery(this).closest('li').attr('form-id');

        swal("Here is your shortcode", "[core37_form id=" + formID + "]", "success");
    });


})(jQuery);