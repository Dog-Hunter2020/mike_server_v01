var open_id;
var identify;

function initRollCallResult(){
	open_id = QueryString('openID');
	identify = QueryString('identify');
	$.post("http://localhost:3306/mike_server_v01/index.php/Wechat/Index/getRollCallResult", {action:'get_test_result', openid:open_id, identify:identify}, function(data){
		var json = eval('(' + data + ')'); 
		console.log(json);
		if (json.status == 0) {
			alert('获得点名结果失败，请稍后尝试');
		}else{
			type = json.test_type;
			// title = json.test_title;
			// document.getElementById('title').innerHTML = title;
			document.getElementById('submitCount').innerHTML = json.result.length;
			document.getElementById('unSubmitCount').innerHTML = json.test_nonSubmit.length;

			var result = json.result;
			var submit = "";
			for (var i = 0; i < result.length; i++) {
				submit += result[i] + ' ';
			};
			document.getElementById('submitMore_content').innerHTML = submit;

			var test_nonSubmit = json.test_nonSubmit;
			var nonSubmit = "";
			for (var i = 0; i < test_nonSubmit.length; i++) {
				nonSubmit += test_nonSubmit[i] + ' ';
			};
			document.getElementById('unSubmitMore_content').innerHTML = nonSubmit;
		}//end else
	});
}

function show(buttonID, contentID){
	if (document.getElementById("more_" + buttonID).innerHTML == '+展开') {
		document.getElementById("submitMore_" + contentID).style.display = '';
		document.getElementById("more_" + buttonID).innerHTML = "-收起";
		// document.getElementById('more').onclick= "";
	}else {
		document.getElementById("submitMore_" + contentID).style.display = 'none';
		document.getElementById("more_" + buttonID).innerHTML = "+展开";
		// document.getElementById('more').onclick= "";
	};
}