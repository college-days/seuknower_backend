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
	$.post("/market/getsamecate", {
		'id': $("#likeinfo").attr("cid")
	}, function(data){
		for(var i=0; i<data.data.length; i++){
			var html = '<div class="content"><div class="picture"><a target="_blank" href="/market/commodity/'+data.data[i]['id']+'"><img class="img-responsive" style="width:712px;height:505px;" src="'+data.data[i]['picture']+'"/></a></div><div class="title"><a target="_blank" href="/market/commodity/'+data.data[i]['id']+'">'+data.data[i]['title']+'</a></div></div>'
			$("#samecate").append(html);
		}
	}, "json");

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
		content = content.replace(/<strong>@.*?<\/strong>/, "");
		content = content.replace(/\s(style|class).[^<=]*"\B/g,"");
		if(content.replace(/[ ]/g, "")){
			// showVerify();
			submitComment();
		}else{
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

	$(".arrow-right").click(function(){
		var scroll = document.getElementById("scroll");
		if(scroll.scrollLeft<($(".scroll li").length-4)*77) scroll.scrollLeft += 77;
	});

	$(".arrow-left").click(function(){
		var scroll = document.getElementById("scroll");
		if(scroll.scrollLeft>0) scroll.scrollLeft -= 77;
	});

	$(".thumbnail").hover(function(){
		$(".big img").attr("src", $(this).attr("src"));
	});

	$(".share").click(function(){
		var flag = $(this).text().replace(/[ ]/g, "");
		if(flag == "分享"){
			$(".bshare-custom").slideDown();
			$(this).text("收起分享");
		}else{
			$(".bshare-custom").slideUp();
			$(this).text("分享");
		}
	});

	$(".edit").click(function(){
		var commodity_id = $("#commodity_id").text();
		window.location.href = "/market/modify_commodity/"+commodity_id;
	});

	$(".delete").click(function(){
		showDelete();
	});
});

function submitComment(){
	$("div.alert").hide();
	var content = window.editor.html();
	var at = content.match(/<strong>@.*?<\/strong>/);
	ccontent = content.replace(/\s(style|class).[^<=]*"\B/g,"");

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
					$('.verifycodealert').text("");
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
		var commodity_id = $("#commodity_id").text();
		$.post("/market/delete_commodity", {
			'cid': commodity_id
		}, function(data){
			if(data.status == 1){
				alert("商品下架成功");
				window.location.href = "/market";
			}else{
				alert("商品下架失败");
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
	var html = '<div class="title-bar"><span>确认要下架吗</span></div><div class="content"><div class="verifycodealert" style="color:red;"></div><input type="button" value="取消" class="cancledelete"><div style="display:inline-block;margin-left:50px;"></div><input type="button" value="确认" class="deletesubmit"></div>';
	newWin.innerHTML = html;

	document.body.appendChild(newMask);
	document.body.appendChild(newWin);
}

function removeDelete(){
	document.body.removeChild(document.getElementById('deletemask'));
	document.body.removeChild(document.getElementById('deletewin'));
}
