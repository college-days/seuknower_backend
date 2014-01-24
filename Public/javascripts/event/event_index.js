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
	$(".category a").hover(function(){
		var tag = $(this).text().replace(/[ ]/g,"");
		var time = $(".time .active").text().replace(/[ ]/g,"");						
		var link_to = "/event/" + tag + "/" + time;
		$(this).attr("href", link_to);
	});

	$(".time a").hover(function(){			
		var tag = $(".category .active").text().replace(/[ ]/g,"");
		var time = $(this).text().replace(/[ ]/g,"");
		var link_to = "/event/" + tag + "/" + time;
		$(this).attr("href", link_to);
	});

});
