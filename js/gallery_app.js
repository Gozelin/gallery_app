
var edit_global = 1;

$(document).ready(function(){
	buildApp();
    $(document).on("click", ".galleryEdit-btn", function(){
        e = $(this).parent().parent(".GalleryBox");
        toggleEditPanel(e);
    });
    $(document).on('click', '#galleryForm-btn', function(){
		var _action = "";
		var _data = "";
		var _file = null;
		id = $(this).parent().siblings(".galleryBox").attr("id");
		_data = getForm(document.getElementById("gDataForm"));
		_file = getForm(document.getElementById("gFileForm"));
        if (id == "g") {
			_action = "insertJson";
		} else if (id != null) {
			_action = "updateJson";
			_data["id"] = id.substr(1);
		}
		if (_data != null)
			JSON.stringify(_data);
		queryApp({action:_action, data:_data, class:"cGallery", file:_file}, function (data){
			// console.log(data);
			buildApp(data);
		});
	});
	$(document).on('click', '#galleryForm-del', function(){
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

function getForm(form) {
	if ($(form).attr("class") == "fileForm") {
		formData = new FormData(form);
		return (formData);
	} else if ($(form).attr("class") == "regForm") {
		arr = {};
		$(form).children("input").each(function(){
			arr[$(this).attr("name")] = $(this).val();
		});
		return (arr);
	}
}

function queryApp(arr, callback = console.log()) {
	if (typeof arr == "object") {
		if (!("action" in arr))
			arr["action"] = null;
		if (!("class" in arr))
			arr["class"] = null;
		if (!("data" in arr))
			arr["data"] = null;
		if (("file" in arr) && arr["file"] instanceof FormData) {
			sendForm(arr, callback);
		} else {
			req = $.ajax({
				url: "src/AppAccessor.php",
				method: "POST",
				data: {action:arr["action"], class:arr["class"], data:arr["data"]},
				dataType: "text",
			});
			req.done(function(data) {
				if (typeof callback == "function")
					callback(data);
			});
		}
	}
}

function sendForm(arr, callback) {
	if (("file" in arr) && (arr["file"] instanceof FormData)) {
		var formData = arr["file"];
		formData.append("class", arr["class"]);
		formData.append("action", arr["action"]);
		formData.append("data", JSON.stringify(arr["data"]));
		var xhr = new XMLHttpRequest(),
		method = "POST",
		url = "src/AppAccessor.php";
		xhr.open(method, url, true);
		xhr.onreadystatechange = function () {
			if(xhr.readyState === 4 && xhr.status === 200) {
				callback(xhr.responseText);
			}
		};
		xhr.send(formData);
	}
};

/*
ANIMATIONS
*/

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