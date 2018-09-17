function adsforwpGetParamByTabName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}


jQuery( document ).ready(function($) {

$(".adsforwp-analytics-tabs a").click(function(e){
		var href = $(this).attr("href");
		var currentTab = adsforwpGetParamByTabName("tab",href);
		if(!currentTab){
			currentTab = "all";
		}                   
                               
		$(this).siblings().removeClass("nav-tab-active");
		$(this).addClass("nav-tab-active");
		$(".form-wrap").find(".adsforwp-"+currentTab).siblings().hide();
		$(".form-wrap .adsforwp-"+currentTab).show();
		window.history.pushState("", "", href);
		return false;
	});
        
});