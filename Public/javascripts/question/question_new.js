$(function(){
	$(".new a").click(function(){
		/*if($(".win").length > 0) {
			$(".mask").css("display","block");
			$(".win").css("display","block");
		}
		else{
			newQuestion();
		}*/
		newQuestion();
	});
	
});


function submitQuestion(){
	var title = $("#questiontitle").val();
	var intro = $("#questionintro").val();
	var anonymous = document.getElementById('anonymous').checked;
	var typeid = document.getElementById("questiontype").value;

	var titleLock = false;
	var introLock = true;

	if(title.replace(/[ ]/g, "")){
		titleLock = true;
	}else{
		alert("title is empty");
		$("#titlealert").show();
	}

	if(intro.replace(/[ ]/g, "")){
		introLock = true;
	}else{
		alert("intro is empty");
		$("#introalert").show();
	}

	if(titleLock && introLock){
		$.post('/question/new', {
			'title': title,
			'intro': intro,
			'typeid': typeid,
			'anonymous': anonymous
		}, function(data){
			if(data.status == 1){
				//alert(data.data['id']);
				window.location.href = "/question/" + parseInt(data.data['id']);
			}
			if(data.status == 0){
				alert('问题创建失败');
			}
			if(data.status == -1){
				window.location.href = "/login";
			}
		}, "json");
	}
}

function newQuestion(){
	var newMask = document.createElement("div");
	newMask.className = "mask";
	newMask.style.width = document.body.scrollWidth + "px";
	newMask.style.height = document.body.scrollHeight + "px";
	document.body.appendChild(newMask);
		
	var newWin = document.createElement("div");
	newWin.className = "win";
	newWin.style.left = (parseInt(document.body.scrollWidth) - 544)/2 + "px";
	document.body.appendChild(newWin);
		
	var html = '<div class="win"><div class="title-bar"><span>提问</span><div class="close"></div></div><div class="content"><div class="search"><p>提问之前请先搜索</p><input type="text" class="value"/><ul><li><a href="#">有没四牌楼校区的风景图 </a></li><li><a href="#">有没四牌楼校区的风景图 </a></li><li><a href="#">有没四牌楼校区的风景图 </a></li><li><a href="#">有没四牌楼校区的风景图 </a></li><li class="ask">我要提问</li></ul></div><div class="question-new"><p>问题</p><div id="titlealert" style="color:red;display:none;">标题不能为空</div><input type="text" class="value" id="questiontitle"/><p>问题描述</p><div id="introalert" style="color:red;display:none;">问题描述不能为空</div><textarea id="questionintro"></textarea><p>问题类型</p><select id="questiontype"><option value="0">生活娱乐</option><option value="1">学习考试</option><option value="2">规章制度</option><option value="3">技术专业</option><option value="4">其他</option></select><div><input type="checkbox" class="check" id="anonymous"/><span>匿名提问</span><input type="button" value="确定" class="btn" onclick="submitQuestion()"/></div></div></div></div>';

	/*var html = '<div class="win">
					<div class="title-bar">
						<span>提问</span>
						<div class="close"></div>
					</div>
					<div class="content">
						<div class="search">
							<p>提问之前请先搜索</p>
							<input type="text" class="value"/>
							<ul>
								<li><a href="#">有没四牌楼校区的风景图 </a></li>
								<li><a href="#">有没四牌楼校区的风景图 </a></li>
								<li><a href="#">有没四牌楼校区的风景图 </a></li>
								<li><a href="#">有没四牌楼校区的风景图 </a></li>
								<li class="ask">我要提问</li>
							</ul>
						</div>
						<div class="question-new">
							<p>问题</p>
							<input type="text" class="value"/>
							<p>问题描述</p>
							<textarea></textarea>
							<p>问题类型</p>
							<select>
								<option value="0">生活娱乐</option>
								<option value="1">学习考试</option>
								<option value="2">规章制度</option>
								<option value="3">技术专业</option>
								<option value="4">其他</option>
							</select>
							<div>
								<input type="checkbox" class="check"/>
								<span>匿名提问</span>
								<input type="button" value="确定" class="btn"/>
							</div>
						</div>
					</div>
				</div>';
	*/
	$(".win").append(html);
	
	$(".close").click(function(){
		$(".win").css("display","none");
		$(".mask").css("display","none");
		$("#questiontitle").val("");
		$("#questionintro").val("");
	});
	
	$(".mask").click(function(){
		$(".win").css("display","none");
		$(".mask").css("display","none");
		$("#questiontitle").val("");
		$("#questionintro").val("");
	});
	
	$(".ask").click(function(){
		$(".win .search").slideUp();
		$(".question-new").css("display","block");
	});

	$("#questiontitle").blur(function(){
		var title = $("#questiontitle").val();
		if(title.replace(/[ ]/g, "")){
			$("#titlealert").hide();
		}else{
			$("#titlealert").show();
		}
	});

	$("#questionintro").blur(function(){
		var intro = $("#questionintro").val();
		if(intro.replace(/[ ]/g, "")){
			$("#introalert").hide();
		}else{
			$("#introalert").show();
		}
	});

}