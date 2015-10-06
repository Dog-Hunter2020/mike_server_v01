var type = '';
var title = '默认标题';
var open_id;
var identify;

function initTestResult(){
	open_id = QueryString('openID');
	identify = QueryString('identify');
	$.post("http://localhost:3306/mike_server_v01/index.php/Wechat/Index/getTestResult", {action:'get_test_result', openid:open_id, identify:identify}, function(data){
		var json = eval('(' + data + ')'); 
		console.log(json);
		if (json.status == 0) {
			alert('获得小测结果失败，请稍后尝试');
		}else{
			type = json.test_type;
			title = json.test_title;
			document.getElementById('title').innerHTML = title;
			document.getElementById('submitCount').innerHTML = json.result.length;
			document.getElementById('unSubmitCount').innerHTML = json.test_nonSubmit.length;

			var result = json.result;
			if(json.test_type == 'other'){
				var submit = "";
				for (var i = 0; i < result.length; i++) {
					submit += result[i] + ' ';
				};
				document.getElementById('submitMore_content').innerHTML = submit;
			}else {
				var submit = "";
				var submitLength = 0;
				for (var i = 0; i < result.length; i++) {
					var users = getUsers(result[i].users);
					submitLength += result[i].users.length;
					submit += users + ' ';
				}
				document.getElementById('submitCount').innerHTML = submitLength;
				document.getElementById('submitMore_content').innerHTML = submit;
			}

			var test_nonSubmit = json.test_nonSubmit;
			var nonSubmit = "";
			for (var i = 0; i < test_nonSubmit.length; i++) {
				nonSubmit += test_nonSubmit[i] + ' ';
			};
			document.getElementById('unSubmitMore_content').innerHTML = nonSubmit;

			if(json.test_type != 'other'){
				for (var i = 0; i < result.length; i++) {
					var users = getUsers(result[i].users);
					document.getElementById('resultTable').innerHTML += 
					"<tr>" + 
					"<th>" + 
					result[i].choice + 
					"</th>" + 
					"<td >" + 
					result[i].count + 
					"<label id='more_" + i + "' onclick='show(" + i + "," + i + ")' >+展开</label></td>" + 
					"</tr>" +
					"<tr id='submitMore_" + i + "' style='display:none;'>" + 
					"<td colspan='2'>" + 
					users +
					"</td>" +
					"</tr>";
				}//end for
			}//end if
		}//end else
	});
}

function getUsers(users){
	var resultString = "";
	for (var i = 0; i < users.length; i++) {
		resultString += users[i] + ',';
	};
	resultString = resultString.substring(0, resultString.length - 1);
	return resultString;
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
