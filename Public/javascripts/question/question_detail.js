$(function(){
    var atUserId = 0;
    $(".reply").click(function(){
        var href = $(this).attr("href");
        var pos = $(href).offset().top;
        var adjustPos = pos-100;
        $("html,body").animate({scrollTop: adjustPos}, 1000);
        
        var replyContent = $(this).parents("li").find("div.reply-content");
        replyContent.slideDown();
        var content = replyContent.find("input.write");
        content.focus();
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
  		var at = replyMsg.match(/@.*?\t/);
  		var current = $(this);

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

		var aid = $(this).parents("li").attr("aid");

		if(replyMsg.replace(/[ ]/g, "")){
			$.post('/answer/add_reply',{
				a_id: aid,
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
			$($(this).parents("div.reply-content").find("div.alert")[0]).slideDown();
		}

    });

    $(".canclewrite").click(function(){
    	var replyContent = $(this).parents("div.reply-content");
    	replyContent.find('input.write').val("");
    	replyContent.slideUp();
    });

});

$(function(){
	//虽然动态改变了agree的class，但是貌似因为jquery的初始化问题，仍然需要作判断才可以
	//$(".addagree")和$(".cancelagree")是初始化的时候决定的，很奇怪
	$(".addagree").click(function(){
		var agree = $(this);
		if(agree.attr("class") == "addagree"){
			$.post('/answer/add_agree', {'id': $(this).parents("li").attr("aid")}, function(data){
				if(data.status) {
					agree.text(parseInt(agree.text())+1);
					agree.attr("class", "cancelagree");
					agree.attr("title", "不赞同");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}else{
			$.post('/answer/cancel_agree', {'id': $(this).parents("li").attr("aid")}, function(data){
				if(data.status){
					agree.text(parseInt(agree.text())-1);
					agree.attr("class", "addagree");
					agree.attr("title", "赞同");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}
	});

	$(".cancelagree").click(function(){
		var agree = $(this);
		if(agree.attr("class") == "cancelagree"){
			$.post('/answer/cancel_agree', {'id': $(this).parents("li").attr("aid")}, function(data){
				if(data.status){
					agree.text(parseInt(agree.text())-1);
					agree.attr("class", "addagree");
					agree.attr("title", "赞同");
				}else{
					window.location.href="/login";
				}
		}, "json");
		}else{
			$.post('/answer/add_agree', {'id': $(this).parents("li").attr("aid")}, function(data){
				if(data.status) {
					agree.text(parseInt(agree.text())+1);
					agree.attr("class", "cancelagree");
					agree.attr("title", "不赞同");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}
	});
	
	$(".addobject").click(function(){
		var object = $(this);
		if(object.attr("class") == "addobject"){
			$.post('/answer/add_object', {'id': $(this).parents("li").attr("aid")}, function(data){
				if(data.status) {
					object.text(parseInt(object.text())+1);
					object.attr("class", "cancelobject");
					object.attr("title", "不反对");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}else{
			$.post('/answer/cancel_object', {'id': $(this).parents("li").attr("aid")}, function(data){
				if(data.status) {
					object.text(parseInt(object.text())-1);
					object.attr("class", "addobject");
					object.attr("title", "反对");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}
		
	});

	$(".cancelobject").click(function(){
		var object = $(this);
		if(object.attr("class") == "cancelobject"){
			$.post('/answer/cancel_object', {'id': $(this).parents("li").attr("aid")}, function(data){
				if(data.status) {
					object.text(parseInt(object.text())-1);
					object.attr("class", "addobject");
					object.attr("title", "反对");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}else{
			$.post('/answer/add_object', {'id': $(this).parents("li").attr("aid")}, function(data){
				if(data.status) {
					object.text(parseInt(object.text())+1);
					object.attr("class", "cancelobject");
					object.attr("title", "不反对");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}
	});

	$("#submit").click(function(){
		$("div.alert").hide();
		var content = window.editor.html();
		var at = content.match(/<strong>@.*?<\/strong>/);

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
			$.post('/answer/add_answer',{
				q_id: qid,
				content: finalContent,
				anonymous: 0
			}, function(data){
				if(data.status == 1){
					window.location.reload();	
				}
				if(data.status == 0){
					$("#failmsg").show();
				}
				if(data.status == 3){
					$("#limitmsg").show();
				}
				if(data.status == -1){
					window.location.href = "/login";
				}
			},'json');
		}
		else{
			$('#answermsg').show();
		}
    });

    $('#anonymous_submit').click(function(){
		$("div.alert").hide();
		var content = window.editor.html();
		var at = content.match(/<strong>@.*?<\/strong>/);
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
			$.post('/answer/add_answer', {
				"q_id": qid,
				"content": finalContent, 
				"anonymous": 1
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
				
			}, 'json');
		}
		else{
			$('#answermsg').show();
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