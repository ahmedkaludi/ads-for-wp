<?php 
class adsforwp_output_service{
    
   public function adsforwp_enque_amp_popup_ad_css($ad_id){
        
    global $redux_builder_amp;

    $post_meta_dataset = get_post_meta($ad_id,$key = '',true);       
    $ad_img_width  = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_ad_img_width', 'adsforwp_array');
    $ad_img_height = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_ad_img_height', 'adsforwp_array');                     
    $delay_time    = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_delay_time', 'adsforwp_array');                     
        ?>       
        @keyframes amp-Pop-up-delay {
  to {
    visibility: visible;
  }
}

body #adsforwp-popup-ad-<?php echo $ad_id ?>.amp-active {border-color: #242323b3; z-index: 10000; height: -webkit-fill-available; height: -moz-fill-available; top:0; visibility: hidden; animation: 0s linear <?php echo esc_attr($delay_time); ?>s forwards amp-Pop-up-delay;
}

.adsforwp-popup-ad { position: fixed;
    -webkit-overflow-scrolling: touch; 
    top: 0;    
    left: 0; 
    display: flex;
    align-items: center;
    justify-content: 
    center;margin:0 auto;
    width:100%;
    height:100%;
    background: hsla(0,0%,100%,0.7);
    
}
.adsforwp-popup-ad .afw_ad_image{position:relative;}

    #adsforwp-popup-ad-<?php echo $ad_id; ?> amp-img{
        width:<?php echo esc_attr($ad_img_width); ?>px;
        height:<?php echo esc_attr($ad_img_height); ?>px;
    }
        
    .afw_ad_amp_achor{
            text-align:center;
        }        
        .adsforwp-popup-ad-close {
          position: absolute;
          right: 0px;
          top: 0px;
          padding:2px;
          cursor:pointer;
          color:#000;
          background-color:transparent;
          border: #fff;
          font-size: 28px;
          line-height: 1;
        }
        .adsforwp-popup-ad-close:after{
        display: inline-block;
        content: "\00d7"; 
        }
    
        <?php
        
    }
    
    
   public function adsforwp_enque_amp_sticky_ad_css($ad_id){
        
        $post_meta_dataset = get_post_meta($ad_id,$key = '',true);       
        $ad_img_width      = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_ad_img_width', 'adsforwp_array');
        $ad_img_height     = adsforwp_rmv_warnings($post_meta_dataset, 'adsforwp_ad_img_height', 'adsforwp_array');                     
        
        ?>       
        .adsforwp-stick-ad{
            padding-top:20px;
        }               
        .afw_ad_amp_achor{
            text-align:center;
        }
        .afw_ad_amp_achor amp-img{
            width:<?php echo $ad_img_width; ?>px;
            height:<?php echo $ad_img_height; ?>px;
        }
        .adsforwp-sticky-ad-close {
          position: absolute;
          right: 0px;
          top: 0px;
          padding:2px;
          cursor:pointer;
          color:#000;
          background-color:#fff;
          border: #fff;
        }
        .adsforwp-sticky-ad-close:after{
        display: inline-block;
        content: "\00d7"; 
        }
        
        <?php
        
    } 
    
        
}

