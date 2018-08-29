jQuery( document ).ready(function($) {    
    
    /**
     * We are here fetching ads by their id from database via ajax call
     * @param {type} ads_group_id
     * @param {type} ads_group_type
     * @param {type} ad_id
     * @returns {html tags}
     */
    function adsforwpShowAdsById(ads_group_id, ads_group_type, adbyindex){ 
         
            var container = $(".afw_ad_container[data-id='"+ads_group_id+"']");                
            var content ='';
            switch(adbyindex.ad_type){
                case "adsense":
                    var bannersize =(adbyindex.ad_banner_size).split("x");
                    var width = bannersize[0];
                    var height = bannersize[1];
                    if(adbyindex.ad_adsense_type == "normal"){                        
                    content +='<div class="afw afw-ga afw_'+adbyindex.ad_id+'">';
                    content +='<script async="" src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
                    content +='<ins class="adsbygoogle" style="display:inline-block;width:'+width+'px;height:'+height+'px" data-ad-client="'+adbyindex.ad_data_client_id+'" data-ad-slot="'+adbyindex.ad_data_ad_slot+'"></ins>';
                    content +='<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
                    content +='</div>';                    
                    container.html(content);                       
                    }
                    break;
                case "media_net":
                    var bannersize =(adbyindex.ad_banner_size).split("x");
                    var width = bannersize[0];
                    var height = bannersize[1];
                    
                    content +='<div class="afw afw-md afw_'+adbyindex.ad_id+'">';
                    content +='<script id="mNCC" language="javascript">';
                    content +='medianet_width = '+width+'";"';
                    content +='medianet_height = '+height+'";"';
                    content +='medianet_crid ='+adbyindex.ad_data_crid;
                    content +='medianet_versionId ="3111299"';
                    content +='</script>';
                    content +='<script src="//contextual.media.net/nmedianet.js?cid='+adbyindex.ad_data_cid+'"></script>';
                    content +='</div>';
                    container.html(content);
                    
                    break;
                case "custom":
                    content +='<div class="afw afw_custom afw_'+adbyindex.ad_id+'">';
                    content +=adbyindex.ad_custom_code;
                    content +='</div>';
                    container.html(content);
                    break;
                case "ad_image":
                    content +='<div class="afw afw_ad_image afw_'+adbyindex.ad_id+'">';
                    content +='<a target="_blank" href="'+adbyindex.ad_redirect_url+'"><img src="'+adbyindex.ad_image+'"></a>';
                    content +='</div>';
                    container.html(content);
                    break;
            }
                        
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
         
            var ad_ids = obj.ads; 
               
            var ad_ids_length = Object.keys(ad_ids).length;            
            var i=0;
            
            if(ads_group_refresh_type ==='on_interval'){                
                            
            var adsforwp_ad_on_interval = function () {
                
            if(i >= ad_ids_length){
                 i = 0;
             }    
                var adbyindex ='';
                    adbyindex = ad_ids[i];                    
                if(ads_group_type == 'ordered')   {              
                 adsforwpShowAdsById(ads_group_id, ads_group_type, adbyindex);                                             
                i++;    
                } else{                    
                var random_adbyindex = ad_ids[Math.floor(Math.random()*ad_ids.length)];                 
                adsforwpShowAdsById(ads_group_id, ads_group_type, random_adbyindex);                 
                
                }                
                 setTimeout(adsforwp_ad_on_interval, ads_group_ref_interval_sec);
                };
             adsforwp_ad_on_interval();              
            }           
    });  
    
    
});