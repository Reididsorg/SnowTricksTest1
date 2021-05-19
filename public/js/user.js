jQuery(document).ready(function() {
    // To avoid invisible text in file input (Due to "bug with Bootstrap 4")
    $('.custom-file-input').on('change', function(event) {
        var inputFile = event.currentTarget;
        console.warn(inputFile);
        console.warn(inputFile.files[0].name);
        $(inputFile).parent()
            .find('.custom-file-label')
            .html(inputFile.files[0].name);
    });
});