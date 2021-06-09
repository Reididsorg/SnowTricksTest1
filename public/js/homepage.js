/**
 * Simple (ugly) code to load more tricks
 */
var click = 0;

$("#seeMoreTricks").on("click", function(e) {
    click++;
    var offset = 5 * click;
    /*console.warn(offset);*/
    var newUrl = "/seemoretricks/" + offset;
    $.ajax({
        url: newUrl,
        method: "POST"
    }).then(function(data) {
        /*console.warn(data);*/
        $("#tricksList").append(data);
    });
});

/**
 * Pass trick slug to "remove trick" modal
 */
$("#removeTrickModal").on("show.bs.modal", function (event) {
    var target = event.relatedTarget.id;
    var trickSlug = target.replace("remove-trick-", "");
    var trickName = trickSlug.replace("-", " ");
    var firstLetterUpperTrickName = trickName.charAt(0).toUpperCase() + trickName.slice(1);
    $("#modal-remove-trick").attr("href", "/remove/tricks/"+trickSlug);
    $("#modal-title").text("Voulez-vous vraiment supprimer le trick '"+firstLetterUpperTrickName+"' ?");
});