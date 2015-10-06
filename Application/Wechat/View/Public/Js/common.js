function post(URL, PARAMS) {      
	var temp = document.createElement("form");      
	temp.action = URL;      
	temp.method = "post";      
	temp.style.display = "none";      
	for (var x in PARAMS) {      
		var opt = document.createElement("textarea");      
		opt.name = x;      
		opt.value = PARAMS[x];      
        // alert(opt.name)      
        temp.appendChild(opt);      
    }      
    document.body.appendChild(temp);      
    temp.submit();      
    return temp;      
}      

//调用方法 如      
// post('pages/statisticsJsp/excel.action', {html :prnhtml,cm1:'sdsddsd',cm2:'haha'});


function QueryString(val){
	var uri = window.location.search;
	uri = decodeURI(uri);
	var re = new RegExp("" +val+ "=([^&?]*)", "ig");
	result = ((uri.match(re))?(uri.match(re)[0].substr(val.length+1)):null);
	return result;
}