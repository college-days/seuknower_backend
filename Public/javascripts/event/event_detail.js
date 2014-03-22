var atUserId = 0;

$(function(){
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
	$(".share").click(function(){
		var flag = $(this).text().replace(/[ ]/g, "");
		if(flag == "分享"){
			$(".bshare-custom").slideDown();
			$(this).text("收起分享");
		}else{
			// $(".bshare-custom").slideUp();
			$(".bshare-custom").hide();
			$(this).text("分享");
		}
	});

	$(".edit").click(function(){
		var event_id = $("#event_id").text();
		window.location.href = "/event/modify_event/"+event_id;
	});

	$(".delete").click(function(){
		showDelete();
	});

	$(".joinbtn").click(function(){
		var join = $(this);
		var event_id = $("#event_id").text();
		var content = $(this).val();
		if(content == "我要参加"){
			$.post('/event/add_join', {'e_id': event_id}, function(data){
				if(data.status){
					$("#joinCount").text(parseInt($("#joinCount").text())+1);
					join.val("取消参加");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}

		if(content == "取消参加"){
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
					interest.val("取消感兴趣");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}

		if(content == "取消感兴趣"){
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
		content = content.replace(/<strong>@.*?<\/strong>/, "");
		content = content.replace(/\s(style|class).[^<=]*"\B/g,"");
		if (content.replace(/[ ]/g, "")){
			// showVerify();
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
        }
        if($(this).attr("id") == "failcancle"){
        	$("#failmsg").hide();
        }
        if($(this).attr("id") == "answercancle"){
        	$("#answermsg").hide();
        }
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

	var event_id = $("#event_id").text();
	if(content.replace(/[ ]/g, "")){
		$.post('/event/add_comment',{
			e_id: event_id,
			at_id: atUserId,
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

//for verify dialog
function showVerify(){
	var verifytop = $(".speak").offset().top;
	if($(".verifywin").length > 0) {
		removeVerifyCode();
	}
	else{
		newVerifyCode();
		$(".verifywin").css('top', verifytop);
	}
}

function newVerifyCode(){
	initVerifyCode();

	$(".verifyclose").click(function(){
		removeVerifyCode();
	});
	
	$(".verifymask").click(function(){
		removeVerifyCode();
	});

	$(".verifysubmit").click(function(){
		var verifycode = $(".verifycode").val();
		if(verifycode.replace(/[ ]/g, "")){
			$.post('/account/check_verify', {
	            verify: verifycode
	        }, function(data) {
	            if (!data.status){
					$('.verifycodealert').text("验证码不正确");
				}
				else {
					removeVerifyCode();
					$('#verifycodealert').text("");
					submitComment();
				}
	        }, 'json');
		}else{
			$(".verifycodealert").text("请填写验证码");
		}
		
	});

}

function initVerifyCode(){
	var newMask = document.createElement("div");
	newMask.id = 'verifymask';  
	newMask.className = "verifymask";
	newMask.style.width = document.body.scrollWidth + "px";
	newMask.style.height = document.body.scrollHeight + "px";
		
	var newWin = document.createElement("div");
	newWin.id = 'verifywin';
	newWin.className = "verifywin";
	newWin.style.left = (parseInt(document.body.scrollWidth) - 544)/2 + "px";
	var html = '<div class="title-bar"><span>请输入验证码</span><div class="verifyclose"></div></div><div class="content"><div class="verifycodealert" style="color:red;"></div><img src="/account/verifycode"><input type="text" class="verifycode"><input type="button" value="确认" class="verifysubmit"></div>';
	newWin.innerHTML = html;

	document.body.appendChild(newMask);
	document.body.appendChild(newWin);
}

function removeVerifyCode(){
	document.body.removeChild(document.getElementById('verifymask'));
	document.body.removeChild(document.getElementById('verifywin'));
}

//for delete dialog
function showDelete(){
	var deletetop = $(".delete").offset().top - 100;
	if($(".deletewin").length > 0) {
		removeDelete();
	}
	else{
		newDelete();
		$(".deletewin").css('top', deletetop);
	}
}

function newDelete(){
	initDelete();

	$(".deleteclose").click(function(){
		removeDelete();
	});

	$(".cancledelete").click(function(){
		removeDelete();
	});
	
	$(".deletemask").click(function(){
		removeDelete();
	});

	$(".deletesubmit").click(function(){
		var event_id = $("#event_id").text();
		$.post("/event/delete_event", {
			'eid': event_id
		}, function(data){
			if(data.status == 1){
				alert("活动删除成功");
				window.location.href = "/event";
			}else{
				alert("活动删除失败");
			}
		}, "json");
	});

}

function initDelete(){
	var newMask = document.createElement("div");
	newMask.id = 'deletemask';  
	newMask.className = "deletemask";
	newMask.style.width = document.body.scrollWidth + "px";
	newMask.style.height = document.body.scrollHeight + "px";
		
	var newWin = document.createElement("div");
	newWin.id = 'deletewin';
	newWin.className = "deletewin";
	newWin.style.left = (parseInt(document.body.scrollWidth) - 100)/2 + "px";
	var html = '<div class="title-bar"><span>确认要删除吗</span></div><div class="content"><div class="verifycodealert" style="color:red;"></div><input type="button" value="取消" class="cancledelete"><div style="display:inline-block;margin-left:50px;"></div><input type="button" value="确认" class="deletesubmit"></div>';
	newWin.innerHTML = html;

	document.body.appendChild(newMask);
	document.body.appendChild(newWin);
}

function removeDelete(){
	document.body.removeChild(document.getElementById('deletemask'));
	document.body.removeChild(document.getElementById('deletewin'));
}
