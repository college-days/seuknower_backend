var isa = false;
var isp = false;
var isv = false;
//get from jwc via a spider
var name;
verifyURL = "/game/verifycode";

$(function(){
	$("#refreshVerify").click(function(){
		$("#verifykey").attr("src", verifyURL+'/'+Math.random());
	});

	$("#username").blur(function(){
		checkUsername();
	});

	$('#email').bind('input propertychange', function() {
		if($("#email").val().replace(/[ ]/g, "").length == 9){
			checkEmail();	
		} 
	});

	$("#password").blur(function() {
		checkPassword();
	});

	$("#verify").blur(function() {
		checkVerify();
	});
			
	$('#verify').bind('input propertychange', function() {
		if($("#verify").val().replace(/[ ]/g, "").length == 4){
			checkVerify();	
		} 
	});

	$("#register_submit").click(function() {
		if(isa && isp && isv){
			$("#title").text("请稍等，正在为你注册");
			$.post('/account/load_stuinfo', {
		        id: $("#username").val().replace(/[ ]/g, "")+"@seu.edu.cn"
		    }, function(data) {
		        if (data.status) {
		            name = data.data['name'].replace(/[ ]/g, "");
		            alert("你好:"+name+"!");
					if(name) {
						isa = true;
						if(isv){
							postRegisterInfo(name);
						} 
					}else {
						$('#usernamealert').text("一卡通号不正确");
						$("#title").text("一卡通号不正确");
					}
		        }else{
		        	// alert(data.info);	
		        	$("#title").text(data.info);
		        } 
		    }, 'json');
		}else{
			checkUsername();
			checkPassword();
			checkVerify();
		}
	});
});

function postRegisterInfo(name){
	if(isa && isp && isv ){
		  $.post('/game/verify_register', {
            username: $("#username").val().replace(/[ ]/g, ""),
            password: $("#password").val().replace(/[ ]/g, ""),
            name: name,
            verify: $("#verify").val().replace(/[ ]/g, "")
        }, function(data) {
			if(data.status){
				alert("身份验证成功，");
				window.location.href = "/game/login";
			}
			else{
				alert(data.info);
				$("#title").text(data.info);
			}
        }, 'json');
	}else{
		checkUsername();
		checkPassword();
		checkVerify();
	}
}

function checkUsername(){
	var username = $("#username").val().replace(/[ ]/g, "");
	if(!username){
		$("#usernamealert").text('请填写一卡通号');
		isa = false;
	}
	else{
		if(isNaN(username)){
			$("#usernamealert").text("请填写正确的一卡通号");
			isa = false;
		}else{
			$("#usernamealert").text("");
			isa = true;
		}
	}
}

function checkPassword(){
	var password = $("#password").val().replace(/[ ]/g, "");
    if(!password) {
		isp = false;
        $('#passwordalert').text("请填写密码");
    } else {
        $('#passwordalert').text("");
        isp = true;
    }
}

function checkVerify() {
    var verify = $("#verify").val().replace(/[ ]/g, "");
    if (!verify) {
		isv = false;
        $('#verifyalert').text("验证码不能为空");
    } else {
        $.post('/account/check_verify', {
            verify: verify
        }, function(data) {
            if (!data.status){
				isv = false;
				$('#verifyalert').text("验证码不正确");
			}
			else {
				isv = true;
				$('#verifyalert').text("");
			}
        }, 'json');
    }
}
