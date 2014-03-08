$(function(){
	//create new event stuff talk about it later
	/*$("#new_event").click(function(){
		if($("#user_name").length>0){
			window.location.href =URL+"/new_event";
		}
		else{
			$('#loginModel').modal();
		}
	});*/
	var id = 1;
	var load = 1;
	$("#cate_type a").hover(function(){
		var tag = $(this).text().replace(/[ ]/g,"");
		var time = $("#cate_time .active").text().replace(/[ ]/g,"");						
		var link_to = "/event/" + tag + "/" + time;
		$(this).attr("href", link_to);
	});

	$("#cate_time a").hover(function(){			
		var tag = $("#cate_type .active").text().replace(/[ ]/g,"");
		var time = $(this).text().replace(/[ ]/g,"");
		var link_to = "/event/" + tag + "/" + time;
		$(this).attr("href", link_to);
	});

	$("#load").click(function(){
		
		
	});
	
	$(window).scroll(function(){
		if($("body").height() > 900){
			if($(document).scrollTop() < 100){
				$(".left").attr("class","left");
				$("#left_event").attr("class","column");
			}
			else{
				$("#left_event").attr("class","column column-fix");
				$(".left").attr("class","left left-fix");
			}
		}

		if($(document).scrollTop() > $(".load").offset().top - $(window).height() && $(".load").text().replace(/[ ]/g,"")){
			id = id+1;
			var tag = $("#cate_type .active").text().replace(/[ ]/g,"");
			var time = $("#cate_time .active").text().replace(/[ ]/g,"");
			$.post(APP+'/event/load',{'tag':tag,'time':time,'id':id},function(data){
				if(data.status){
					var events = data.data;
					for (var i=0; i<events.length; i++){
						if(events[i]["poster"] != "notexists"){
							html = '<div class="content"><img src="'+events[i]["poster"]+'"/><div class="info"><dl><dt align="center"><a href="/event/'+events[i]["id"]+'">'+events[i]["title"]+'</a></dt><dd>时间：'+events[i]["time"]+'</dd><dd>地点：'+events[i]["location"]+'</dd><dd class="interest">'+events[i]["join_count"]+'人参加<span>|</span>'+events[i]["interest_count"]+'人感兴趣</dd></dl></div></div>';
						}
						else{
							html = '<div class="content"><div class="info"><dl><dt align="center"><a href="/event/'+events[i]["id"]+'">'+events[i]["title"]+'</a></dt><dd>时间：'+events[i]["time"]+'</dd><dd>地点：'+events[i]["location"]+'</dd><dd class="interest">'+events[i]["join_count"]+'人参加<span>|</span>'+events[i]["interest_count"]+'人感兴趣</dd></dl></div></div>';
						}
						var left_height = $("#left_event").height();
						var middle_height = $("#middle_event").height();
						var right_height = $("#right_event").height();
						if(left_height < middle_height && left_height < right_height){
							$("#left_event").append(html);
						}
						else if(middle_height < left_height && middle_height < right_height){
							$("#middle_event").append(html);
						}
						else{
							$("#right_event").append(html);
						}
					
					}
					if(events.length < 6){
						$(".load").empty();
						if($("#none").length > 0){
						}
						else{
							$(".right").append('<div style="padding-top:20px;margin-left:auto;margin-right:auto;font-size:16px;text-align:center;" id="none">没有更多活动</div>');
						}
					}
				}
				else{
					$(".load").empty();
					if($("#none").length > 0){
					}
					else{
						$(".right").append('<div style="padding-top:20px;margin-left:auto;margin-right:auto;font-size:16px;text-align:center;" id="none">没有更多活动</div>');
					}
				}
			},'json');
		}
	});
});
