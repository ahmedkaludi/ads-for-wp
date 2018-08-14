jQuery( document ).ready(function($) {    
var currentAdID       		= adsforwp_localize_data.id;      

 $('#adsforwp_ad_expire_from, #adsforwp_ad_expire_to').datepicker({
     dateFormat: "yy-mm-dd",
     minDate: 0,
     onSelect: function(selected){
         var dt = new Date(selected);
            dt.setDate(dt.getDate() + 1);
            $("#adsforwp_ad_expire_to").datepicker("option", "minDate", dt);
     }     
 });
 
 $('#adsforwp_ad_expire_to').datepicker({
     dateFormat: "yy-mm-dd",
     minDate: 0
     
 });
$(".adsforwp_ad_expire_from_span").on("click", function(){    
$('#adsforwp_ad_expire_from').focus();
});

$(".adsforwp_ad_expire_to_span").on("click", function(){    
$('#adsforwp_ad_expire_to').focus();
});

  
 $("#adsforwp_ad_expire_enable").change(function(){
     if(!$(this).is(':checked')){
        $(".afw-table-expire-ad tr").each(function(index, element){
        if(index !=0 && index<3){
            $(element).hide();
        }                           
     })
     }else{
        $(".afw-table-expire-ad tr").each(function(index, element){
        if(index !=0 && index<3){
            $(element).show();
        }                           
     }) 
     }     
 }).change();
 
 $("#adsforwp_ad_expire_day_enable").change(function(){
     if(!$(this).is(':checked')){
        $(".afw-table-expire-ad tr").each(function(index, element){
        if(index>3){
            $(element).hide();
        }                           
     })
     }else{
        $(".afw-table-expire-ad tr").each(function(index, element){
        if(index>3){
            $(element).show();
        }                           
     }) 
     }     
 }).change();
 

    
$(".adsforwp-ad-img-upload").click(function(e) {	// Application Icon upload
		e.preventDefault();
		var pwaforwpMediaUploader = wp.media({
			title: adsforwp_localize_data.uploader_title,
			button: {
				text: adsforwp_localize_data.uploader_button
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on("select", function() {
			var attachment = pwaforwpMediaUploader.state().get("selection").first().toJSON();
			$(".adsforwp-icon").val(attachment.url);
		})
		.open();
	});    
    
 
$(".afw_group_ad_list").parent().parent().hide();
$(".afw-add-new-note").hide();
$(".afw_add_more").on("click", function(){
$(this).hide();    
$(".afw_group_ad_list").parent().parent().show(); 
});

// Metaboxes field selection on different dropdown selection
$("#paragraph_number").parent().parent("tr").addClass("afw_hide");
$("#custom_code, #data_client_id, #data_ad_slot, #paragraph_number, #banner_size, #manual_ads_type, #data_cid, #data_crid, #adsforwp_ad_image").parent().parent("tr").hide();

$("#select_adtype").change(function(){        
      $(this).find("option:selected").each(function(){
          var optionValue = $(this).attr("value");
          var optionHtml = $(this).html().toLowerCase();          
          if(optionHtml){                                                            
              switch (optionValue) {
                  
                case "custom":
                   $(".afw_pointer").hide(); 
                   $("#custom_code").parent().parent("tr").show();                                       
                   $("#data_client_id, #data_ad_slot, #banner_size, #data_cid, #data_crid, #adsforwp_ad_image").parent().parent("tr").hide();                                          
                   $("#custom_code").attr("required",true);                    
                   $("#banner_size, #data_client_id, #data_ad_slot, #data_cid, #data_crid, #adsforwp_ad_image").attr("required",false);                    
                    break;
                case "adsense":
                  $("#custom_code, #data_cid, #data_crid, #adsforwp_ad_image").parent().parent("tr").hide();                                                      
              	  $("#data_client_id, #data_ad_slot, #banner_size").parent().parent("tr").show();                                    
                  $("#banner_size, #data_client_id, #data_ad_slot").attr("required",true);                                                      
                  $("#data_cid, #data_crid, #custom_code, #adsforwp_ad_image").attr("required",false); 
                  $(".afw_pointer").show();
                  $(".afw_pointer").attr("id", "afw_adsense_pointer");
                    break
                case "media_net":
                  $("#data_cid, #banner_size, #data_crid").parent().parent("tr").show();                                                      
                  $("#custom_code, #data_ad_slot, #data_client_id, #adsforwp_ad_image").parent().parent("tr").hide();              	                                      
                  $("#banner_size, #data_crid, #data_cid").attr("required",true);                                                      
                  $("#custom_code, #data_client_id, #data_ad_slot, #adsforwp_ad_image").attr("required",false);  
                  $(".afw_pointer").show();
                  $(".afw_pointer").attr("id", "afw_media_net_pointer");
                    break 
                case "ad_image":
                  $("#adsforwp_ad_image").attr("required",true);  
                  $("#banner_size, #data_client_id, #data_ad_slot, #data_cid, #data_crid, #custom_code").attr("required",false);
                  $("#adsforwp_ad_image").parent().parent("tr").show();                                                      
                  $("#custom_code, #data_ad_slot, #data_client_id, #banner_size, #data_crid, #data_cid").parent().parent("tr").hide();              	                                                                                            
                  $(".afw_pointer").hide();
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
            
            
     $("#adsforwp_refresh_type").change(function(){   
        
      $(this).find("option:selected").each(function(){     
          var optionValue = $(this).attr("value");          
          if(optionValue){
              
              switch (optionValue) {
                 case "on_interval":
                    $("#adsforwp_group_ref_interval_sec").parent().show();                    
                    break;               
                default:
                    $("#adsforwp_group_ref_interval_sec").parent().hide();
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
    
    $(document).on("click",'.afw-ads-group-button', function(){
        var adsval = $("#adsforwp_group_ad_list option:selected").val();
        var adstext = $("#adsforwp_group_ad_list option:selected").text();        
        var status = 'Not exist';
            $(".afw-group-ads").find("tr.afw-group-add-ad-list").each(function(){
            var optionValue = $(this).attr('name');            
            if(adsval === optionValue){
                status = 'Exist';                  
                return false; 
            }
            });           
          if(status === 'Not exist' && adsval) {
              
            var html ='';
            html +='<tr class="afw-group-add-ad-list" name="'+adsval+'" >';
            html +='<td>'+adstext+' <input type="hidden" name="'+adsval+'" value="'+adstext+'"></td>';
            html +='<td><button type="button" class="afw-remove-ad-from-group button">x</button></td>';
            html +='</tr>';
            $(".afw-group-ads tbody").append(html);  
            $("#adsforwp_group_ad_list option:selected").remove();
            $(".afw-add-new-note").hide();
          } else{
            $(".afw-add-new-note").show();  
          }
                
    });
    $("input[name='ads-for-wp_amp_compatibilty']").change(function(){
        var checked = $("input[name=ads-for-wp_amp_compatibilty]:checked").val();        
        if(checked =='enable'){
          $(".afw_amp_comp_check").text("AMP compatibility has been activated");  
        }else{
          $(".afw_amp_comp_check").text("AMP compatibility has been deactivated");  
        }
    }).change();
    
    $(document).on("click", ".afw-remove-ad-from-group", function(){
        var ad_id = $(this).parent().parent().find('input[type="hidden"]').attr('name');
        var ad_title = $(this).parent().parent().find('input[type="hidden"]').val();
        var optionhtml = '<option value="'+ad_id+'">'+ad_title+'</option>';
        $("#adsforwp_group_ad_list").append(optionhtml);
        $(this).parent().parent('tr').remove();
    });
            
    // setting shortcode on page load
        if(document.getElementById('manual_ads_type')){
	document.getElementById('manual_ads_type').value = '[adsforwp id="'+currentAdID+'"]';
        }
        if(document.getElementById('adsforwp_group_shortcode')){
	document.getElementById('adsforwp_group_shortcode').value = '[adsforwp-group id="'+currentAdID+'"]';
        }
});

 
