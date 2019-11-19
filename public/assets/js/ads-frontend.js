(function($){
var size = {
  width: window.innerWidth || document.body.clientWidth,
  height: window.innerHeight || document.body.clientHeight
}

$.each( adsforwp_browser_obj, function( key, value ) {
	var resultAndSet =  new Array();
	var resultOrSet = new Array();
		$.each( adsforwp_browser_obj[key] ,function(conK,conV){
			var condWidth = '';
			if(conK == 'and'){
				$.each( conV ,function(akey,aval){
					if( conV[akey]['key_3'] == 'browser_width_custom'){
						condWidth = conV[akey]['key_5'];
					}else{
						condWidth = conV[akey]['key_3']
					}
					switch(conV[akey]['key_2']){
						case 'equal':
							if( size.width == condWidth){
								resultAndSet[akey] = true;
							}else{
								resultAndSet[akey] = false;
							}
						break;
						case 'equal_or_greater':
							if( size.width >= condWidth){
								resultAndSet[akey] = true;
							}else{
								resultAndSet[akey] = false;
							}
						break;
						case 'equal_or_lesser':
							if( size.width <= condWidth){
								resultAndSet[akey] = true;
							}else{
								resultAndSet[akey] = false;
							}
						break;
						default:
						break;
					}
				});
				
			}else{
				$.each( conV ,function(akey,aval){
					if( conV[akey]['key_3'] == 'browser_width_custom'){
						condWidth = conV[akey]['key_5'];
					}else{
						condWidth = conV[akey]['key_3']
					}
					switch(conV[akey]['key_2']){
						case 'equal':
							if( size.width == condWidth){
								resultOrSet[akey] = true;
							}else{
								resultOrSet[akey] = false;
							}
						break;
						case 'equal_or_greater':
							if( size.width >= condWidth){
								resultOrSet[akey] = true;
							}else{
								resultOrSet[akey] = false;
							}
						break;
						case 'equal_or_lesser':
							if( size.width <= condWidth){
								resultOrSet[akey] = true;
							}else{
								resultOrSet[akey] = false;
							}
						break;
						default:
						break;
					}
				});
			}
		});
		if(resultAndSet.length){
			var resultAnd = resultAndSet.every(checkAndResult);
		}
		if(resultOrSet.length){
			var resultOr = checkOrResult(resultOrSet.indexOf(true));	
		}
		if(resultAndSet.length>0 && resultOrSet.length>0){
			if(resultAnd || resultOr){
				$('.afw_brw-'+ key).show();
			}else{
				$('.afw_brw-'+ key).hide();
			}
		}else if( resultAndSet.length == 0 && resultOrSet.length>0){
			if(resultOr){
				$('.afw_brw-'+ key).show();
			}else{
				$('.afw_brw-'+ key).hide();
			}
		}else if( resultAndSet.length > 0 && resultOrSet.length == 0 ){
			if(resultAnd){
				$('.afw_brw-'+ key).show();
			}else{
				$('.afw_brw-'+ key).hide();
			}
		}
	});
})(jQuery);

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