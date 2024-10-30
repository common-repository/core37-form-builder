/**
 * Created by luis on 9/12/16.
 */

jQuery(function(){

    if (typeof Modernizr != "undefined" && typeof Modernizr.inputtypes != "undefined" && !Modernizr.inputtypes.date) {
        jQuery('input[type=date]').pikaday({firstDay: 1});
    }
});

//settings for toastr
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-top-center",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

