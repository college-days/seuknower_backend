$(function(){
	$(".addlike").click(function(){
		var id = $("#likeinfo").attr("cid");
		var like = $(this);
		var likeCount = $("#likecount");
		if(like.attr("class") == "addlike"){
			$.post('/market/add_like', {'id': id}, function(data){
				if(data.status) {
					likeCount.text(parseInt(likeCount.text())+1);
					like.attr("class", "cancellike");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}else{
			$.post('/market/cancel_like', {'id': id}, function(data){
				if(data.status){
					likeCount.text(parseInt(likeCount.text())-1);
					like.attr("class", "addlike");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}
	});

	$(".cancellike").click(function(){
		var id = $("#likeinfo").attr("cid");
		var like = $(this);
		var likeCount = $("#likecount");
		if(like.attr("class") == "addlike"){
			$.post('/market/add_like', {'id': id}, function(data){
				if(data.status) {
					likeCount.text(parseInt(likeCount.text())+1);
					like.attr("class", "cancellike");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}else{
			$.post('/market/cancel_like', {'id': id}, function(data){
				if(data.status){
					likeCount.text(parseInt(likeCount.text())-1);
					like.attr("class", "addlike");
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

		var commodity_id = $("#commodity_id").text();
		if(content.replace(/[ ]/g, "")){
			$.post('/market/add_comment',{
				commodity_id: commodity_id,
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