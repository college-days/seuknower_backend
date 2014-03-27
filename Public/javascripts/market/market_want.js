var wantEditor;
KindEditor.ready(function(K) {
	wantEditor = K.create("#commoditywantintro", {
		items: ["bold", "italic", "underline", "preview", "code", "image", "link", "quickformat", "removeformat", "insertorderedlist", "insertunorderedlist"]
	});
	window.editor = wantEditor;

$(function(){
	var newMask = document.createElement("div");
	newMask.className = "newmask";
	newMask.style.width = document.body.scrollWidth + "px";
	newMask.style.height = document.body.scrollHeight + "px";
	document.body.appendChild(newMask);
	
	$('#wantBuydialog').css('left', (parseInt(document.body.scrollWidth) - 544)/2); 

	$(".newmask").hide();

	$(".newmask").click(function(){
		window.editor.html("");
		window.editor = wantEditor;
		if(window.editor){
			window.editor.html("");
		}
		$(".newwin").css("display","none");
		$(".newmask").css("display","none");
		$("#commoditywanttitle").val("");
	});

	$("#closecommoditywant").click(function(){
		window.editor.html("");
		window.editor = wantEditor;
		if(window.editor){
			window.editor.html("");
		}
		$(".newwin").css("display","none");
		$(".newmask").css("display","none");
		$("#commoditywanttitle").val("");
	});

	$("#commoditywanttitle").blur(function(){
		var title = $("#questiontitle").val();
		if(title.replace(/[ ]/g, "")){
			$("#commoditywanttitlealert").hide();
		}else{
			$("#commoditywanttitlealert").show();
		}
	});
});

$(function(){
	$(".newcommoditywant a").click(function(){
		window.editor = wantEditor;
		$(".newmask").css("display","block");
		$("#wantBuydialog").css("display","block");
	});
});
});

function submitCommodityWant(){
	var title = $("#commoditywanttitle").val();
	var intro = window.editor.html().replace(/\s(style|class).[^<=]*"\B/g,"");

	var titleLock = false;
	var introLock = false;

	if(title.replace(/[ ]/g, "")){
		titleLock = true;
	}else{
		$("#commoditywanttitlealert").show();
	}

	if(intro.replace(/[ ]/g, "")){
		introLock = true;
	}else{
		$("#commoditywantintroalert").show();
	}

	if(titleLock && introLock){
		$.post('/market/new_commodity_want', {
			'title': title,
			'intro': intro,
		}, function(data){
			if(data.status == 1){
				window.location.href = "/question/" + parseInt(data.data['id']);
			}
			if(data.status == 0){
				alert('问题创建失败');
			}
			if(data.status == -1){
				window.location.href = "/login";
			}
		}, "json");
	}
}

