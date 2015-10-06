var numOfOptions = 1;
var duration  = 120;
var courseList;
var open_id = QueryString('openID');
var id;
var identifyID = QueryString('random');

function checkTest(){
	alert(open_id);
	$.post("http://112.124.101.41/test_server/bl/wechat_web_index2.php", {action:'is_test_on', openid:open_id, identify:identifyID}, function(data){
		var json = eval('(' + data + ')'); 
		var status = json.status;
		console.log(status);
		switch(status){
			case -1:
			//关闭
			window.location.href = "teacher_test_result.html?"+ "openID=" + QueryString('openID') + "&identify=" + identifyID;
			break;
			case 0:
			//不存这个小测	
			break;
			case 1:
			//小测进行中
			window.location.href = "isTesting.html?"+ "openID=" + QueryString('openID') + "&identify=" + identifyID;
			break;
			default:
			alert('未知错误');
		}
	});
}

function initCourseList(){
	var isEnd = checkTest();
	$.post("http://112.124.101.41/test_server/bl/wechat_web_index2.php", {action:'get_courseList', openid:open_id}, function(data){
		var json = eval('(' + data + ')'); 
		if (json.status == 0) {
			alert('获得课程列表失败');
		}else{
			courseList = json.courses;
			var courses = "";
			for (var i = 0; i < json.course_count; i++) {
				var time_place = courseList[i].time_place;
				var temp = time_place.split("||");
				var time_places = " ";
				for (var j = 0; j < temp.length; j++) {
					time_places += temp[j].substring(0,temp[j].indexOf('节')+1);
				};
				courses += "<option value='" + courseList[i].id + "'>" + courseList[i].name + time_places + "</option>";
				if (i == 0) {
					id = courseList[i].id;
				};
			};
			document.getElementById('course-list').innerHTML = courses;
		};
	});
}

function checkType(){
	if (document.getElementById('radio_1').checked) {
		document.getElementById('type1').style.display = "block";
	}else {
		document.getElementById('type1').style.display = "none";
	};
	if (document.getElementById('radio_2').checked) {
		document.getElementById('type2').style.display = "block";
	}else {
		document.getElementById('type2').style.display = "none";
	};
	if (document.getElementById('radio_3').checked) {
		document.getElementById('type3').style.display = "block";
	}else {
		document.getElementById('type3').style.display = "none";
	};
}


function createTestRadio(value){
	window.location.href = "radio_test.html?num=" + numOfOptions + "&title=" + document.getElementById('title').value + "&type=radio" + "&open_id=" + QueryString('openID') + "&duration=" + duration + "&course_id=" + id + "&random=" + identifyID;
	// post('http://elearning2.sinaapp.com/index.php',{action:'creat_test'});
}

function createTestMultiple(){
	window.location.href = "radio_test.html?num=" + numOfOptions + "&title=" + document.getElementById('title').value + "&type=multiple" + "&open_id=" + QueryString('openID') + "&duration=" + duration + "&course_id=" + id + "&random=" + identifyID;
}

function createTestOther(){
	$.post("http://112.124.101.41/test_server/bl/wechat_web_index2.php", {
		action:'create_test',
		test_type:'other',
		course_id:id,
		identify:identifyID,
		openid:QueryString('openID'),
		test_title:document.getElementById('title').value,
		option_count:0,
		test_duration:duration,
		test_content:document.getElementById('other_type').value}, function(data){
			var json = eval('(' + data + ')'); 
			if(json.status == 1){
				document.write('<h3 style="margin-top: 30px;text-align: center;width: 100%">已成功创建小测</h3>');
			}
		});
}

function changeAmount(num){
	numOfOptions = num;
}

function changeDuration(value){
	duration = value;
}

function changeCourse(course_id){
	id = course_id;
}