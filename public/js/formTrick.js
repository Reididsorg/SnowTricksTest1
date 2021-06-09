function addFormToCollection($collectionHolderClass) {
    // Get the ul that holds the collection of tags
    var $collectionHolder = $("." + $collectionHolderClass);

    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data("prototype");

    // get the new index
    var index = $collectionHolder.data("index");

    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your tags field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // Increase the index with one for the next item
    $collectionHolder.data("index", index + 1);

    // Display the form a li containing a row div, 2 cols and a remove button
    var $form = $('<div class="col-sm-9"></div>').append(newForm);
    var $button = $('<div class="col-sm-3 my-auto text-center"></div>')
        .append('<button type="button" class="remove-element btn btn-danger" data-collection-holder-class="'+$collectionHolderClass+'">' +
            '<i class="fas fa-trash"></i> Supprimer</button>');
    var $divRow = $('<div class="row"></div>').append($form).append($button);
    var $subFormLi = $('<li class="subForm"></li>').append($divRow);

    // Add the new form at the end of the list
    $collectionHolder.append($subFormLi);

    // Handle the removal
    $(".remove-element").click(function(e) {
        e.preventDefault();
        // Delete the whole li.subForm container
        $(this).parent().parent().parent().remove();
        // Delete also last hr
        $(".separation").last().remove();
        return false;
    });

    // To avoid invisible text in file input (Due to "bug with Bootstrap 4")
    $(".custom-file-input").on("change", function(event) {
        var inputFile = event.currentTarget;
        $(inputFile).parent()
            .find(".custom-file-label")
            .html(inputFile.files[0].name);
    });
}

jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    var $imagesCollectionHolder = $("ul.images");
    var $videosCollectionHolder = $("ul.videos");
    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $imagesCollectionHolder.data("index", $imagesCollectionHolder.find("li").length);
    $videosCollectionHolder.data("index", $videosCollectionHolder.find("input").length);

    $("body").on("click", ".add_item_link", function(e) {
        var $collectionHolderClass = $(e.currentTarget).data("collectionHolderClass");
        // add a new image form (see next code block)
        addFormToCollection($collectionHolderClass);
    });

    // Handle the removal
    $(".remove-element").click(function(e) {
        e.preventDefault();
        // Delete the whole li.subForm container
        $(this).parent().parent().parent().remove();
        // Delete also last hr
        $(".separation").last().remove();
        return false;
    });

    // To avoid invisible text in file input (Due to "bug with Bootstrap 4")
    $(".custom-file-input").on("change", function(event) {
        var inputFile = event.currentTarget;
        $(inputFile).parent()
            .find(".custom-file-label")
            .html(inputFile.files[0].name);
    });
});