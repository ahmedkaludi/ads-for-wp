<?php
/**
 * This class handls all the file creation functions,
 * which we use in different different part in project
 */
class adsforwp_file_creation{
                
        public $ad_support;
        public $wppath;

        public function __construct(){
           $this->wppath     = str_replace("//","/",str_replace("\\","/",realpath(ABSPATH))."/");          
           $this->ad_support = $this->wppath."front.js";

       }
        /**
         * Function to create a ad blocker js file
         * @return boolean
         */    
        public function adsforwp_create_adblocker_support_js(){   
            
        $writestatus = '';
        if(file_exists($this->ad_support)){
            
            unlink($this->ad_support);
            
        }
        if(!file_exists($this->ad_support)){ 
            
            $url = site_url();            		
	    $swHtmlContent  = file_get_contents(ADSFORWP_PLUGIN_DIR."public/assets/js/ads-front.js");	    
            $handle         = fopen($this->ad_support, 'w');
            $writestatus    = fwrite($handle, $swHtmlContent);
            fclose($handle);
            
        }
        if($writestatus){
            
            return true;   
            
        }else{
            
            return false;   
        }
    }  
    
    /**
     * Function to delete a ad blocker js file
     * @return type
     */
    public function adsforwp_delete_adblocker_support_js(){  
        
        $result ='';
        
        if(file_exists($this->ad_support)){
            
         $result =  unlink($this->ad_support);  
         
        }
        
       return $result;
       
    }
    
}