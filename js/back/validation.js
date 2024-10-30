/**
 * Created by luis on 9/21/16.
 */

//this class contains functions, data to generate HTML form for elements
var C37BackendValidation =
{
    /**
     * This function take the validation options (in HTML) and return a wrapper div with class .validation
     * @param code
     * @returns {string}
     */
    makeValidationArea: function(code)
    {
        return '<div class="validation">' + code + '</div>';
    },

//this is the file that store validation settings for the backend
    validationHTML:
    {
        common: '<label>Validation</label>'+
        '<% var elementValidation = validation[this.model.get("name")] || {}; %><input data-for="required" type="checkbox" <%= elementValidation.required? "checked" : "" %> /> Required',
        min_length: '<label>Min length (characters)</label>',
        max_length: '<label>Max length (characters)</label>',
        textInput: '',
        textarea: '',
        file:   '<label>File type</label>' +//for file, file type validation is needed
        '<select <% var value=elementValidation.fileType %> data-for="file-type">' +
            '<option <%= value==""? "selected" : "" %> value="">Any</option>' +
            '<option <%= value=="image/*"? "selected" : "" %> value="image/*">Images</option>' +
            '<option <%= value=="audio/*"? "selected" : "" %> value="audio/*">Audios</option>' +
            '<option <%= value=="video/*"? "selected" : "" %> value="video/*">Videos</option>' +
            '<option <%= value=="text/html"? "selected" : "" %> value="text/html">HTML Files</option>' +
            '<option <%= value==".doc,.docx,.pdf"? "selected" : "" %> value=".doc,.docx,.pdf">Documents</option>' +
            //'<option <%= value==""? "selected" : "" %> value="custom">Custom</option>' +
        '</select>'
    },
    textValidation: function()
    {
        return this.makeValidationArea(this.validationHTML.common + this.validationHTML.textInput);
    },
    textAreaValidation: function()
    {
        return this.makeValidationArea(this.validationHTML.common);
    },
    fileValidation: function()
    {
        return this.makeValidationArea(this.validationHTML.common + this.validationHTML.file);
    }

};
