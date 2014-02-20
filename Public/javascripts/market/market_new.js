var width;
var height;

var imageWidth;
var imageHeight;
var iconX;
var iconY;
var iconWidth;
var iconHeight;

var uploading = false;

$(function(){
	$("#commoditytitle").blur(function(){
		checkTitle();
	});

	$("#commodityintro").blur(function(){
		checkIntro();
	});

	$("#commodityprice").blur(function(){
		checkPrice();
	});

	$("#commoditycontact").blur(function(){
		checkContact();
	});

	$('#nextstep').click(function(){
		if($("#commoditytitle").val().replace(/[ ]/g,"") && $("#commodityprice").val().replace(/[ ]/g,"") && $("#commoditycontact").val().replace(/[ ]/g,"") && $("#commodityintro").val().replace(/[ ]/g,"")){
			if(iconWidth && iconHeight){
				iconX = iconX*width/imageWidth;
				iconWidth = iconWidth*width/imageWidth;
				iconY = iconY*height/imageHeight;
				iconHeight = iconHeight*height/imageHeight;
				$.post('/market/thumb_picture', {
					imagepath: '.'+$('#target').attr('src'),
					iconx: iconX,
					icony: iconY,
					width: iconWidth,
					height: iconHeight
				}, function(data){
					$('#rawpath').val(data.data.rawpath.replace(".",""));
					$('#thumbpath').val(data.data.thumbpath.replace(".",""));
					$("#commodity").submit();
				}, 'json');
			}else{
				if(!uploading){
					checkPicture();
				}
				else{
					alert('文件正在上传。。。');
				}
			}
		}else{
			checkTitle();
			checkPrice();
			checkContact();
			checkIntro();
			checkPicture();
		}
	});

	$('#picture').change(function(){
		uploading = true;
		$('#div_image').show();
		$('#target').attr('src','/Public/images/common/loading.gif');
		ajaxFileUpload();
	});
});

function jcorp(){
	var api;
	$('#target').Jcrop({
		onChange: showPreview,
		onSelect: showPreview,
		aspectRatio: 1.16
	}, function(){
	api = this;
	api.setSelect([imageWidth/2-75,imageHeight/2-65,imageWidth/2+75,imageHeight/2+65]);
	api.setOptions({ bgFade: true });
	api.ui.selection.addClass('jcrop-selection');
	});
}

function showPreview(coords){
	var rx = 150 / coords.w;//preview的宽
	var ry = 130 / coords.h;//preview的高
	//$('#result').text(coords.x+","+coords.y+","+coords.w+","+coords.h);
	iconX = coords.x;
	iconY = coords.y;
	iconWidth = coords.w;
	iconHeight = coords.h;
	$('#preview').css({
		width: Math.round(rx * imageWidth) + 'px',
		height: Math.round(ry * imageHeight) + 'px',
		marginLeft: '-' + Math.round(rx * coords.x) + 'px',
		marginTop: '-' + Math.round(ry * coords.y) + 'px'
	});
}

function ajaxFileUpload(){
    $.ajaxFileUpload(
	    {
	    	url: '/market/upload_picture', //你处理上传文件的服务端
	        secureuri: false,
	        fileElementId: 'picture',
	        dataType: 'json',
			complete: function(){
			},
	        success: function (data, status){
				uploading = false;
				var path=data.data.path.replace('.', "");
				width=data.data.width;
				height=data.data.height;
				if(width<300 && height<300){
					imageWidth=width;
					imageHeight=height;
				}
				else if(width>height){
					imageWidth=300;
					imageHeight=300*height/width;
				}
				else{
					imageHeight=300;
					imageWidth=300*width/height;
				}
				$("#picture").hide();
				$('#div_image').show();

				$('#target').attr('src',path);
				$('#preview').attr('src',path);
				$('#target').load(function(){
					$('#target').attr('width',imageWidth);
					$('#target').attr('height',imageHeight);
					jcorp();
				});					
			},
			error: function (data, status, e){
				alert('图片不能大于3M,请压缩后上传');
			}
	    }
    );
	return false;
} 
	

function checkTitle(){
	var title = $("#commoditytitle").val();
	if(title.replace(/[ ]/g, "")){
		$("#titlealert").hide();
	}else{
		$("#titlealert").show();
	}
}

function checkIntro(){
	var intro = $("#commodityintro").val();
	if(intro.replace(/[ ]/g, "")){
		$("#introalert").hide();
	}else{
		$("#introalert").show();
	}
}

function checkPrice(){
	var price = $("#commodityprice").val();
	if(price.replace(/[ ]/g, "")){
		//判断是不是仅由数字组成
		if(!isNaN(price)){
			$("#pricealert").text("");
		}else{
			$("#pricealert").text("亲～～只要输入数字就可以了");
		}
	}else{
		$("#pricealert").text("请填写商品的价格");
	}
}

function checkContact(){
	var contact = $("#commoditycontact").val();
	if(contact){
		if(!isNaN(contact) && contact.length == 11){
			$("#contactalert").text("");
		}else{
			$("#contactalert").text("手机号格式不正确");
		}
	}
	else{
		$("#contactalert").text("手机号仅用于买家与您联系，不会用作他途，请放心");
	}
}

function checkPicture(){
	if($("#thumbpath").val().replace(/[ ]/g,"")){
		console.log("yes");
		$("#picturealert").text("");
	}
	else{
		console.log("no");
		$("#picturealert").text("上传照片更有利于买家了解产品哦～～");
	}
}