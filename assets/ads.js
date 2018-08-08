jQuery( document ).ready(function($) {
var currentAdID       		= adsforwp_localize_data.id;       
// Metaboxes field selection on different dropdown selection

//$("#wp-admin-bar-view").addClass('dnone');
$("#paragraph_number").parent().parent("tr").addClass("afw_hide");
$("#custom_code, #data_client_id, #data_ad_slot, #paragraph_number, #banner_size, #manual_ads_type, #data_cid, #data_crid").parent().parent("tr").hide();

$("#select_adtype").change(function(){        
      $(this).find("option:selected").each(function(){
          var optionValue = $(this).attr("value");
          var optionHtml = $(this).html().toLowerCase();          
          if(optionHtml){                                                            
              switch (optionValue) {
                  
                case "custom":
                   $(".afw_pointer").hide(); 
                   $("#custom_code").parent().parent("tr").show();                                       
                   $("#data_client_id, #data_ad_slot, #banner_size, #data_cid, #data_crid").parent().parent("tr").hide();                                          
                   $("#custom_code").attr("required",true);                    
                   $("#banner_size, #data_client_id, #data_ad_slot, #data_cid, #data_crid").attr("required",false);                    
                    break;
                case "adsense":
                  $("#custom_code, #data_cid, #data_crid").parent().parent("tr").hide();                                                      
              	  $("#data_client_id, #data_ad_slot, #banner_size").parent().parent("tr").show();                                    
                  $("#banner_size, #data_client_id, #data_ad_slot").attr("required",true);                                                      
                  $("#data_cid, #data_crid, #custom_code").attr("required",false); 
                  $(".afw_pointer").show();
                  $(".afw_pointer").attr("id", "afw_adsense_pointer");
                    break
                case "media_net":
                  $("#data_cid, #banner_size, #data_crid").parent().parent("tr").show();                                                      
                  $("#custom_code, #data_ad_slot, #data_client_id").parent().parent("tr").hide();              	                                      
                  $("#banner_size, #data_crid, #data_cid").attr("required",true);                                                      
                  $("#custom_code, #data_client_id, #data_ad_slot").attr("required",false);  
                  $(".afw_pointer").show();
                  $(".afw_pointer").attr("id", "afw_media_net_pointer");
                    break    
                default:
                  $("#custom_code, #data_client_id, #data_ad_slot, #banner_size").parent().parent("tr").hide();              	  
                  $(".afw_pointer").hide();
                  break;   
                }                                                        
          }      
      });
      var id = $(".afw_pointer").attr('id');          
          $("#"+id).pointer().pointer('close');
    }).change();
    
    
    
    $("#wheretodisplay").change(function(){   
        
      $(this).find("option:selected").each(function(){     
          var optionValue = $(this).attr("value");          
          if(optionValue){
              
              switch (optionValue) {
                 case "between_the_content":
                    $("#adposition").parent().parent("tr").show();
                    $("#manual_ads_type").parent().parent("tr").hide();                    
                    break;
                case "ad_shortcode":
                    $("#manual_ads_type").parent().parent("tr").show();
                    $("#adposition").parent().parent("tr").hide(); 
                    $("#paragraph_number").parent().parent("tr").addClass("afw_hide");                    
                    $("#paragraph_number").parent().parent("tr").hide();
                    break
                default:
                    $("#adposition").parent().parent("tr").hide(); 
                    $("#manual_ads_type").parent().parent("tr").hide();
                    $("#paragraph_number").parent().parent("tr").addClass("afw_hide");
                  break;   
                }                          
          }      
      });
    }).change();
            
    $("#adposition").change(function(){        
      $(this).find("option:selected").each(function(){  
          var optionValue = $(this).attr("value");                 
          if(optionValue){                                 
              if("number_of_paragraph" === optionValue){
          	$("#paragraph_number").parent().parent("tr").show();
                $("#paragraph_number").parent().parent("tr").removeClass("afw_hide");
              }else{
               $("#paragraph_number").parent().parent("tr").hide();   
              }                                                      
          }      
      });
    }).change();  
    
    // setting shortcode on page load
        if(document.getElementById('manual_ads_type')){
	document.getElementById('manual_ads_type').value = '[adsforwp id="'+currentAdID+'"]';
        }	
});

 
