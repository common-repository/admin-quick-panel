jQuery( document ).ready(function() {
	/**
	 * onload
	 */
	 jQuery('#aqpanel .collapse-button-icon').click(function() {
	 	if ( jQuery(this).hasClass( "closed" ) == true ){
	 		window.setUserSetting( 'aqpanel_collapsed', 'false' );
	 		jQuery(this).removeClass("closed");
	 		jQuery('#aqpanel').removeClass("minimized");
	 		jQuery('body.wp-admin').removeClass("aqpanel-closed"); 		
	 	}
	 	else {
	 		jQuery(this).addClass("closed");
	 		window.setUserSetting( 'aqpanel_collapsed', 'true' );
	 		jQuery('#aqpanel').addClass("minimized");
	 		jQuery('body.wp-admin').addClass("aqpanel-closed");		
	 	}
	 });

	 /* display settings */
	 jQuery('#aqpanel input[name="display_thumbnails"]').click(function() {
	 	if ( jQuery(this).is(':checked') ){;
	 		window.setUserSetting( 'aqpanel_show_thumbnails', 'true' );
	 		jQuery('body.wp-admin').addClass("aqpanel-show-thumbnails"); 		
	 	}
	 	else {
	 		window.setUserSetting( 'aqpanel_show_thumbnails', 'false' );
	 		jQuery('body.wp-admin').removeClass("aqpanel-show-thumbnails");
	 	}
	 });
	 jQuery(document).ready(function($){
	 	jQuery('.my-color-field').wpColorPicker();
	 });

	 /* minimize */
	 jQuery('#aqpanel').find( '.post-type-container' ).hoverIntent({

			over: function() {
				var $menuItem = jQuery( this ),
					submenu = $menuItem.find( '.link-list' );
				jQuery( this ).addClass( 'active' );
			},
			out: function(){
				jQuery( this ).removeClass( 'active' );
			},
			timeout: 200,
			sensitivity: 7,
			interval: 90
		});
});
