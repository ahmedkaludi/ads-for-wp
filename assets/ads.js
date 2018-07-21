

jQuery( document ).ready(function($) {
var currentAdID       		= adsforwp_localize_data.id;       
// Metaboxes field selection on different dropdown selection

//$("#wp-admin-bar-view").addClass('dnone');

$("#custom_code").parent().parent("tr").hide();
$("#data_client_id").parent().parent("tr").hide();
$("#data_ad_slot").parent().parent("tr").hide();
$("#paragraph_number").parent().parent("tr").hide();
$("#paragraph_number").parent().parent("tr").addClass("afw_dnone");
$("#banner_size").parent().parent("tr").hide();
$("#manual_ads_type").parent().parent("tr").hide();

$("#select_adtype").change(function(){        
      $(this).find("option:selected").each(function(){
          var optionValue = $(this).attr("value");
          var optionHtml = $(this).html().toLowerCase();
          
          if(optionHtml){                                                            
              switch (optionValue) {
                  
                case "custom":
                    $("#custom_code").parent().parent("tr").show();
                    $("#data_client_id").parent().parent("tr").hide();
                    $("#data_ad_slot").parent().parent("tr").hide();
                    $("#banner_size").parent().parent("tr").hide();                   
                    break;
                case "adsense":
                  $("#custom_code").parent().parent("tr").hide();
              	  $("#data_client_id").parent().parent("tr").show();
                  $("#data_ad_slot").parent().parent("tr").show();
                  $("#banner_size").parent().parent("tr").show();
                    break
                default:
                  $("#custom_code").parent().parent("tr").hide();
              	  $("#data_client_id").parent().parent("tr").hide();
                  $("#data_ad_slot").parent().parent("tr").hide();
                  $("#banner_size").parent().parent("tr").hide();
                  break;   
                }                                                        
          }      
      });
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
                    $("#paragraph_number").parent().parent("tr").addClass("afw_dnone");                    
                    $("#paragraph_number").parent().parent("tr").hide();
                    break
                default:
                    $("#adposition").parent().parent("tr").hide(); 
                    $("#manual_ads_type").parent().parent("tr").hide();
                    $("#paragraph_number").parent().parent("tr").addClass("afw_dnone");
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
                $("#paragraph_number").parent().parent("tr").removeClass("afw_dnone");
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

 
