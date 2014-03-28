var answerEditor;

KindEditor.ready(function(K) {
	answerEditor = K.create("#editor_id", {
		items: ["bold", "italic", "underline", "preview", "code", "image", "link", "quickformat", "removeformat", "insertorderedlist", "insertunorderedlist"]
	});

    window.editor = answerEditor;

$(function(){
	var targetaid = $("#question").attr("aid");
	if(targetaid){
		$('li[aid='+targetaid+']').find("div.reply-content").slideDown();
		$('li[aid='+targetaid+']').find('a.reply').text("收起评论");
		var href = $('li[aid='+targetaid+']').find('a.reply').attr("href");
        var pos = $(href).offset().top;
        var adjustPos = pos-100;
        $("html,body").animate({scrollTop: adjustPos}, 1000);
	}
});

$(function(){
    var atUserId = 0;
    $(".reply").click(function(){
    	var flag = $(this).text().split("(")[0];
    	var replycount = $(this).parents("li").attr("replycount");
    	if(flag == "评论"){
    		var href = $(this).attr("href");
	        var pos = $(href).offset().top;
	        var adjustPos = pos-100;
	        $("html,body").animate({scrollTop: adjustPos}, 1000);
	        
	        var replyContent = $(this).parents("li").find("div.reply-content");
	        replyContent.slideDown();
	        var content = replyContent.find("input.write");
	        content.focus();
	        $(this).text("收起评论("+replycount+")");
    	}else{
    		var href = $(this).attr("href");
	        var pos = $(href).offset().top;
	        var adjustPos = pos-100;
	        $("html,body").animate({scrollTop: adjustPos}, 1000);

    		var replyContent = $(this).parents("li").find("div.reply-content");
	    	replyContent.find('input.write').val("");
	    	replyContent.slideUp();
	    	$(this).text("评论("+replycount+")");
    	}
        
    });

    $(".replytoreply").click(function(){
    	var href = $(this).attr("href");
        var pos = $(href).offset().top;
        var adjustPos = pos-100;
        $("html,body").animate({scrollTop: adjustPos}, 1000);

        var atUserName = $(this).parents("div.row").find("a.name").text().replace(/[ ]/g,"");
        var content = $(this).parents("div.reply-content").find("input.write");
        content.val("");
        content.val('@'+atUserName+'\t');
        content.focus();
	    /*atUserId = $(this).parents("li").attr("uid");
        window.editor.focus();
        window.editor.appendHtml('<strong>@'+atUserName+'\t'+'</strong>');*/
    });

    $(".sendreply").click(function(){
    	var replyMsg = $(this).parents("div.reply-content").find("input.write").val();
    	replyMsg = replyMsg.replace(/@.*?\t/, "");
    	if(replyMsg.replace(/[ ]/g, "")){
    		// showReplyVerify($(this));
    		submitReply($(this));
    	}else{
    		$($(this).parents("div.reply-content").find("div.alert")[0]).slideDown();
    	}
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

function submitReply($object){
	var replyMsg = $object.parents("div.reply-content").find("input.write").val();
	replyMsg = String(String(replyMsg).replace(/<script>/, "")).replace(/<\/script>/, "");
	var at = replyMsg.match(/@.*?\t/);
	var current = $object;

	if(at){
		replyMsg = replyMsg.replace(/@.*?\t/, "");
		var atUserName = String(String(at).replace(/@/, "")).replace(/\t/, "");
		atUserName = String(atUserName).replace(/[ ]/g, "");
		// var atUserId = $("li[uname='"+atUserName+"']").attr("uid");
		// var finalMsg = "<a href='/user/"+atUserId+"' target='_blank'>"+at+"</a>" + content;
		var atUserId = $("div[uname='"+atUserName+"']").attr("uid");
		var finalMsg = "<strong><a href='/user/"+atUserId+"' target='_blank'>"+at+"</a></strong>" + replyMsg;
	}else{
		var finalMsg = replyMsg;
	}

	var aid = $object.parents("li").attr("aid");
	var qid = $("#question").attr("qid");

	if(replyMsg.replace(/[ ]/g, "")){
		$.post('/answer/add_reply',{
			q_id: qid,
			a_id: aid,
			at_id: atUserId,
			msg: finalMsg
		}, function(data){
			if(data.status == 1){
				window.location.reload();	
			}
			if(data.status == 0){
				$(current.parents("div.reply-content").find("div.alert")[1]).slideDown();
			}
		}, 'json');
	}else{
		$($object.parents("div.reply-content").find("div.alert")[0]).slideDown();
	}
}

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
