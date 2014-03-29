var answerEditor;

KindEditor.ready(function(K) {
	answerEditor = K.create("#editor_id", {
		items: ["bold", "italic", "underline", "preview", "code", "image", "link", "quickformat", "removeformat", "insertorderedlist", "insertunorderedlist"]
	});

    window.editor = answerEditor;

$(function(){
    var atUserId = 0;
    $(".reply").click(function(){
        var href = $(this).attr("href");
        var pos = $(href).offset().top;
        var adjustPos = pos-100;
        $("html,body").animate({scrollTop: adjustPos}, 1000);
        
        var atUserName = $(this).parents("li").find("a.name").text().replace(/[ ]/g,"");
        atUserId = $(this).parents("li").attr("uid");
        window.editor.focus();
        window.editor.appendHtml('<strong>@'+atUserName+'\t'+'</strong>');
    });
});

$(function(){
	$("#submit").click(function(){
		$("div.alert").hide();
		var content = window.editor.html();
		content = content.replace(/<strong>@.*?<\/strong>/, "");
		content = content.replace(/\s(style|class).[^<=]*"\B/g,"");
		if(content.replace(/[ ]/g, "")){
			submitComment();
		}else{
			$("#answermsg").show();
		}
    });

    $("a.close").click(function(){
    	var href = $(this).attr("href");
        var pos = $(href).offset().top;
        var adjustPos = pos-100;
        $("html,body").animate({scrollTop: adjustPos}, 1000);
        	
        if($(this).attr("id") == "successcancle"){
        	$("#successmsg").hide();
        }else if($(this).attr("id") == "failcancle"){
        	$("#failmsg").hide();
        }else if($(this).attr("id") == "answercancle"){
        	$("#answermsg").hide();
        }else if($(this).attr("id") == "limitcancle"){
        	$("#limitmsg").hide();
        }else{
        	$(this).parents(".alert").slideUp();
        }

    });
});
});

function submitComment(){
	$("div.alert").hide();
	var content = window.editor.html();
	var at = content.match(/<strong>@.*?<\/strong>/);
	content = content.replace(/\s(style|class).[^<=]*"\B/g,"");

	if(at){
		content = content.replace(/<strong>@.*?<\/strong>/, "");
		var atUserName = String(String(at).replace(/<strong>@/, "")).replace(/<\/strong>/, "");
		atUserName = String(atUserName).replace(/[ ]/g, "");
		var atUserId = $("li[uname='"+atUserName+"']").attr("uid");
		var finalContent = "<a href='/user/"+atUserId+"' target='_blank'>"+at+"</a>" + content;
	}else{
		var finalContent = content;
	}

	var qid = $("#question").attr("qid");
	if(content.replace(/[ ]/g, "")){
		$.post('/market/want/add_answer',{
			q_id: qid,
			content: finalContent,
		}, function(data){
			if(data.status == 1){
				window.location.reload();	
			}
			if(data.status == 0){
				$("#failmsg").show();
			}
			if(data.status == -1){
				window.location.href = "/login";
			}
		},'json');
	}
	else{
		$('#answermsg').show();
	}
}
