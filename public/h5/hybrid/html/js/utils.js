//序列化url，将url链接后面的get参数序列化成json对象
function parseUrl(url){
	var param=url.substring(url.indexOf("?")+1);
	var paramArr=param.split("&");
	var urlArr={};
	for (let i = 0; i < paramArr.length; i++) {
	  urlArr[paramArr[i].split("=")[0]] = decodeURI(paramArr[i].split("=")[1]);
	  // 将数组元素中'='左边的内容作为对象的属性名，'='右边的内容作为对象对应属性的属性值
	}
	return urlArr;
}