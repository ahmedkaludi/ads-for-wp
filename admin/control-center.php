<?php
add_action( 'init', 'adsforwp_setup_post_type' );

function adsforwp_setup_post_type() {
    $args = array(
      'labels' => array(
        'name' 			=> esc_html__( 'Ads', 'ads-for-wp' ),
        'singular_name' => esc_html__( 'Ad', 'ads-for-wp' )
      ),
      	'public' 		=> true,
      	'has_archive' => false,
      	'exclude_from_search'	=> true,
    	'publicly_queryable'	=> false
    );
    register_post_type( 'ads-for-wp-ads', $args );
}


/*
 * Hiding Visaul Editor part, as there is no need for Visual Editor to add Advert Code 
*/
add_filter( 'user_can_richedit', 'adsforwp_hide_visual_editor');

function adsforwp_hide_visual_editor($content) {
    global $post_type;

    if ('ads-for-wp-ads' == $post_type)
        return false;
    return $content;
}

/*
 * Generating Ad ShortCode
 */

add_shortcode('ads-for-wp', 'adsforwp_shortcode_generator');
function adsforwp_shortcode_generator( $atts ){

	$content = '';
	$show_ads 	= '';

	$show_ads = 'yes';		
	$show_ads = apply_filters('adsforwp_advert_on_off', $show_ads);

	if ( $show_ads == 'yes' ) {
		$content = get_post_field('post_content', $atts['ads-id']);
	}

	return $content ;
}




add_action('admin_footer', function(){ ?>
	<script type="text/javascript">

		jQuery( document ).ready(function($) {


			/* ADS CPT */
			var currentSelectedField 	= $('#adsforwp-current-ad-type').val();
			var adsforwpGlobalCode 		= $('#adsforwp_position_global_code');		
			var adsforwpSpecificCode 	= $('#adsforwp_ads_position_specific_controls');

			if ( currentSelectedField == 'show' ) {
				$(adsforwpGlobalCode).show();
				$(adsforwpSpecificCode).slideUp();
			} else {
				$(adsforwpGlobalCode).hide();
				$(adsforwpSpecificCode).slideDown();
			}

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


			// function marked() {        // define event handler
			//         var $this   = $(this),
			//             parent = $this.parent(".ads-child");
			//             siblings = $this.siblings(".ads-visibility");

			//             //saveButton = '<label class="ads-save">Save</label>';

			//             $(siblings).prop('disabled', false);
			//            // $this.before(saveButton);
			//     }


			//     $('.edit-ads').on('click',marked);


			// function functionToRun(){
			// 	var $this = $(this);
			// 	console.log( $this );

			// }

			// $('#adsforwp-all-ads').on('click', function(){
			// 	console.log(this);
			// });


			ajaxURL 		= "<?php echo admin_url( 'admin-ajax.php' );?>";
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

		});
		
	</script>
	<style>
		.edit-ads:hover,
		.save-ads:hover {
			cursor: pointer;
		}
		
		.edit-ads,
		.save-ads {
			background: #006799;
			color: #fff;
			margin: 5px;
			padding: 0 5px;
		}
	</style>
	 <?php

});



add_action( 'wp_ajax_save_ads_data', 'adsforwp_save_ads_data' );

function adsforwp_save_ads_data() {
    // Handle request then generate response using WP_Ajax_Response
	$data 		= "";
	$save_data 	= array();

	$data 		= $_POST['adsdata'];
	
	//echo $data['post_id'];
	//echo $data['adsid'];
	// echo $data['visibility'];
	// echo $data['paragraph'];

 
	//$save_data[] = '';

	//if ( ! array_key_exists($data['adsid'], $data) ) {
	$save_data[ $data['ads_id'] ] = $data;
	//}

	//echo json_encode($save_data);

	

	// var_dump($_POST['adsdata']);


	$current_post_meta = get_post_meta($data['post_id'], 'new-data-daala', true);

	if (  $current_post_meta  ) {
		$save_data = array_replace_recursive($current_post_meta, $save_data);
	}

	update_post_meta($data['post_id'], 'new-data-daala', $save_data);

	// Send the updated and final data back to ajax so it can update the view dynamically
	echo json_encode($save_data);	
    // Don't forget to stop execution afterward.
    wp_die();
}


add_action('wp_head', 'add_action_in_header');
function add_action_in_header(){

	 global $post;

	// echo "$post->ID <br />";

	$post_meta = get_post_meta($post->ID, 'new-data-daala', true);

	var_dump($post_meta);

	// $clean_array = unserialize( $post_meta[0] );

	// var_dump($post_meta);

	// $post_meta_1 =  array(
	// '2524' => 
	//     array  (
	//       'post_id' 	=>  '2474',
	//       'ads_id' 		=>  '2524',
	//       'visibility' 	=>  'show',
	//       'paragraph' 	=>  '2',
	//   	),
	// '2663' => 
	//     array  (
	//       'post_id' 	=>  '2570',
	//       'ads_id' 		=>  '2524',
	//       'visibility' 	=>  'HIDE',
	//       'paragraph' 	=>  '55',
	//   	),

 //  	);
 //  	$post_meta_2 =  array(
	// '4444' => 
	//     array  (
	//       'post_id' 	=>  '333',
	//       'ads_id' 		=>  '25424',
	//       'visibility' 	=>  'HIvfDE',
	//       'paragraph' 	=>  '5522',
	//   	),

 //  	);
 //  	$post_meta_3 =  array(
	// '4444' => 
	//     array  (
	//       'post_id' 	=>  '333',
	//       'ads_id' 		=>  '25424',
	//       'visibility' 	=>  'HIvfDE',
	//       'paragraph' 	=>  '5522',
	//   	),

 //  	);


 
 // 	$post_meta_5 =  array_replace_recursive($post_meta_1, $post_meta_2, $post_meta_3);

 	 




 //  	 $post_meta_3 =  array_merge($post_meta_1, $post_meta_2);
 //  	 $post_meta_4 = array_merge_recursive($post_meta_1, $post_meta_2);

	// var_dump($post_meta_3);
	// var_dump($post_meta_4);
	// var_dump($post_meta_5);



	echo "this";

}