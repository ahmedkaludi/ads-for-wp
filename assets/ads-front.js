jQuery( document ).ready(function($) {    

//setInterval(function(){
//    $(".afw-groups-ads-div").hide();
//    var divs = $("div.afw-groups-ads-div").get().sort(function(){
//     return Math.round(Math.random());
//   }).slice(0,1);
//    $(divs).show();
//}, 2000);

setInterval(function(){
       var ad_id = 123; 
             $.ajax({
                    url:adsforwp_obj.ajax_url,
                    dataType: "json",
                    data:{ad_id:ad_id, action:"adsforwp_get_groups_ad"},
                    success:function(response){
                    console.log(response);
                    }                
                });    
    
},2000);



});

 
