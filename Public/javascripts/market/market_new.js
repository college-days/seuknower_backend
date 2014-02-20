$(function(){
	$("#commoditytitle").blur(function(){
		var title = $("#commoditytitle").val();
		if(title.replace(/[ ]/g, "")){
			$("#titlealert").hide();
		}else{
			$("#titlealert").show();
		}
	});

	$("#commodityintro").blur(function(){
		var intro = $("#commodityintro").val();
		if(intro.replace(/[ ]/g, "")){
			$("#introalert").hide();
		}else{
			$("#introalert").show();
		}
	});

	$("#commodityprice").blur(function(){
		var price = $("#commodityprice").val();
		if(price.replace(/[ ]/g, "")){
			$("#pricealert").hide();
		}else{
			$("#pricealert").show();
		}
	});

	$("#commodityintro").blur(function(){
		var intro = $("#commodityintro").val();
		if(intro.replace(/[ ]/g, "")){
			$("#introalert").hide();
		}else{
			$("#introalert").show();
		}
	});

	$("#commoditycontact").blur(function(){
		var contact = $("#commoditycontact").val();
		if(contact.replace(/[ ]/g, "")){
			$("#contactalert").hide();
		}else{
			$("#contactalert").show();
		}
	});
});