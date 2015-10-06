var courseList;
var open_id;
var id;

function initCourseList(){
	open_id = document.getElementById('openID').innerHTML;
	$.post("http://localhost:3306/mike_server_v01/index.php/Wechat/Index/getCourseList", {action:'get_courseList', openid:open_id}, function(data){
		var json = eval('(' + data + ')'); 
		if (json.status == 0) {
			alert('获得课程列表失败');
		}else{
			courseList = json.courses;
			console.log(courseList);
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


function changeCourse(course_id){
	id = course_id;
}

function createAnnounce(){
	//alert(id + document.getElementById('announce_content').value);
	$.post("http://localhost:3306/mike_server_v01/index.php/Wechat/Index/createAnnounce", {
		'action':'send_announce',
		'openid':open_id,
		'course_id':id,
		'content':document.getElementById('announce_content').value
	}, function(data){
		console.log(data);
		var json = eval('(' + data + ')'); 
		if(json.status == 1){
			document.write('<h3 style="margin-top: 30px;text-align: center;width: 100%">已成功发布公告！</h3>');
		}else {
			document.write('<h3 style="margin-top: 30px;text-align: center;width: 100%">发布公告失败，请检查是否已经发布此公告！</h3>')
		}
	});
}