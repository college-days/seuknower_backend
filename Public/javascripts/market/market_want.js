var wantEditor;
KindEditor.ready(function(K) {
	wantEditor = K.create("#commoditywantintro", {
		items: ["bold", "italic", "underline", "preview", "code", "image", "link", "quickformat", "removeformat", "insertorderedlist", "insertunorderedlist"]
	});
	window.editor = wantEditor;

$(function(){
	var newMask = document.createElement("div");
	newMask.className = "newmask";
	newMask.style.width = document.body.scrollWidth + "px";
	newMask.style.height = document.body.scrollHeight + "px";
	document.body.appendChild(newMask);
	
	$('#wantBuydialog').css('left', (parseInt(document.body.scrollWidth) - 544)/2); 

	$(".newmask").hide();

	$(".newmask").click(function(){
		window.editor.html("");
		window.editor = wantEditor;
		if(window.editor){
			window.editor.html("");
		}
		$(".newwin").css("display","none");
		$(".newmask").css("display","none");
		$("#commoditywanttitle").val("");
	});

	$("#closecommoditywant").click(function(){
		window.editor.html("");
		window.editor = wantEditor;
		if(window.editor){
			window.editor.html("");
		}
		$(".newwin").css("display","none");
		$(".newmask").css("display","none");
		$("#commoditywanttitle").val("");
	});

	$("#commoditywanttitle").blur(function(){
		var title = $("#commoditywanttitle").val();
		if(title.replace(/[ ]/g, "")){
			$("#commoditywanttitlealert").hide();
		}else{
			$("#commoditywanttitlealert").show();
		}
	});
});

$(function(){
	$(".newcommoditywant a").click(function(){
		window.editor = wantEditor;
		$(".newmask").css("display","block");
		$("#wantBuydialog").css("display","block");
	});
});
});


// 商品分类
var goods_type = [{
		type_id: "衣物鞋帽",
		type_name: "衣物鞋帽",
		good_list: [{
				cat_type: "衣服",
				cat_name: "衣服"
			},{
				cat_type: "裤子",
				cat_name: "裤子"
			},{
				cat_type: "鞋子",
				cat_name: "鞋子"
			},{
				cat_type: "帽子",
				cat_name: "帽子"
			},{
				cat_type: "手套",
				cat_name: "手套"
			},{
				cat_type: "其他",
				cat_name: "其他"
			}
		]
	},{
		type_id: "数码电子",
		type_name: "数码电子",
		good_list: [{
				cat_type: "电脑",
				cat_name: "电脑"
			},{
				cat_type: "手机",
				cat_name: "手机"
			},{
				cat_type: "数码影音",
				cat_name: "数码影音"
			},{
				cat_type: "存储产品",
				cat_name: "存储产品"
			},{
				cat_type: "配件",
				cat_name: "配件"
			},{
				cat_type: "其他",
				cat_name: "其他"
			}
		]
	},{
		type_id: "生活日用",
		type_name: "生活日用",
		good_list: [{
				cat_type: "自行车",
				cat_name: "自行车"
			},{
				cat_type: "日用电器",
				cat_name: "日用电器"
			},{
				cat_type: "体育用品",
				cat_name: "体育用品"
			},{
				cat_type: "乐器",
				cat_name: "乐器"
			},{
				cat_type: "其他",
				cat_name: "其他"
			}
		]
	},{
		type_id: "书籍杂志",
		type_name: "书籍杂志",
		good_list: [{
				cat_type: "本科教材",
				cat_name: "本科教材"
			},{
				cat_type: "GRE",
				cat_name: "GRE"
			},{
				cat_type: "考研",
				cat_name: "考研"
			},{
				cat_type: "雅思",
				cat_name: "雅思"
			},{
				cat_type: "托福",
				cat_name: "托福"
			},{
				cat_type: "其他",
				cat_name: "其他"
			}
		]
	},{
		type_id: "0元转让",
		type_name: "0元转让"
	},{
		type_id: "其他",
		type_name: "其他"
	}
];

$(function(){
	var commoditytype = $("#commoditytype"),
		secondtype = $("#secondtype");
		
	// 商品分类
	$.each(goods_type, function(k, v){
		var goodOption = "<option value="+ v.type_id +">"+ v.type_name +"</option>";
		commoditytype.append(goodOption);
	});

	/*setCommoditytype(commoditytype.val());
	commoditytype.change(function(){
		setCommoditytype($(this).val());
	});
	function setCommoditytype(val){
		$.each(goods_type, function(k, v){
			if(val == v.type_id){
				secondtype.empty().hide();
				if(v.good_list){
					secondtype.show();
					$.each(v.good_list, function(i,m){
						var catOption = "<option value="+ m.cat_type +">"+ m.cat_name +"</option>";
						secondtype.append(catOption);
					})
				}
			}
		})
	}*/
})
function submitCommodityWant(){
	var title = $("#commoditywanttitle").val();
	var intro = window.editor.html().replace(/\s(style|class).[^<=]*"\B/g,"");
	var type = $("#commoditytype").val();

	var titleLock = false;
	var introLock = false;

	if(title.replace(/[ ]/g, "")){
		titleLock = true;
	}else{
		$("#commoditywanttitlealert").show();
	}

	if(intro.replace(/[ ]/g, "")){
		introLock = true;
	}else{
		$("#commoditywantintroalert").show();
	}

	if(titleLock && introLock){
		$.post('/market/new_commodity_want', {
			'title': title,
			'intro': intro,
			'type': type
		}, function(data){
			if(data.status == 1){
				window.location.href = "/market/want/" + parseInt(data.data['id']);
			}
			if(data.status == 0){
				alert('求购创建失败');
			}
			if(data.status == -1){
				window.location.href = "/login";
			}
		}, "json");
	}
}