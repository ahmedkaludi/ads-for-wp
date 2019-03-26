<?php
class adsforwp_ajax_selectbox{
    
public function __construct() {
    
      add_action('wp_ajax_adsforwp_create_ajax_select_box',array($this,'adsforwp_ajax_select_creator')); 
      add_action('wp_ajax_adsforwp_ajax_select_taxonomy',array($this,'adsforwp_create_ajax_select_taxonomy'));      
      add_action('wp_ajax_adsforwp_visitor_condition_type_values',array($this,'adsforwp_visitor_condition_type_values'));
}
    
public function adsforwp_post_type_generator(){

    $post_types = '';
    $post_types = get_post_types( array( 'public' => true ), 'names' );
    
    unset($post_types['attachment'], $post_types['adsforwp'], $post_types['adsforwp-groups']);

    return $post_types;
}


public function adsforwp_visitor_condition_type_values($data = '', $saved_data= '',$selected_val_key_4='',$selected_val_key_5='', $current_number = '', $current_group_number  = '') {
   
    $adsforwp_settings = adsforwp_defaultSettings();
    
    $response = $data;
    $is_ajax = false;
    if( $_SERVER['REQUEST_METHOD']=='POST'){
        
        $is_ajax = true;
        
        if(wp_verify_nonce($_POST["adsforwp_visitor_condition_call_nonce"],'adsforwp_visitor_condition_action_nonce')){   
            
            if ( isset( $_POST["id"] ) ) {
                
                    $response = sanitize_text_field(wp_unslash($_POST["id"]));
              
            }
            
            if ( isset( $_POST["number"] ) ) {
                
                    $current_number   = intval($_POST["number"]);
              
            }
            
            if ( isset( $_POST["group_number"] ) ) {
                
                    $current_group_number   = intval($_POST["group_number"]);
              
            }
        }else{            
            exit;
        }
       
    }        
        // send the response back to the front end
       // vars
    $choices = array();      
    
    $options['param'] = $response;
      
        switch($options['param'])
        {
          case "device":

            $choices = array(
                'desktop' => 'Desktop',
                'mobile'  => 'Mobile or Tablet',                             
            );                       
            break;
          
          case "referrer_url":
               $choices = array(
                'https://www.google.com/' => 'Google',
                'https://www.bing.com/'   => 'Bing',
                'https://www.yahoo.com/'  => 'Yahoo',
                'url_custom' => 'Custom',   
            );
            break;
      
          case "geo_location":
               $choices = array(
                'AFG' =>  'Afghanistan',
                'ALB' =>  'Albania',
                'DZA' =>  'Algeria',
                'ASM' =>  'American Samoa',
                'AND' =>  'Andorra',
                'AGO' =>  'Angola',
                'AIA' =>  'Anguilla',
                'ATA' =>  'Antarctica',
                'ATG' =>  'Antigua and Barbuda',
                'ARG' =>  'Argentina',
                'ARM' =>  'Armenia',
                'ABW' =>  'Aruba',
                'AUS' =>  'Australia',
                'AUT' =>  'Austria',
                'AZE' =>  'Azerbaijan',
                'BHS' =>  'Bahamas',
                'BHR' =>  'Bahrain',
                'BGD' =>  'Bangladesh',
                'BRB' =>  'Barbados',
                'BLR' =>  'Belarus',
                'BEL' =>  'Belgium',
                'BLZ' =>  'Belize',
                'BEN' =>  'Benin',
                'BMU' =>  'Bermuda',
                'BTN' =>  'Bhutan',
                'BOL' =>  'Bolivia',
                'BIH' =>  'Bosnia and Herzegovina',
                'BWA' =>  'Botswana',
                'BRA' =>  'Brazil',
                'IOT' =>  'British Indian Ocean Territory',
                'VGB' =>  'British Virgin Islands',
                'BRN' =>  'Brunei',
                'BGR' =>  'Bulgaria',
                'BFA' =>  'Burkina Faso',
                'BDI' =>  'Burundi',
                'KHM' =>  'Cambodia',
                'CMR' =>  'Cameroon',
                'CAN' =>  'Canada',
                'CPV' =>  'Cape Verde',
                'CYM' =>  'Cayman Islands',
                'CAF' =>  'Central African Republic',
                'TCD' =>  'Chad',
                'CHL' =>  'Chile',
                'CHN' =>  'China',
                'CXR' =>  'Christmas Island',
                'CCK' =>  'Cocos Islands',
                'COL' =>  'Colombia',
                'COM' =>  'Comoros',
                'COK' =>  'Cook Islands',
                'CRI' =>  'Costa Rica',
                'HRV' =>  'Croatia',
                'CUB' =>  'Cuba',
                'CUW' =>  'Curacao',
                'CYP' =>  'Cyprus',
                'CZE' =>  'Czech Republic',
                'COD' =>  'Democratic Republic of the Congo',
                'DNK' =>  'Denmark',
                'DJI' =>  'Djibouti',
                'DMA' =>  'Dominica',
                'DOM' =>  'Dominican Republic',
                'TLS' =>  'East Timor',
                'ECU' =>  'Ecuador',
                'EGY' =>  'Egypt',
                'SLV' =>  'El Salvador',
                'GNQ' =>  'Equatorial Guinea',
                'ERI' =>  'Eritrea',
                'EST' =>  'Estonia',
                'ETH' =>  'Ethiopia',
                'FLK' =>  'Falkland Islands',
                'FRO' =>  'Faroe Islands',
                'FJI' =>  'Fiji',
                'FIN' =>  'Finland',
                'FRA' =>  'France',
                'PYF' =>  'French Polynesia',
                'GAB' =>  'Gabon',
                'GMB' =>  'Gambia',
                'GEO' =>  'Georgia',
                'DEU' =>  'Germany',
                'GHA' =>  'Ghana',
                'GIB' =>  'Gibraltar',
                'GRC' =>  'Greece',
                'GRL' =>  'Greenland',
                'GRD' =>  'Grenada',
                'GUM' =>  'Guam',
                'GTM' =>  'Guatemala',
                'GGY' =>  'Guernsey',
                'GIN' =>  'Guinea',
                'GNB' =>  'Guinea-Bissau',
                'GUY' =>  'Guyana',
                'HTI' =>  'Haiti',
                'HND' =>  'Honduras',
                'HKG' =>  'Hong Kong',
                'HUN' =>  'Hungary',
                'ISL' =>  'Iceland',
                'IND' =>  'India',
                'IDN' =>  'Indonesia',
                'IRN' =>  'Iran',
                'IRQ' =>  'Iraq',
                'IRL' =>  'Ireland',
                'IMN' =>  'Isle of Man',
                'ISR' =>  'Israel',
                'ITA' =>  'Italy',
                'CIV' =>  'Ivory Coast',
                'JAM' =>  'Jamaica',
                'JPN' =>  'Japan',
                'JEY' =>  'Jersey',
                'JOR' =>  'Jordan',
                'KAZ' =>  'Kazakhstan',
                'KEN' =>  'Kenya',
                'KIR' =>  'Kiribati',
                'XKX' =>  'Kosovo',
                'KWT' =>  'Kuwait',
                'KGZ' =>  'Kyrgyzstan',
                'LAO' =>  'Laos',
                'LVA' =>  'Latvia',
                'LBN' =>  'Lebanon',
                'LSO' =>  'Lesotho',
                'LBR' =>  'Liberia',
                'LBY' =>  'Libya',
                'LIE' =>  'Liechtenstein',
                'LTU' =>  'Lithuania',
                'LUX' =>  'Luxembourg',
                'MAC' =>  'Macau',
                'MKD' =>  'Macedonia',
                'MDG' =>  'Madagascar',
                'MWI' =>  'Malawi',
                'MYS' =>  'Malaysia',
                'MDV' =>  'Maldives',
                'MLI' =>  'Mali',
                'MLT' =>  'Malta',
                'MHL' =>  'Marshall Islands',
                'MRT' =>  'Mauritania',
                'MUS' =>  'Mauritius',
                'MYT' =>  'Mayotte',
                'MEX' =>  'Mexico',
                'FSM' =>  'Micronesia',
                'MDA' =>  'Moldova',
                'MCO' =>  'Monaco',
                'MNG' =>  'Mongolia',
                'MNE' =>  'Montenegro',
                'MSR' =>  'Montserrat',
                'MAR' =>  'Morocco',
                'MOZ' =>  'Mozambique',
                'MMR' =>  'Myanmar',
                'NAM' =>  'Namibia',
                'NRU' =>  'Nauru',
                'NPL' =>  'Nepal',
                'NLD' =>  'Netherlands',
                'ANT' =>  'Netherlands Antilles',
                'NCL' =>  'New Caledonia',
                'NZL' =>  'New Zealand',
                'NIC' =>  'Nicaragua',
                'NER' =>  'Niger',
                'NGA' =>  'Nigeria',
                'NIU' =>  'Niue',
                'PRK' =>  'North Korea',
                'MNP' =>  'Northern Mariana Islands',
                'NOR' =>  'Norway',
                'OMN' =>  'Oman',
                'PAK' =>  'Pakistan',
                'PLW' =>  'Palau',
                'PSE' =>  'Palestine',
                'PAN' =>  'Panama',
                'PNG' =>  'Papua New Guinea',
                'PRY' =>  'Paraguay',
                'PER' =>  'Peru',
                'PHL' =>  'Philippines',
                'PCN' =>  'Pitcairn',
                'POL' =>  'Poland',
                'PRT' =>  'Portugal',
                'PRI' =>  'Puerto Rico',
                'QAT' =>  'Qatar',
                'COG' =>  'Republic of the Congo',
                'REU' =>  'Reunion',
                'ROU' =>  'Romania',
                'RUS' =>  'Russia',
                'RWA' =>  'Rwanda',
                'BLM' =>  'Saint Barthelemy',
                'SHN' =>  'Saint Helena',
                'KNA' =>  'Saint Kitts and Nevis',
                'LCA' =>  'Saint Lucia',
                'MAF' =>  'Saint Martin',
                'SPM' =>  'Saint Pierre and Miquelon',
                'VCT' =>  'Saint Vincent and the Grenadines',
                'WSM' =>  'Samoa',
                'SMR' =>  'San Marino',
                'STP' =>  'Sao Tome and Principe',
                'SAU' =>  'Saudi Arabia',
                'SEN' =>  'Senegal',
                'SRB' =>  'Serbia',
                'SYC' =>  'Seychelles',
                'SLE' =>  'Sierra Leone',
                'SGP' =>  'Singapore',
                'SXM' =>  'Sint Maarten',
                'SVK' =>  'Slovakia',
                'SVN' =>  'Slovenia',
                'SLB' =>  'Solomon Islands',
                'SOM' =>  'Somalia',
                'ZAF' =>  'South Africa',
                'KOR' =>  'South Korea',
                'SSD' =>  'South Sudan',
                'ESP' =>  'Spain',
                'LKA' =>  'Sri Lanka',
                'SDN' =>  'Sudan',
                'SUR' =>  'Suriname',
                'SJM' =>  'Svalbard and Jan Mayen',
                'SWZ' =>  'Swaziland',
                'SWE' =>  'Sweden',
                'CHE' =>  'Switzerland',
                'SYR' =>  'Syria',
                'TWN' =>  'Taiwan',
                'TJK' =>  'Tajikistan',
                'TZA' =>  'Tanzania',
                'THA' =>  'Thailand',
                'TGO' =>  'Togo',
                'TKL' =>  'Tokelau',
                'TON' =>  'Tonga',
                'TTO' =>  'Trinidad and Tobago',
                'TUN' =>  'Tunisia',
                'TUR' =>  'Turkey',
                'TKM' =>  'Turkmenistan',
                'TCA' =>  'Turks and Caicos Islands',
                'TUV' =>  'Tuvalu',
                'VIR' =>  'U.S. Virgin Islands',
                'UGA' =>  'Uganda',
                'UKR' =>  'Ukraine',
                'ARE' =>  'United Arab Emirates',
                'GBR' =>  'United Kingdom',
                'USA' =>  'United States',
                'URY' =>  'Uruguay',
                'UZB' =>  'Uzbekistan',
                'VUT' =>  'Vanuatu',
                'VAT' =>  'Vatican',
                'VEN' =>  'Venezuela',
                'VNM' =>  'Vietnam',
                'WLF' =>  'Wallis and Futuna',
                'ESH' =>  'Western Sahara',
                'YEM' =>  'Yemen',
                'ZMB' =>  'Zambia',
                'ZWE' =>  'Zimbabwe',   
            );
            break;
        
          case "logged_in_visitor":

            $choices = array(
                'true'  => 'True',
                'false' => 'False',                                
            );                       
            break; 
            
          case "user_agent":

            $choices = array(
                'opera'             => 'Opera',
                'edge'              => 'Edge',
                'chrome'            => 'Chrome',
                'safari'            => 'Safari',
                'firefox'           => 'Firefox',
                'internet_explorer' => 'MSIE',
                'android'           => 'Android',               
                'iphone'            => 'iPhone',
                'ipad'              => 'iPad',
                'ipod'              => 'iPod',                                
                'user_agent_custom' => 'Custom',
            );                       
            break; 
        
          case "user_type" :
              
           global $wp_roles;
              
            $choices = $wp_roles->get_names();

            if( is_multisite() )
            {
              $choices['super_admin'] = esc_html__('Super Admin','ads-for-wp');
            }

            break;
            
          case "browser_language" :
            $choices = array(
                    'af' => 'Afrikanns',
                    'sq' => 'Albanian',
                    'ar' => 'Arabic',
                    'hy' => 'Armenian',
                    'eu' => 'Basque',
                    'bn' => 'Bengali',
                    'bg' => 'Bulgarian',
                    'ca' => 'Catalan',
                    'km' => 'Cambodian',
                    'zh' => 'Chinese (Mandarin)',
                    'hr' => 'Croation',
                    'cs' => 'Czech',
                    'da' => 'Danish',
                    'nl' => 'Dutch',
                    'en' => 'English',
                    'et' => 'Estonian',
                    'fj' => 'Fiji',
                    'fi' => 'Finnish',
                    'fr' => 'French',
                    'ka' => 'Georgian',
                    'de' => 'German',
                    'el' => 'Greek',
                    'gu' => 'Gujarati',
                    'he' => 'Hebrew',
                    'hi' => 'Hindi',
                    'hu' => 'Hungarian',
                    'is' => 'Icelandic',
                    'id' => 'Indonesian',
                    'ga' => 'Irish',
                    'it' => 'Italian',
                    'ja' => 'Japanese',
                    'jw' => 'Javanese',
                    'ko' => 'Korean',
                    'la' => 'Latin',
                    'lv' => 'Latvian',
                    'lt' => 'Lithuanian',
                    'mk' => 'Macedonian',
                    'ms' => 'Malay',
                    'ml' => 'Malayalam',
                    'mt' => 'Maltese',
                    'mi' => 'Maori',
                    'mr' => 'Marathi',
                    'mn' => 'Mongolian',
                    'ne' => 'Nepali',
                    'no' => 'Norwegian',
                    'fa' => 'Persian',
                    'pl' => 'Polish',
                    'pt' => 'Portuguese',
                    'pa' => 'Punjabi',
                    'qu' => 'Quechua',
                    'ro' => 'Romanian',
                    'ru' => 'Russian',
                    'sm' => 'Samoan',
                    'sr' => 'Serbian',
                    'sk' => 'Slovak',
                    'sl' => 'Slovenian',
                    'es' => 'Spanish',
                    'sw' => 'Swahili',
                    'sv' => 'Swedish ',
                    'ta' => 'Tamil',
                    'tt' => 'Tatar',
                    'te' => 'Telugu',
                    'th' => 'Thai',
                    'bo' => 'Tibetan',
                    'to' => 'Tonga',
                    'tr' => 'Turkish',
                    'uk' => 'Ukranian',
                    'ur' => 'Urdu',
                    'uz' => 'Uzbek',
                    'vi' => 'Vietnamese',
                    'cy' => 'Welsh',
                    'xh' => 'Xhosa'
            );

            break;
                            
        }        
    
        $choices = $choices; 
   
        if($options['param'] == 'url_parameter'  || $options['param'] == 'cookie') {
            
            if($options['param'] == 'url_parameter'){
            
                $output = '<input type="text" placeholder="'.esc_html__('blog', 'ads-for-wp').'" class="widefat adsforwp-url-parameter" value="'.esc_attr($saved_data).'" name="visitor_conditions_array[group-'.esc_attr($current_group_number).'][visitor_conditions]['. esc_attr($current_number) .'][key_3]">';                 
            }
            
            if($options['param'] == 'cookie'){
            
                $output = '<div class="adsforwp-cookie-value"><input type="text" placeholder="'.esc_html__('Cookie Value', 'ads-for-wp').'" class="widefat " value="'.esc_attr($saved_data).'" name="visitor_conditions_array[group-'.esc_attr($current_group_number).'][visitor_conditions]['. esc_attr($current_number) .'][key_3]">'
                        . '<p>Leave empty to check if cookie is set</p></div>';                 
            }
                                    
        }else{
           if ( empty( $choices)) {
               
          $choices = array('none' => esc_html__('No Items', 'ads-for-wp') );
          
        }
    
          $output = '<select  class="widefat adsforwp-visitor-condition-ajax-output" name="visitor_conditions_array[group-'.esc_attr($current_group_number).'][visitor_conditions]['. esc_attr($current_number) .'][key_3]">'; 
      
          foreach ($choices as $key => $value) {
              
                if ( $saved_data ==  $key ) {
                    
                        $selected = 'selected="selected"';
                    
                } else {
                    
                        $selected = '';
                  
                }

            $output .= '<option '. esc_attr($selected) .' value="' . esc_attr($key) .'"> ' .  esc_html__($value, 'ads-for-wp') .'  </option>';            
          } 
        
          $output .= ' </select> '; 
          
          if(adsforwp_rmv_warnings($adsforwp_settings, 'adsforwp_geolocation_api', 'adsforwp_string') =='' && $options['param'] == 'geo_location'){
            $output .= '<div class="adsforwp-user-targeting-note">'.esc_html__('To use this condition, provide', 'ads-for-wp').' <strong>'.esc_html__('IP Geolocation API', 'ads-for-wp').'</strong> '.esc_html__('in advanced settings', 'ads-for-wp').'</div>';   
          }
                                  
                if ( $saved_data ==  'url_custom' || $response =='referrer_url') {
                    
                 if($selected_val_key_4 && $saved_data ==  'url_custom'){
                     
                    $output .= ' <input type="text" class="widefat adsforwp_url_custom" value="'.esc_attr($selected_val_key_4).'" name="visitor_conditions_array[group-'.esc_attr($current_group_number).'][visitor_conditions]['. esc_attr($current_number) .'][key_4]"> ';                           
                    
                 }else{
                     
                    $output .= ' <input placeholder ="https://www.example.com/" type="text" class="widefat afw_hide adsforwp_url_custom" value="'.esc_attr($selected_val_key_4).'" name="visitor_conditions_array[group-'.esc_attr($current_group_number).'][visitor_conditions]['. esc_attr($current_number) .'][key_4]"> ';                           
                    
                 }   
                 
                }
                
                if ( $saved_data ==  'user_agent_custom' || $response =='user_agent') { 
                    
                 if($selected_val_key_5 && $saved_data ==  'user_agent_custom'){
                     
                    $output .= ' <input type="text" class="widefat adsforwp_user_agent_custom" value="'.esc_attr($selected_val_key_5).'" name="visitor_conditions_array[group-'.esc_attr($current_group_number).'][visitor_conditions]['. esc_attr($current_number) .'][key_5]"> ';        
                    
                 } else{
                     
                    $output .= ' <input placeholder ="Android" type="text" class="widefat afw_hide adsforwp_user_agent_custom" value="'.esc_attr($selected_val_key_5).'" name="visitor_conditions_array[group-'.esc_attr($current_group_number).'][visitor_conditions]['. esc_attr($current_number) .'][key_5]"> ';        
                    
                 }  
                 
                }    
        }                           
                
    $common_function_obj = new adsforwp_admin_common_functions();  
    $allowed_html = $common_function_obj->adsforwp_expanded_allowed_tags();
    echo wp_kses($output, $allowed_html); 
    
    if ( $is_ajax ) {
      die();
    }

}

/**
 * Here, We are getting dynamic value for dropdown in conditions metabox
 * @global type $wp_roles
 * @param type $data
 * @param type $saved_data
 * @param type $current_number
 */
public function adsforwp_ajax_select_creator($data = '', $saved_data= '', $current_number = '', $current_group_number  = '') {
 
    $response = $data;
    $is_ajax  = false;
    
    if( $_SERVER['REQUEST_METHOD']=='POST'){
        
        $is_ajax = true;
        
        if(wp_verify_nonce($_POST["adsforwp_call_nonce"],'adsforwp_select_action_nonce')){
            
            if ( isset( $_POST["id"] ) ) {
                
                    $response = sanitize_text_field(wp_unslash($_POST["id"]));
              
            }
            
            if ( isset( $_POST["number"] ) ) {
                
                    $current_number   = intval($_POST["number"]);
              
            }
            
            if ( isset( $_POST["group_number"] ) ) {
                
                    $current_group_number   = intval($_POST["group_number"]);
              
            }
            
        }else{
            exit;
        }
       
    }        
        // send the response back to the front end
       // vars
    $choices = array();    
    
    $options['param'] = $response;
    // some case's have the same outcome
        if($options['param'] == "page_parent")
        {
          $options['param'] = "page";
        }
    
        switch($options['param'])
        {
          case "post_type":

            $choices = $this->adsforwp_post_type_generator();
            
            $choices = apply_filters('adsforwp_modify_select_post_type', $choices );           
            break;

          case "page":

            $post_type = 'page';
            $posts = get_posts(array(
              'posts_per_page'          =>  -1,
              'post_type'               => $post_type,
              'orderby'                 => 'menu_order title',
              'order'                   => 'ASC',
              'post_status'             => 'any',
              'suppress_filters'        => false,
              'update_post_meta_cache'  => false,
            ));

            if( $posts )
            {
              // sort into hierachial order!
              if( is_post_type_hierarchical( $post_type ) )
              {
                $posts = get_page_children( 0, $posts );
              }

              foreach( $posts as $page )
              {
                $title = '';
                $ancestors = get_ancestors($page->ID, 'page');
                
                if($ancestors)
                {
                  foreach($ancestors as $a)
                  {
                    $title .= '- ';
                  }
                }

                $title .= apply_filters( 'the_title', $page->post_title, $page->ID );                        
                // status
                if($page->post_status != "publish")
                {
                  $title .= " ($page->post_status)";
                }

                $choices[ $page->ID ] = $title;

              }
              // foreach($pages as $page)

            }

            break;

          case "page_template" :

            $choices = array(
              'default' =>  esc_html__('Default Template','ads-for-wp'),
            );

            $templates = get_page_templates();
            
            foreach($templates as $k => $v)
            {
              $choices[$v] = $k;
            }

            break;

          case "post" :

            $post_types = get_post_types();

            unset( $post_types['page'], $post_types['attachment'], $post_types['revision'] , $post_types['nav_menu_item'], $post_types['acf'] , $post_types['amp_acf']  );

            if( $post_types )
            {
              foreach( $post_types as $post_type )
              {

                $posts = get_posts(array(
                  'numberposts'      => '-1',
                  'post_type'        => $post_type,
                  'post_status'      => array('publish', 'private', 'draft', 'inherit', 'future'),
                  'suppress_filters' => false,
                ));

                if( $posts)
                {
                  $choices[$post_type] = array();

                  foreach($posts as $post)
                  {
                    $title = apply_filters( 'the_title', $post->post_title, $post->ID );

                    // status
                    if($post->post_status != "publish")
                    {
                      $title .= " ($post->post_status)";
                    }

                    $choices[$post_type][$post->ID] = $title;

                  }
                  // foreach($posts as $post)
                }
                // if( $posts )
              }
              // foreach( $post_types as $post_type )
            }
            // if( $post_types )


            break;

          case "post_category" :

            $terms = get_terms( 'category', array( 'hide_empty' => false ) );

            if( !empty($terms) ) {

              foreach( $terms as $term ) {

                $choices[ $term->term_id ] = $term->name;

              }

            }

            break;

          case "post_format" :

            $choices = get_post_format_strings();

            break;

          case "user_type" :
              
           global $wp_roles;
            $choices = $wp_roles->get_names();

            if( is_multisite() )
            {
              $choices['super_admin'] = esc_html__('Super Admin','ads-for-wp');
            }

            break;

          case "ef_taxonomy" :

            $choices    = array('all' => esc_html__('All','ads-for-wp'));
            $taxonomies = $this->adsforwp_post_taxonomy_generator();        
            $choices    = array_merge($choices, $taxonomies);                      
            break;

        }        
    // allow custom location rules
    $choices = $choices; 

    // Add None if no elements found in the current selected items
    if ( empty( $choices) ) {
        
      $choices = array('none' => esc_html__('No Items', 'ads-for-wp') );
      
    }
     //  echo $current_number;
    // echo $saved_data;

      $output = '<select  class="widefat ajax-output" name="data_group_array[group-'.esc_attr($current_group_number).'][data_array]['. esc_attr($current_number) .'][key_3]">'; 

        // Generate Options for Posts
        if ( $options['param'] == 'post' ) {
            
          foreach ($choices as $choice_post_type) {  
              
            foreach ($choice_post_type as $key => $value) { 
                
                if ( $saved_data ==  $key ) {
                    
                         $selected = 'selected="selected"';
                    
                } else {
                    
                         $selected = '';
                  
                }

                $output .= '<option '. esc_attr($selected) .' value="' .  esc_attr($key) .'"> ' .  esc_html__($value, 'ads-for-wp') .'  </option>';            
            }
            
          }
         // Options for Other then posts
        } else {
            
          foreach ($choices as $key => $value) {
              
                if ( $saved_data ==  $key ) {
                    
                    $selected = 'selected="selected"';
                    
                } else {
                    
                    $selected = '';
                  
                }

            $output .= '<option '. esc_attr($selected) .' value="' . esc_attr($key) .'"> ' .  esc_html__($value, 'ads-for-wp') .'  </option>';            
            
          } 
          
        }
    $output .= ' </select> '; 
    $common_function_obj = new adsforwp_admin_common_functions();  
    $allowed_html = $common_function_obj->adsforwp_expanded_allowed_tags();
    echo wp_kses($output, $allowed_html); 
    
    if ( $is_ajax ) {
      die();
    }
// endif;  

}

/**
 * Here, this function generate taxonomy
 * @return type
 */
public function adsforwp_post_taxonomy_generator(){
    
    $taxonomies = '';  
    $choices    = array();
    $taxonomies = get_taxonomies( array('public' => true), 'objects' );
    

      foreach($taxonomies as $taxonomy) {
          
            $choices[ $taxonomy->name ] = $taxonomy->labels->name;
        
      }
      
      // unset post_format (why is this a public taxonomy?)
      if( isset($choices['post_format']) ) {
          
            unset( $choices['post_format']);
        
      }
      
    return $choices;
}


/**
 * Here, We are getting value for taxonomy
 * @param type $selectedParentValue
 * @param type $selectedValue
 * @param type $current_number
 */
public function adsforwp_create_ajax_select_taxonomy($selectedParentValue = '',$selectedValue='', $current_number ='', $current_group_number  = ''){
    
    $is_ajax = false;
    
    if( $_SERVER['REQUEST_METHOD']=='POST'){
        
        $is_ajax = true;
        
        if(wp_verify_nonce($_POST["adsforwp_call_nonce"],'adsforwp_select_action_nonce')){
            
              if(isset($_POST['id'])){
                  
                    $selectedParentValue = sanitize_text_field(wp_unslash($_POST['id']));
                
              }
              
              if(isset($_POST['number'])){
                  
                    $current_number = intval($_POST['number']);
                
              }
              
              if ( isset( $_POST["group_number"] ) ) {
                  
                    $current_group_number   = intval($_POST["group_number"]);
              
              }
        }else{
            
            exit;
            
        }       
    }
    $taxonomies = array(); 
    if($selectedParentValue == 'all'){
        
        $taxonomies =  get_terms( array(
                        'hide_empty' => true,
                    ) ); 
    
    }else{
        
        $taxonomies =  get_terms($selectedParentValue, array(
                        'hide_empty' => true,
                    ) );   
    
    }    
    
    $choices = '<option value="all">'.esc_html__('All','ads-for-wp').'</option>';
    
    foreach($taxonomies as $taxonomy) {
        
      $sel="";
      
      if($selectedValue == $taxonomy->slug){
          
        $sel = "selected";
        
      }
      
      $choices .= '<option value="'.esc_attr($taxonomy->slug).'" '.esc_attr($sel).'>'.esc_html__($taxonomy->name,'ads-for-wp').'</option>';
      
    }
    
    $common_function_obj = new adsforwp_admin_common_functions();  
    $allowed_html = $common_function_obj->adsforwp_expanded_allowed_tags();  
    
    echo '<select  class="widefat afw-ajax-output-child" name="data_group_array[group-'. esc_attr($current_group_number) .'][data_array]['.esc_attr($current_number).'][key_4]">'. wp_kses($choices, $allowed_html).'</select>';
    
    if($is_ajax){
      die;
    }
}

}

if (class_exists('adsforwp_ajax_selectbox')) {
	new adsforwp_ajax_selectbox;
};