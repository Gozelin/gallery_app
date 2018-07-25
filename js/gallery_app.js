
$(document).ready(function(){
    $(".galleryEdit-btn").click(function(){
        e = $(this).parent().parent(".GalleryBox");
        toggleEditPanel(e);
    });
});

function toggleEditPanel(e) {
    if ($("#galleryEdit-form").html() != "") {
        $("#galleryWrapper").css("position", "absolute");
        $("#galleryWrapper").animate({
            top: "0px"
        }, 200, function(){
            $("#galleryWrapper").css("position", "");
        });
        $("#galleryEdit-form ."+e.attr("class")).detach();
        $("#galleryWrapper").prepend(e);
    } else {
        e.css("display", "none");
        e.css("opacity", "0");
        $("#galleryEdit-form").prepend(e);
        $("#galleryWrapper").css("position", "absolute");
        $("#galleryWrapper").animate({
            top: "300px"
        }, 200, function(){
            e.css("display", "flex");
            $("#galleryWrapper").css("position", "");
            e.animate({
                opacity: "1"
            }, 100);
        });
    }
}