<?php
class adsforwp_file_creation{
                
     public $ad_support;
     public $wppath;
     
     public function __construct(){
        $this->wppath = str_replace("//","/",str_replace("\\","/",realpath(ABSPATH))."/");          
        $this->ad_support = $this->wppath."front.js";
       
    }
            
        public function adsforwp_create_adblocker_support_js(){   
        $writestatus = '';
        if(file_exists($this->ad_support)){
            unlink($this->ad_support);
        }
        if(!file_exists($this->ad_support)){            
            $url = site_url();            		
	    $swHtmlContent = file_get_contents(ADSFORWP_PLUGIN_DIR."public/ads-front.js");	    
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
    public function adsforwp_delete_adblocker_support_js(){   
        $result ='';
        if(file_exists($this->ad_support)){
         $result =  unlink($this->ad_support);          
        }
       return $result;
       
    }
}