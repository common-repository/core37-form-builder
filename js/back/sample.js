/**
 * Created by luis on 8/31/16.
 */
var FormsList = Backbone.View.extend({

    default: {
        forms: {}
    },
    el: '#forms-list',

    initialize: function()
    {
        this.render();
    },
    template: _.template(
        '<label>All forms</label>'+
            '<ol>'+
            '<% _.each(forms, function(form) {)%>'+
                '<li>form.title</li>'+
            '<% } %>'+
            '</ol>'
    )


});

