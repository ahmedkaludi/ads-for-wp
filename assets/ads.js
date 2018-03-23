jQuery( document ).ready(function($) {
	/* ADS CPT */
	var currentGlobalAdsField 	= $('#adsforwp-current-ad-default').val();
	var currentSelectedField 	= $('#adsforwp-current-ad-type').val();
	var adsforwpGlobalCode 		= $('#adsforwp_position_global_code');		
	var adsforwpSpecificCode 	= $('#adsforwp_ads_position_specific_controls');
	var adsWrapper 				= $('#adsforwp-ads-control-wrapper');
	var currentAdID       		= adsforwp_localize_data.id;
	// alert(currentAdID);
$("#select_ads_for").change(function(){
      $(this).find("option:selected").each(function(){
          var optionValue = $(this).attr("value");
          var optionHtml = $(this).html().toLowerCase();
          if(optionValue){
          		$("[id=ampforwp_adsforwp_metabox]").hide();
          		$("[id=ampforwp_adsforwp_for_amp_metabox]").hide();
              if('ampforwp' == optionHtml){
          		$("[id=ampforwp_adsforwp_metabox]").show();
              }
              if('amp by automattic' == optionHtml){
              	$("[id=ampforwp_adsforwp_for_amp_metabox]").show();
              }
          }      
      });
    }).change();

// AMPforWP Ads Options




 $("#ad_type_format").change(function(){
      $(this).find("option:selected").each(function(){
          var optionValue = $(this).attr("value");
          if(optionValue){
              $(".ad-type").hide();
              $("[id=adsense_parallax]").hide();
              $("[id=dfp_parallax]").hide();
              $("[id=custom_parallax]").hide();	
              $("[id=medianet_parallax]").hide();
              $(".ad-type.ad-type-" + optionValue).show();
              
              if('2' == optionValue){
              	 $("[id=adsense_parallax]").show();
              	 $("[id=dfp_parallax]").show();
              	  $("[id=custom_parallax]").show();
                  $("[id=medianet_parallax]").show();
              }
              

          } else{
              $(".ad-type").hide();
          }

      });
    }).change();



  $("#ad_vendor").change(function(){
      $(this).find("option:selected").each(function(){
          var optionValue = $(this).attr("value");
          var optionHtml = $(this).html().toLowerCase();
          if(optionValue){
              $(".vendor-fields").hide();
              $(".vendor-fields."+optionHtml+"-data-" + optionValue).show();
          } else{
              $(".vendor-data").hide();
          }         
      });
    }).change();

  $("#link_ads_dimensions").change(function(){
    $(this).find("option:selected").each(function(){
      var optionValue = $(this).attr("value");
      if(optionValue){
        $(".link-custom-dimensions").hide();
        if('7' === optionValue){
          $(".link-custom-dimensions.link-custom-data-1").show();
        }
        
      }
    });
  }).change();

  $("#adsense_dimensions").change(function(){
  	$(this).find("option:selected").each(function(){
  		var optionValue = $(this).attr("value");
  		if(optionValue){
  			$(".custom-dimensions").hide();
  			if('8' === optionValue){
  				$(".custom-dimensions.adsense-custom-data-1").show();
  			}
  			
  		}
  	});
  }).change();

    $("#dfp_dimensions").change(function(){
  	$(this).find("option:selected").each(function(){
  		var optionValue = $(this).attr("value");
  		if(optionValue){
  			$(".custom-dimensions").hide();
  			if('8' === optionValue){
  				$(".custom-dimensions.dfp-custom-data-2").show();
  			}
  			
  		}
  	});
  }).change();

  $("#medianet_dimensions").change(function(){
    $(this).find("option:selected").each(function(){
      var optionValue = $(this).attr("value");
      if(optionValue){
        $(".custom-dimensions").hide();
        if('8' === optionValue){
          $(".custom-dimensions.medianet-custom-data-4").show();
        }
        
      }
    });
  }).change();

$("#adsense_link").change(function(){
	$(".link-ads-dimensions").hide();
	$(".cmb2-id-adsense-dimensions").show();
 var checkbox =  $('#adsense_link').is(":checked");
	if(checkbox){
		$(".link-ads-dimensions").show();
		$(".cmb2-id-adsense-dimensions").hide();
	}
 
}).change();

$("#adsense_responsive").change(function(){
  
 var checkbox =  $('#adsense_responsive').is(":checked");
  if(checkbox){
    $("#adsense_link").hide();
    $("#adsense_dimensions").hide();
   /* $("#optimize_ads").hide();*/
    
  }
  else{
    $("#adsense_link").show();
     $("#adsense_dimensions").show();
    /* $("#optimize_ads").show();*/
  }
 
}).change();

// AMP By AUTOMATTIC Ads Options

$("#_amp_ad_type_format").change(function(){
      $(this).find("option:selected").each(function(){
          var optionValue = $(this).attr("value");
          if(optionValue){
              $(".amp-ad-type").hide();
              $("[id=_amp_adsense_parallax]").hide();
              $("[id=_amp_dfp_parallax]").hide();
              $("[id=_amp_custom_parallax]").hide();
               $("[id=_amp_medianet_parallax]").hide();
              $(".amp-ad-type.amp-ad-type-" + optionValue).show();
              if('2' == optionValue){
              	 $("[id=_amp_adsense_parallax]").show();
              	 $("[id=_amp_dfp_parallax]").show();
              	  $("[id=_amp_custom_parallax]").show();
                   $("[id=_amp_medianet_parallax]").show();
              }
          } else{
              $(".amp-ad-type").hide();
          }         
      });
    }).change();

  $("#_amp_ad_vendor").change(function(){
      $(this).find("option:selected").each(function(){
          var optionValue = $(this).attr("value");
          var optionHtml = $(this).html().toLowerCase();
          if(optionValue){
              $(".amp-vendor-fields").hide();
              $(".amp-vendor-fields.amp-"+optionHtml+"-data-" + optionValue).show();
          } else{
              $(".amp-vendor-fields").hide();
          }         
      });
    }).change();

  $("#_amp_link_ads_dimensions").change(function(){
    $(this).find("option:selected").each(function(){
      var optionValue = $(this).attr("value");
      if(optionValue){
        $(".amp-link-custom-dimensions").hide();
        if('7' === optionValue){
          $(".amp-link-custom-dimensions.amp-link-custom-data-1").show();
        }
        
      }
    });
  }).change();

  $("#_amp_adsense_dimensions").change(function(){
  	$(this).find("option:selected").each(function(){
  		var optionValue = $(this).attr("value");
  		if(optionValue){
  			$(".amp-custom-dimensions").hide();
  			if('8' === optionValue){
  				$(".amp-custom-dimensions.amp-adsense-custom-data-1").show();
  			}
  			
  		}
  	});
  }).change();

    $("#_amp_dfp_dimensions").change(function(){
  	$(this).find("option:selected").each(function(){
  		var optionValue = $(this).attr("value");
  		if(optionValue){
  			$(".amp-custom-dimensions").hide();
  			if('8' === optionValue){
  				$(".amp-custom-dimensions.amp-dfp-custom-data-2").show();
  			}
  			
  		}
  	});
  }).change();

  $("#_amp_medianet_dimensions").change(function(){
    $(this).find("option:selected").each(function(){
      var optionValue = $(this).attr("value");
      if(optionValue){
        $(".amp-custom-dimensions").hide();
        if('8' === optionValue){
          $(".amp-custom-dimensions.amp-medianet-custom-data-4").show();
        }
        
      }
    });
  }).change();

$("#_amp_adsense_link").change(function(){
	$(".amp-link-ads-dimensions").hide();
	$(".cmb2-id--amp-adsense-dimensions").show();
 var checkbox =  $('#_amp_adsense_link').is(":checked");
	if(checkbox){
		$(".amp-link-ads-dimensions").show();
		$(".cmb2-id--amp-adsense-dimensions").hide();
	}
 
}).change();

$("#_amp_adsense_responsive").change(function(){
  
 var checkbox =  $('#adsense_responsive').is(":checked");
  if(checkbox){
    $("#_amp_adsense_link").hide();
    $("#_amp_adsense_dimensions").hide();
   /* $("#_amp_optimize_ads").hide();*/
    
  }
  else{
    $("#_amp_adsense_link").show();
     $("#_amp_adsense_dimensions").show();
     /*$("#_amp_optimize_ads").show();*/
  }
 
}).change();

	if ( currentGlobalAdsField == 'show' ) {
		$(adsWrapper).show();
	} else {
		$(adsWrapper).hide();
	}

	if ( currentSelectedField == 'show' ) {
		$(adsforwpGlobalCode).show();
		$(adsforwpSpecificCode).slideUp();
	} else {
		$(adsforwpGlobalCode).hide();
		$(adsforwpSpecificCode).slideDown();
	}

	$('#adsforwp_ads_controller_default_show').on('click', function() {
		$(adsWrapper).slideDown();
	});

	$('#adsforwp_ads_controller_default_hide').on('click', function() {
		$(adsWrapper).slideUp();
	});

	$('#adsforwp_ads_position_global').on('click', function() {
		$(adsforwpGlobalCode).show();
		$(adsforwpSpecificCode).slideUp();
	});

	$('#adsforwp_ads_position_specific').on('click', function() {
		$(adsforwpGlobalCode).hide();
		$(adsforwpSpecificCode).slideDown();
	});

	

	/* Global */
	var singleAdsStatus = $('#adsforwp-current-ad-status').val(); 
	if ( singleAdsStatus == 'show') {
		$('#adsforwp-all-ads').show();
	} else {
		$('#adsforwp-all-ads').hide();
	}

	$('.adsforwp-ads-controls').on('change', '#adsforwp_ads_meta_box_radio_show', function(){
		$('#adsforwp-all-ads').show();
	} );

	$('.adsforwp-ads-controls').on('change', '#adsforwp_ads_meta_box_radio_hide', function(){
		$('#adsforwp-all-ads').hide();
	} );

	ajaxURL 		= adsforwp_localize_data.ajax_url;
	currentPostID 	= $("#current-post-id").val();

	$("#adsforwp-all-ads").on('click','span', function() {
		var adsID, visibility,paragraph, currentClass, parentElement, saveAds, editAds ;

		currentClass 		= $(this).attr("class");
		parentElement 		= $(this).parent();

		// Get parents of all the elements
		saveAds 			= $(parentElement[0]).children('.save-ads');
		editAds  			= $(parentElement[0]).children('.edit-ads');
		visibilityParent 	= $(parentElement).find('select');
		paragraphParent 	= $(parentElement).find('input');
		
		// Get proper value
		parentElement		= parentElement[0];
		visibility 			= visibilityParent[0];
		paragraph  			= paragraphParent[0];
		saveAds 			= saveAds[0];
		editAds  			= editAds[0];

		// Values of ads sending via ajax
		adsID 				= $(parentElement).attr('data-ads-id');
		visibility 			= $(visibility).attr('data-ad-visibility');
		paragraph 			= $(paragraph).attr('data-ad-paragraph');

		// Get the new values and update it if it is changed by the user
		new_visibility_data = $(visibilityParent[0]).val();
		new_paragraph 		= $(paragraphParent[0]).val();

		if ( paragraph != new_paragraph) {
			paragraph = new_paragraph;
		}
		if ( visibility != new_visibility_data) {
			visibility = new_visibility_data;
		}
		
		// Edit Button is pressed
		if ( currentClass == 'edit-ads') {
			$(saveAds).show();
			$(editAds).hide();

			// Enable Fields to edit 
			$(visibilityParent[0]).prop('disabled', false); 
			$(paragraphParent[0]).prop('disabled', false);
		}

		// Save Button is pressed and values will be updated via AJAX
		if ( currentClass == 'save-ads') {
			$(saveAds).hide();
			$(editAds).show();

        	// Disable the fields back
			$(visibilityParent[0]).prop('disabled', 'disabled'); 
			$(paragraphParent[0]).prop('disabled', 'disabled'); 

			// Start the Spinner
			var spinnerDiv = $(parentElement).find('.spinner');
			$( spinnerDiv[0] ).css('visibility','visible');

			// Ajax will run now.
		      $.ajax({
		        url : ajaxURL,
		        method : "POST",			        
		        data: { 
		       		action: "save_ads_data", 	
		          	adsdata:{
		          		post_id 	: currentPostID,
			          	ads_id 		: adsID,
			          	visibility 	: visibility,
			          	paragraph 	: paragraph,
		          	},
		        },
		        beforeSend: function(){ 
		        },
		        success: function(data){
		        	console.log( 'Done !!!');
		        	$( spinnerDiv[0] ).css('visibility','hidden');
		        },
		        error: function(data){
		          console.log('Request Failed');
		          console.log(data);
		        }
		      }); // End of Ajax
		}
	});
// Correct Shortcode added
if(document.getElementById('manual_ad_type')){
	document.getElementById('manual_ad_type').value = '[ads-for-wp ads-id="'+currentAdID+'"]';}
	if(document.getElementById('_amp_manual_ad_type')){
	document.getElementById('_amp_manual_ad_type').value = '[ads-for-wp ads-id="'+currentAdID+'"]';}


});
