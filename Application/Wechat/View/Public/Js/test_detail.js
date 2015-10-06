var quizID;
var answer;
var json_result;
var type = '';
var optionLength;
var openID;
var title;

function isSubmit(){
	quizID = QueryString('quiz_id');
	openID = QueryString('openID');
    $.post("http://112.124.101.41/test_server/bl/wechat_web_index2.php", {action:'is_submitted', openid:openID, quiz_id:quizID}, function(data){
		var json = eval('(' + data + ')'); 
		var isSubmitted = json.status;
		if (isSubmitted == 1) {
			document.write("<h3 style='margin-top: 30px;text-align: center;width: 100%'>已提交小测,请勿重复提交！</h3>");
			return;
		}else if(isSubmitted == 0){
            window.location.href="end_page.html";
//			alert('小测已结束！');
			return;
		}else{
            getTestDetail();
            //window.location.href="submit_test.html";
            return;
        };

	});
}

function getTestDetail(){
	$.post("http://112.124.101.41/test_server/bl/wechat_web_index2.php", {action:'get_test', openid:openID, quiz_id:quizID}, function(data){
		json_result = eval('(' + data + ')');
		console.log(json_result);
		if (json_result.status == 0) {
			alert('获取小测失败，请稍后尝试！');
		};

		var testDetail = document.getElementById('testDetail');
		var detailStr = "";
		optionLength = json_result.option_count;
		type = json_result.test_type;
		title = json_result.test_title;
		switch(json_result.test_type){
			case 'multiple':
			var option = eval('(' + json_result.test_content + ')');
			for (var i = 0; i < optionLength; i++) {
				var temp= String.fromCharCode(97+i);
				detailStr += "<p><input name='Check' type='checkbox' value='" + String.fromCharCode(97+i) + "' id='" + i + "' />" + String.fromCharCode(65+i) + ":" + option[String.fromCharCode(97+i)] + "</p>"; 	
			};
			break;

			case 'radio':
			var option = eval('(' + json_result.test_content + ')');
			for (var i = 0; i < optionLength; i++) {
				var temp= String.fromCharCode(97+i);
				detailStr += "<p><input name='Radio' type='radio' value='" + String.fromCharCode(97+i) + "' id='" + i + "' />" + String.fromCharCode(65+i) + ":" + option[String.fromCharCode(97+i)] + "</p>";
			};
			break;

			case 'other':
			detailStr = "<p><input name='textarea' type='textarea' placeholder='回答' id='other' /></p>";
			break;

			default:
			detailStr="小测类型错误";
		}
		testDetail.innerHTML = detailStr;
		document.getElementById('title').innerHTML = title;
	});

}

function submitAnswer(){
	var isNull = true;
	answer = new Array();
	switch(type){
		case 'radio':
		for (var i = 0; i < optionLength; i++) {
			if(document.getElementById(i).checked){
				isNull = false;
				$.post("http://112.124.101.41/test_server/bl/wechat_web_index2.php",
					{'action':'submit_test', 
					'openid':openID, 
					'test_id':quizID, 
					'submit_content':document.getElementById(i).value
				}, function(data){
					var json = eval('(' + data + ')'); 
					if(json.status == 1){
						document.write('<h3 style="margin-top: 30px;text-align: center;width: 100%">已成功提交结果！</h3>');
					}
				});
			};
		}
		if (isNull) {
			alert('请选择至少一个答案');
		};
		break;
		case 'multiple':
		var content = "";
		for (var i = 0; i < optionLength; i++) {
			if(document.getElementById(i).checked){
				isNull = false;
				content += document.getElementById(i).value + ',';
			};
		}
		if (isNull) {
			alert('请选择至少一个答案');
		}else {
			content = content.substring(0, content.length - 1);
			$.post("http://112.124.101.41/test_server/bl/wechat_web_index2.php", 
				{'action':'submit_test', 
				'openid':openID, 
				'test_id':quizID, 
				'submit_content':content
			}, function(data){
				var json = eval('(' + data + ')'); 
				if(json.status == 1){
					document.write('<h3 style="margin-top: 30px;text-align: center;width: 100%">已成功提交结果！</h3>');
				}
			});
		};
		break;
		case 'other':
		$.post("http://112.124.101.41/test_server/bl/wechat_web_index2.php", 
			{'action':'submit_test', 
			'openid':openID, 
			'test_id':quizID, 
			'submit_content':document.getElementById('other').value
		}, function(data){
			var json = eval('(' + data + ')'); 
			if(json.status == 1){
				document.write('<h3 style="margin-top: 30px;text-align: center;width: 100%">已成功提交结果！</h3>');
			}
		});
		break;
		default:
		alert('小测类型错误');
	}
	//多选

}