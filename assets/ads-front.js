jQuery( document ).ready(function($) {    

setInterval(function(){
    $(".afw-groups-ads-div").hide();
    var divs = $("div.afw-groups-ads-div").get().sort(function(){
     return Math.round(Math.random());
   }).slice(0,1);
    $(divs).show();
}, 2000);



});

 
