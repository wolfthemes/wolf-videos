var WolfVideos = WolfVideos || {},
	WolfVideosParams = WolfVideosParams || {};

/* jshint -W062 */
WolfVideos = function ( $ ) {

	'use strict';

	return {

		/**
		 * Init videos isotope masonry
		 */
		init : function () {
			
			var $this = this,
				mainVideosContainer = $( '.videos' ),
				videoOptionFilter = $( '#videos-filter' ),
				videoOptionFilterLinks = videoOptionFilter.find( 'a' ),
				selector;
			
			mainVideosContainer.imagesLoaded( function() {
				$this.setColumnWidth( '.video-item-container', mainVideosContainer );
				mainVideosContainer.isotope( {
					itemSelector : '.video-item-container'
				} );
			} );

			videoOptionFilterLinks.click( function() {
				selector = $( this ).attr( 'data-filter' );
				videoOptionFilterLinks.attr( 'href', '#' );
				$this.setColumnWidth( '.video-item-container', mainVideosContainer );
				mainVideosContainer.isotope( {
					filter : '.' + selector,
					itemSelector : '.video-item-container',
					layoutMode : 'fitRows',
					animationEngine : 'best-available'
				} );

				videoOptionFilterLinks.removeClass( 'active' );
				$( this ).addClass( 'active' );
				return false;
			} );

			$( window ).smartresize( function() {
				$this.setColumnWidth( '.video-item-container', mainVideosContainer );
				mainVideosContainer.isotope( 'reLayout' );
			} );
		},

		/**
		 * Get column count depending on container width
		 */
		getNumColumns : function ( mainContainer ) {
			var winWidth = mainContainer.width(),
				column = WolfVideosParams.columns;
			if ( 481 > winWidth ) {
				column = 1;
			} else if ( 481 <= winWidth && 767 > winWidth ) {
				column = 2;
			} else if ( 767 <= winWidth ) {
				column = WolfVideosParams.columns;
			}
			return column;
		},
		
		/**
		 * Get column width depending on column number
		 */
		getColumnWidth : function ( mainContainer ) {
			var columns = this.getNumColumns( mainContainer ),
				wrapperWidth = mainContainer.width(),
				columnWidth = Math.floor( wrapperWidth / columns );
			return columnWidth;
		},

		/**
		 * Set column width
		 */
		setColumnWidth : function ( selector, mainContainer ) {
			var ColumnWidth = this.getColumnWidth( mainContainer );
			$( selector ).each( function() {
				$( this ).css( { 'width' : ColumnWidth + 'px' } );
			} );
		}

	};

}( jQuery );


;( function( $ ) {

	'use strict';

	$( document ).ready( function() {

		if ( $( '.videos' ).length ) {
			WolfVideos.init();
		}

	} );

} )( jQuery );