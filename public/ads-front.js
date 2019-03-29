//Creating bait in admin
var e = document.createElement('div');
e.id = 'adsforwp-hidden-block';
e.style.display = 'none';
document.body.appendChild(e);

jQuery( document ).ready(function($) {        
    /**
     * We are here fetching ads by their id from database via ajax call
     * @param {type} ads_group_id
     * @param {type} ads_group_type
     * @param {type} ad_id
     * @returns {html tags}
     */
    function adsforwpShowAdsById(ads_group_id, ads_group_type, adbyindex, j){                   
            var container = $(".afw_ad_container[data-id='"+ads_group_id+"']");  
            var container_pre = $(".afw_ad_container_pre[data-id='"+ads_group_id+"']");  
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
                    }
                    container.html(content);
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
                    content +='<div class="">Advertisement</div>';
                    content +='<div class="afw afw_ad_image afw_'+adbyindex.ad_id+'">';                    
                    content +='<a target="_blank" href="'+adbyindex.ad_redirect_url+'"><img src="'+adbyindex.ad_image+'"></a>';
                    content +='</div>';
                    content +='<div class="adsforwp-popup-close"></div>';
                    if(j==1){
                    container.html(content);       
                    }                    
                    if(j==2){
                    container_pre.html(content);  
                    }
                    if(j>2){                                                
                      container.html(container_pre.html()); 
                      container_pre.html(content);   
                    }
                    
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
            var j = 0;
            if(ads_group_refresh_type ==='on_interval'){  
                 j = 1;
                 
                 if(ads_group_type == 'ordered')   {              
                    adsforwpShowAdsById(ads_group_id, ads_group_type, ad_ids[i], j);                                             
                i++;    
                } else{                    
                    var random_adbyindex = ad_ids[Math.floor(Math.random()*ad_ids.length)];                 
                    adsforwpShowAdsById(ads_group_id, ads_group_type, random_adbyindex, j);                                 
                } 
              j++;  
            var adsforwp_ad_on_interval = function () {                
            if(i >= ad_ids_length){
                 i = 0;
             }    
                var adbyindex ='';
                    adbyindex = ad_ids[i];                    
                if(ads_group_type == 'ordered')   {              
                    adsforwpShowAdsById(ads_group_id, ads_group_type, adbyindex, j);                                             
                i++;    
                } else{                    
                    var random_adbyindex = ad_ids[Math.floor(Math.random()*ad_ids.length)];                 
                    adsforwpShowAdsById(ads_group_id, ads_group_type, random_adbyindex, j);                                 
                }    
                    j++;
                    setTimeout(adsforwp_ad_on_interval, ads_group_ref_interval_sec);
                };
             adsforwp_ad_on_interval();              
            }           
    });  
    
    
    //Analytics js starts here
    
    function adsforwp_detect_device(){
        var device = 'desktop';
            var isiPad = /ipad/i.test(navigator.userAgent.toLowerCase());
                if (isiPad)
                {
                 device = 'ipad';
                }
                                  
                if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
                || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) 
                {
                device = 'mobile';
                }
                return device;
        }
        
      if(adsforwp_obj.ad_performance_tracker == 1){
        
        setTimeout(function(){   
            
        var device_name = adsforwp_detect_device();    
        var ad_ids ={};    
        $(".afw_ad").each(function(index){
           ad_ids[index]= ($(this).attr('data-ad-id'));
        });  
        
        if($.isEmptyObject( ad_ids ) == false){           
        $.ajax({
                    type: "POST",    
                    url:adsforwp_obj.ajax_url,                    
                    dataType: "json",
                    data:{action:"adsforwp_insert_ad_impression", ad_ids:ad_ids, device_name:device_name, adsforwp_front_nonce:adsforwp_obj.adsforwp_front_nonce},                    
                    error: function(response){                    
                    console.log(response);
                    }
                });     
        }                   
        }, 1000);
        
        $(".afw_ad").on("click",function(){
            
         var ad_id = $(this).attr('data-ad-id');
         var device_name = adsforwp_detect_device();
         if(ad_id){
            $.post(adsforwp_obj.ajax_url, 
                  { action:"adsforwp_insert_ad_clicks", ad_id:ad_id, device_name:device_name, adsforwp_front_nonce:adsforwp_obj.adsforwp_front_nonce},
                    function(response){
                    console.log(response);       		   		
		   });  
             }         
        });
                
        //Detecting click event on iframe based ads
         window.addEventListener('blur',function(){		
	    if (document.activeElement instanceof HTMLIFrameElement) {
                var data = $(this);                   
                var el = data.context.activeElement;
                 while (el.parentElement) {
                     el = el.parentElement;     
                       if(el.attributes[0].name =='data-ad-id'){
                       var ad_id = el.attributes[0].value;
                       var device_name = adsforwp_detect_device();
                       if(ad_id){
                          $.post(adsforwp_obj.ajax_url, 
                             { action:"adsforwp_insert_ad_clicks", ad_id:ad_id, device_name:device_name},
                                function(response){
                                console.log(response);       		   		
                              });  
                          }
                       }
                   }
	       }
	  });
        
      }
                                  
    //Analytics js ends here
    
    //Popup ad starts here
    
    
        $(".adsforwp-popup-modal").each(function(){             
               var current = $(this);
               var time = $(current).attr('delay-sec');
               setTimeout(function(){ 
                 $(current).removeClass('adsforwp-hide');
                 $(current).find('.adsforwp-popup-content').removeClass('adsforwp-hide');
                },
               time+'000');
        });                             
    
    $(document).on("click",".adsforwp-popup-close", function(e){
       e.preventDefault();        
       var ad_id = $(this).parent().parent().find('.afw').attr('data-ad-id');
       var group_id = $(this).parent().parent().parent().find('.afw_group').attr('data-id');
          
       $("#adsforwp-popup-"+ad_id).addClass('adsforwp-hide');
       $("#adsforwp-popup-"+group_id).addClass('adsforwp-hide');
                         
       var afw_cookie = getCookie("adsforwp-stick-ad-id7");
       
            if(ad_id){
                
                if(afw_cookie ==""){
                    afw_cookie += ad_id;   
                }else{
                    afw_cookie += ','+ad_id;   
                }
                
             }
            
            if(group_id){
                
                if(afw_cookie ==""){
                 afw_cookie += group_id;   
                }else{
                afw_cookie += ','+group_id;   
                }
            
            }
                        
         setCookie("adsforwp-stick-ad-id7", afw_cookie, 7);                                 
    });
    
    
    //popup ad ends here
    
    //Sticky Ads script starts here
    
    $(".adsforwp-sticky-ad-close").on("click", function(e){
       e.preventDefault();
       $(this).parent().hide();
       var ad_id = $(this).parent().find('.afw').attr('data-ad-id');
       var group_id = $(this).parent().find('.afw_group').attr('data-id');
                            
       var afw_cookie = getCookie("adsforwp-stick-ad-id7");
       
            if(ad_id){
                
                if(afw_cookie ==""){
                    afw_cookie += ad_id;   
                }else{
                    afw_cookie += ','+ad_id;   
                }
                
             }
            
            if(group_id){
                
                if(afw_cookie ==""){
                 afw_cookie += group_id;   
                }else{
                afw_cookie += ','+group_id;   
                }
            
            }
                        
         setCookie("adsforwp-stick-ad-id7", afw_cookie, 7);                                 
    });
    
    function getCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i < ca.length; i++) {
              var c = ca[i];
              while (c.charAt(0) == ' ') {
                c = c.substring(1);
              }
              if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
              }
            }
            return "";
}
    function setCookie(cname,cvalue,exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            var expires = "expires=" + d.toGMTString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    
    //Sticky Ads script ends here
    
    
});