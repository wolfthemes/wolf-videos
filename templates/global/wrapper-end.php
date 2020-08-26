<?php
/**
 * Content wrappers
 *
 * @author WolfThemes
 * @package WolfVideos/Templates
 * @since 1.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$template = get_option( 'template' );

switch( $template ) {
	case 'twentyeleven' :
		echo '</div>';
		get_sidebar( 'shop' );
		echo '</div>';
		break;
	case 'twentytwelve' :
		echo '</div></div>';
		break;
	case 'twentythirteen' :
		echo '</div></div>';
		break;
	case 'twentyfourteen' :
		echo '</div></div></div>';
		get_sidebar( 'content' );
		break;
	case 'twentyfifteen' :
		echo '</div></div>';
		break;
	case 'twentysixteen' :
		echo '</main></div>';
		break;
	case 'twentyseventeen' :
		echo '</main></div></div>';
		break;
	default :
		echo '</div></div>';
		break;
}