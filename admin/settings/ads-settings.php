<?php
add_action( 'admin_menu', 'adsforwp_add_admin_menu' );
add_action( 'admin_init', 'adsforwp_settings_init' );


function adsforwp_add_admin_menu(  ) { 

	add_menu_page( 'Ads for WP', 'Ads for WP', 'manage_options', 'ads_for_wp', 'adsforwp_options_page' );

}


function adsforwp_settings_init(  ) { 


	// Tab #1
	register_setting( 'pluginPage', 'adsforwp_settings' ); 

	$control = create_controls();
	create_setting_control($control);

	// add_settings_section(
	// 	'adsforwp_pluginPage_section', 
	//  	__( 'Your section description', 'ads-for-wp' ), 
	// 	'adsforwp_settings_section_callback', 
	// 	'pluginPage'
	// );

	add_settings_field( 
		'adsforwp_text_field_0', 
		__( 'Settings field description', 'ads-for-wp' ), 
		'adsforwp_text_field_0_render', 
		'pluginPage', 
		'opt-structured-data' 
	);
	add_settings_field( 
		'adsforwp_text_field_2', 
		__( 'dfgdfgdgdfgdgdfg', 'ads-for-wp' ), 
		'adsforwp_text_field_0_render', 
		'pluginPage_2', 
		'opt-structured-data-2' 
	);

	// add_settings_field( 
	// 	'adsforwp_checkbox_field_1', 
	// 	__( 'Settings field description', 'ads-for-wp' ), 
	// 	'adsforwp_checkbox_field_1_render', 
	// 	'pluginPage', 
	// 	'adsforwp_pluginPage_section' 
	// );

	// add_settings_field( 
	// 	'adsforwp_radio_field_2', 
	// 	__( 'Settings field description', 'ads-for-wp' ), 
	// 	'adsforwp_radio_field_2_render', 
	// 	'pluginPage', 
	// 	'adsforwp_pluginPage_section' 
	// );


}



function create_setting_control( $data ){

	$setting = array();

	foreach ($data as $key => $value) {

		$setting[$key]['id'] 			= $value['id'];
		$setting[$key]['title'] 		= $value['title'];
		$setting[$key]['page'] 			= $value['page'];

	}

	foreach ($setting as $key => $value) {
		add_settings_section($value['id'], $value['title'], 'adsforwp_settings_section_callback', $value['page']);
	}

	$total_fields = array();

	foreach ($data as $key => $value) {

			foreach ($data[$key]['fields'] as $fields_key => $fields_value) {			
				$total_fields[] = $data[$key]['fields'][$fields_key];
			}
	}
 


	$field = array();

	foreach ( $total_fields as $total_fields_key => $total_fields_value ) {
		 $field[$total_fields_key]['id'] 		= $total_fields_value['id'];
		 $field[$total_fields_key]['title'] 	= $total_fields_value['title'];
		 $field[$total_fields_key]['callback'] 	= 'adsforwp_radio_field_'.$total_fields_key.'_render';
		 $field[$total_fields_key]['page'] 		= $total_fields_value['page'];
		 $field[$total_fields_key]['section'] 	= $total_fields_value['section'];
		 $field[$total_fields_key]['type'] 		= $total_fields_value['type'];


		//var_dump( $total_fields_value['id'] );
		//var_dump( $total_fields_value['title'] );
	}

	foreach ($field as $field_key => $field_value) {

			add_settings_field( 
				$field_value['id'], 
				__( $field_value['title'], 'ads-for-wp' ), 
				$field_value['callback'], 
				$field_value['page'], 
				$field_value['section'] 
			);
	
	}



	// add_settings_field( 
	// 	'adsforwp_radio_field_2', 
	// 	__( 'Settings field description', 'ads-for-wp' ), 
	// 	'adsforwp_radio_field_2_render', 
	// 	'pluginPage', 
	// 	'adsforwp_pluginPage_section' 
	// );
	
	
//var_dump($data['0']['fields']);   
	//var_dump($new_data);
	// die;
	 
	 
}




require ( ADSFORWP_PLUGIN_DIR.'/admin/settings/render.php' );


function adsforwp_settings_section_callback( $arg ) { 
	echo '<p>title: ' . $arg['title'] . '</p>';       // title: Example settings section in reading
	

}


function adsforwp_options_page(  ) { 

	?>

	<h2>Ads for WP</h2>
	<?php settings_errors(); ?>

   <?php
        if( isset( $_GET[ 'tab' ] ) ) {
            $active_tab = $_GET[ 'tab' ];
        } // end if
    ?>
     
    <h2 class="nav-tab-wrapper">
        <a href="?page=ads_for_wp&tab=pluginPage" class="nav-tab <?php echo $active_tab == 'pluginPage' ? 'nav-tab-active' : ''; ?>">Display Options</a>
        <a href="?page=ads_for_wp&tab=pluginPage_2" class="nav-tab <?php echo $active_tab == 'pluginPage_2' ? 'nav-tab-active' : ''; ?>">Social Options</a>
        <a href="?page=ads_for_wp&tab=pluginPage_3" class="nav-tab <?php echo $active_tab == 'pluginPage_3' ? 'nav-tab-active' : ''; ?>"> #3 </a>
    </h2>

	<form action='options.php' method='post'>
		<?php

		if ( $active_tab == 'pluginPage' ) {

			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );

        } else if ( $active_tab == 'pluginPage_2' ) {

			settings_fields( 'pluginPage_2' );
			do_settings_sections( 'pluginPage_2' );

        } else {

            settings_fields( 'pluginPage_3' );
            do_settings_sections( 'pluginPage_3' );

        }

		submit_button();
		?>

	</form>
	<?php

}

 




function create_controls(){
	$section = array();

	$section[] =  array(
        'title'     	=> __( 'Section Heading Testing', 'ads-for-wp' ),
        'page'   		=> 'pluginPage',
        'id'        	=> 'opt-structured-data',
        'fields'		=> array(
    		array(
				'title' 		=> 'Control Heading',
				'id'			=> 'contol-id',
				'description' 	=> 'desc',
				'type'			=> 'text',
				'default' 		=> 'DEFAULT',
				'page'   		=> 'pluginPage',
				'section'   	=> 'opt-structured-data',
				'required'		=> array('some_other_id' => 1 )
			),			
			array(
				'title' 		=> 'fgh gfhgfgfhhgf',
				'id'			=> 'contol-id-1',
				'description' 	=> 'desc',
				'type'			=> 'text',
				'default' 		=> 'DEFAULT',
				'page'   		=> 'pluginPage',
				'section'   	=> 'opt-structured-data',
				'required'		=> array('some_other_id' => 1 )
			),			
			array(
				'title' 		=> '-------------------',
				'id'			=> 'contol-id-3',
				'description' 	=> 'desc',
				'type'			=> 'text',
				'default' 		=> 'DEFAULT',
				'page'   		=> 'pluginPage',
				'section'   	=> 'opt-structured-data',
				'required'		=> array('some_other_id' => 1 )
			)

		)
	);





	$section[] =  array(
        'title'     	=> __( 'Section #2 Heading', 'ads-for-wp' ),
        'page'   		=> 'pluginPage_2',
        'id'        	=> 'opt-structured-data-2',
        'fields'		=> array(
    		array(
				'title' 		=> 'Control Heading #2',
				'id'			=> 'contol-id-2',
				'description' 	=> 'desc',
				'type'			=> 'text',
				'default' 		=> 'DEFAULT',
				'page'   		=> 'pluginPage_2',
				'section'   	=> 'opt-structured-data-2',
				'required'		=> array('some_other_id_2' => 1 )
			)
		)
	);

	// var_dump($section);

	return $section;

}





/*
	// Tab #2
	register_setting( 'pluginPage_2', 'adsforwp_settings' ); 
	add_settings_section(
		'adsforwp_pluginPage_section_2', 
	 	__( 'Your section description #2', 'ads-for-wp' ), 
		'adsforwp_settings_section_callback', 
		'pluginPage_2'
	);

	add_settings_field( 
		'adsforwp_textarea_field_3', 
		__( 'Settings field description', 'ads-for-wp' ), 
		'adsforwp_textarea_field_3_render', 
		'pluginPage_2', 
		'adsforwp_pluginPage_section_2' 
	);

	add_settings_field( 
		'adsforwp_select_field_4', 
		__( 'Settings field description', 'ads-for-wp' ), 
		'adsforwp_select_field_4_render', 
		'pluginPage_2', 
		'adsforwp_pluginPage_section_2' 
	);

	// Tab #3
	register_setting( 'pluginPage_3', 'adsforwp_settings' ); 
	add_settings_section(
		'adsforwp_pluginPage_section_3', 
	 	__( 'Your section description #3', 'ads-for-wp' ), 
		'adsforwp_settings_section_callback', 
		'pluginPage_3'
	);

	add_settings_field( 
		'adsforwp_textarea_field_3', 
		__( 'Settings field description', 'ads-for-wp' ), 
		'adsforwp_textarea_field_3_render', 
		'pluginPage_3', 
		'adsforwp_pluginPage_section_3' 
	);

	add_settings_field( 
		'adsforwp_select_field_4', 
		__( 'Settings field description', 'ads-for-wp' ), 
		'adsforwp_select_field_4_render', 
		'pluginPage_3', 
		'adsforwp_pluginPage_section_3' 
	);



Working Saving fucntion
	function adsforwp_options_page(  ) { 

	?>

	<h2>Ads for WP</h2>
	<?php settings_errors(); ?>

   <?php
        if( isset( $_GET[ 'tab' ] ) ) {
            $active_tab = $_GET[ 'tab' ];
        } // end if
    ?>
     
    <h2 class="nav-tab-wrapper">
        <a href="?page=ads_for_wp&tab=pluginPage" class="nav-tab <?php echo $active_tab == 'pluginPage' ? 'nav-tab-active' : ''; ?>">Display Options</a>
        <a href="?page=ads_for_wp&tab=pluginPage_2" class="nav-tab <?php echo $active_tab == 'pluginPage_2' ? 'nav-tab-active' : ''; ?>">Social Options</a>
        <a href="?page=ads_for_wp&tab=pluginPage_3" class="nav-tab <?php echo $active_tab == 'pluginPage_3' ? 'nav-tab-active' : ''; ?>"> #3 </a>
    </h2>

	<form action='options.php' method='post'>
		<?php

		if ( $active_tab == 'pluginPage' ) {

			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );

        } else if ( $active_tab == 'pluginPage_2' ) {

			settings_fields( 'pluginPage_2' );
			do_settings_sections( 'pluginPage_2' );

        } else {

            settings_fields( 'pluginPage_3' );
            do_settings_sections( 'pluginPage_3' );

        }

		submit_button();
		?>

	</form>
	<?php

}


*/