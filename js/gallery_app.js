
var edit_global = 1;

$(document).ready(function(){
	buildApp();
    $(document).on("click", ".galleryEdit-btn", function(){
        e = $(this).parent().parent(".GalleryBox");
        toggleEditPanel(e);
    });
    $(document).on('click', '#galleryForm-submit', function(){
		var _action = "";
		var _data = "";
        id = $(this).parent().siblings(".galleryBox").attr("id");
        if (id == "g") {
			_action = "insertJson";
			_data = parseForm(document.getElementById("galleryForm"));
		} else if (id != null) {
			_action = "updateJson";
			_data = parseForm(document.getElementById("galleryForm"));
			_data["id"] = id.substr(1);
		}
		if (_data != null)
			JSON.stringify(_data);
		queryApp({action:_action, data:_data, class:"cGallery"}, function (data){
			buildApp(data);
		});
	});
	$(document).on('click', '#galleryForm-delete', function(){
		id = $(this).parent().siblings(".galleryBox").attr("id").substr(1);
		queryApp({action:"deleteJson", data:id, class:"cGallery"}, function (data){
			buildApp(data);
		});
	});
});

function buildApp(a = null) {
	queryApp({action:"buildApp","data":edit_global}, function(data){
		$("#galleryApp").html(data);
	});
}

function parseForm(form) {
	arr = {};
	$(form).children("input").each(function(){
		arr[$(this).attr("name")] = $(this).val();
	});
    // var formElem = document.getElementById("galleryForm");
	// var formData = new FormData(formElem);
	return (arr);
}

function queryApp(arr, callback = console.log()) {
	// console.log(arr);
	_contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
	opt = true;
	if (typeof arr == "object") {
		if (!("action" in arr))
			arr["action"] = null;
		if (!("class" in arr))
			arr["class"] = null;
		if (!("data" in arr)) {
			arr["data"] = null;
		}
		if (arr["data"] instanceof FormData) {
			_contentType = false;
			opt = false;
		}
		req = $.ajax({
			url: "src/AppAccessor.php",
			method: "POST",
			data: {action:arr["action"], class:arr["class"], data:arr["data"]},
			dataType: "text",
			contentType: _contentType,
			processData: opt,
			cache: opt
		});
		req.done(function(data) {
			console.log(data);
			if (typeof callback == "function")
				callback(data);
		}); 
	}
}

function toggleEditPanel(e) {
    if ($("#galleryEdit-form").html() != "") {
        if (e.parent().attr("id") == "galleryEdit-form") {
            closeEditPanel(e);
        } else {
            $("#galleryWrapper").prepend($("#galleryEdit-form .galleryBox"));
            $.when(closeEditPanel(e)).done(openEditPanel(e), buildForm(e));
        }
    } else {
        openEditPanel(e);
        buildForm(e);
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

function buildForm(e) {
	_id = parseInt(e.attr("id").substr(1));
	queryApp({action:"getForm", class:"cGallery",data:_id}, function(data) {
		$("#galleryEdit-form").append(data)
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