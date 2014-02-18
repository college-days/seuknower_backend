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
				}else{
					window.location.href="/login";
				}
			}, "json");
		}else{
			$.post('/answer/cancel_agree', {'id': $(this).parents("li").attr("aid")}, function(data){
				if(data.status){
					agree.text(parseInt(agree.text())-1);
					agree.attr("class", "addagree");
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
				}else{
					window.location.href="/login";
				}
		}, "json");
		}else{
			$.post('/answer/add_agree', {'id': $(this).parents("li").attr("aid")}, function(data){
				if(data.status) {
					agree.text(parseInt(agree.text())+1);
					agree.attr("class", "cancelagree");
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
				}else{
					window.location.href="/login";
				}
			}, "json");
		}else{
			$.post('/answer/cancel_object', {'id': $(this).parents("li").attr("aid")}, function(data){
				if(data.status) {
					object.text(parseInt(object.text())-1);
					object.attr("class", "addobject");
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
				}else{
					window.location.href="/login";
				}
			}, "json");
		}else{
			$.post('/answer/add_object', {'id': $(this).parents("li").attr("aid")}, function(data){
				if(data.status) {
					object.text(parseInt(object.text())+1);
					object.attr("class", "cancelobject");
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
        }
        if($(this).attr("id") == "failcancle"){
        	$("#failmsg").hide();
        }
        if($(this).attr("id") == "answercancle"){
        	$("#answermsg").hide();
        }
        if($(this).attr("id") == "limitcancle"){
        	$("#limitmsg").hide();
        }
    });
});