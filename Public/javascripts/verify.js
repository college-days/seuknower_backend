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
					isGoodToPost = false;
				}
				else {
					removeVerifyCode();
					$('#verifycodealert').text("");
					isGoodToPost = true;
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
