var courseList;
var open_id = "";
var id;
var duration = 120;
var identifyID;
var myLocation = "";

function initCourseList(){
	open_id = document.getElementById('openID').innerHTML;
	identifyID = document.getElementById('quizID').innerHTML;
	getLocation();

	$.post("http://112.124.101.41/mike_server_v01/index.php/Wechat/Index/getCourseList", {action:'get_courseList', openid:open_id}, function(data){
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
                    time_places +=" ";
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

function changeDuration(value){
	duration = value;
}

function changeCourse(course_id){

}

function getSelectCourseID(){
	var select = document.getElementById('course-list');
	return select.value;
}

function beginRollCall(){
	var courseId = getSelectCourseID();
	$.post("http://112.124.101.41/mike_server_v01/index.php/Wechat/Index/createRollCall",{action:'begin_count', 'course_id':courseId, 'openid':open_id, 'duration':duration, 'identify':identifyID, 'location':myLocation}, function(data){
		var json = eval('(' + data + ')');
		if (json.status == 1) {
			document.write("<h3 style='margin-top: 30px;text-align: center;width: 100%'>已成功开启点名</h3>");
		};
	});

}

function initEndCount(){
	open_id = document.getElementById('openID').innerHTML;
	identifyID = document.getElementById('quizID').innerHTML;
}

function endCount(){
	$.post("http://112.124.101.41/mike_server_v01/index.php/Wechat/Index/endRollCall", {
		action:"end_count", 
		openid:open_id, 
		identify:identifyID
	}, function(data){
		var json = eval('(' + data + ')'); 
		if(json.status == 1){
			window.location.href = "countForTeacher?"+ "openID=" + open_id + "&random=" + identifyID;
		}
	});
}

function getLocation() { 
	if (navigator.geolocation) { 
		var config = { enableHighAccuracy: true, timeout: 5000, maximumAge: 30000 }; 
		navigator.geolocation.getCurrentPosition(showPosition, showError, config); 
	} else { 
		alert("定位失败,用户已禁用位置获取权限"); 
	} 
} 
/** 
* 获取地址位置成功 
*/ 
function showPosition(position) { 
	//获得经度纬度 
	var x = position.coords.latitude; 
	var y = position.coords.longitude;
	myLocation = x + ',' + y;
	document.getElementById('gotLocation').innerHTML = "已成功获取位置";
} 
/** 
* 获取地址位置失败[暂不处理] 
*/ 
function showError(error) { 
	switch (error.code) { 
		case error.PERMISSION_DENIED: 
		alert("定位失败,用户拒绝请求地理定位"); 
		break; 
		case error.POSITION_UNAVAILABLE: 
		alert("定位失败,位置信息是不可用"); 
		break; 
		case error.TIMEOUT: 
		alert("定位失败,请求获取用户位置超时"); 
		break; 
		case error.UNKNOWN_ERROR: 
		alert("定位失败,定位系统失效"); 
		break; 
	} 
} 
