/**
 * Created by luis on 9/7/16.
 */

(function(jQuery){

    var MAX_ROW_COUNT = 20;
    var formDetails = {};
    function loadSubscribersDetails()
    {

        //show loading toast
        toastr.info(INFO_LOADING_SUBSCRIBERS_DATA);


        //send post to server to get the list of subscribers
        jQuery.post(ajaxurl, {
            formID: formDetails.formID,
            action: 'core37_get_subscribers',
            limit: MAX_ROW_COUNT, //get 10 each time
            start: formDetails.start //start with index 0
        }, function(response){

            //this is the response data
            //https://drive.google.com/file/d/0B3ls_n4DVo9mQjIxYnV3bTkwS2M/view?usp=sharing
            var data = JSON.parse(response);

            //the response data contains list of keys and list of data for each key in two separate arrays
            //the keys will be used to display on the <thead> section
            var keys = [];
            var heading = '';

            var tableCode;
            _.each(data.keys, function(key){
                keys.push(key[0]);
                heading += '<th class="c37-form-field">'+key[0]+'</th>';
            });

            tableCode = '<thead><tr>' + heading + '</tr></thead>';
            var subscribersDetails = jQuery('#c37-list-subscribers');
            subscribersDetails.html("");
            //subscribersDetails.append(heading);

            var tableBody ='';

            //insert subscriber details into the table (single data set, one row)
            _.each(data.details, function(detail){

                var rowContent = '';
                _.each(keys, function(key){
                    if (typeof detail.info[key] == 'undefined')
                        detail.info[key] = '-';
                    //process data if it's an array
                    if(detail.info[key].indexOf('[') == 0)
                    {
                        var dataArray = JSON.parse(detail.info[key]);
                        console.log(dataArray);
                        var cellContent = '';
                        for (var d in dataArray)
                        {

                            if (dataArray[d].indexOf('http') ==0)
                                cellContent+='<a class="single-attachment" target="_blank" href="'+dataArray[d]+'">Link</a>';
                            else
                                cellContent+= '<span class="single-list-item">'+decodeURIComponent(dataArray[d])+'</span>';
                        }

                        rowContent += '<td>'+cellContent+'</td>';

                    } else
                    {
                        rowContent += '<td>'+detail.info[key]+'</td>';
                    }

                });
                rowContent = '<tr session-id="'+detail.sessionID+'" >'+rowContent+'</tr>';

                tableBody += rowContent;

            });

            tableCode += '<tbody>'+tableBody+'</tbody>';
            subscribersDetails.append(tableCode);

            toastr.remove();

        });

    }

    function clearFormSubscribers()
    {
        jQuery.post(
            ajaxurl,
            {
                formID: formDetails.formID,
                action: 'c37_clear_forms_subscribers'
            },
            function(response){
                //clear current form data on the menu
                jQuery('#c37-list-subscribers').html('');
                toastr.success(INFO_SUBSCRIBERS_CLEARED);
            }
        )
    }

    jQuery(document).on('click', '.c37-form-item', function(){

        //load form
        var formID = jQuery(this).attr('form-id');
        formDetails = {formID: formID, start: 0};
        loadSubscribersDetails();
    });

    jQuery(document).on('click', '#next', function(){

        formDetails.start+=MAX_ROW_COUNT;

        loadSubscribersDetails();
    });


    jQuery(document).on('click', '#prev', function(){

        formDetails.start = formDetails.start-MAX_ROW_COUNT > 0? formDetails.start-MAX_ROW_COUNT: 0;

        loadSubscribersDetails();
    });

    jQuery(document).on('click', '#clean', function(){

        if (typeof formDetails.formID == 'undefined')
            return;
        //confirm with the user to clear or not
        swal({
            title: INFO_CLEAR_SUBSCRIBERS,
            text: INFO_CLEAR_SUBSCRIBERS_EXPLAIN,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, clear it!",
            cancelButtonText: "No",
            closeOnConfirm: true,   closeOnCancel: true
        },
            function(isConfirm){
                if (isConfirm) {
                    clearFormSubscribers();
                }
            });




    });

})(jQuery);
