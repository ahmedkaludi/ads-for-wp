//Creating bait in admin
        var e = document.createElement('div');
        e.id = 'adsforwp-hidden-block';
        e.style.display = 'none';
        document.body.appendChild(e);

//Ajax selection starts here
        var adsforwpclone = function(){
                    jQuery(".placement-row-clone").off("click").click(function(){                        
                            var group_index = jQuery(this).closest(".afw-placement-group").attr('data-id');                         
                            var selectrow = jQuery(document).find("#call_html_template_afw").html();
                            nextId = jQuery(this).parents("tbody").find("tr").length;
                            selectrow = selectrow.replace(/\[0\]/g, "["+nextId+"]");
                            selectrow = selectrow.replace(/\[group-0\]/g, "[group-"+group_index+"]");
                            jQuery(this).parents("tr").after(selectrow);adsforwpremoveHtml();adsforwpclone();
                    });
            }
        var adsforwpremoveHtml = function(){
                    jQuery(".placement-row-delete").off("click").click(function(){
                            var class_count = jQuery(".afw-placement-group").length; 

                            if(class_count==1){
                                if(jQuery(this).parents("tbody").find("tr").length>1){
                                         jQuery(this).parents("tr").remove();
                                 }   
                               }else{
                                  if(jQuery(this).parents("tbody").find("tr").length == 1){
                                         jQuery(this).parents(".afw-placement-group").remove();
                                 } else{
                                         jQuery(this).parents("tr").remove();
                                 }
                          }
                    });
            }
        function taxonomyDataCall(){
            jQuery('select.ajax-output').change(function(){
                    var mainSelectedValue = jQuery(".afw-select-post-type").val();
                    if(mainSelectedValue=="ef_taxonomy"){
                            parentSelector = jQuery(this).parents("td").find(".afw-insert-ajax-select");
                            var selectedValue = jQuery(this).val();
                            var currentFiledNumber = jQuery(this).attr("name").split("[")[1].replace("]",'');
                            var adsforwp_call_nonce = $("#adsforwp_select_name_nonce").val();

                            parentSelector.find(".afw-ajax-output-child").remove();
                            parentSelector.find(".spinner").attr("style","visibility:visible");
                            parentSelector.children(".spinner").addClass("show");

                            var ajaxURL = adsforwp_localize_data.ajax_url;
                            var group_index = jQuery(this).closest(".afw-placement-group").attr('data-id'); 
                            //ajax call
                            jQuery.ajax({
                    url : ajaxURL,
                    method : "POST",
                    data: { 
                      action: "adsforwp_ajax_select_taxonomy", 
                      id: selectedValue,
                      number : currentFiledNumber,
                      group_number : group_index,
                      adsforwp_call_nonce: adsforwp_call_nonce
                    },
                    beforeSend: function(){ 
                    },
                    success: function(data){ 
                            // This code is added twice " withThis.find('.ajax-output').remove(); "
                                    parentSelector.find(".afw-ajax-output-child").remove();
                                    parentSelector.children(".spinner").removeClass("show");
                                    parentSelector.find(".spinner").attr("style","visibility:hidden").hide();
                                    parentSelector.append(data);
                                    taxonomyDataCall();
                    },
                    error: function(data){
                      console.log("Failed Ajax Request");
                      console.log(data);
                    }
                  }); 
                    }
            });
    }       
//Ajax selection ends here

/* Visitor Conditions jquery starts here */

        var adsforwpvisitorclone = function(){
                    jQuery(".adsforwp-visitor-condition-row-clone").off("click").click(function(){                                                
                            var group_index = jQuery(this).closest(".adsforwp-visitor-condition-group").attr('data-id');                                                     
                            var selectrow = jQuery(document).find("#adsforwp_visitor_condition_html_template").html();                            
                            var visitornextId = jQuery(this).parents("tbody").find("tr").length;
                            selectrow = selectrow.replace(/\[0\]/g, "["+visitornextId+"]");
                            selectrow = selectrow.replace(/\[group-0\]/g, "[group-"+group_index+"]");
                            jQuery(this).parents("tr").after(selectrow);adsforwpvisitorremoveHtml();adsforwpvisitorclone();
                    });
            }
        var adsforwpvisitorremoveHtml = function(){
                    jQuery(".adsforwp-visitor-condition-row-delete").off("click").click(function(){
                            var class_count = jQuery(".adsforwp-visitor-condition-group").length; 
                            var button = '<div class="adsforwp-visitor-condition-div"><a class="adsforwp-enable-click afw-placement-button">User Targeting</a><p>User Targeting conditions to limit the number of users who can see your ad.</p></div>';
                            if(class_count==1){
                                if(jQuery(this).parents("tbody").find("tr").length>1){
                                         jQuery(this).parents("tr").remove();
                                 }else{
                                  if(jQuery(this).parents(".adsforwp_visitor_condition_group").find(".adsforwp-visitor-condition-div").length < 1 ){
                                      $(button).insertBefore($('.adsforwp-visitor-condition-groups'));
                                      $("#adsforwp_v_condition_enable").val('');
                                      $(".adsforwp-visitor-condition-div").show();
                                      $(".adsforwp-visitor-condition-groups").addClass('afw_hide');
                                  }else{
                                      $(".adsforwp-visitor-condition-div").show();
                                      $(".adsforwp-visitor-condition-groups").addClass('afw_hide');
                                      $("#adsforwp_v_condition_enable").val('');
                                  }
                                }      
                               }else{
                                  if(jQuery(this).parents("tbody").find("tr").length == 1){
                                         jQuery(this).parents(".adsforwp-visitor-condition-group").remove();
                                 } else{
                                         jQuery(this).parents("tr").remove();
                                 }
                          }
                    });
            }

/* Visitor Conditions jquery ends here */


function adsforwpGetParamByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}


function adsforwp_pointer(id,content, status){
       $("#"+id).pointer({
        content: content,
         position: {
            edge: 'top',
        },
        show: function(event, t){
            t.pointer.css({'right':'95px','left':'auto'});
        },
        close: function() {
            // This function is fired when you click the close button
        }
      }).pointer(status); 
   }
   function adsforwp_pointer_hover(id, status){      
       var content ='default';                        
        switch(id){
            case 'afw_adsense_pointer':
                 content = '<h3>'+adsforwp_localize_data.pointer_help+'</h3><p>'+adsforwp_localize_data.adsense_pointer+'</p>';
                break;
            case 'afw_media_net_pointer':
                content = '<h3>'+adsforwp_localize_data.pointer_help+'</h3><p>'+adsforwp_localize_data.media_net_pointer+'</p>';
                break;           
            case 'afw_ad_now_pointer':
                content = '<h3>'+adsforwp_localize_data.pointer_help+'</h3><p>'+adsforwp_localize_data.ad_now_pointer+'</p>';
                break; 
            case 'afw_contentad_pointer':
               content = '<h3>'+adsforwp_localize_data.pointer_help+'</h3><p>'+adsforwp_localize_data.contentad_pointer+'</p>';
                break;
            case 'afw_infolinks_pointer':
                content = '<h3>'+adsforwp_localize_data.pointer_help+'</h3><p>'+adsforwp_localize_data.infolinks_pointer+'</p>';
                break; 
            case 'afw_ad_image_pointer':
                content = '<h3>'+adsforwp_localize_data.pointer_help+'</h3><p>'+adsforwp_localize_data.ad_image_pointer+'</p>';
                break; 
            case 'afw_custom_pointer':
                content = '<h3>'+adsforwp_localize_data.pointer_help+'</h3><p>'+adsforwp_localize_data.custom_pointer+'</p>';
                break;     
            case 'afw_doubleclick_pointer':
                content = '<h3>'+adsforwp_localize_data.pointer_help+'</h3><p>'+adsforwp_localize_data.doubleclick_pointer+'</p>';
                break;
            case 'afw_ad_background_pointer':
                content = '<h3>'+adsforwp_localize_data.pointer_help+'</h3><p>'+adsforwp_localize_data.ad_background_pointer+'</p>';
                break;    
            default:
                break;
        }         
        adsforwp_pointer(id,content, status);        
   }  

jQuery( document ).ready(function($) {
    
    /* Newletters js starts here */      
        
            if(adsforwp_localize_data.do_tour){
                
                var content = '<h3>You are awesome for using Ads for WP</h3>';
			content += '<p>Do you want the latest on <b>Ads update</b> before others and some best resources on monetization in a single email? - Free just for users of ADS!</p>';
                        content += '<style type="text/css">';
                        content += '.wp-pointer-buttons{ padding:0; overflow: hidden; }';
                        content += '.wp-pointer-content .button-secondary{  left: -25px;background: transparent;top: 5px; border: 0;position: relative; padding: 0; box-shadow: none;margin: 0;color: #0085ba;} .wp-pointer-content .button-primary{ display:none}	#afw_mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }';
                        content += '</style>';                        
                        content += '<div id="afw_mc_embed_signup">';
                        content += '<form action="//app.mailerlite.com/webforms/submit/o1s7u3" data-id="258182" data-code="o1s7u3" method="POST" target="_blank">';
                        content += '<div id="afw_mc_embed_signup_scroll">';
                        content += '<div class="afw-mc-field-group" style="    margin-left: 15px;    width: 195px;    float: left;">';
                        content += '<input type="text" name="fields[name]" class="form-control" placeholder="Name" hidden value="'+adsforwp_localize_data.current_user_name+'" style="display:none">';
                        content += '<input type="text" value="'+adsforwp_localize_data.current_user_email+'" name="fields[email]" class="form-control" placeholder="Email*"  style="      width: 180px;    padding: 6px 5px;">';
                        content += '<input type="text" name="fields[company]" class="form-control" placeholder="Website" hidden style=" display:none; width: 168px; padding: 6px 5px;" value="'+adsforwp_localize_data.get_home_url+'">';
                        content += '<input type="hidden" name="ml-submit" value="1" />';
                        content += '</div>';
                        content += '<div id="mce-responses">';
                        content += '<div class="response" id="mce-error-response" style="display:none"></div>';
                        content += '<div class="response" id="mce-success-response" style="display:none"></div>';
                        content += '</div>';
                        content += '<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_a631df13442f19caede5a5baf_c9a71edce6" tabindex="-1" value=""></div>';
                        content += '<input type="submit" value="Subscribe" name="subscribe" id="pointer-close" class="button mc-newsletter-sent" style=" background: #0085ba; border-color: #006799; padding: 0px 16px; text-shadow: 0 -1px 1px #006799,1px 0 1px #006799,0 1px 1px #006799,-1px 0 1px #006799; height: 30px; margin-top: 1px; color: #fff; box-shadow: 0 1px 0 #006799;">';
                        content += '</div>';
                        content += '</form>';
                        content += '</div>';
                
                var setup;                
                var wp_pointers_tour_opts = {
                    content:content,
                    position:{
                        edge:"top",
                        align:"left"
                    }
                };
                                
                wp_pointers_tour_opts = $.extend (wp_pointers_tour_opts, {
                        buttons: function (event, t) {
                                button= jQuery ('<a id="pointer-close" class="button-secondary">' + adsforwp_localize_data.button1 + '</a>');
                                button_2= jQuery ('#pointer-close.button');
                                button.bind ('click.pointer', function () {
                                        t.element.pointer ('close');
                                });
                                button_2.on('click', function() {
                                        t.element.pointer ('close');
                                } );
                                return button;
                        },
                        close: function () {
                                $.post (adsforwp_localize_data.ajax_url, {
                                        pointer: 'adsforwp_subscribe_pointer',
                                        action: 'dismiss-wp-pointer'
                                });
                        },
                        show: function(event, t){
                         t.pointer.css({'left':'170px', 'top':'160px'});
                      }                                               
                });
                setup = function () {
                        $(adsforwp_localize_data.displayID).pointer(wp_pointers_tour_opts).pointer('open');
                         if (adsforwp_localize_data.button2) {
                                jQuery ('#pointer-close').after ('<a id="pointer-primary" class="button-primary">' + adsforwp_localize_data.button2+ '</a>');
                                jQuery ('#pointer-primary').click (function () {
                                        adsforwp_localize_data.function_name;
                                });
                                jQuery ('#pointer-close').click (function () {
                                        $.post (adsforwp_localize_data.ajax_url, {
                                                pointer: 'adsforwp_subscribe_pointer',
                                                action: 'dismiss-wp-pointer'
                                        });
                                });
                         }
                };
                if (wp_pointers_tour_opts.position && wp_pointers_tour_opts.position.defer_loading) {
                        $(window).bind('load.wp-pointers', setup);
                }
                else {
                        setup ();
                }
                
            }
                
    /* Newletters js ends here */ 
           
    /* WP Pointer for ad type information */      
    
    $(".afw_pointer").mouseover(function(){
        var status = 'open';
        var id = $(this).attr('id');         
        adsforwp_pointer_hover(id, status);
    }); 
        
   /* Visitor condition jquery starts here */   
   
   $(document).on("change",".adsforwp-visitor-condition-ajax-output",function(){
      var value = $(this).val();           
      $(this).parent().find(".adsforwp_user_agent_custom").addClass('afw_hide');
      $(this).parent().find(".adsforwp_url_custom").addClass('afw_hide');   
     if(value =='url_custom'){
      $(this).parent().find(".adsforwp_url_custom").removeClass('afw_hide');   
     }
     if(value =='user_agent_custom'){
      $(this).parent().find(".adsforwp_user_agent_custom").removeClass('afw_hide');
     }
   });
   $(".adsforwp-visitor-condition-or-group").on("click", function(e){
            e.preventDefault();
          var group_index ='';
          var group_index = $(".adsforwp-visitor-condition-group").length;             
          
            
          var selectrow = jQuery(document).find("#adsforwp_visitor_condition_html_template").html();          
              selectrow = selectrow.replace(/\[group-0\]/g, "[group-"+group_index+"]");
          var placement_group_html = '';
              placement_group_html +='<table class="widefat adsforwp-visitor-condition-widefat" style="border:0px;">';
              placement_group_html += selectrow; 
              placement_group_html +='</table>';  
                              
          var html='';  
              html +='<div class="adsforwp-visitor-condition-group" name="visitor_conditions_array['+group_index+']" data-id="'+group_index+'">';
              html +='<span style="margin-left:10px;font-weight:600">Or</span>';
              html +=placement_group_html;
              html +='</div>';                
           $(".adsforwp-visitor-condition-group[data-id="+(group_index-1)+"]").after(html); 
           group_index++;
           adsforwpvisitorclone();
           adsforwpvisitorremoveHtml();
        });


        var selectvisitorrow = $("#adsforwp_visitor_condition_metabox").find("table.widefat tr").html();        
	$("body").append("<script type='template/html' id='adsforwp_visitor_condition_html_template'><tr class='adsforwp-toclone cloneya'>"+selectvisitorrow+"</tr>");
	adsforwpvisitorclone();
	adsforwpvisitorremoveHtml();
	$(document).on("change", ".adsforwp-select-visitor-condition-type", function(){           
                var current_change = $(this);
                var selectedValue = $(this).val();
                var tdindex = [1,2,3,4]; 
                if(selectedValue !='show_globally'){
                
                 $.each(tdindex, function(i,e){  
                    $(current_change).closest('tr').find('td').eq(e).show();  
                 });
                
		var parent = $(this).parents('tr').find(".adsforwp-insert-condition-select");		
		var currentFiledNumber = $(this).attr("class").split(" ")[2];
                var adsforwp_visitor_condition_call_nonce = $("#adsforwp_visitor_condition_name_nonce").val();
		
		parent.find(".adsforwp-visitor-condition-ajax-output").remove();
                parent.find(".adsforwp_user_agent_custom").remove();
                parent.find(".adsforwp-url-parameter").remove(); 
                parent.find(".adsforwp-cookie-value").remove(); 
                parent.find(".adsforwp_url_custom").remove();
                parent.find(".adsforwp-user-targeting-note").remove();
		//parent.find(".afw-ajax-output-child").remove();
		parent.find(".spinner").attr("style","visibility:visible");
		parent.children(".spinner").addClass("show");
		var ajaxURL = adsforwp_localize_data.ajax_url;
                var group_index = jQuery(this).closest(".adsforwp-visitor-condition-group").attr('data-id'); 
		//ajax call
              $.ajax({
        url : ajaxURL,
        method : "POST",
        data: { 
          action: "adsforwp_visitor_condition_type_values", 
          id: selectedValue,
          number : currentFiledNumber,
          group_number : group_index,
          adsforwp_visitor_condition_call_nonce : adsforwp_visitor_condition_call_nonce
        },
        beforeSend: function(){ 
        },
        success: function(data){ 
        	// This code is added twice " withThis.find('.ajax-output').remove(); "
      			parent.find(".ajax-output").remove();
      			parent.children(".spinner").removeClass("show");
      			parent.find(".spinner").attr("style","visibility:hidden").hide();
      			parent.append(data);
      			//taxonomyDataCall();
        },
        error: function(data){
          console.log("Failed Ajax Request");
          console.log(data);
        }
      }); 
            }else{            
            $.each(tdindex, function(i,e){   
            $(current_change).closest('tr').find('td').eq(e).hide();             
            
            });           
        }
	});
   
   
   
   
   
   /* Visitor condition jquery starts here */
    
//Ajax selectin starts here


    $(".afw-placement-or-group").on("click", function(e){
            e.preventDefault();
            var group_index ='';
            var group_index = $(".afw-placement-group").length;             
           
            
          var selectrow = jQuery(document).find("#call_html_template_afw").html();
              selectrow = selectrow.replace(/\[group-0\]/g, "[group-"+group_index+"]");
          var placement_group_html = '';
              placement_group_html +='<table class="widefat afw-placement-table" style="border:0px;">';
              placement_group_html += selectrow; 
              placement_group_html +='</table>';  
                              
          var html='';  
              html +='<div class="afw-placement-group" name="data_group_array['+group_index+']" data-id="'+group_index+'">';
              html +='<span style="margin-left:10px;font-weight:600">Or</span>';
              html +=placement_group_html;
              html +='</div>';                
           $(".afw-placement-group[data-id="+(group_index-1)+"]").after(html); 
           group_index++;
           adsforwpclone();
           adsforwpremoveHtml();
        });


        var selectrow = $("#adsforwp_placement_metabox").find("table.widefat tr").html();
	$("body").append("<script type='template/html' id='call_html_template_afw'><tr class='toclone cloneya'>"+selectrow+"</tr>");
	adsforwpclone();
	adsforwpremoveHtml();
	$(document).on("change", ".afw-select-post-type", function(){
            
                var current_change = $(this);
                var selectedValue = $(this).val();
                var tdindex = [1,2,3,4]; 
                if(selectedValue !='show_globally'){
                
                 $.each(tdindex, function(i,e){  
                    $(current_change).closest('tr').find('td').eq(e).show();  
                 });
                
		var parent = $(this).parents('tr').find(".afw-insert-ajax-select");		
		var currentFiledNumber = $(this).attr("class").split(" ")[2];
                var adsforwp_call_nonce = $("#adsforwp_select_name_nonce").val();
		
		parent.find(".ajax-output").remove();
		parent.find(".afw-ajax-output-child").remove();
		parent.find(".spinner").attr("style","visibility:visible");
		parent.children(".spinner").addClass("show");
		var ajaxURL = adsforwp_localize_data.ajax_url;
                var group_index = jQuery(this).closest(".afw-placement-group").attr('data-id'); 
		//ajax call
              $.ajax({
        url : ajaxURL,
        method : "POST",
        data: { 
          action: "adsforwp_create_ajax_select_box", 
          id: selectedValue,
          number : currentFiledNumber,
          group_number : group_index,
          adsforwp_call_nonce : adsforwp_call_nonce
        },
        beforeSend: function(){ 
        },
        success: function(data){ 
        	// This code is added twice " withThis.find('.ajax-output').remove(); "
      			parent.find(".ajax-output").remove();
      			parent.children(".spinner").removeClass("show");
      			parent.find(".spinner").attr("style","visibility:hidden").hide();
      			parent.append(data);
      			taxonomyDataCall();
        },
        error: function(data){
          console.log("Failed Ajax Request");
          console.log(data);
        }
      }); 
            }else{            
            $.each(tdindex, function(i,e){   
            $(current_change).closest('tr').find('td').eq(e).hide();             
            
            });           
        }
	});
	taxonomyDataCall();
	
//Ajax selectin ends here
    
        var currentAdID = adsforwp_localize_data.id;      

    $(".adsforwp-tabs a").click(function(e){
                    var href = $(this).attr("href");
                    var currentTab = adsforwpGetParamByName("tab",href);
                    if(!currentTab){
                            currentTab = "general";
                    }                   
                    switch(currentTab){

                        case "general":
                            $(".adsforwp-settings-form #submit").show();
                            break;   
                        case "support":
                            $(".adsforwp-settings-form #submit").hide();
                            break;
                        case "tools":
                            $(".adsforwp-settings-form #submit").show();
                            break;
                    }                
                    $(this).siblings().removeClass("nav-tab-active");
                    $(this).addClass("nav-tab-active");
                    $(".form-wrap").find(".adsforwp-"+currentTab).siblings().hide();
                    $(".form-wrap .adsforwp-"+currentTab).show();
                    window.history.pushState("", "", href);
                    return false;
            });
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
 

    $(".adsforwp-general table th").on("click", function(){    
        $(this).parent().find("input[type=checkbox]").click();   
    });

    $("#adsforwp_ad_img_width").parent().parent("tr").hide();  
    $("#adsforwp_ad_img_height").parent().parent("tr").hide();    
    $(".adsforwp-ad-img-upload").click(function(e) {	// Application Icon upload
                    e.preventDefault();
                    var adsforwpMediaUploader = wp.media({
                            title: adsforwp_localize_data.uploader_title,
                            button: {
                                    text: adsforwp_localize_data.uploader_button
                            },
                            multiple: false,  // Set this to true to allow multiple files to be selected
                            library:{type : 'image'}
                    })
                    .on("select", function() {
                            var attachment = adsforwpMediaUploader.state().get("selection").first().toJSON();                        
                            $(".adsforwp-icon").val(attachment.url);
                            $(".afw_ad_img_div").html('<div class="afw_ad_thumbnail"><img class="afw_ad_image_prev" src="'+attachment.url+'"/><a href="#" class="afw_ad_prev_close">X</a></div>');
                            $("#adsforwp_ad_img_height").val(attachment.height);
                            $("#adsforwp_ad_img_width").val(attachment.width);
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
    afw_amp_status = $("#ads-for-wp_amp_compatibilty").val();
    $("#select_adtype").change(function(){        
          $(this).find("option:selected").each(function(){
              var optionValue = $(this).attr("value");
              var optionHtml = $(this).html().toLowerCase();          
              if(optionHtml){ 
                  
                  if($("#wheretodisplay option:selected").val() != 'sticky'){
                    $("#wheretodisplay option[value='sticky']").remove();
                  }
                                               
                  $(".afw-amp-support").addClass('afw_hide'); 
                  $(".afw-amp-support").addClass('afw_hide');
                  $(".afw-amp-support span").text("");
                  $("#afw-amp-status-display").text(afw_amp_status);
                  $(".afw-amp-edit-post-status").show();
                  $("#ads-for-wp_amp_compatibilty").val(afw_amp_status);
                  
                  $('.adsforwp-ad-type-table tbody tr').not(':first').hide();
                  
                  $('.adsforwp-ad-type-table select').attr("required",false);
                  $('.adsforwp-ad-type-table input').attr("required",false);
                  $('#select_adtype').attr("required",true);  
                  $("#custom_code").attr("required",false);  
                  $(".afw_pointer").show();
                  $(".afw_pointer").attr("id", "afw_"+optionValue+"_pointer");
                  $("#adsforwp-location").show();    
                  switch (optionValue) {

                    case "mgid":
                        
                       $("#display-metabox").show();  
                       $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();
                       $("#adsforwp_mgid_data_publisher, #adsforwp_mgid_data_widget, #adsforwp_mgid_data_container, #banner_size, #adsforwp_mgid_data_js_src").parent().parent("tr").show();                                                             
                                                                  
                        break;

                    case "custom":
                       $("#display-metabox").show();  
                       $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();
                       $("#custom_code").parent().parent("tr").show();                                                             
                       $("#custom_code").attr("required",true);                                           
                        break;
                    case "adsense":
                      $("#adsense_type").parent().parent("tr").show();
                      var adsense_type = $("#adsense_type option:selected").val();                 
                      switch(adsense_type){
                          case "normal":  
                               $("#display-metabox").show();  
                               $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();
                               $("#banner_size, #data_client_id, #data_ad_slot, #adsforwp_ad_responsive").parent().parent("tr").show();
                               $("#banner_size, #data_client_id, #data_ad_slot").attr("required",true);
                              
                              break;
                          case "adsense_sticky_ads":  
                               $("#display-metabox").hide();
                               $("#adsforwp-location").hide();
                               $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();
                               $("#banner_size, #data_client_id, #data_ad_slot").parent().parent("tr").show();
                               $("#banner_size, #data_client_id, #data_ad_slot").attr("required",true);
                              
                              break;    
                          case "adsense_auto_ads":
                                $("#display-metabox").hide();
                               // $("#adsforwp-location").hide();   
                                $("#adsforwp_visitor_condition_metabox").hide();
                                $("#data_client_id").parent().parent("tr").show();                                
                                $("#data_client_id").attr("required",true);                               

                              break;
                          default:
                              $("#display-metabox").show();
                              $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();
                                                            
                              break;
                      }                                     
                                                               
                        break
                    case "media_net":
                      $("#display-metabox").show();
                      $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();
                      $("#data_cid, #banner_size, #data_crid").parent().parent("tr").show();                                                                            
                      $("#banner_size, #data_crid, #data_cid").attr("required",true);                                                                                                 
                        break
                    
                    case "doubleclick":
                      $("#display-metabox").show();
                      $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();
                      $("#banner_size").parent().parent("tr").show();
                      $(".adsforwp_dfp").show();     
                                                                                                                                            
                        break
                    
                    case "contentad":
                      $("#display-metabox").show();
                      $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();
                      $("#contentad_id, #contentad_id_d, #contentad_widget_id").parent().parent("tr").show();                                                                                                                 
                      $("#contentad_id, #contentad_id_d, #contentad_widget_id").attr("required",true);                                                                                                                 
                        break     
                    case "infolinks":
                      $("#display-metabox").show();
                      $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();
                      $("#infolinks_pid, #infolinks_wsid").parent().parent("tr").show();                                         
                      $("#infolinks_pid, #infolinks_wsid").attr("required",true);                                                                                                  
                      $(".afw-amp-support").removeClass('afw_hide');
                      $(".afw-amp-support span").text(adsforwp_localize_data.infolinks_note);
                      $("#afw-amp-status-display").text('disable');
                      $(".afw-amp-edit-post-status").hide();
                      $("#ads-for-wp_amp_compatibilty").val('disable');
                        break     

                    case "ad_image":
                        
                       if($("#wheretodisplay option:selected").val() != 'sticky'){
                           
                        $("#wheretodisplay option[value='custom_target']").after('<option value="sticky">Sticky</option>');   
                        
                       }                       
                                                
                      $("#display-metabox").show();
                      $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();
                      $("#adsforwp_ad_image").attr("required",true); 
                      $("#adsforwp_ad_image").attr("readonly",true);                      
                      $("#adsforwp_ad_image, #adsforwp_ad_redirect_url, #adsforwp_ad_responsive").parent().parent("tr").show();                                                                                                
                        break
                    
                    case "ad_background":
                                                        
                      $("#display-metabox").hide();
                      $("#adsforwp-location").hide();
                      $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();
                      $("#ad_background_image").attr("required",true); 
                      $("#ad_background_image").attr("readonly",true);                      
                      $("#ad_background_image, #ad_background_redirect_url").parent().parent("tr").show();  
                      $("#ad_background_image").parent().parent().parent("tr").show();  
                        break                                                                    
                    case "ad_now":
                      $("#display-metabox").show();
                      $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();
                      $("#ad_now_widget_id").attr("required",true);                                        
                      $("#ad_now_widget_id").parent().parent("tr").show();                                                                                                 
                      $(".afw-amp-support").removeClass('afw_hide');
                      $(".afw-amp-support span").text(adsforwp_localize_data.adnow_note);
                      $("#afw-amp-status-display").text('disable');
                      $(".afw-amp-edit-post-status").hide();
                      $("#ads-for-wp_amp_compatibilty").val('disable');
                        break     
                    default:
                      $("#display-metabox").show();   
                      $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();
                      $(".afw_pointer").hide();
                      break;   
                    }                                                     
              }      
          });
          var id = $(".afw_pointer").attr('id');          
              $("#"+id).pointer().pointer('close');
        }).change();
    
    
    
    $("#adsense_type").change(function(){   
                
          var optionValue = $("#adsense_type option:selected").val();            
          if(optionValue){
              
              switch (optionValue) {
                 case "normal":
                    $("#banner_size, #data_client_id, #data_ad_slot, #adsforwp_ad_responsive").parent().parent("tr").show();
                    $("#banner_size, #data_client_id, #data_ad_slot").attr("required",true);                    
                    $("#display-metabox").show();
                    $("#adsforwp-location").show(); 
                    $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();
                    $(".afw-select-post-type option[value=post_type]").attr("selected", "selected");
                            var tdindex = [1,2,3,4]; 
                            $.each(tdindex, function(i,e){  
                                $(".afw-select-post-type").closest('tr').find('td').eq(e).show();  
                             });
                    break;
                
                case "adsense_sticky_ads":
                    $("#banner_size, #data_client_id, #data_ad_slot").parent().parent("tr").show();
                    $("#banner_size, #data_client_id, #data_ad_slot").attr("required",true);                    
                    $("#display-metabox").hide();
                    $("#adsforwp-location").hide(); 
                    $("#adsforwp_visitor_condition_metabox, #adsforwp_placement_metabox").show();                    
                          
                    break;
                
                case "adsense_auto_ads":
                   $(".afw-select-post-type option[value=show_globally]").attr("selected", "selected");
                            var tdindex = [1,2,3,4]; 
                            $.each(tdindex, function(i,e){  
                                $(".afw-select-post-type").closest('tr').find('td').eq(e).hide();  
                             }); 
                   $("#display-metabox").hide();
                  // $("#adsforwp-location").hide();   
                   $("#adsforwp_visitor_condition_metabox").hide();
                   $("#data_client_id").parent().parent("tr").show();
                   $("#banner_size, #data_ad_slot, #adsforwp_ad_responsive").parent().parent("tr").hide();
                   $("#adsforwp_ad_responsive").prop("checked", false);
                   $("#data_client_id").attr("required",true);
                   $("#banner_size, #data_ad_slot").attr("required",false);
                   $.ajax({
                    url:adsforwp_localize_data.ajax_url,
                    async: false, 
                    dataType: "json",
                    data:{action:"adsforwp_check_meta", adsforwp_security_nonce:adsforwp_localize_data.adsforwp_security_nonce},
                    success:function(response){
                     if(response['status'] == 't' ){                         
                         $(".afw_adsense_auto_note").removeClass('afw_hide'); 
                         var location_name = location.protocol + "//" + location.host;
                         $(".afw_adsense_auto").attr('href', ''+location_name+'/wordpress/wp-admin/post.php?post='+response['post_id']+'&action=edit')
                     }                     
                    }                
                    }); 
                   
                    break
                default:                    
                  break;   
                }                          
          }      
      
    });
    
    $(".adsforwp-advance-option-click").click(function(e){   
        e.preventDefault();
       $("#display_tag_name").parent().parent("tr").toggle();
       if($("#display_tag_name").val() ==='custom_tag' && $('#display_tag_name').css('display') == 'none'){
        $("#entered_tag_name").parent().parent("tr").show();    
       }else{
        $("#entered_tag_name").parent().parent("tr").hide();       
       }
      
    }).change();
    
    $("#display_tag_name").change(function(){           
      $(this).find("option:selected").each(function(){     
          var optionValue = $(this).attr("value");          
         $("#entered_tag_name").parent().parent("tr").hide(); 
          if(optionValue ==='custom_tag'){              
             $("#entered_tag_name").parent().parent("tr").show(); 
          }      
      });
    }).change();
    
    $("#wheretodisplay").change(function(){   
        
      $(this).find("option:selected").each(function(){     
          var optionValue = $(this).attr("value");          
          if(optionValue){
              $('.adsforwp-display-table tbody tr').not(':first').hide();
              $(".afw_ad_img_margin").parent().parent("tr").show();
               $(".adsforwp-advance-option-click").hide();
              switch (optionValue) {
                 case "between_the_content":
                    var pragraph_no = $("#adposition").val();
                    if(pragraph_no ==='number_of_paragraph'){
                     $("#paragraph_number").parent().parent("tr").show();
                     $(".adsforwp-advance-option-click").show();
                     if($("#display_tag_name").val() !==''){
                     $("#display_tag_name").parent().parent("tr").show();     
                     }
                     if($("#display_tag_name").val() ==='custom_tag'){
                     $("#entered_tag_name").parent().parent("tr").show();     
                     }
                     
                    }
                    $("#adposition").parent().parent("tr").show();                    
                    $(".afw_ad_align_field").parent().parent("tr").show();
                    break;
                case "ad_shortcode":
                    $("#manual_ads_type").parent().parent("tr").show();                   
                    $(".afw_ad_align_field").parent().parent("tr").show();
                    break
                case "after_the_content":                      
                    $(".afw_ad_align_field").parent().parent("tr").show();
                    break;
                case "before_the_content":                      
                    $(".afw_ad_align_field").parent().parent("tr").show();
                    break;                
                case "custom_target":                      
                   $(".afw_ad_img_margin").parent().parent("tr").hide();
                   $(".adsforwp-custom-target-fields").parent().parent("tr").show();
                   $('input[name=adsforwp_custom_target_position]').change();
                    break; 
                    
                case "adsforwp_after_featured_image":
                case "adsforwp_below_the_header":
                case "adsforwp_below_the_footer":
                case "adsforwp_above_the_footer":
                case "adsforwp_above_the_post_content":
                case "adsforwp_below_the_post_content":
                case "adsforwp_below_the_title":
                case "adsforwp_above_related_post":
                case "adsforwp_below_author_box":
                                                                 
                $(".afw_ad_align_field").parent().parent("tr").show();
                break;
                
                case "adsforwp_ads_in_loops":
                                                          
                    $(".afw_ad_align_field").parent().parent("tr").show();
                    $("#adsforwp_after_how_many_post").parent().parent("tr").show();
                      
                break;        
                default:                                      
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
    
    $('input[name=adsforwp_custom_target_position]').change(function() {
        $("#adsforwp_new_element").parent().parent("tr").hide(); 
        $("#adsforwp_jquery_selector").parent().parent("tr").hide(); 
        $("#adsforwp_existing_element_action").parent().parent("tr").hide();
        
     if($(this).is(":visible")){
     switch($("input[name='adsforwp_custom_target_position']:checked").val()) {
         case 'existing_element':
             $("#adsforwp_jquery_selector").parent().parent("tr").show(); 
             $("#adsforwp_existing_element_action").parent().parent("tr").show(); 
             break;
         case 'new_element':
             $("#adsforwp_new_element").parent().parent("tr").show(); 
             
             break;
     }    
     }   
     
     
    }).change();
    
    $("#paragraph_number").change(function(){       
        
        console.log($(this).val());
        
      $(".adsforwp-every-paragraphs-text").text('Display After Every '+ $(this).val());
      
    }).change();  
    
    $("#adposition").change(function(){        
      $(this).find("option:selected").each(function(){  
          var optionValue = $(this).attr("value");    
          var wheretodisplay = $("#wheretodisplay").val();
          $("#display_tag_name").parent().parent("tr").hide(); 
          $("#entered_tag_name").parent().parent("tr").hide(); 
          if(optionValue){                                 
              if("number_of_paragraph" === optionValue && wheretodisplay ==='between_the_content'){
                  
                var tagval = $("#display_tag_name").val();
                if(tagval == 'div_tag' || tagval == 'img_tag') {
                 $("#display_tag_name").parent().parent("tr").show();    
                } 
                if(tagval == 'custom_tag'){
                 $("#display_tag_name").parent().parent("tr").show();
                 $("#entered_tag_name").parent().parent("tr").show();                 
                }
          	$("#paragraph_number").parent().parent("tr").show();  
                $(".adsforwp-advance-option-click").show();                  
              }else{
               $("#paragraph_number").parent().parent("tr").hide();                
               $(".adsforwp-advance-option-click").hide();  
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
    $(document).on("click", ".afw-remove-ad-from-group", function(){
        var ad_id = $(this).parent().parent().find('input[type="hidden"]').attr('name');
        var ad_title = $(this).parent().parent().find('input[type="hidden"]').val();
        var optionhtml = '<option value="'+ad_id+'">'+ad_title+'</option>';
        $("#adsforwp_group_ad_list").append(optionhtml);
        $(this).parent().parent('tr').remove();
    });
            
    $(document).on("click", ".afw_ad_prev_close", function(e){
        e.preventDefault();
        $(".afw_ad_thumbnail").remove();
        $("#adsforwp_ad_image").val("");
        $("#adsforwp_ad_img_height").val("");
        $("#adsforwp_ad_img_width").val("");
    });    
    
    $(".afw_ad_revenue_sharing").change(function(){
     if($(this).is(':checked')){
       $(".afw_revenue_divider").show();
     }else{
       $(".afw_revenue_divider").hide(); 
     }     
 }).change();
 
 $(".afw_sponsorship_label").change(function(){
     if($(this).is(':checked')){
       $(".afw_sponsorship_divider").show();
     }else{
       $(".afw_sponsorship_divider").hide(); 
     }     
 }).change();
    
    $("#adsforwp_owner_revenue_per").keyup(function(){
        var owner_per = $(this).val();
        var author_per = 100 - owner_per;
        $("#adsforwp_author_revenue_per").val(author_per);
        
    });
    $("#adsforwp_author_revenue_per").keyup(function(){
        var author_per= $(this).val();
        var owner_per = 100 - author_per;
        $("#adsforwp_owner_revenue_per").val(owner_per);
        
    });
    
    
    $("#display-metabox .handlediv").after('<a type="button" href="#" class="button afw-embed-code-button" aria-expanded="true" style="float: right; margin-top: 3px;">'+adsforwp_localize_data.embed_code_button_text+'</a>');
    $(document).on("click", '.afw-embed-code-button', function(){
            if(adsforwp_localize_data.post_type == 'adsforwp'){
              var shortcode = '[adsforwp id="'+currentAdID+'"]';
              var themefunction ='&lt;?php adsforwp_the_ad( '+currentAdID+' ) ?&gt;';
            }
            if(adsforwp_localize_data.post_type == 'adsforwp-groups'){
              var shortcode = '[adsforwp-groups id="'+currentAdID+'"]';
              var themefunction ='&lt;?php adsforwp_the_group( '+currentAdID+' ) ?&gt;';
            }
        var html = '<ul class="afw-embed-code-ul">';                       
            html+= '<li><strong>Shortcode =</strong> '+shortcode+'</li>';
            html+= '<li><strong>Theme Function =</strong> '+themefunction+'</li>';            
            html+= '</ul>';
           
            
        $("#afw-embed-code-div").html(html);
        
       tb_show("Embed Shortcode", "#TB_inline??width=600&height=550&inlineId=afw-embed-code-div");
       $(document).find('#TB_window').width(600).height(200).css({'top':'200px', 'margin-top': '0px'});
    });
    var tb_unload_count = 1;
    $(window).bind('tb_unload', function () {
        if (tb_unload_count > 1) {
            tb_unload_count = 1;
        } else {
           $("#afw-embed-code-div").hide();
            tb_unload_count = tb_unload_count + 1;
        }
    });
   //query form send starts here

    $(".afw-send-query").on("click", function(e){
        
    e.preventDefault();   
    var message = $("#adsforwp_query_message").val();    
    
    if($.trim(message) !=''){
     
            $.ajax({
                    type: "POST",    
                    url:adsforwp_localize_data.ajax_url,                    
                    dataType: "json",
                    data:{action:"adsforwp_send_query_message", message:message, adsforwp_security_nonce:adsforwp_localize_data.adsforwp_security_nonce},
                    success:function(response){                       
                      if(response['status'] =='t'){
                        $(".afw-query-success").show();
                        $(".afw-query-error").hide();
                      }else{
                        $(".afw-query-success").hide();  
                        $(".afw-query-error").show();
                      }
                    },
                    error: function(response){                    
                    console.log(response);
                    }
            });
        
    } else{
        alert('Please type message');
    }                                                           
    
});

    //Importer from schema plugin starts here
    
    
     $(".adsforwp-feedback-notice-close").on("click", function(e){
      e.preventDefault();               
                $.ajax({
                    type: "POST",    
                    url:adsforwp_localize_data.ajax_url,                    
                    dataType: "json",
                    data:{action:"adsforwp_review_notice_close", adsforwp_security_nonce:adsforwp_localize_data.adsforwp_security_nonce},
                    success:function(response){                       
                      if(response['status'] =='t'){
                       $(".adsforwp-feedback-notice").hide();
                      }
                    },
                    error: function(response){                    
                    console.log(response);
                    }
                    });
    
});

$(".adsforwp-feedback-notice-remindme").on("click", function(e){
      e.preventDefault();               
                $.ajax({
                    type: "POST",    
                    url:adsforwp_localize_data.ajax_url,                    
                    dataType: "json",
                    data:{action:"adsforwp_review_notice_remindme", adsforwp_security_nonce:adsforwp_localize_data.adsforwp_security_nonce},
                    success:function(response){                       
                      if(response['status'] =='t'){
                       $(".adsforwp-feedback-notice").hide();
                      }
                    },
                    error: function(response){                    
                    console.log(response);
                    }
                    });
    
});

    $(".adsforwp-import-plugins").on("click", function(e){
            e.preventDefault(); 
            var current_selection = $(this);
            current_selection.addClass('updating-message');
            var plugin_name = $(this).attr('data-id');                      
                         $.get(ajaxurl, 
                             { action:"adsforwp_import_plugin_data", plugin_name:plugin_name, adsforwp_security_nonce:adsforwp_localize_data.adsforwp_security_nonce},
                              function(response){                                  
                              if(response['status'] =='t'){                                  
                                  $(current_selection).parent().find(".adsforwp-imported-message").text(response['message']);
                                  $(current_selection).parent().find(".adsforwp-imported-message").removeClass('adsforwp-error');
                                   setTimeout(function(){ location.reload(); }, 2000);
                              }else{
                                  $(current_selection).parent().find(".adsforwp-imported-message").addClass('adsforwp-error');
                                  $(current_selection).parent().find(".adsforwp-imported-message").text(response['message']);                                 
                              }  
                              current_selection.removeClass('updating-message');
                             },'json');
        });
        
        $(".adsforwp-enable-click").on("click", function(e){
            e.preventDefault();
            $("#adsforwp_v_condition_enable").val('enable');
            $(".adsforwp-visitor-condition-div").hide();
            $(".adsforwp-visitor-condition-groups").removeClass('afw_hide');
        });
        
        
   
    $("#afw-amp-status-display").text(afw_amp_status);
    $(".afw-amp-edit-post-status").on("click", function(e){
        e.preventDefault();
        $(this).hide();
        $("#afw-amp-status-select").show();
    });
    $(".afw-amp-status-save").on("click", function(e){
        e.preventDefault();
        var select_val = $("#ads-for-wp_amp_compatibilty").val();
        $("#afw-amp-status-display").text(select_val);
        $("#afw-amp-status-select").hide();
        $(".afw-amp-edit-post-status").show();
    });
    $(".afw-amp-status-cancel").on("click", function(e){
        e.preventDefault();
        $("#ads-for-wp_amp_compatibilty").val(afw_amp_status);
        $("#afw-amp-status-display").text(afw_amp_status);
        $("#afw-amp-status-select").hide();
        $(".afw-amp-edit-post-status").show();
    });

    var afw_amp_status = $("#ads_for_wp_non_amp_visibility").val();
    $("#afw-non-amp-visib-status-display").text(afw_amp_status);
    $(".afw-non-amp-visib-status").on("click", function(e){
        e.preventDefault();
        $(this).hide();
        $("#afw-non-amp-visib-status-select").show();
    });
    $(".afw-non-amp-visib-save").on("click", function(e){
        e.preventDefault();
        var select_val = $("#ads_for_wp_non_amp_visibility").val();
        $("#afw-non-amp-visib-status-display").text(select_val);
        $("#afw-non-amp-visib-status-select").hide();
        $(".afw-non-amp-visib-status").show();
    });
    $(".afw-non-amp-visib-cancel").on("click", function(e){
        e.preventDefault();
        $("#ads_for_wp_non_amp_visibility").val(afw_amp_status);
        $("#afw-non-amp-visib-status-display").text(afw_amp_status);
        $("#afw-non-amp-visib-status-select").hide();
        $(".afw-non-amp-visib-status").show();
    });
    
    
    $(document).on("click", "input[media-id=media]" ,function(e) {	// Application Icon upload
		e.preventDefault();
                var button = $(this);
                var id = button.attr('id').replace('_button', '');                
		var saswpMediaUploader = wp.media({
			title: "Application Icon",
			button: {
				text: "Select Icon"
			},
			multiple: false,  // Set this to true to allow multiple files to be selected
                        library:{type : 'image'}
		})
		.on("select", function() {
			var attachment = saswpMediaUploader.state().get('selection').first().toJSON();                            
			
                         $("#"+id).val(attachment.url);
                         $("input[data-id='"+id+"_id']").val(attachment.id);
                         $("input[data-id='"+id+"_height']").val(attachment.height);
                         $("input[data-id='"+id+"_width']").val(attachment.width);
                         $("input[data-id='"+id+"_thumbnail']").val(attachment.url);
                         $(".afw_ad_img_div").html('<div class="afw_ad_thumbnail"><img class="afw_ad_image_prev" src="'+attachment.url+'"/><a href="#" class="afw_ad_prev_close">X</a></div>');
		})
		.open();
	});
    
    $(document).on("click",".adsforwp-reset-data", function(e){
                e.preventDefault();
             
                var ads_confirm = confirm("Are you sure?");
             
                if(ads_confirm == true){
                    
                $.ajax({
                            type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"adsforwp_reset_all_settings", adsforwp_security_nonce:adsforwp_localize_data.adsforwp_security_nonce},
                            success:function(response){                               
                                setTimeout(function(){ location.reload(); }, 1000);
                            },
                            error: function(response){                    
                            console.log(response);
                            }
                            });                 
                }                                                                 
        });
              
    if(adsforwp_localize_data.post_type === "adsforwp-groups" || adsforwp_localize_data.post_type === "adsforwp"){
        $("#wp-admin-bar-view").hide();
    }
//query form send ends here

    $(".afw-group-ads tbody").sortable();
    // setting shortcode on page load
        if(document.getElementById('manual_ads_type')){
            if(adsforwp_localize_data.post_type === "adsforwp-groups"){
           document.getElementById('manual_ads_type').value = '[adsforwp-group id="'+currentAdID+'"]';     
            }else{
           document.getElementById('manual_ads_type').value = '[adsforwp id="'+currentAdID+'"]';          
            }	
        }
        if(document.getElementById('adsforwp_group_shortcode')){            
	document.getElementById('adsforwp_group_shortcode').value = '[adsforwp-group id="'+currentAdID+'"]';
        }
        
        if(adsforwp_localize_data.current_page === "adsforwp_page_adsforwp"){
                 if ($('#adsforwp-hidden-block').length == 0 ) {
                       $(".afw-blocker-notice").show();
                 }else{
                       $(".afw-blocker-notice").hide(); 
                 }
        }
        
});