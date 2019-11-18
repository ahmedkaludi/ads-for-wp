function myadsdata(){
var size = {
  width: window.innerWidth || document.body.clientWidth,
  height: window.innerHeight || document.body.clientHeight
}
var resultAndSet =  new Array();
var resultOrSet = new Array();
var condWidth = '';
AMP.getState("adsforwp_browser_obj").then(serializedItems => {
	var adsforwp_browser_obj = JSON.parse(serializedItems);
	for (var key in adsforwp_browser_obj) {
		if(adsforwp_browser_obj[key].hasOwnProperty('and')){
			for(var conk in adsforwp_browser_obj[key].and){
				for(var akey in adsforwp_browser_obj[key].and[conk]){
					if(adsforwp_browser_obj[key].and[conk].key_3 == 'browser_width_custom'){
						condWidth = adsforwp_browser_obj[key].and[conk].key_5;
					}else{
						condWidth = adsforwp_browser_obj[key].and[conk].key_3;
					}
					switch(adsforwp_browser_obj[key].and[conk].key_2){
						case 'equal':
							if( size.width == condWidth){
								resultAndSet[conk] = true;
							}else{
								resultAndSet[conk] = false;
							}
						break;
						case 'equal_or_greater':
							if( size.width >= condWidth){
								resultAndSet[conk] = true;
							}else{
								resultAndSet[conk] = false;
							}
						break;
						case 'equal_or_lesser':
							if( size.width <= condWidth){
								resultAndSet[conk] = true;
							}else{
								resultAndSet[conk] = false;
							}
						break;
						default:
						break;
					}
				}
			}		
		}
		if(adsforwp_browser_obj[key].hasOwnProperty('or')){
			for(var conk in adsforwp_browser_obj[key].or){
				for(var akey in adsforwp_browser_obj[key].or[conk]){
					if(adsforwp_browser_obj[key].or[conk].key_3 == 'browser_width_custom'){
						condWidth = adsforwp_browser_obj[key].or[conk].key_5;
					}else{
						condWidth = adsforwp_browser_obj[key].or[conk].key_3;
					}

					switch(adsforwp_browser_obj[key].or[conk].key_2){
						case 'equal':
							if( size.width == condWidth){
								resultOrSet[conk] = true;
							}else{
								resultOrSet[conk] = false;
							}
						break;
						case 'equal_or_greater':
							if( size.width >= condWidth){
								resultOrSet[conk] = true;
							}else{
								resultOrSet[conk] = false;
							}
						break;
						case 'equal_or_lesser':
							if( size.width <= condWidth){
								resultOrSet[conk] = true;
							}else{
								resultOrSet[conk] = false;
							}
						break;
						default:
						break;
					}
				}
			}
		}
		if(resultAndSet.length){
			var resultAnd = resultAndSet.every(checkAndResult);
		}
		if(resultOrSet.length){
			var resultOr = checkOrResult(resultOrSet.indexOf(true));
		}
		
		if(resultAndSet.length>0 && resultOrSet.length>0){
			if(resultAnd || resultOr){
				document.getElementsByClassName('afw_brw-'+ key)[0].style.display = 'block';
			}else{
				document.getElementsByClassName('afw_brw-'+ key)[0].style.display = 'none';
			}
		}else if( resultAndSet.length == 0 && resultOrSet.length>0){
			if(resultOr){
				document.getElementsByClassName('afw_brw-'+ key)[0].style.display = 'block';
			}else{
				document.getElementsByClassName('afw_brw-'+ key)[0].style.display = 'none';
			}
		}else if( resultAndSet.length > 0 && resultOrSet.length == 0 ){
			if(resultAnd){
				document.getElementsByClassName('afw_brw-'+ key)[0].style.display = 'block';
			}else{
				document.getElementsByClassName('afw_brw-'+ key)[0].style.display = 'none';
			}
		}
	}
	
});
}
document.getElementsByClassName('sp-cnt')[0].addEventListener("click", function(){
	myadsdata();
});

function checkOrResult(orArg){
	if(orArg == -1){
		return false;
	}else{
		return true;
	}
}
function checkAndResult(andArg) {
  if(andArg === true){
  	return true;
  }
}
	
