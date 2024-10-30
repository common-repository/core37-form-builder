
/**
 * Created by luis on 6/8/16.
 */
(function (jQuery) {
    jQuery(function(){
        //var formModel = formModel || new C37ElementModel();
        //var formEdit;
        //
        /*
        | Element edit is the single instance of edit form of every element. Use this to make sure that
        | there is only one instance of edit view.
         */

        if (versionNangCap)
        {
            jQuery('#c37-go-pro').hide();
        }
        /**
         * On page load, make the current c37-box boxes droppable
         */
        makeFromDroppable(jQuery);
        makeC37BoxDroppable(jQuery);
        makeC37StepDroppable(jQuery);

        //assign id to form
        jQuery('#construction-site form').attr('id', core37Form.formSettings.cssID);


        jQuery(document).on('click', '.c37-step .c37-item-element', function (e) {
            e.preventDefault();
        });


        jQuery('#elements-panel').accordion();

        //drag n drop of row
        jQuery('.c37-container-element').draggable({
            connectToSortable: '.c37-step',
            helper: 'clone',
            revert: 'invalid',
            addClasses: false
        });

        //drag and drop step
        jQuery('.c37-form-multi-element').draggable({
            connectToSortable: '.c37-step-container',
            helper: 'clone',
            revert: 'invalid',
            addClasses: false
        });
        

        //drag n drop of elements
        jQuery('.c37-item-element').draggable({
            connectToSortable: '.c37-box',
            helper: "clone",
            revert: "invalid",
            iframeFix: true,
            addClasses: false,
            refreshPositions: true,
            stop: function()
            {
                localStorage.setItem('dragging-stop', true);
            },
            start: function()
            {
                localStorage.setItem('dragging-stop', false);
            }
        });



    });


})(jQuery);