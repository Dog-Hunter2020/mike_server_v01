var open_id;

function initAnnounceList(){
	open_id = document.getElementById('openID').innerHTML;
	$.post("http://112.124.101.41/mike_server_v01/index.php/Wechat/Index/getAnnounces", {action:'get_announces',openid:open_id}, function(data){
		// console.log(data);
		var json = eval('(' + data + ')'); 
		if(json.status == 0){
			//alert('获取公告失败！');
            document.write("<h3 style='text-align: left;margin-top: 20px'>获取失败</h3><br>" +
                            "<div style='margin-top: 5px;'>您可以确认一下您是否已绑定教务网账号，并检查一下当前网络是否畅通</div><br>" +
                            "<div style='margin-top: 5px;'>请您稍后再试</div><br>" +
                            "<div style='margin-top: 5px;'>对于给您带来的不便我们深表歉意</div>");
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
	"<tr><td class ='content_panel' colspan=2>" + json.content + "</td></tr>"
	"</table>";
	return listStr;
}
