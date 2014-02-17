$(function(){
	$(".joinbtn").click(function(){
		var join = $(this);
		var event_id = $("#event_id").text();
		var content = $(this).val();
		if(content == "我要参加"){
			$.post('/event/add_join', {'e_id': event_id}, function(data){
				if(data.status){
					$("#joinCount").text(parseInt($("#joinCount").text())+1);
					join.val("不想参加");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}

		if(content == "不想参加"){
			$.post('/event/cancel_join', {'e_id': event_id}, function(data){
				if(data.status){
					$("#joinCount").text(parseInt($("#joinCount").text())-1);
					join.val("我要参加");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}
	});

	$(".interestbtn").click(function(){
		var interest = $(this);
		var event_id = $("#event_id").text();
		var content = $(this).val();
		if(content == "我感兴趣"){
			$.post('/event/add_interest', {'e_id': event_id}, function(data){
				if(data.status){
					$("#interestCount").text(parseInt($("#interestCount").text())+1);
					interest.val("不感兴趣");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}

		if(content == "不感兴趣"){
			$.post('/event/cancel_interest', {'e_id': event_id}, function(data){
				if(data.status){
					$("#interestCount").text(parseInt($("#interestCount").text())-1);
					interest.val("我感兴趣");
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

		var event_id = $("#event_id").text();
		if(content.replace(/[ ]/g, "")){
			$.post('/event/add_comment',{
				e_id: event_id,
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
    });
});