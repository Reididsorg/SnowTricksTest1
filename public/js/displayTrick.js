jQuery(document).ready(function() {
    $('#seeMedias').click(function(e) {
        $('#medias').show();
    });

    /**
     * Simple (ugly) code to load more comments
     */
    var click = 0;
    $('#loadMoreComments').on('click', function(e) {
        click++;
        var offset = 5 * click;
        var limit = 0;
        /*console.warn(offset);*/
        /* Javascript data attribute */
        var comments = e.target.dataset.commentstodisplay;
        var commentsJsonObj = JSON.parse(comments);
        $.each(commentsJsonObj, function( index, value ) {
            if (index >= offset) {
                var commentToDisplay = '<div class="comment row mx-auto">\n' +
                    '    <div class="col-xl-8 col-lg-10 col-12 row mx-auto">\n' +
                    '        <p class="col-lg-2 col-12 d-none d-lg-block  comment-img">\n' +
                    '            <img src="'+value.userImagePath+value.userImageFilename+'" class="rounded mx-auto d-block" alt="'+value.userImageAlt+'" height="50">\n' +
                    '        </p>\n' +
                    '        <p class="col-lg-10 col-12 comment-text">\n' +
                    '            <strong>'+value.userName+'</strong> <small class="text-muted">('+value.trickCreatedAt+') :</small>\n' +
                    '            <br>'+value.content+'\n' +
                    '        </p>\n' +
                    '    </div>\n' +
                    '</div>';
                    if (limit < 5) {
                        $('#comments').append(commentToDisplay);
                    }
                limit++;
            }
        });
    });

    /**
     * Display photos in full size modal
     */
    $('.pop').on('click', function() {
        $('.imagepreview').attr('src', $(this).find('img').attr('src'));
        $('#imagemodal').modal('show');
    });

    $('.close').click(function() {
        $('#imagemodal').modal('hide');
    });
});
