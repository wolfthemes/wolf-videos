/* jshint -W062 */
var WolfVideos = function ( $ ) {

	'use strict';

	return {

		/**
		 * Init isotope masonry
		 */
		init : function () {
			
			if ( $( '.videos' ).length ) {
				var mainContainer = $( '.videos' ),
					OptionFilter = $( '#videos-filter' ),
					OptionFilterLinks = OptionFilter.find( 'a' ),
					selector;

				mainContainer.imagesLoaded( function() {
					mainContainer.isotope( {
						itemSelector : '.video-item-container'
					} );
				} );

				OptionFilterLinks.click( function() {
					selector = $( this ).attr( 'data-filter' );
					OptionFilterLinks.attr( 'href', '#' );
					mainContainer.isotope( {
						filter : '.' + selector,
						itemSelector : '.video-item-container',
						layoutMode : 'fitRows',
						animationEngine : 'best-available'
					} );

					OptionFilterLinks.removeClass( 'active' );
					$( this ).addClass( 'active' );
					return false;
				} );
			}
		}
	};

}( jQuery );

;( function( $ ) {

	'use strict';

	$( document ).ready( function() {
		WolfVideos.init();
	} );

} )( jQuery );