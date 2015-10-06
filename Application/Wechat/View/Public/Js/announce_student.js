var open_id = QueryString('openID');

function initAnnounceList(){
	$.post("http://112.124.101.41/test_server/bl/wechat_web_index2.php", {action:'get_announces',openid:open_id}, function(data){
		// console.log(data);
		var json = eval('(' + data + ')'); 
		if(json.status == 0){
			alert('获取公告失败！');
			return;
		}

		// console.log(json);
		var announces = new Array();
		for (var i in json.announces) {
			var table = createList(json.announces[i]);
			announces.push(table);
			// document.getElementById('announce_content').innerHTML += table;
		};

		for (var i = announces.length - 1; i >= 0; i--) {
			document.getElementById('announce_content').innerHTML += announces[i];
		};
	});
}

function createList(json){
	var listStr = "";
	listStr += 
	"<table>" + 
	"<tr><td class='klytd'>课程：</td><td class ='hvttd'>" + json.name + "</td></tr>" + 
	"<tr><td class='klytd'>公告时间：</td><td class='hvttd'>" + json.posttime + "</td></tr>" +   
	"<tr><td class='klytd'>发布人：</td><td class ='hvttd'>" + json.user_name + "</td></tr>" + 
	"<tr><td class='klytd'>内容：</td><td class ='hvttd'>" + json.content + "</td></tr>"
	"</table>";
	return listStr;
}

function initSingleAnnounce(){
	var course_name = QueryString('course_name');
	var teacher_name = QueryString('teacher_name');
	var content = QueryString('content');
	var posttime = QueryString('posttime');
	document.getElementById('course_name').innerHTML = course_name;
	document.getElementById('teacher_name').innerHTML = teacher_name;
	document.getElementById('content').innerHTML = content;
	document.getElementById('posttime').innerHTML = posttime;
}