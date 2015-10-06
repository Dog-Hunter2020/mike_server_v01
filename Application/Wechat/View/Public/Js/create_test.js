var numOfOptions = 1;
var duration  = 120;
var courseList;
var open_id;
var id;
var identifyID;

function initCourseList(){
    open_id = document.getElementById('openID').innerHTML;
    identifyID = document.getElementById('random').innerHTML;
    console.log(open_id);
	$.post("http://localhost:3306/mike_server_v01/index.php/Wechat/Index/getCourseList", {action:'get_courseList', openid:open_id}, function(data){
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
	window.location.href = "radioTest?num=" + numOfOptions + "&title=" + document.getElementById('title').value + "&type=radio" + "&open_id=" + QueryString('openID') + "&duration=" + duration + "&course_id=" + id + "&random=" + identifyID;
	// post('http://elearning2.sinaapp.com/index.php',{action:'creat_test'});
}

function createTestMultiple(){
	window.location.href = "multipleTest?num=" + numOfOptions + "&title=" + document.getElementById('title').value + "&type=multiple" + "&open_id=" + QueryString('openID') + "&duration=" + duration + "&course_id=" + id + "&random=" + identifyID;
}

function createTestOther(){
	$.post("http://localhost:3306/mike_server_v01/index.php/Wechat/Index/createOtherTest", {
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