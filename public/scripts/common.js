//搜索
$(document).ready(function(){
    $("#selopt").hover(
        function(){
            $("#options").slideDown();
            $("#options li a").click(function(){
                $("#cursel").text($(this).text());
                $("#type").attr("value", $(this).attr("name"));
                $("#options").hide();
            });
        },
        
        function(){$("#options").hide();}
    )   
})

//搜索伪静态
function rewrite_search(){
	var type = $("#type").val();
	var query = $.trim($("#query").val());
	if (type == null) {type = "tags"}
	if (query == "") {
		alert("\u8bf7\u8f93\u5165\u641c\u7d22\u5173\u952e\u5b57\uff01");
		$("#query").focus();
		return false;
	} else {
		if (rewrite == 1) {
			window.location.href = sitepath + "search-" + type + "-" + encodeURI(query) + ".html";
		} else if (rewrite == 2) {
			window.location.href = sitepath + "search/" + type + "/" + encodeURI(query) + ".html";
		} else if (rewrite == 3) {
			window.location.href = sitepath + "search/" + type + "/" + encodeURI(query);
		} else {
			this.form.submit();
		}
	}
	return false;
}
//评论
function post_comment() {
	var $content = $('#content').val();
	var $email = $('#email').val();
	var $nick = $('#nick').val();
	var $wid = parseInt($('#wid').val());
	var $rid = parseInt($('#rid').val());
	if ($content == '') {
		$('#content').focus();
		return false;
	} else {
		if ($content.length > 250) {
			alert('内容长度超过250个字符！');	
			return false;
		}
	}
	if ($email == '') {
		$('#email').focus();
		return false;
	} else {
		var $reg = /^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/;	
		if (!$reg.test($email)) {
			alert('Email格式不正确！');
			$('#email').focus();
			return false;
		}
	}
	if ($nick == '') {
		$('#nick').focus();
		return false;
	}
	
	$.ajax({
		type: 'POST',
		url: sitepath + '?mod=ajaxpost',
		datatype: 'html',
		data: {'type' : 'comment', 'rid' : $rid, 'wid' : $wid, 'content' : $content, 'email' : $email, 'nick' : $nick},
		success: function($data) {
			if ($data == 1) {
				location.reload();
			} else {
				alert($data);	
			}
		}	
	});
	return false;
}
//验证url
function checkurl(url){
	if (url == '') {
		$("#msg").html('请输入网站域名！');
		return false;
	}
	
	$(document).ready(function(){$("#msg").html('<img src="' + sitepath + 'public/images/loading.gif" align="absmiddle"> 正在验证，请稍候...'); $.ajax({type: "GET", url: sitepath + '?mod=ajaxget&type=check', data: 'url=' + url, cache: false, success: function(data){$("#msg").html(data)}});});
return true;
};

//获取META
function getmeta() {
	var url = $("#web_url").attr("value");
	if (url == '') {
		alert('请输入网站域名！');
		$("#web_url").focus();
		return false;
	}
	$(document).ready(function(){$("#meta_btn").val('正在获取，请稍候...'); $.ajax({type: "GET", url: sitepath + '?mod=ajaxget&type=crawl', data: 'url=' + url, datatype: "script", cache: false, success: function(data){$("body").append(data); $("#meta_btn").val('重新获取');}});});	
}

//获取IP, PageRank, Sogou PageRank, Alexa
function getdata() {
	var url = $("#web_url").attr("value");
	if (url == '') {
		alert('请输入网站域名！');
		$("#web_url").focus();
		return false;
	}
	$(document).ready(function(){$("#data_btn").val('正在获取，请稍候...'); $.ajax({type: "GET", url: sitepath + '?mod=ajaxget&type=data', data: 'url=' + url, datatype: "script", cache: false, success: function(data){$("body").append(data)}}); $("#data_btn").val('重新获取');});
}

//添加收藏
function addfav(wid) {
	$(document).ready(function(){$.ajax({type: "GET", url: sitepath + "?mod=getdata&type=addfav", data: "wid=" + wid, cache: false, success: function(data){$("body").append(data)}});});
};

//点出统计
function clickout(wid) {
	$(document).ready(function(){$.ajax({type: "GET", url: sitepath + "?mod=getdata&type=outstat", data: "wid=" + wid, cache: false, success: function(data){}});});
};

//错误报告
function report(obj, wid) {
	$(document).ready(function(){if (confirm("确认报告此错误吗？")){ $("#" + obj).html("正在提交，请稍候..."); $.ajax({type: "GET", url: sitepath + "?mod=getdata&type=error", data: "wid=" + wid, cache: false, success: function(data){$("#" + obj).html(data);}})};});
};

//验证码
function refreshimg(obj) {
	var randnum = Math.random();
	$("#" + obj).html('<img src="' + sitepath + 'source/include/captcha.php?s=' + randnum + '" align="absmiddle" alt="看不清楚?换一张" onclick="this.src+='+ randnum +'" style="cursor: pointer;">');
}