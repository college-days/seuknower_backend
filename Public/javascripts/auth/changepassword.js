var isa = false;
var isp = false;
var ispr = false;
var isv = false;
//get from jwc via a spider
var name;

$(function(){
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

     $("#passwordrepeat").blur(function() {
     	checkPasswordRepeat();
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
			$("#title").text("请稍等，正在初始化修改密码服务");
			$.post('/account/load_stuinfo', {
		        id: $("#username").val().replace(/[ ]/g, "")+"@seu.edu.cn"
		    }, function(data) {
		        if (data.status) {
		            name = data.data['name'].replace(/[ ]/g, "");
					if(name) {
						isa = true;
						if(isv){
							postRegisterInfo();
						} 
					}else {
						$('#username').text("一卡通号不正确");
					}
		        }else{
		        	// alert(data.info);	
		        	$("#title").text(data.info);
		        } 
		    }, 'json');
		}else{
			checkUsername();
			checkPassword();
			checkPasswordRepeat();
			checkVerify();
		}
	});
});

function postRegisterInfo(){
	if(isa && isp && isv ){
		  $.post('/account/save_password', {
            account: $("#username").val().replace(/[ ]/g, "")+"@seu.edu.cn",
            password: $("#password").val().replace(/[ ]/g, ""),
            verify: $("#verify").val().replace(/[ ]/g, "")
        }, function(data) {
			if(data.status){
				alert('请去邮箱重新激活账号，激活邮件可能会被拦截，如果没收到，请查看拦截队列，给您带来不便，敬请谅解！');
				window.location.href = "http://my.seu.edu.cn";
			}
			else{
				// alert(data.info);
				$("#title").text(data.info);
			}
        }, 'json');
	}else{
		checkUsername();
		checkPassword();
		checkPasswordRepeat();
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
		if ($('#password').val() == $('#passwordrepeat').val()) {
            isp = true;
            $('#passwordalert').text("");
        } else {
            isp = false;
        }
    }
}

function checkPasswordRepeat(){
	var passwordrepeat = $("#passwordrepeat").val().replace(/[ ]/g, "");
    if (!passwordrepeat) {
		isp = false;
        $('#passwordrepeatalert').text("请确认你的密码");
    } else {
        $('#passwordrepeatalert').text("");
        if ($('#password').val() == $('#passwordrepeat').val()) {
            isp = true;
            $('#passwordrepeatalert').text("");
        } else {
            $('#passwordrepeatalert').text("密码验证不正确");
            isp = false;
        }
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
