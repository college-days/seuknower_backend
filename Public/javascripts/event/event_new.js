var width;
var height;

var imageWidth;
var imageHeight;
var iconX;
var iconY;
var iconWidth;
var iconHeight;

var uploading = false;
var hasCost = false;

var time = 0;

$(function(){
	for (var i=0; i<=48; i++) {
		$("#startlasttime").append('<option value="'+adjustNumber(parseInt(time/60))+':'+adjustNumber(parseInt(time%60))+'">'+adjustNumber(parseInt(time/60))+':'+adjustNumber(parseInt(time%60))+'</option>')
		$("#endlasttime").append('<option value="'+adjustNumber(parseInt(time/60))+':'+adjustNumber(parseInt(time%60))+'">'+adjustNumber(parseInt(time/60))+':'+adjustNumber(parseInt(time%60))+'</option>')
		time += 30;
	};

	$("#title").blur(function(){
		checkTitle();
	});

	$("#location").blur(function(){
		checkLocation();
	});

	$("#intro").blur(function(){
		checkIntro();
	});

	$("#organizer").blur(function(){
		checkOrganizer();
	});

	$("#cost").blur(function(){
		checkCost();
	});

	$("#div_cost").hide();
	$("#yes_cost").click(function(){
		hasCost = true;
		$("#div_cost").show();
	});
	$("#no_cost").click(function(){
		hasCost = false;
		$("#div_cost").hide();
	});

	$('#startdate').focus(function(){
		$(".calender").hide();
		HS_setDate(this);
		
	});
	$('#enddate').focus(function(){
		$(".calender").hide();
		HS_setDate(this);
	});

	$('#submitevent').click(function(){
		if($("#title").val().replace(/[ ]/g,"") && $("#startdate").val().replace(/[ ]/g,"") && $("#enddate").val().replace(/[ ]/g,"") && $("#location").val().replace(/[ ]/g,"") && window.editor.html().replace(/[ ]/g,"") && $("#organizer").val().replace(/[ ]/g,"")){
			if(iconWidth && iconHeight){
				iconX=iconX*width/imageWidth;
				iconWidth=iconWidth*width/imageWidth;
				iconY=iconY*height/imageHeight;
				iconHeight=iconHeight*height/imageHeight;
				$.post('/event/thumb_poster', {
					imagepath: '.'+$('#target').attr('src'),
					iconx: iconX,
					icony: iconY,
					width: iconWidth,
					height: iconHeight
				}, function(data){
					$('#rawpath').val(data.data.rawpath.replace(".",""));
					$('#thumbpath').val(data.data.thumbpath.replace(".",""));
					$("#intro").val(window.editor.html().replace(/\s\w[^<\/>]*/g,""));
					$('#event').submit();
				},'json');
			}else{
				if(!uploading){
					checkPoster();
				}
				else{
					alert('文件正在上传。。。');
				}
			}
		}else{
			checkTitle();
			checkStartTime();
			checkEndTime();
			checkIntro();
			checkLocation();
			checkOrganizer();
			checkPoster();
		}
	});

	$('#poster').change(function(){
		uploading = true;
		$('#div_image').css('display', 'block');
		$('#target').attr('src', '/Public/images/common/loading.gif');
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
    $.ajaxFileUpload({
        url: '/event/upload_poster', //你处理上传文件的服务端
        secureuri: false,
        fileElementId: 'poster',
        dataType: 'json',
		complete:function(){
		},
        success: function (data, status){
			uploading = false;
			var path=data.data.path.replace('.',"");
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
			$("#poster").hide();
			$('#div_image').css('display','block');
			$('#target').attr('src',path);
			$('#preview').attr('src',path);
			$('#target').load(function(){
				$('#target').attr('width',imageWidth);
				$('#target').attr('height',imageHeight);
				jcorp();
			});					
		},
		error: function (data, status, e)
		{
			alert('图片不能大于3M,请压缩后上传');
		}
    });
	return false;
} 

//点击页面的其余地方隐藏calender插件
$(document).bind("click",function(e){
	var target  = $(e.target);//表示当前对象，切记，如果没有e这个参数，即表示整个BODY对象
	if((target.attr('name') != "startdate") && (target.attr('name') != "enddate") && (!target.is('a'))){
		if(target.closest(".calender").length == 0){
			$(".calender").hide();
		}
	}
}); 

function adjustNumber(number){
	if (number<10) {
		return "0"+String(number);
	}else{
		return number;
	}
}

function checkTitle(){
	var title = $("#title").val();
	if(title.replace(/[ ]/g, "")){
		$("#titlealert").text("");
	}else{
		$("#titlealert").text("请填写活动的名称");
	}
}

//这俩日期的检查只能是提交的时候再做了，不然会和日期选择插件起冲突
function checkStartTime(){
	var date = $("#startdate").val();
	if(date.replace(/[ ]/g, "")){
		$("#starttimealert").text("");
	}else{
		$("#starttimealert").text("请填写活动的开始日期");
	}
}

function checkEndTime(){
	var date = $("#enddate").val();
	if(date.replace(/[ ]/g, "")){
		$("#endtimealert").text("");
	}else{
		$("#endtimealert").text("请填写活动的结束日期");
	}
}

function checkLocation(){
	var location = $("#location").val();
	if(location.replace(/[ ]/g, "")){
		$("#locationalert").text("");
	}else{
		$("#locationalert").text("请填写活动的地点");
	}
}

function checkIntro(){
	// var intro = $("#intro").val();
	var intro = window.editor.html();
	if(intro.replace(/[ ]/g, "")){
		$("#introalert").text("");
	}else{
		$("#introalert").text("请填写活动简介");
	}
}

function checkCost(){
	if(hasCost){
		var cost = $("#cost").val();
		if(cost.replace(/[ ]/g, "")){
			if(!isNaN(cost)){
				$("#costalert").text("");
			}else{
				$("#costalert").text("只要填写经费的数字就可以啦");
			}
			
		}else{
			$("#costalert").text("请填写活动的费用");
		}
	}
}

function checkPoster(){
	if($("#thumbpath").val().replace(/[ ]/g,"")){
		console.log("yes");
		$("#posteralert").text("");
	}
	else{
		console.log("no");
		$("#posteralert").text("请上传活动的海报");
	}
}

function checkOrganizer(){
	var organizer = $("#organizer").val();
	if(organizer.replace(/[ ]/g, "")){
		$("#organizeralert").text("");
	}else{
		$("#organizeralert").text("请填写活动的主办单位");
	}
}

//for calender plugin
var calenderObj;
function HS_DateAdd(interval,number,date){  
   number = parseInt(number);  
   if (typeof(date)=="string"){var date = new Date(date.split("-")[0],date.split("-")[1],date.split("-")[2])}  
   if (typeof(date)=="object"){var datedate = date}  
   switch(interval){  
   case "y":return new Date(date.getFullYear()+number,date.getMonth(),date.getDate()); break;  
   case "m":return new Date(date.getFullYear(),date.getMonth()+number,checkDate(date.getFullYear(),date.getMonth()+number,date.getDate())); break;  
   case "d":return new Date(date.getFullYear(),date.getMonth(),date.getDate()+number); break;  
   case "w":return new Date(date.getFullYear(),date.getMonth(),7*number+date.getDate()); break;  
   }  
}  

function checkDate(year,month,date){  
   var enddate = ["31","28","31","30","31","30","31","31","30","31","30","31"];  
   var returnDate = "";  
   if (year%4==0){enddate[1]="29"}  
   if (date>enddate[month]){returnDate = enddate[month]}else{returnDate = date}  
   return returnDate;  
}  

function HS_setDate(inputObj){
	calenderObj = document.createElement("span");  
	calenderObj.style.zIndex = "33333";
	calenderObj.innerHTML = HS_calender(new Date()); 
	calenderObj.style.position = "absolute";  
	calenderObj.targetObj = inputObj;  
	inputObj.parentNode.insertBefore(calenderObj,inputObj.nextSibling);  
}  

function WeekDay(date){  
   var theDate;  
   if (typeof(date)=="string"){theDate = new Date(date.split("-")[0],date.split("-")[1],date.split("-")[2]);}  
   if (typeof(date)=="object"){theDate = date}  
   return theDate.getDay();  
}  

function HS_calender(){  
   var lis = "";  
   var style = "";  
   /* http://www.webdm.cn*/  
   style +="<style type='text/css'>";  
   style +=".calender { width:170px; height:auto; font-size:12px; margin-right:14px; background:url(calenderbg.gif) no-repeat right center #fff; border:1px solid #397EAE; padding:1px;}";  
   style +=".calender ul {list-style-type:none; margin:0; padding:0;}";  
   style +=".calender .day { background-color:#EDF5FF; height:20px;}";  
   style +=".calender .day li,.calender .date li{ float:left; width:14%; height:20px; line-height:20px; text-align:center}";  
   style +=".calender li a { text-decoration:none; font-family:Tahoma; font-size:11px; color:#333}";  
   style +=".calender li a:hover { color:#f30; text-decoration:underline}";  
   style +=".calender li a.hasArticle {font-weight:bold; color:#f60 !important}";  
   style +=".lastMonthDate, .nextMonthDate {color:#bbb;font-size:11px}";  
   style +=".selectThisYear a, .selectThisMonth a{text-decoration:none; margin:0 2px; color:#000; font-weight:bold}";  
   style +=".calender .LastMonth, .calender .NextMonth{ text-decoration:none; color:#000; font-size:18px; font-weight:bold; line-height:16px;}";  
   style +=".calender .LastMonth { float:left;}";  
   style +=".calender .NextMonth { float:right;}";  
   style +=".calenderBody {clear:both}";  
   style +=".calenderTitle {text-align:center;height:20px; line-height:20px; clear:both}";  
   style +=".today { background-color:#ffffaa;border:1px solid #f60; padding:2px}";  
   style +=".today a { color:#f30; }";  
   style +=".calenderBottom {clear:both; border-top:1px solid #ddd; padding: 3px 0; text-align:left}";  
   style +=".calenderBottom a {text-decoration:none; margin:2px !important; font-weight:bold; color:#000}";  
   style +=".calenderBottom a.closeCalender{float:right}";  
   style +=".closeCalenderBox {float:right; border:1px solid #000; background:#fff; font-size:9px; width:11px; height:11px; line-height:11px; text-align:center;overflow:hidden; font-weight:normal !important}";  
   style +="</style>";  
  
   var now;  
   if (typeof(arguments[0])=="string"){  
      selectDate = arguments[0].split("-");  
      var year = selectDate[0];  
      var month = parseInt(selectDate[1])-1+"";  
      var date = selectDate[2];  
      now = new Date(year,month,date);  
   }else if (typeof(arguments[0])=="object"){  
      now = arguments[0];  
   }  
   var lastMonthEndDate = HS_DateAdd("d","-1",now.getFullYear()+"-"+now.getMonth()+"-01").getDate();  
   var lastMonthDate = WeekDay(now.getFullYear()+"-"+now.getMonth()+"-01");  
   var thisMonthLastDate = HS_DateAdd("d","-1",now.getFullYear()+"-"+(parseInt(now.getMonth())+1).toString()+"-01");  
   var thisMonthEndDate = thisMonthLastDate.getDate();  
   var thisMonthEndDay = thisMonthLastDate.getDay();  
   var todayObj = new Date();  
   today = todayObj.getFullYear()+"-"+todayObj.getMonth()+"-"+todayObj.getDate();  
     
   for (i=0; i<lastMonthDate; i++){  // Last Month's Date  
      lis = "<li class='lastMonthDate'>"+lastMonthEndDate+"</li>" + lis;  
      lastMonthEndDate--;  
   }  
   for (i=1; i<=thisMonthEndDate; i++){ // Current Month's Date  
  
      if(today == now.getFullYear()+"-"+now.getMonth()+"-"+i){  
         var todayString = now.getFullYear()+"-"+(parseInt(now.getMonth())+1).toString()+"-"+i;  
         lis += "<li><a href=javascript:void(0) class='today' onclick='_selectThisDay(this)' title='"+now.getFullYear()+"-"+(parseInt(now.getMonth())+1)+"-"+i+"'>"+i+"</a></li>";  
      }else{  
         lis += "<li><a href=javascript:void(0) onclick='_selectThisDay(this)' title='"+now.getFullYear()+"-"+(parseInt(now.getMonth())+1)+"-"+i+"'>"+i+"</a></li>";  
      }  
        
   }  
   var j=1;  
   for (i=thisMonthEndDay; i<6; i++){  // Next Month's Date  
      lis += "<li class='nextMonthDate'>"+j+"</li>";  
      j++;  
   }  
   lis += style;  
  
   var CalenderTitle = "<a href='javascript:void(0)' class='NextMonth' onclick=HS_calender(HS_DateAdd('m',1,'"+now.getFullYear()+"-"+now.getMonth()+"-"+now.getDate()+"'),this) title='Next Month'>»</a>";  
   CalenderTitle += "<a href='javascript:void(0)' class='LastMonth' onclick=HS_calender(HS_DateAdd('m',-1,'"+now.getFullYear()+"-"+now.getMonth()+"-"+now.getDate()+"'),this) title='Previous Month'>«</a>";  
   CalenderTitle += "<span class='selectThisYear'><a href='javascript:void(0)' onclick='CalenderselectYear(this)' title='Click here to select other year' >"+now.getFullYear()+"</a></span>年<span class='selectThisMonth'><a href='javascript:void(0)' onclick='CalenderselectMonth(this)' title='Click here to select other month'>"+(parseInt(now.getMonth())+1).toString()+"</a></span>月";   
  
   if (arguments.length>1){  
      arguments[1].parentNode.parentNode.getElementsByTagName("ul")[1].innerHTML = lis;  
      arguments[1].parentNode.innerHTML = CalenderTitle;  
  
   }else{  
      var CalenderBox = style+"<div class='calender'><div class='calenderTitle'>"+CalenderTitle+"</div><div class='calenderBody'><ul class='day'><li>日</li><li>一</li><li>二</li><li>三</li><li>四</li><li>五</li><li>六</li></ul><ul class='date' id='thisMonthDate'>"+lis+"</ul></div><div class='calenderBottom'><a href='javascript:void(0)' class='closeCalender' onclick='closeCalender(this)'>×</a><span><span><a href=javascript:void(0) onclick='_selectThisDay(this)' title='"+todayString+"'>Today</a></span></span></div></div>";  
      return CalenderBox;  
   }  
}  

function _selectThisDay(d){  
   var boxObj = d.parentNode.parentNode.parentNode.parentNode.parentNode;  
      boxObj.targetObj.value = d.title;  
      boxObj.parentNode.removeChild(boxObj);  
}  
function closeCalender(d){  
   var boxObj = d.parentNode.parentNode.parentNode;  
      boxObj.parentNode.removeChild(boxObj);  
}  
  
function CalenderselectYear(obj){  
      var opt = "";  
      var thisYear = obj.innerHTML;  
      for (i=1970; i<=2020; i++){  
         if (i==thisYear){  
            opt += "<option value="+i+" selected>"+i+"</option>";  
         }else{  
            opt += "<option value="+i+">"+i+"</option>";  
         }  
      }  
      opt = "<select onblur='selectThisYear(this)' onchange='selectThisYear(this)' style='font-size:11px'>"+opt+"</select>";  
      obj.parentNode.innerHTML = opt;  
}  
  
function selectThisYear(obj){  
   HS_calender(obj.value+"-"+obj.parentNode.parentNode.getElementsByTagName("span")[1].getElementsByTagName("a")[0].innerHTML+"-1",obj.parentNode);  
}  
  
function CalenderselectMonth(obj){  
      var opt = "";  
      var thisMonth = obj.innerHTML;  
      for (i=1; i<=12; i++){  
         if (i==thisMonth){  
            opt += "<option value="+i+" selected>"+i+"</option>";  
         }else{  
            opt += "<option value="+i+">"+i+"</option>";  
         }  
      }  
      opt = "<select onblur='selectThisMonth(this)' onchange='selectThisMonth(this)' style='font-size:11px'>"+opt+"</select>";  
      obj.parentNode.innerHTML = opt;  
}  

function selectThisMonth(obj){  
   HS_calender(obj.parentNode.parentNode.getElementsByTagName("span")[0].getElementsByTagName("a")[0].innerHTML+"-"+obj.value+"-1",obj.parentNode);  
}  

function removeCalender(){
	
	calenderObj.parentNode.removeChild(calenderObj);
}
