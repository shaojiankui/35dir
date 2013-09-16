//全选
function CheckAll(form){
	for (var i = 0; i < form.elements.length; i++) {
    	var e = form.elements[i];
        if (e.Name != "ChkAll" && e.disabled == false)
			e.checked = form.ChkAll.checked;
	}
}

//判断是否选择
function IsCheck(ObjName){
	var Obj = document.getElementsByName(ObjName); //获取复选框数组
    var ObjLen = Obj.length; //获取数组长度
    var Flag = false; //是否有选择
    for (var i = 0; i < ObjLen; i++) {
		if (Obj[i].checked == true) {
			Flag = true;
			break;
		}
	}
	return Flag;
}

//栏目合并判断
function ConfirmUnite() {
	if ($("#CurrentClassID").attr("value") == $("#TargetClassID").attr("value")) {
		alert("请不要在相同栏目内进行操作！");
		$("#TargetClassID").focus();
		return false;
	}
return true;
}

//获取META
function GetMeta() {
	var url = $("#web_url").attr("value");
	if (url == '') {
		alert('请输入网站域名！');
		$("#web_url").focus();
		return false;
	}
	$(document).ready(function(){$("#meta_btn").val('正在获取，请稍候...'); $.ajax({type: "GET", url: 'website.php?act=metainfo', data: 'url=' + url, datatype: "script", cache: false, success: function(data){$("body").append(data); $("#meta_btn").val('重新获取');}});});		
}

//获取ip, PageRank, Sogou PageRank, Alexa
function GetData() {
	var url = $("#web_url").attr("value");
	if (url == '') {
		alert('请输入网站域名！');
		$("#web_url").focus();
		return false;
	}
	$(document).ready(function(){$("#data_btn").val('正在获取，请稍候...'); $.ajax({type: "GET", url: 'website.php?act=webdata', data: 'url=' + url, datatype: "script", cache: false, success: function(data){$("body").append(data); $("#data_btn").val('重新获取');}});});		
}