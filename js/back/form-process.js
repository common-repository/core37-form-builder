/**
 * Process form in the front end, including form submit, behavior of buttons (next, open link, back), 
 * condition branching
 * ... 
 */

(function(jQuery){
    jQuery(function(){
        var elementsAction = elementsActions || {};
        var form = jQuery('form.c37-form');
        var steps = form.find('.c37-step');
        var currentStep = 0;

        /*
         | HANDLING ACTIONS
         | All clickable elements in for has the class .c37-item-element. The main
         | logic of action handling is:
         |  1. Get the id of the elment
         |  2. find in the elementsActions object the property that match the element ID
         |  3. If the property exists, execute the action associated with that element
         */

        jQuery(document).on('click', '.c37-item-element button, .c37-item-element input', function(){

            var elementID = jQuery(this.parent('.c37-item-element')).attr('id');

            /*
            | If there isn't a property in elementsActions matches the elementID, do nothing
             */
            if (!elementsAction.hasOwnProperty(elementID))
                return;

            /*
            | Otherwise, extract the action defined and perform it
             */
            var actionData = elementsAction[elementID];

            switch (actionData.action){
                case 'open-link':
                    window.open(actionData.target, '_self');
                    break;
                case 'submit-form':
                    form.submit();
                    break;
                case 'next-step':
                    if (currentStep >= steps.length)
                        break;
                    _.each(steps, function(step){
                       //hide all steps
                        jQuery(step).hide();
                    });
                    jQuery(steps.eq(currentStep+1)).show();

                    break;
                case 'previous-step':
                    //go to previous step in multistep form
                    if (currentStep===0)
                        break;
                    _.each(steps, function(step){
                        //hide all steps
                        jQuery(step).hide();
                    });
                    jQuery(steps.eq(currentStep-1)).show();
                    break;
                case 'show-element':
                    jQuery('#' + actionData.target).show();
                    break;
                case 'hide-element':
                    jQuery('#' + actionData.target).hide();
                    break;

            }


        });


        jQuery(document).on('change', '.c37-item-element input, .c37-item-element select', function(){

            var elementID = jQuery(this.parent('.c37-item-element')).attr('id');

            /*
             | If there isn't a property in elementsActions matches the elementID, do nothing
             */
            if (!elementsAction.hasOwnProperty(elementID))
                return;

            /*
             | Otherwise, extract the action defined and perform it
             */
            var actionData = elementsAction[elementID];

            switch (actionData.action){
                case 'show-element':
                    jQuery('#' + actionData.target).show();
                    break;
                case 'hide-element':
                    jQuery('#' + actionData.target).hide();
                    break;
            }

        });



    });




    /*
     | VALIDATION
     */






})(jQuery);
