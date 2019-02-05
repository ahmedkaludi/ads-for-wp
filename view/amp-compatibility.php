<?php
class adsforwp_amp_compatibility {
    
    public function __construct() {                                                                                                     
		add_action( 'add_meta_boxes', array( $this, 'adsforwp_amp_comp_add_meta_box' ) );
		add_action( 'save_post', array( $this, 'adsforwp_amp_comp_save' ) );
	}
        function adsforwp_amp_comp_add_meta_box() {
	add_meta_box(
		'adsforwp-location',
		esc_html__( 'AMP', 'ads-for-wp' ),
		array( $this, 'adsforwp_meta_box_callback' ),
		array('adsforwp','adsforwp-groups'),
		'side',
		'low'
	);
    }
        function adsforwp_amp_comp_get_meta( $value ) {
            global $post;
            
            $field = get_post_meta( $post->ID, $value, true );
           
            if ( ! empty( $field ) ) {
                    return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
            } else {
                    return false;
            }
    }
        function adsforwp_meta_box_callback( $post) {
                wp_nonce_field( 'adsforwp_amp_compatibility_nonce', 'adsforwp_amp_compatibility_nonce' ); ?>                                               
                <div class="misc-pub-section">
                    <div class="afw-amp-compatibility">
                        <span style="font-size: 15px;"><?php echo esc_html__('AMP Compatibility', 'ads-for-wp') ?></span><br>
                        <?php echo esc_html__('Status', 'ads-for-wp') ?> : <span id="afw-amp-status-display"></span>                        
                        <a href="#" class="afw-amp-edit-post-status hide-if-no-js" role="button">
                            <span aria-hidden="true"><?php echo esc_html__('Edit', 'ads-for-wp') ?></span>                             
                        </a>
                        <br><span class="afw_hide afw-amp-support"><?php echo esc_html__('Note', 'ads-for-wp') ?> : <span class="description"></span></span>
                        <div id="afw-amp-status-select" class="hide-if-js">                           
                           <label for="afw_amp_status" class="screen-reader-text"><?php echo esc_html__('Set Status', 'ads-for-wp') ?></label>
                           <select name="ads-for-wp_amp_compatibilty" id="ads-for-wp_amp_compatibilty">                                
                                <option value="enable" <?php echo ($this->adsforwp_amp_comp_get_meta( 'ads-for-wp_amp_compatibilty' ) === 'enable' ) ? 'selected' : '' ?> ><?php echo esc_html__('Enable', 'ads-for-wp') ?></option>
                                <option value="disable" <?php echo ($this->adsforwp_amp_comp_get_meta( 'ads-for-wp_amp_compatibilty' ) === 'disable' ) ? 'selected' : '' ?>><?php echo esc_html__('Disable', 'ads-for-wp') ?></option>
                           </select>
                         <a href="#" class="afw-amp-status-save hide-if-no-js button"><?php echo esc_html__('OK', 'ads-for-wp') ?></a>
                         <a href="#" class="afw-amp-status-cancel hide-if-no-js button-cancel"><?php echo esc_html__('Cancel', 'ads-for-wp') ?></a>
                        </div>
                  </div>
                    <div class="afw-non-amp-visibility">
                       <span style="font-size: 15px;"><?php echo esc_html__('Non AMP Visibility', 'ads-for-wp') ?></span><br>
                        <?php echo esc_html__('Status', 'ads-for-wp') ?> : <span id="afw-non-amp-visib-status-display"></span>
                        <a href="#" class="afw-non-amp-visib-status hide-if-no-js" role="button">
                            <span aria-hidden="true"><?php echo esc_html__('Edit', 'ads-for-wp') ?></span>                             
                        </a>
                        <div id="afw-non-amp-visib-status-select" class="hide-if-js">                           
                           <label for="afw_non_amp_visibility" class="screen-reader-text"><?php echo esc_html__('Set status', 'ads-for-wp') ?></label>
                           <select name="ads_for_wp_non_amp_visibility" id="ads_for_wp_non_amp_visibility">                                
                                <option value="show" <?php echo ($this->adsforwp_amp_comp_get_meta( 'ads_for_wp_non_amp_visibility' ) === 'show' ) ? 'selected' : '' ?> ><?php echo esc_html__('Show', 'ads-for-wp') ?></option>
                                <option value="hide" <?php echo ($this->adsforwp_amp_comp_get_meta( 'ads_for_wp_non_amp_visibility' ) === 'hide' ) ? 'selected' : '' ?>><?php echo esc_html__('Hide', 'ads-for-wp') ?></option>
                           </select>
                         <a href="#" class="afw-non-amp-visib-save hide-if-no-js button"><?php echo esc_html__('OK', 'ads-for-wp') ?></a>
                         <a href="#" class="afw-non-amp-visib-cancel hide-if-no-js button-cancel"><?php echo esc_html__('Cancel', 'ads-for-wp') ?></a>
                        </div> 
                        
                    </div> 
                    
                </div>
                    <?php
        }
   
        function adsforwp_amp_comp_save( $post_id ) {  
            
                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
                
                if ( ! isset( $_POST['adsforwp_amp_compatibility_nonce'] ) || ! wp_verify_nonce( $_POST['adsforwp_amp_compatibility_nonce'], 'adsforwp_amp_compatibility_nonce' ) ) return;
                
                if ( ! current_user_can( 'edit_post', $post_id ) ) return;
                               
                if ( isset( $_POST['ads-for-wp_amp_compatibilty'] ) )
                        update_post_meta( $post_id, 'ads-for-wp_amp_compatibilty', esc_attr( $_POST['ads-for-wp_amp_compatibilty'] ) );
               
                if ( isset( $_POST['ads_for_wp_non_amp_visibility'] ) )
                        update_post_meta( $post_id, 'ads_for_wp_non_amp_visibility', esc_attr( $_POST['ads_for_wp_non_amp_visibility'] ) );
                                              
        }    
}
if (class_exists('adsforwp_amp_compatibility')) {
	new adsforwp_amp_compatibility;
};
