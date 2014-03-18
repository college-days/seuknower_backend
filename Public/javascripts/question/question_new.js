var newEditor;
KindEditor.ready(function(K) {
	newEditor = K.create("#questionintro", {
		items: ["bold", "italic", "underline", "preview", "code", "image", "link", "quickformat", "removeformat", "insertorderedlist", "insertunorderedlist"]
	});
	window.editor = newEditor;
	
$(function(){
	var newMask = document.createElement("div");
	newMask.className = "newmask";
	newMask.style.width = document.body.scrollWidth + "px";
	newMask.style.height = document.body.scrollHeight + "px";
	document.body.appendChild(newMask);
	
	$('#newquesdialog').css('left', (parseInt(document.body.scrollWidth) - 544)/2); 

	$(".newmask").hide();

	$(".newmask").click(function(){
		window.editor.html("");
		window.editor = newEditor;
		if(window.editor){
			window.editor.html("");
		}
		$(".newwin").css("display","none");
		$(".newmask").css("display","none");
		$("#questiontitle").val("");
	});

	$("#closenewques").click(function(){
		window.editor.html("");
		window.editor = newEditor;
		if(window.editor){
			window.editor.html("");
		}
		$(".newwin").css("display","none");
		$(".newmask").css("display","none");
		$("#questiontitle").val("");
	});

	$("#questiontitle").blur(function(){
		var title = $("#questiontitle").val();
		if(title.replace(/[ ]/g, "")){
			$("#newquestitlealert").hide();
		}else{
			$("#newquestitlealert").show();
		}
	});
});

$(function(){
	$(".newquestion a").click(function(){
		window.editor = newEditor;
		$(".newmask").css("display","block");
		$(".newwin").css("display","block");
	});

	$("#questiontitle").bind('input propertychange', function(){
		if($(this).val().replace(/[ ]/g,"")){
			$('#preview_ask').show();
			$('#preview_ask').empty();
			$.post('/question/more_search', {
				content: $(this).val(),
				count: 5
			}, function(data){
				var search = data.data;
				for(var i=0; i<search.length; i++){
					if($('#preview_ask [href="/question/'+search[i].id+'"]').length > 0){
					}
					else{
						html = "<li style=\"color:#222;margin:4px 0 8px;padding:2px 8px 2px 10px;font-family: 'Helvetica Neue',Helvetica,Arial,Sans-serif;line-height:24px;font-size:13px;\"><a href=\"/question/"+search[i].id+"\" >"+search[i].title+"</a></li>";
						$('#preview_ask').append(html);
					}
				}
				$("#ask_new").css("display","block");
			}, 'json');
		}else{
			$("#preview_ask").hide();
		}
	});

	$("#preview_ask").hover(function(){
		$("#preview_ask").show();
	});

	$("#questiontitle").blur(function(){
		$("#preview_ask").hide();
	});

});
});

function submitQuestion(){
	var title = $("#questiontitle").val();
	// var intro = $("#questionintro").val();
	var intro = window.editor.html().replace(/\s(style|class).[^<\s]*"\B/g,"");

	// var anonymousBool = document.getElementById('anonymous').checked;
	// if (anonymousBool) {
	// 	var anonymous = 1;
	// }else{
	// 	var anonymous = 0;
	// }

	var typeid = document.getElementById("questiontype").value;

	var titleLock = false;
	var introLock = false;

	if(title.replace(/[ ]/g, "")){
		titleLock = true;
	}else{
		$("#newquestitlealert").show();
	}

	if(intro.replace(/[ ]/g, "")){
		introLock = true;
	}else{
		$("#newquesintroalert").show();
	}

	if(titleLock && introLock){
		$.post('/question/new', {
			'title': title,
			'intro': intro,
			'typeid': typeid
			// 'anonymous': anonymous
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

