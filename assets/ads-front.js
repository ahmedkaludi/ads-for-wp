jQuery( document ).ready(function($) {    
    
    /**
     * We are here fetching ads by their id from database via ajax call
     * @param {type} ads_group_id
     * @param {type} ads_group_type
     * @param {type} ad_id
     * @returns {html tags}
     */
    function adsforwpGetAdsById(ads_group_id, ads_group_type, ad_id){            
            $.ajax({
                    url:adsforwp_obj.ajax_url,
                    dataType: "json",
                    data:{ads_group_id:ads_group_id, ads_group_type:ads_group_type,ad_id:ad_id, action:"adsforwp_get_groups_ad"},
                    success:function(response){
                     if(response['status'] == 't' ){
                      var result =  response['ad_code'];
                       $(".afw-groups-ads-json[afw-group-id="+ads_group_id+"]").html(result);
                     }                       
                    }                
                });                 
            }
    /**
     * we are here iterating on each group div to display all ads 
     * randomly or ordered on interval or on reload
     */        
    $(".afw-groups-ads-json").each(function(){
            var ad_data_json = $(this).attr('data-json');
            var obj = JSON.parse(ad_data_json);            
                        
            var ads_group_id = obj.afw_group_id;
            var ads_group_refresh_type = obj.adsforwp_refresh_type; 
            var ads_group_ref_interval_sec = obj.adsforwp_group_ref_interval_sec;
            var ads_group_type = obj.adsforwp_group_type;
            var ad_ids = obj.ad_ids;                
            var ad_ids_length = Object.keys(ad_ids).length;            
            var i=0;
            if(ads_group_refresh_type ==='on_interval'){                
                
             var startTime = new Date().getTime();
             var interval =  setInterval(function(){
                
             if(new Date().getTime() - startTime > 300000){
               clearInterval(interval);
               return;
            }                 
             if(i >= ad_ids_length){
                 i = 0;
             }    
             var adbyindex ='';
                 adbyindex = ad_ids[i].ad_id;
             if(ads_group_type === 'ordered')   {                  
             adsforwpGetAdsById(ads_group_id, ads_group_type, adbyindex);             
             i++;    
             } else{
             adsforwpGetAdsById(ads_group_id, ads_group_type, adbyindex);                 
             }             
            }, ads_group_ref_interval_sec);   
            
            }           
    });            
});

 
