"use strict";

function superfish_menu(){
    
      var screen_width = jQuery(window).width();
      jQuery('.sf-menu').superfish('destroy');
        if(screen_width < 900){
        			jQuery(".main_nav").addClass("resp_menu");
                    
                     jQuery(".sf-menu").addClass("mobile-menu");
                     jQuery(".mobile-menu").removeClass("sf-menu");
	                    jQuery("#menu-icon").on("click", function(){
	                            jQuery(".menu-top-container").slideToggle();
	                            jQuery(this).toggleClass("active");
	                            
	                            return false;
	                    });
                        
        } 
        else{
        		jQuery(".main_nav").removeClass("resp_menu");
        		  jQuery(".mobile-menu").addClass("sf-menu");
                     jQuery(".sf-menu").removeClass("mobile-menu");
                      jQuery(".menu-top-container").fadeIn();
           var superfish_menu = jQuery('.sf-menu').superfish({
                    //add options here if required
                }); 
        }   
}

jQuery(document).ready(function($){
		 superfish_menu();
	});
