var width;
var height;

var imageWidth;
var imageHeight;
var iconX;
var iconY;
var iconWidth;
var iconHeight;
var count = -1;

$(function(){
	var sex = $('#sex').text().replace(/[ ]/g,"");
	var dept = $('#dept').text().replace(/[ ]/g,"");
	var grade = $('#grade').text().replace(/[ ]/g,"");
	var campus = $('#campus').text().replace(/[ ]/g,"");
	var intro = $('#intro').text();
	var qq = $('#qq').text().replace(/[ ]/g,"");
	var email = $('#email').text().replace(/[ ]/g,"");
	var weibo = $('#weibo').text().replace(/[ ]/g,"");

	if(sex == "male"){
		$("#usersex").find("option[value='男']").attr("selected", true);
	}else if(sex == "female"){
		$("#usersex").find("option[value='女']").attr("selected", true);
	}else{
		$("#usersex").find("option[value='']").attr("selected", true);
	}

	if(dept == "建筑学院"){
		$("#userdept").find("option[value='建筑学院']").attr("selected", true);
	}else if(dept == "机械工程学院"){
		$("#userdept").find("option[value='机械工程学院']").attr("selected", true);
	}else if(dept == "能源与环境学院"){
		$("#userdept").find("option[value='能源与环境学院']").attr("selected", true);
	}else if(dept == "信息科学与工程学院"){
		$("#userdept").find("option[value='信息科学与工程学院']").attr("selected", true);
	}else if(dept == "土木工程学院"){
		$("#userdept").find("option[value='建土木工程学院筑学院']").attr("selected", true);
	}else if(dept == "电子科学与工程学院"){
		$("#userdept").find("option[value='建电子科学与工程学院筑学院']").attr("selected", true);
	}else if(dept == "数学系"){
		$("#userdept").find("option[value='数学系']").attr("selected", true);
	}else if(dept == "自动化学院"){
		$("#userdept").find("option[value='自动化学院']").attr("selected", true);
	}else if(dept == "计算机科学与工程学院"){
		$("#userdept").find("option[value='计算机科学与工程学院']").attr("selected", true);
	}else if(dept == "物理系"){
		$("#userdept").find("option[value='物理系']").attr("selected", true);
	}else if(dept == "生物科学与医学工程学院"){
		$("#userdept").find("option[value='生物科学与医学工程学院']").attr("selected", true);
	}else if(dept == "材料科学与工程学院"){
		$("#userdept").find("option[value='材料科学与工程学院']").attr("selected", true);
	}else if(dept == "人文学院"){
		$("#userdept").find("option[value='人文学院']").attr("selected", true);
	}else if(dept == "经济管理学院"){
		$("#userdept").find("option[value='经济管理学院']").attr("selected", true);
	}else if(dept == "电气工程学院"){
		$("#userdept").find("option[value='电气工程学院']").attr("selected", true);
	}else if(dept == "外国语学院"){
		$("#userdept").find("option[value='外国语学院']").attr("selected", true);
	}else if(dept == "体育系"){
		$("#userdept").find("option[value='体育系']").attr("selected", true);
	}else if(dept == "化学化工学院"){
		$("#userdept").find("option[value='化学化工学院']").attr("selected", true);
	}else if(dept == "交通学院"){
		$("#userdept").find("option[value='交通学院']").attr("selected", true);
	}else if(dept == "仪器科学与工程学院"){
		$("#userdept").find("option[value='仪器科学与工程学院']").attr("selected", true);
	}else if(dept == "艺术学院"){
		$("#userdept").find("option[value='艺术学院']").attr("selected", true);
	}else if(dept == "法学院"){
		$("#userdept").find("option[value='法学院']").attr("selected", true);
	}else if(dept == "医学院"){
		$("#userdept").find("option[value='医学院']").attr("selected", true);
	}else if(dept == "公共卫生学院"){
		$("#userdept").find("option[value='公共卫生学院']").attr("selected", true);
	}else if(dept == "吴健雄学院"){
		$("#userdept").find("option[value='吴健雄学院']").attr("selected", true);
	}else if(dept == "软件学院"){
		$("#userdept").find("option[value='软件学院']").attr("selected", true);
	}else if(dept == "成贤学院"){
		$("#userdept").find("option[value='成贤学院']").attr("selected", true);
	}else if(dept == "集成电路（IC）学院"){
		$("#userdept").find("option[value='集成电路（IC）学院']").attr("selected", true);
	}else if(dept == "其它院系"){
		$("#userdept").find("option[value='其它院系']").attr("selected", true);
	}else{
		$("#userdept").find("option[value='']").attr("selected", true);
	}

	if(grade == "本科一年级"){
		$("#usergrade").find("option[value='本科一年级']").attr("selected", true);
	}else if(grade == "本科二年级"){
		$("#usergrade").find("option[value='本科二年级']").attr("selected", true);
	}else if(grade == "本科三年级"){
		$("#usergrade").find("option[value='本科三年级']").attr("selected", true);	
	}else if(grade == "本科四年级"){
		$("#usergrade").find("option[value='本科四年级']").attr("selected", true);
	}else if(grade == "研究生一年级"){
		$("#usergrade").find("option[value='研究生一年级']").attr("selected", true);
	}else if(grade == "研究生二年级"){
		$("#usergrade").find("option[value='研究生二年级']").attr("selected", true);
	}else if(grade == "研究生三年级"){
		$("#usergrade").find("option[value='研究生三年级']").attr("selected", true);
	}else{
		$("#usergrade").find("option[value='']").attr("selected", true);
	}

	if(campus == "九龙湖"){
		$("#usercampus").find("option[value='九龙湖']").attr("selected", true);
	}else if(campus == "四牌楼"){
		$("#usercampus").find("option[value='四牌楼']").attr("selected", true);
	}else if(campus == "丁家桥"){
		$("#usercampus").find("option[value='丁家桥']").attr("selected", true);
	}

	if(intro != ""){
		$("#userintro").attr("placeholder", intro);
	}else{
		$("#userintro").attr("placeholder", "还没有填写个人签名");
	}

	if(qq != ""){
		$("#userqq").attr("placeholder", qq);
	}else{
		$("#userqq").attr("placeholder", "还没有填写qq号");
	}

	if(email != ""){
		$("#useremail").attr("placeholder", email);
	}else{
		$("#useremail").attr("placeholder", "还没有填写邮箱地址");
	}

	if(weibo != ""){
		$("#userweibo").attr("placeholder", weibo);
	}else{
		$("#userweibo").attr("placeholder", "还没有填写微博昵称");
	}

});

function jcorp(){
	var api;
	$('#target').Jcrop({
		onChange: showPreview,
		onSelect: showPreview,
		aspectRatio: 1
	},function(){
		api = this;
		api.setSelect([imageWidth/2-50,imageHeight/2-50,imageWidth/2+50,imageHeight/2+50]);
		api.setOptions({ bgFade: true });
		api.ui.selection.addClass('jcrop-selection');
		$("#icon").click(function(){
			api.destroy();
		});
	});
}

function showPreview(coords){
	var rx = 48 / coords.w;//preview的宽
	var ry = 48 / coords.h;//preview的高
	iconX=coords.x;
	iconY=coords.y;
	iconWidth=coords.w;
	iconHeight=coords.h;
	$('#preview').css({
		width: Math.round(rx * imageWidth) + 'px',
		height: Math.round(ry * imageHeight) + 'px',
		marginLeft: '-' + Math.round(rx * coords.x) + 'px',
		marginTop: '-' + Math.round(ry * coords.y) + 'px'
	});
}

function ajaxFileUpload(){
    $.ajaxFileUpload
    (
        {
            url: '/user/upload_icon', //你处理上传文件的服务端
            secureuri: false,
            fileElementId: 'icon',
            dataType: 'json',
			complete:function(){
			},
            success: function (data, status)
			{
				var path = data.data.path.replace('.',"");
				width = data.data.width;
				height = data.data.height;
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

				$('#div_image').css('display', 'block');					
				$('#target').attr('src', path);
				$('#preview').attr('src', path);

				$('#target').load(function(){
					$('#target').css('width', 222);
					$('#target').css('height', 222);
					jcorp();
				});					
			},
			error: function (data, status, e)
			{
				alert(e);
			}
        }
    )
	return false;
} 

// $(function(){
// 	width = parseInt($("#target").attr('raw_width'));
// 	height = parseInt($("#target").attr('raw_height'));
	
// 	if(width<300 && height<300){
// 		imageWidth=width;
// 		imageHeight=height;
// 	}
// 	else if(width>height){
// 		imageWidth=300;
// 		imageHeight=300*height/width;
// 	}
// 	else if(height<width){
// 		imageHeight=300;
// 		imageWidth=300*width/height;
// 	}

// 	$('#target').css('width',imageWidth);
// 	$('#target').css('height',imageHeight);
	
// 	$("#target").click(function(){
// 		if($("#target").attr("src") != $("#preview").attr("src")) {
// 			$("#preview").attr("src",$("#target").attr("src"));
// 			jcorp();
// 		}
// 	});
// });
