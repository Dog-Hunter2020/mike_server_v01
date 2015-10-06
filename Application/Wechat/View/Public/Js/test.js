var number = 0;
var title = "";
var type = "";
var open_id = "";
var duration;
var id;
var identifyID;

function init(){
	number = QueryString('num');
	title = QueryString('title');
	type = QueryString('type');
	open_id = QueryString('open_id');
	duration = QueryString('duration');
	id = QueryString('course_id');
	identifyID = QueryString('random');
	document.getElementById('title').innerHTML = title;
	
	var form_content = "";
	for(i = 0; i < number; i++){
		form_content +=
		"<div class='option'>" +
		"<label for='A'>" + String.fromCharCode(65 +i) + ":</label>" +
		"<input type='text' id='" + String.fromCharCode(65 +i) +"'>" +
		"</div>";
	}
	
	document.getElementById('form').innerHTML = form_content;
}

function submit(){
	var cao = {};
	for(i = 0; i < number; i++){
		cao[String.fromCharCode(97 +i)] = document.getElementById(String.fromCharCode(65 +i)).value;
	}
	// alert(type+open_id+identifyID+title+number+cao+duration);
	cao = JSON.stringify(cao);
	$.post("http://localhost:3306/mike_server_v01/index.php/Wechat/Index/multipleTest", {
		action:"create_test", 
		test_type:type, 
		openid:open_id, 
		course_id:id,
		identify:identifyID,
		test_title:title, 
		option_count:number, 
		test_content:cao, 
		test_duration:duration}, function(data){
            //console.log(data);
			var json = eval('(' + data + ')');
			// alert(json.status);
			if(json.status == 1){
				document.write('<h3 style="margin-top: 30px;text-align: center;width: 100%">已成功创建小测</h3>');
			}
		});
}

function initEndTest(){
	open_id = QueryString('openID');
	identifyID = QueryString('identify');
}

function endTest(){
	$.post("http://112.124.101.41/test_server/bl/wechat_web_index2.php", {
		action:"end_test", 
		openid:open_id, 
		identify:identifyID
	}, function(data){
		var json = eval('(' + data + ')');
		if (json.status == 1) {
			window.location.href = "teacher_test_result.html?"+ "openID=" + QueryString('openID') + "&identify=" + identifyID;
		};
	});
}