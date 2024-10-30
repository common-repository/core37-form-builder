/**
 * Created by luis on 9/4/16.
 */
//define actions responses for users' interactions
//to handle click

jQuery(document).on('click', '.c37-child', function(){
    var form = jQuery(this).closest('form');

    var parentID = jQuery(this).closest('.c37-item-element').attr('id');

    var formActionObject = elementsActions[form.find('input[name=form_id]').first().val()];

    if (_.isEmpty(formActionObject))
    {
        return;
    }

    var elementAction = formActionObject[parentID];
    if (typeof elementAction != "object")
    {
        return;
    }

    /**
     * If the trigger is other than click, do nothing since this code handle event click only
     */
    if (elementAction.trigger != 'click')
    {
        console.log('not triggered by click');
        return;
    }

    if (elementAction.action == 'submit-form')
        form.submit();

});

