

jQuery( document ).ready(function($) {
    
// Metaboxes field selection on different dropdown selection

//$("#wp-admin-bar-view").addClass('dnone');

$("#custom_code").parent().parent("tr").hide();
$("#data_client_id").parent().parent("tr").hide();
$("#data_ad_slot").parent().parent("tr").hide();
$("#paragraph_number").parent().parent("tr").hide();
$("#banner_size").parent().parent("tr").hide();

$("#select_adtype").change(function(){        
      $(this).find("option:selected").each(function(){
          var optionValue = $(this).attr("value");
          var optionHtml = $(this).html().toLowerCase();
          
          if(optionHtml){    
              
              
              if("Custom" === optionValue){
          		$("#custom_code").parent().parent("tr").show();
                        $("#data_client_id").parent().parent("tr").hide();
                        $("#data_ad_slot").parent().parent("tr").hide();
                        $("#banner_size").parent().parent("tr").hide();
              }
              if("AdSense" === optionValue){
                  $("#custom_code").parent().parent("tr").hide();
              	  $("#data_client_id").parent().parent("tr").show();
                  $("#data_ad_slot").parent().parent("tr").show();
                  $("#banner_size").parent().parent("tr").show();
              }
              if("Select Ad Type" === optionValue){
                  $("#custom_code").parent().parent("tr").hide();
              	  $("#data_client_id").parent().parent("tr").hide();
                  $("#data_ad_slot").parent().parent("tr").hide();
                  $("#banner_size").parent().parent("tr").hide();
              }
              
              
          }      
      });
    }).change();
    
    
    
    $("#wheretodisplay").change(function(){   
        
      $(this).find("option:selected").each(function(){          
          var optionHtml = $(this).html().toLowerCase();       
          if(optionHtml){                                 
              if("between the content" === optionHtml){
          	$("#adposition").parent().parent("tr").show();               
              }else{
               $("#adposition").parent().parent("tr").hide();  
               $("#paragraph_number").parent().parent("tr").addClass("afw_dnone");
              }                                                      
          }      
      });
    }).change();
            
    $("#adposition").change(function(){        
      $(this).find("option:selected").each(function(){          
          var optionHtml = $(this).html().toLowerCase();        
          if(optionHtml){                                 
              if("number of paragraph" === optionHtml){
          	$("#paragraph_number").parent().parent("tr").show();
                $("#paragraph_number").parent().parent("tr").removeClass("afw_dnone");
              }else{
               $("#paragraph_number").parent().parent("tr").hide();   
              }                                                      
          }      
      });
    }).change();            
});

 
