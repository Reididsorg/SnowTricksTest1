jQuery(document).ready(function() {
    // Hide user image when input file change
    $(".custom-file-input").on("change paste keyup", function() {
        $("img.img-user").hide();
    });
    // To avoid invisible text in file input (Due to "bug with Bootstrap 4")
    $(".custom-file-input").on("change", function(event) {
        var inputFile = event.currentTarget;
        $(inputFile).parent()
            .find('.custom-file-label')
            .html(inputFile.files[0].name);
    });
});