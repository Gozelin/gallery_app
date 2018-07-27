
var edit_global = 1;

$(document).ready(function(){
    buildApp(edit_global);
    $(document).on("click", ".galleryEdit-btn", function(){
        e = $(this).parent().parent(".GalleryBox");
        toggleEditPanel(e);
    });
    $(document).on('click', '#galleryForm-submit', function(){
        var action = "";
        id = $(this).parent().siblings(".galleryBox").attr("id");
        if (id == "g")
            action = "add";
        else if (id != null)
            action = "modif";
        sendForm(action);
    });
});

function buildApp(edit_) {
    req = $.ajax({
        url: "src/cGalleryAccessor.php",
        method: "POST",
        data: {action:"buildApp", edit:edit_},
        datatype: "text"
    });
    req.done(function(data) {
        $("#galleryApp").html(data);
    });
}

function toggleEditPanel(e) {
    if ($("#galleryEdit-form").html() != "") {
        if (e.parent().attr("id") == "galleryEdit-form") {
            closeEditPanel(e);
        } else {
            $("#galleryWrapper").prepend($("#galleryEdit-form .galleryBox"));
            $.when(closeEditPanel(e)).done(openEditPanel(e), getForm(e));
        }
    } else {
        openEditPanel(e);
        getForm(e);
    }
}

function closeEditPanel(e) {
    $("#galleryWrapper").css("position", "absolute");
    $("#galleryWrapper").animate({
        top: "0px"
    }, 200, function(){
        $("#galleryWrapper").css("position", "");
    });
    $("#galleryForm").remove();
    $("#galleryWrapper").prepend(e);
}

function openEditPanel(e) {
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

function getForm(e) {
    id_ = parseInt(e.attr("id").substr(1));
    json_ = JSON.stringify({id:id_, action: "getForm"});
    req = $.ajax({
        url: "src/cGalleryAccessor.php",
        method: "POST",
        data: {json:json_},
        datatype: "json"
    });
    req.done(function(data){
        $("#galleryEdit-form").append(data);
    });
}

function sendForm(action) {
    var formElem = document.getElementById("galleryForm");
    var formData = new FormData(formElem);
    formData.append("action", action);
    id = parseInt($("#galleryEdit-form").children(".galleryBox").attr("id").substr(1));
    formData.append("id", id);

    var xhr = new XMLHttpRequest(),
    method = "POST",
    url = "src/classAccessor.php";

    xhr.open(method, url, true);
    xhr.onreadystatechange = function () {
        if(xhr.readyState === 4 && xhr.status === 200) {
            console.log(xhr.responseText);
            buildApp(edit_global);
        }
    };
    xhr.send(formData);
}