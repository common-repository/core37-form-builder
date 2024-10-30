/**
 * Created by luis on 9/4/16.
 */

(function(jQuery){

    jQuery(function(){


        //add validation data to elements of forms
        _.each(jQuery('.c37-form'), function(form){

            var formID = jQuery(form).find('input[name=form_id]').first().val();
            console.log(formID);

            var validationObject = formsValidation[formID];


            /**
             * In case there is an validation object that defines the validation rules,
             * add appropriate parsely attribute to the elements
             *
             */
            if (typeof validationObject != 'undefined')
            {
                var names = Object.keys(validationObject);

                _.each(names, function(name){

                    var vO = validationObject[name];
                    var element = jQuery('[name='+name+']');

                    if (vO.required)
                        element.prop('required', true);

                });
            }

            jQuery(form).parsley();


        });

        //on submit button click, process the form
        jQuery(document).on('click', '.c37-form [data-role=submit]', function(e){
            //e.preventDefault();
            console.log('submit button clicked');
            var form = jQuery(this).closest('.c37-form');
            //form.submit();
        });


        jQuery('.c37-form').bind('submit', function(e){
            e.preventDefault();
            var form = jQuery(this);


            if (form.parsley().isValid())
            {
                console.log('form is valid! wow!');
                submitForm(form);
                return;
            } else
            {
                return;
            }

        });




    });



    /**
     * Given a form, get all data field and submit it
     * @param form
     */
    function submitForm(form)
    {
        //if there is a file input in the form, submit the normal way to get the file
        if (form.find('input[type=file]').length > 0)
        {
            form.unbind('submit');
            form.submit();
            return;
        }

        var data = {};
        var inputs = form.find('input');
        var selects = form.find('select');
        var textareas = form.find('textarea');
        var postURL = form.attr('action');

        _.each(inputs, function(input){
            var currentInput = jQuery(input);
            if (currentInput.attr('type')=='checkbox')
            {
                if (typeof data[currentInput.attr('name')] == 'undefined')
                {
                    console.log('init default array');
                    data[currentInput.attr('name')] = [];
                }

                if (currentInput.is(':checked'))
                {
                    data[currentInput.attr('name')].push(currentInput.val());
                    console.log(data[currentInput.attr('name')]);
                }

            } else if (currentInput.attr('type') == 'radio')
            {
                //check if the radio name was added, if not, init with a blank value
                if (typeof data[currentInput.attr('name')] == "undefined")
                    data[currentInput.attr('name')] = '';

                if (currentInput.is(':checked'))
                {
                    data[currentInput.attr('name')] = currentInput.val();
                }
            } else
            {
                data[currentInput.attr('name')] = currentInput.val();
            }

        });

        //get data from select box
        _.each(selects, function(select){
            var currentSelect = jQuery(select);

            data[currentSelect.attr('name')] = currentSelect.val();
        });

        //get data content from textarea
        _.each(textareas, function(textarea){
            var currentTextarea = jQuery(textarea);

            data[currentTextarea.attr('name')] = currentTextarea.val();
        });

        //get data from textarea


        data['by_ajax'] = 1;
        //send the data to server

        jQuery.post(
            postURL,
            data,
            function(response)
            {

                toastr.remove();
                var responseData = JSON.parse(response);

                if (responseData.error == 1)
                {
                    //in case the error message is a single string, print it out
                    if (typeof responseData.message == 'string')
                    {
                        toastr.error(responseData.message);
                        return false;
                    }

                    //send the error message
                    for (var i = 0; i < responseData.message.length; i++)
                    {
                        var elementName = responseData.message[i].name;
                        var message = responseData.message[i].message;
                        var jqElement = jQuery('[name='+elementName+']');
                        jqElement.closest('.c37-form-element').addClass('hint-right');
                        jqElement.closest('.c37-form-element').addClass('hint-error');
                        jqElement.closest('.c37-form-element').attr('data-hint', message);
                        jqElement.addClass('c37-field-error');

                    }
                } else {
                    //send the toast message and redirect the user to new URL if set
                    toastr.success(responseData.message);
                    if (responseData.url != "")
                        window.location.href = responseData.url;
                }


            }
        );

        return false;
    }

})(jQuery);
