var delete_events = [];
var delete_questions = [];
var delete_commodities = [];
var delete_answers = [];
var delete_eventcommets = [];
var delete_commoditycomments = [];
var delete_answerreplys = [];
var delete_invites = [];

$(function(){
	$(".event").click(function(){
        if(parseInt($(this).attr("picked")) == 0){
            var eid = $(this).attr("eid");
            $(this).attr("picked", 1);
            $(this).parents("li").css("background-color", "red");
            if($.inArray(parseInt(eid), delete_events) == -1){
                delete_events.push(parseInt(eid));
            }
            console.log(delete_events);
        }else{
            var eid = $(this).attr("eid");
            $(this).attr("picked", 0);
            $(this).parents("li").css("background-color", "white");
            if($.inArray(parseInt(eid), delete_events) != -1){
                delete_events = $.grep(delete_events, function(value){
                    return value != parseInt(eid);
                });
            }
            console.log(delete_events);
        }
    });

    $(".commodity").click(function(){
        if(parseInt($(this).attr("picked")) == 0){
            var cid = $(this).attr("cid");
            $(this).attr("picked", 1);
            $(this).parents("li").css("background-color", "red");
            if($.inArray(parseInt(cid), delete_commodities) == -1){
                delete_commodities.push(parseInt(cid));
            }
            console.log(delete_commodities);
        }else{
            var cid = $(this).attr("cid");
            $(this).attr("picked", 0);
            $(this).parents("li").css("background-color", "white");
            if($.inArray(parseInt(cid), delete_commodities) != -1){
                delete_commodities = $.grep(delete_commodities, function(value){
                    return value != parseInt(cid);
                });
            }
            console.log(delete_commodities);
        }
    });

    $(".question").click(function(){
        if(parseInt($(this).attr("picked")) == 0){
            var qid = $(this).attr("qid");
            $(this).attr("picked", 1);
            $(this).parents("li").css("background-color", "red");
            if($.inArray(parseInt(qid), delete_questions) == -1){
                delete_questions.push(parseInt(qid));
            }
            console.log(delete_questions);
        }else{
            var qid = $(this).attr("qid");
            $(this).attr("picked", 0);
            $(this).parents("li").css("background-color", "white");
            if($.inArray(parseInt(qid), delete_questions) != -1){
                delete_questions = $.grep(delete_questions, function(value){
                    return value != parseInt(qid);
                });
            }
            console.log(delete_questions);
        }
    });

	$(".answer").click(function(){
        if(parseInt($(this).attr("picked")) == 0){
            var aid = $(this).attr("aid");
            $(this).attr("picked", 1);
            $(this).parents("li").css("background-color", "red");
            if($.inArray(parseInt(aid), delete_answers) == -1){
                delete_answers.push(parseInt(aid));
            }
            console.log(delete_answers);
        }else{
            var aid = $(this).attr("aid");
            $(this).attr("picked", 0);
            $(this).parents("li").css("background-color", "white");
            if($.inArray(parseInt(aid), delete_answers) != -1){
                delete_answers = $.grep(delete_answers, function(value){
                    return value != parseInt(aid);
                });
            }
            console.log(delete_answers);
        }
    });

    $(".eventcomment").click(function(){
        if(parseInt($(this).attr("picked")) == 0){
            var ecid = $(this).attr("ecid");
            $(this).attr("picked", 1);
            $(this).parents("li").css("background-color", "red");
            if($.inArray(parseInt(ecid), delete_eventcommets) == -1){
                delete_eventcommets.push(parseInt(ecid));
            }
            console.log(delete_eventcommets);
        }else{
            var ecid = $(this).attr("ecid");
            $(this).attr("picked", 0);
            $(this).parents("li").css("background-color", "white");
            if($.inArray(parseInt(ecid), delete_eventcommets) != -1){
                delete_eventcommets = $.grep(delete_eventcommets, function(value){
                    return value != parseInt(ecid);
                });
            }
            console.log(delete_eventcommets);
        }
    });

    $(".commoditycomment").click(function(){
        if(parseInt($(this).attr("picked")) == 0){
            var ccid = $(this).attr("ccid");
            $(this).attr("picked", 1);
            $(this).parents("li").css("background-color", "red");
            if($.inArray(parseInt(ccid), delete_commoditycomments) == -1){
                delete_commoditycomments.push(parseInt(ccid));
            }
            console.log(delete_commoditycomments);
        }else{
            var ccid = $(this).attr("ccid");
            $(this).attr("picked", 0);
            $(this).parents("li").css("background-color", "white");
            if($.inArray(parseInt(ccid), delete_commoditycomments) != -1){
                delete_commoditycomments = $.grep(delete_commoditycomments, function(value){
                    return value != parseInt(ccid);
                });
            }
            console.log(delete_commoditycomments);
        }
    });

    $(".answerreply").click(function(){
        if(parseInt($(this).attr("picked")) == 0){
            var rid = $(this).attr("rid");
            $(this).attr("picked", 1);
            $(this).parents("li").css("background-color", "red");
            if($.inArray(parseInt(rid), delete_answerreplys) == -1){
                delete_answerreplys.push(parseInt(rid));
            }
            console.log(delete_answerreplys);
        }else{
            var rid = $(this).attr("rid");
            $(this).attr("picked", 0);
            $(this).parents("li").css("background-color", "white");
            if($.inArray(parseInt(rid), delete_answerreplys) != -1){
                delete_answerreplys = $.grep(delete_answerreplys, function(value){
                    return value != parseInt(rid);
                });
            }
            console.log(delete_answerreplys);
        }
    });

   	$(".deleteevent").click(function(){
        if(delete_events.length == 0){
            alert("还没有选择要删除的活动");
        }else{
        	$.post("/manage/deleteEvent", {
        		'eids': delete_events
        	}, function(data){
        		if(data.status == 1){
        			window.location.reload();
        		}else{
        			alert("删除失败");
        		}
        	}, "json");
        }
    });


	$(".deletecommodity").click(function(){
        if(delete_commodities.length == 0){
            alert("还没有选择要删除的商品");
        }else{
        	$.post("/manage/deleteCommodity", {
        		'cids': delete_commodities
        	}, function(data){
        		if(data.status == 1){
        			window.location.reload();
        		}else{
        			alert("删除失败");
        		}
        	}, "json");
        }
    });

	$(".deletequestion").click(function(){
        if(delete_questions.length == 0){
            alert("还没有选择要删除的问题");
        }else{
        	$.post("/manage/deleteQuestion", {
        		'qids': delete_questions
        	}, function(data){
        		if(data.status == 1){
        			window.location.reload();
        		}else{
        			alert("删除失败");
        		}
        	}, "json");
        }
    });

	$(".deleteanswer").click(function(){
        if(delete_answers.length == 0){
            alert("还没有选择要删除的回答");
        }else{
        	$.post("/manage/deleteAnswer", {
        		'aids': delete_answers
        	}, function(data){
        		if(data.status == 1){
        			window.location.reload();
        		}else{
        			alert("删除失败");
        		}
        	}, "json");
        }
    });

	$(".deleteeventcomment").click(function(){
        if(delete_eventcommets.length == 0){
            alert("还没有选择要删除的活动评论");
        }else{
        	$.post("/manage/deleteEventComment", {
        		'ecids': delete_eventcommets 
        	}, function(data){
        		if(data.status == 1){
        			window.location.reload();
        		}else{
        			alert("删除失败");
        		}
        	}, "json");
        }
    });

	$(".deletecommoditycomment").click(function(){
        if(delete_commoditycomments.length == 0){
            alert("还没有选择要删除的活动");
        }else{
        	$.post("/manage/deleteCommodityComment", {
        		'ccids': delete_commoditycomments
        	}, function(data){
        		if(data.status == 1){
        			window.location.reload();
        		}else{
        			alert("删除失败");
        		}
        	}, "json");
        }
    });

	$(".deleteanswerreply").click(function(){
        if(delete_answerreplys.length == 0){
            alert("还没有选择要删除的活动");
        }else{
        	$.post("/manage/deleteAnswerReply", {
        		'rids': delete_answerreplys
        	}, function(data){
        		if(data.status == 1){
        			window.location.reload();
        		}else{
        			alert("删除失败");
        		}
        	}, "json");
        }
    });

    $(".invite").click(function(){
        if(parseInt($(this).attr("picked")) == 0){
            var iid = $(this).attr("iid");
            $(this).attr("picked", 1);
            $(this).parents("li").css("background-color", "red");
            if($.inArray(parseInt(iid), delete_invites) == -1){
                delete_invites.push(parseInt(iid));
            }
            console.log(delete_invites);
        }else{
            var iid = $(this).attr("iid");
            $(this).attr("picked", 0);
            $(this).parents("li").css("background-color", "white");
            if($.inArray(parseInt(iid), delete_invites) != -1){
                delete_invites = $.grep(delete_invites, function(value){
                    return value != parseInt(iid);
                });
            }
            console.log(delete_invites);
        }
    });

    $(".deleteinvite").click(function(){
        if(delete_invites.length == 0){
            alert("还没有选择要设为已发送的邀请码");
        }else{
            $.post("/manage/deleteInvite", {
                'iids': delete_invites
            }, function(data){
                if(data.status == 1){
                    window.location.reload();
                }else{
                    alert("操作失败");
                }
            }, "json");
        }
    });

    $(".createinvite").click(function(){
        var count = $("#count").val().replace(/[ ]/g,"");
        if(count){
            var r = /^\d+$/;
            if(r.test(count)){
                $.post("/manage/createInvite", {
                    'count': count
                }, function(data){
                    if(data.status == 1){
                        window.location.reload();
                    }else{
                        alert("生成邀请码失败");
                    }
                }, "json");
            }else{
                $("#createerror").text("请填写整数");
            }
        }else{
            $("#createerror").text("请填写要生成邀请码的数量");
        }
    });
});