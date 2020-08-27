<?php
/**
 * Videos Metaboxes.
 *
 * @class WV_Admin_Metabox
 * @author WolfThemes
 * @category Admin
 * @package WolfVideos/Admin
 * @version 1.2.8
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WV_Admin_Metabox' ) ) {
	/**
	 * Metabox class
	 *
	 * Create metabox easily from an array
	 * Usually used for page styling options
	 */
	class WV_Admin_Metabox {

		var $meta = array();

		/**
		 * WV_Admin_Metabox constructor
		 */
		public function __construct( $meta = array() ) {

			$this->meta = $meta + $this->meta;

			// Enqueue scripts and styles
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			// Add metaboxes
			add_action( 'add_meta_boxes', array( $this, 'add_meta' ) );

			// Save post hook
			add_action( 'save_post', array( $this, 'save' ) );
		}

		/**
		 * Enqueue admin scripts
		 */
		public function admin_scripts() {

			/* Datepicker CSS */
			wp_enqueue_style( 'jquery-ui-custom', WV_CSS . '/admin/jquery-ui-custom.min.css', array(), WV_VERSION, 'all' );

			/* Slider JS */
			wp_enqueue_script( 'jquery-ui-sortable' );

			/* Datepicker JS */
			wp_enqueue_script( 'wolf-videos-datepicker', WV_JS . '/admin/datepicker.min.js', array( 'jquery-ui-datepicker' ), false, true );
			wp_enqueue_script( 'wolf-videos-repeatable', WV_JS . '/admin/repeatable.min.js', array( 'jquery-ui-datepicker' ), false, true );		
		}

		/**
		 * Add meta box
		 */
		public function add_meta() {

			foreach ( $this->meta as $k => $v) {

				if ( is_array( $v['page'] ) ) {
					foreach ( $v['page'] as $p) {
						add_meta_box(
						sanitize_title( $k ).'_we_meta_box',
						$v['title'],
						array( $this, 'render' ),
						$p,
						'normal',
						'default' );
					}
				} else {
					add_meta_box(
					sanitize_title( $k ).'_we_meta_box',
					$v['title'],
					array( $this, 'render' ),
					$v['page'],
					'normal',
					'default' );
				}
			}
		}

		/**
		 * Display Inputs
		 */
		public function render() {

			global $post;
			$post_id = $post->ID;

			$meta_fields = array();

			$current_post_type = get_post_type( $post_id );

			foreach ( $this->meta as $k => $v ) {
				if ( is_array( $v['page'] ) ) {
					if (  in_array( $current_post_type, $v['page'] ) ) {
						$meta_fields = $v['metafields'];
					}
				} else {
					if ( $v['page'] == $current_post_type ) {
						$meta_fields = $v['metafields'];
					}
				}
			}

			// Use nonce for verification
			echo '<input type="hidden" name="custom_meta_box_nonce" value="' . wp_create_nonce( basename( __FILE__ ) ) . '" />';

			// Begin the field table and loop
			echo '<table class="form-table wolf-event-metabox-table">';

			foreach ( $this->meta as $k => $v ) {

				if ( isset( $v['help'] ) ) {
					echo '<div class="wolf-event-metabox-help">' . $v['help'] . '</div>';
				}
			}

			foreach ( $meta_fields as $field ) {

				$field_id	= sanitize_title( $field['id'] );
				$type     	= ( isset( $field['type'] ) ) ? $field['type'] : 'text';
				$label    	= ( isset( $field['label'] ) ) ? $field['label'] : 'Label';
				$desc    	= ( isset( $field['desc'] ) ) ? $field['desc'] : '';
				$default_value = ( isset( $field['value'] ) ) ? $field['value'] : '';
				$dependency	= ( isset( $field['dependency'] ) ) ? $field['dependency'] : array();
				$class 		= "option-section-$field_id";
				$data 		= '';

				if ( array() != $dependency ) {
					$class .= ' has-dependency';

					$data .= ' data-dependency-element="' . $dependency['element'] . '"';

					$dependency_value = '[';
					foreach ( $dependency['value'] as $value ) {
						$dependency_value .= '"' . $value . '"';
					}
					$dependency_value .= ']';

					$data .= " data-dependency-values='$dependency_value'";
				}

				// get value of this field if it exists for this post
				$meta = get_post_meta( $post_id, $field_id, true );

				if ( ! $meta ) $meta = $default_value;

				// begin a table row with
				echo "<tr class='$class'$data>";

				// debug( $dependency );

				echo "<th style='width:15%'><label for='$field_id'>$label</label></th>

				<td>";

					// editor
					if ( 'editor' == $type ) {
						wp_editor( $meta, $field_id, $options = array() );
						echo '<br><span class="description">' . $desc . '</span>';
					// text
					} elseif ( 'text' == $type || 'int' == $type || 'url' == $type ) {

						echo '<input type="text" name="' . $field_id . '" id="' . $field_id . '" value="' . $meta . '" size="30" />
						<br><span class="description">' . $desc . '</span>';

					// textarea
					} elseif ( 'textarea' == $type ) {
						echo '<textarea name="' . $field_id . '" id="' . $field_id . '" cols="60" rows="4">' . $meta . '</textarea>
						<br><span class="description">' . $desc . '</span>';

					// checkbox
					} elseif ( 'checkbox' == $type ) {
						echo '<input type="checkbox" name="' . $field_id . '" id="' . $field_id . '" ', ( $meta ) ? ' checked="checked"' : '','/>
						<span class="description">' . $desc . '</span>';

					// select
					} elseif ( 'select' == $type ) {

						echo '<select name="' . $field_id . '" id="' . $field_id . '">';
						if ( array_keys( $field['choices'] ) != array_keys( array_keys( $field['choices'] ) ) ) {
							foreach ( $field['choices'] as $k => $option) {
								echo '<option', $k == $meta ? ' selected="selected"' : '', ' value="'.$k.'">' . $option . '</option>';
							}
						} else{
							foreach ( $field['choices'] as $option) {
								echo '<option', $option == $meta ? ' selected="selected"' : '', ' value="' . $option . '">' . $option . '</option>';
							}
						}

						echo '</select><br><span class="description">' . $desc . '</span>';

					// datepicker
					} elseif ( $field['type'] == 'datepicker' ) {
						echo '<input type="text" class="wd-metabox-datepicker" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . $meta . '" size="30">
						<br><span class="description">' . $desc . '</span>';
					
					// colorpicker
					} elseif ( 'colorpicker' == $type ) {

						echo '<input type="text" class="wd-options-colorpicker wolf-colorpicker-input" name="' . $field_id . '" id="' . $field_id . '" value="' . $meta . '" />
						<br><span class="description">' . $desc . '</span>';

					// repeatable
					} elseif( 'repeatable' == $type ) {
						
						echo '<a class="wd-repeatable-add button" href="#">+</a>
								<ul id="'.$field['id'].'-repeatable" class="wd-custom_repeatable">';
						$i = 0;
						if ( is_array( $meta ) ) {
							
							foreach( $meta as $row) {

								echo '<li><span class="sort hndle">|||</span>
										<input type="text" name="' . $field['id'] . '['  .$i . ']" id="' . $field_id . '" value="' . esc_attr( $row ) . '" size="30" />
											<a class="wd-repeatable-remove button" href="#">-</a></li>';
								$i++;
							}
						} else {
							echo '<li><span class="sort hndle">|||</span>
								<input type="text" name="' . $field_id . '[' . $i . ']" id="' . $field_id . '" value="" size="30" />
								<a class="wd-repeatable-remove button" href="#">-</a></li>';
						}
						echo '</ul>
						<span class="description">'. $desc .'</span>';

					// file
					} elseif ( 'file' == $type ) {
						$meta_img = get_post_meta( $post_id, $field_id, true );
					?>
					<div>
						<input type="text"  name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_url( $meta_img); ?>">
						<br><a href="#" class="button wolf-options-reset-file"><?php esc_html_e( 'Clear', 'wolf-videos' ); ?></a>
						<a href="#" class="button wolf-options-set-file"><?php esc_html_e( 'Choose a file', 'wolf-videos' ); ?></a>
					</div>

					<div style="clear:both"></div>
					<?php

					// image
					} elseif ( 'image' == $type ) {
						$meta_img = absint( get_post_meta( $post_id, $field_id, true ) );
						$meta_img_url = esc_url( wolf_get_url_from_attachment_id( $meta_img ) );
					?>
					<div>
						<input type="hidden"  name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo absint( $meta_img); ?>">
						<img style="max-width:250px;<?php if ( 0 == $meta_img ) echo ' display:none;'; ?>" class="wolf-options-img-preview" src="<?php echo esc_url( $meta_img_url ); ?>" alt="<?php echo esc_attr( $field_id ); ?>">
						<br><a href="#" class="button wolf-options-reset-img"><?php esc_html_e( 'Clear', 'wolf-videos' ); ?></a>
						<a href="#" class="button wolf-options-set-img"><?php esc_html_e( 'Choose an image', 'wolf-videos' ); ?></a>
					</div>

					<div style="clear:both"></div>
					<?php

					/*  Background
					-------------------------------------------*/
					} elseif ( 'background' == $type ) {

						$parallax           		= isset( $field['parallax'] ) ? $field['parallax'] : false;
						$bg_meta_color      	= get_post_meta( $post_id, $field_id . '_color', true );
						$bg_meta_repeat     	= get_post_meta( $post_id, $field_id . '_repeat', true );
						$bg_meta_position   	= get_post_meta( $post_id, $field_id . '_position', true );
						$bg_meta_attachment = get_post_meta( $post_id, $field_id . '_attachment', true );
						$bg_meta_size       	= get_post_meta( $post_id, $field_id . '_size', true );
						$bg_meta_parallax   	= get_post_meta( $post_id, $field_id . '_parallax', true );
						$exclude_params 	= isset( $field['exclude_params'] ) ?$field['exclude_params'] : array();

						$img = get_post_meta( $post_id, $field_id . '_img', true );

						if ( is_numeric( $img ) ) {
							$img = absint( get_post_meta( $post_id, $field_id . '_img', true ) );
							$img_url = wolf_get_url_from_attachment_id( $img, 'thumbnail' );
						} else {
							$img = esc_url( get_post_meta( $post_id, $field_id . '_img', true ) );
							$img_url = esc_url( $img );
						}

						/* Bg Image */
						if ( ! in_array( 'color', $exclude_params ) ) {
						?>
						<p><?php esc_html_e( 'Background color', 'wolf-videos' ); ?></p>
						<input name="<?php echo esc_attr( $field_id . '_color' ); ?>" name="<?php echo esc_attr( $field_id . '_color' ); ?>" class="wolf-options-colorpicker" type="text" value="<?php echo esc_attr( $bg_meta_color ); ?>">
						<br><br>
						<?php
						}
						if ( ! in_array( 'image', $exclude_params ) ) {

						?>
						<p><?php esc_html_e( 'Background image', 'wolf-videos' ); ?></p>
						<div>
							<input type="hidden" name="<?php echo esc_attr( $field_id ); ?>_img" id="<?php echo esc_attr( $field_id ); ?>_img" value="<?php echo esc_attr( $img ); ?>">
							<img style="max-width:250px;<?php if ( ! $img ) echo ' display:none;'; ?>" class="wolf-options-img-preview" src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $field_id ); ?>">
							<br><a href="#" class="button wolf-options-reset-bg"><?php esc_html_e( 'Clear', 'wolf-videos' ); ?></a>
							<a href="#" class="button wolf-options-set-bg"><?php esc_html_e( 'Choose an image', 'wolf-videos' ); ?></a>
						</div>
						<br><br>
						<?php
						}

						if ( ! in_array( 'repeat', $exclude_params ) ) {
						/* Bg Repeat */
						$options = array(  'no-repeat', 'repeat','repeat-x', 'repeat-y' );

						?>
						<br>
						<p><?php esc_html_e( 'Background repeat', 'wolf-videos' ); ?></p>
						<select name="<?php echo esc_attr( $field_id ) . '_repeat'; ?>" id="<?php echo esc_attr( $field_id ) . '_repeat'; ?>">
							<?php foreach ( $options as $o): ?>
								<option value="<?php echo esc_attr( $o ); ?>" <?php if ( $o == $bg_meta_repeat ) echo 'selected="selected"'; ?>><?php echo esc_attr( $o ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php
						}
						if ( ! in_array( 'position', $exclude_params ) ) {
						/* Bg position */
						$options = array(
							'center center',
							'center top',
							'left top' ,
							'right top' ,
							'center bottom',
							'left bottom' ,
							'right bottom' ,
							'left center' ,
							'right center'
						);

						?>
						<br><br>
						<p><?php esc_html_e( 'Background position', 'wolf-videos' ); ?></p>
						<select name="<?php echo esc_attr( $field_id ) . '_position'; ?>" id="<?php echo esc_attr( $field_id ) . '_position'; ?>">
							<?php foreach ( $options as $o): ?>
								<option value="<?php echo esc_attr( $o ); ?>" <?php if ( $o == $bg_meta_position ) echo 'selected="selected"'; ?>><?php echo esc_attr( $o ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php
						}
						if ( ! in_array( 'size', $exclude_params ) ) {

						/* size
						--------------------*/
						$options = array(
							'cover' => esc_html__( 'cover (resize)', 'wolf-videos' ),
							'normal' => esc_html__( 'normal', 'wolf-videos' ),
							'resize' => esc_html__( 'responsive (hard resize)', 'wolf-videos' ),
						);

						?>
						<br><br>
						<p><?php esc_html_e( 'Background size', 'wolf-videos' ); ?></p>
						<select name="<?php echo esc_attr( $field_id ) . '_size'; ?>" id="<?php echo esc_attr( $field_id ) . '_size'; ?>">
							<?php foreach ( $options as $k => $v ) : ?>
								<option value="<?php echo esc_attr( $k ); ?>" <?php if ( $k == $bg_meta_size ) echo 'selected="selected"'; ?>><?php echo sanitize_text_field( $v ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php
						}
						if ( $parallax ) {
							?>
							<br><br>
							<p><strong><?php esc_html_e( 'Parallax', 'wolf-videos' ); ?></strong></p>
							<input <?php if ( $bg_meta_parallax ) echo 'checked="checked"'; ?> type="checkbox" name="<?php echo esc_attr( $field_id ) . '_parallax'; ?>" id="<?php echo esc_attr( $field_id ) . '_parallax'; ?>">
							<?php
						}

					/*  Font
					-------------------------------------------*/
					} elseif ( 'font' == $type ) {
						$color 			= get_post_meta( $post_id, $field_id . '_font_color', true );
						$name 			= get_post_meta( $post_id, $field_id . '_font_name', true );
						$weight 		= get_post_meta( $post_id, $field_id . '_font_weight', true );
						$transform 		= get_post_meta( $post_id, $field_id . '_font_transform', true );
						$letter_spacing 		= get_post_meta( $post_id, $field_id . '_font_letter_spacing', true );
						$style 			= get_post_meta( $post_id, $field_id . '_font_style', true );
						$exclude_params 	= isset( $field['exclude_params'] ) ? $field['exclude_params'] : array();

						if ( ! in_array( 'color', $exclude_params ) ) {
						?>
						<p><?php esc_html_e( 'Font color', 'wolf-videos' ); ?></p>
						<input name="<?php echo esc_attr( $field_id ) . '_font_color'; ?>" name="<?php echo  $field_id . '_font_color'; ?>" class="wolf-options-colorpicker" type="text" value="<?php echo esc_attr( $color ); ?>">
						<br><br>
						<?php
						}

						if ( ! in_array( 'name', $exclude_params ) ) {
							global $wolf_google_fonts;
							$wolf_fonts = $wolf_google_fonts;
						?>
							<p><?php esc_html_e( 'Font Family', 'wolf-videos' ); ?></p>
							<select name="<?php echo esc_attr( $field_id ) . '_font_name'; ?>" id="<?php echo esc_attr( $field_id ) . '_font_name'; ?>">
								<option value=''><?php esc_html_e( 'default', 'wolf-videos' ); ?></option>
								<?php foreach ( $wolf_fonts as $k =>$v ) : ?>
									<option value="<?php echo esc_attr( $k ); ?>" <?php if ( $k == $name ) echo 'selected="selected"'; ?>><?php echo esc_attr( $k ); ?></option>
								<?php endforeach; ?>
							</select>
						<?php
						}

						if ( ! in_array( 'weight', $exclude_params ) ) {
						?>
							<br><br>
							<p><?php esc_html_e( 'Font weight', 'wolf-videos' ); ?></p>
							<input type="text" name="<?php echo esc_attr( $field_id ) ?>_font_weight" id="<?php echo esc_attr( $field_id ); ?>_font_weight" value="<?php echo esc_attr( $weight ); ?>" >
							<br><span class="description"><?php esc_html_e( 'For example: 400 is normal, 700 is bold.The available font weights depend on the font.<br>Leave empty to use the theme default style', 'wolf-videos' ); ?></span>
						<?php
						}

						if ( ! in_array( 'transform', $exclude_params ) ) {
							$options = array(
								'' => esc_html__( 'auto', 'wolf-videos' ),
								'none' => esc_html__( 'none', 'wolf-videos' ),
								'uppercase' => esc_html__( 'uppercase', 'wolf-videos' ),
							);
						?>
							<br><br>
							<p><?php esc_html_e( 'Font transform', 'wolf-videos' ); ?></p>
							<select name="<?php echo esc_attr( $field_id ) . '_font_transform'; ?>" id="<?php echo esc_attr( $field_id ) . '_font_transform'; ?>">
								<?php foreach ( $options as $k => $v ) : ?>
									<option value="<?php echo esc_attr( $k ); ?>" <?php if ( $k == $transform ) echo 'selected="selected"'; ?>><?php echo sanitize_text_field( $v ); ?></option>
								<?php endforeach; ?>
							</select>
							<br><span class="description"><?php esc_html_e( '"auto" is the default style in the theme options', 'wolf-videos' ); ?></span>
						<?php
						}

						if ( ! in_array( 'style', $exclude_params ) ) {
							$options = array(
								'' => esc_html__( 'auto', 'wolf-videos' ),
								'normal' => esc_html__( 'normal', 'wolf-videos' ),
								'italic' => esc_html__( 'italic', 'wolf-videos' ),
							);
						?>
							<br><br>
							<p><?php esc_html_e( 'Font style', 'wolf-videos' ); ?></p>
							<select name="<?php echo esc_attr( $field_id ) . '_font_style'; ?>" id="<?php echo esc_attr( $field_id ) . '_font_style'; ?>">
								<?php foreach ( $options as $k => $v ) : ?>
									<option value="<?php echo esc_attr( $k ); ?>" <?php if ( $k == $style ) echo 'selected="selected"'; ?>><?php echo sanitize_text_field( $v ); ?></option>
								<?php endforeach; ?>
							</select>
							<br><span class="description"><?php esc_html_e( '"auto" is the default style defined in the theme options', 'wolf-videos' ); ?></span>
						<?php
						}

						if ( ! in_array( 'letter_spacing', $exclude_params ) ) {
						?>
							<br><br>
							<p><?php esc_html_e( 'Font letter spacing (omit px)', 'wolf-videos' ); ?></p>
							<input type="text" name="<?php echo esc_attr( $field_id ) ?>_font_letter_spacing" id="<?php echo esc_attr( $field_id ); ?>_font_letter_spacing" value="<?php echo esc_attr( $letter_spacing ); ?>">
							<br><span class="description"><?php esc_html_e( 'Leave empty to use the style defined in the theme options', 'wolf-videos' ); ?></span>
						<?php
						}

					/*  Video
					-------------------------------------------*/
					} elseif ( 'video' == $type ) {
						$mp4 		= get_post_meta( $post_id, $field_id . '_mp4', true );
						$webm 	= get_post_meta( $post_id, $field_id . '_webm', true );
						$ogv 		= get_post_meta( $post_id, $field_id . '_ogv', true );
						$opacity 	= get_post_meta( $post_id, $field_id . '_opacity', true ) ? intval( get_post_meta( $post_id, $field_id . '_opacity', true ) ) : 100;
						$img 		= get_post_meta( $post_id, $field_id . '_img', true );

						if ( is_numeric( $img ) ) {
							$img = absint( get_post_meta( $post_id, $field_id . '_img', true ) );
							$img_url = wolf_get_url_from_attachment_id( $img, 'thumbnail' );
						} else {
							$img = esc_url( get_post_meta( $post_id, $field_id . '_img', true ) );
							$img_url = esc_url( $img );
						}
						?>
						<div>
							<p><strong><?php esc_html_e( 'mp4 URL', 'wolf-videos' ); ?></strong></p>
							<input type="text"  name="<?php echo esc_attr( $field_id ); ?>_mp4" id="<?php echo esc_attr( $field_id ); ?>_mp4" value="<?php echo esc_url( $mp4 ); ?>">
							<br><a href="#" class="button wolf-options-reset-file"><?php esc_html_e( 'Clear', 'wolf-videos' ); ?></a>
							<a href="#" class="button wolf-options-set-file"><?php esc_html_e( 'Choose a file', 'wolf-videos' ); ?></a>
							<br><br>
						</div>

						<div>
							<p><strong><?php esc_html_e( 'webm URL', 'wolf-videos' ); ?></strong></p>
							<input type="text"  name="<?php echo esc_attr( $field_id ); ?>_webm" id="<?php echo esc_attr( $field_id ); ?>_webm" value="<?php echo esc_url( $webm ); ?>">
							<br><a href="#" class="button wolf-options-reset-file"><?php esc_html_e( 'Clear', 'wolf-videos' ); ?></a>
							<a href="#" class="button wolf-options-set-file"><?php esc_html_e( 'Choose a file', 'wolf-videos' ); ?></a>
							<br><br>
						</div>

						<div>
							<p><strong><?php esc_html_e( 'ogv URL', 'wolf-videos' ); ?></strong></p>
							<input type="text"  name="<?php echo esc_attr( $field_id ); ?>_ogv" id="<?php echo esc_attr( $field_id ); ?>_ogv" value="<?php echo esc_url( $ogv ); ?>">
							<br><a href="#" class="button wolf-options-reset-file"><?php esc_html_e( 'Clear', 'wolf-videos' ); ?></a>
							<a href="#" class="button wolf-options-set-file"><?php esc_html_e( 'Choose a file', 'wolf-videos' ); ?></a>
							<br><br>
						</div>

						<div>
						<p><strong><?php esc_html_e( 'Video Image Fallback', 'wolf-videos' ); ?></strong></p>
						<input type="hidden"  name="<?php echo esc_attr( $field_id ); ?>_img" id="<?php echo esc_attr( $field_id ); ?>_img" value="<?php echo esc_attr( $img ); ?>">
						<img style="max-width:200px;<?php echo ( ! $img ) ? 'display:none;' : '' ?>" src="<?php echo esc_url( $img_url ); ?>" class="wolf-options-img-preview">
						<br><a href="#" class="button wolf-options-reset-img"><?php esc_html_e( 'Clear', 'wolf-videos' ); ?></a>
						<a href="#" class="button wolf-options-set-img"><?php esc_html_e( 'Choose an image', 'wolf-videos' ); ?></a>
						</div>
						<?php


				} //end conditions
			echo '</td></tr>';
			} // end foreach
			echo '</table>'; // end table
		}

		/**
		 * Save the post meta data
		 */
		public function save( $post_id ) {
			global $post;

			$meta_fields = '';

			// verify nonce
			if ( ( isset( $_POST['wolf_meta_box_nonce'] ) ) && ( ! wp_verify_nonce( $_POST['wolf_meta_box_nonce'], basename( __FILE__ ) ) ) )
				return $post_id;

			// check autosave
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return $post_id;

			// check permissions
			if ( isset( $_POST['post_type'] ) && is_object( $post ) ) {

				$current_post_type = get_post_type( $post->ID );

				if ( 'page' == $_POST['post_type'] ) {
					if ( ! current_user_can( 'edit_page', $post_id ) ) {
						return $post_id;

					} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
						return $post_id;
					}
				}

				foreach ( $this->meta as $k => $v ) {

					if ( is_array( $v['page'] ) )
						$condition = isset( $_POST['post_type'] ) && in_array( $_POST['post_type'], $v['page'] );
					else
						$condition = isset( $_POST['post_type'] ) && $_POST['post_type'] == $v['page'];

					if ( $condition ) {

						$meta_fields = $v['metafields'];

						// loop through fields and save the data
						foreach ( $meta_fields as $field ) {

							$field_id = $field['id'];
							$type = $field['type'];
							$meta = get_post_meta( $post_id, $field_id, true );

							if ( 'background' == $type ) {

								$bg_options = array( 'color', 'position', 'repeat', 'attachment', 'size', 'img', 'parallax' );

								foreach ( $bg_options as $s ) {

									$o = $field_id . '_' . $s;

									if ( isset( $_POST[ $o ] ) ) {

										$bg_data = $_POST[ $o ];

										if ( 'img' == $o ) {

											if ( is_numeric( $_POST[ $o ] ) ) {
												$bg_data = esc_url( $_POST[ $o ] );
											} else {
												$bg_data = absint( $_POST[ $o ] );
											}

										} else {
											$data = sanitize_text_field( $bg_data );
										}

										update_post_meta( $post_id, $o, $bg_data );

									} else {

										delete_post_meta( $post_id, $o );
									}
								}
							} // end background

							elseif ( 'font' == $type ) {

								$video_options = array( 'font_color', 'font_name', 'font_weight', 'font_transform', 'font_style', 'font_letter_spacing' );

								foreach ( $video_options as $s ) {

									$o = $field_id . '_' . $s;

									if ( isset( $_POST[ $o ] ) ) {

										update_post_meta( $post_id, $o, $_POST[ $o ] );
									} else {

										delete_post_meta( $post_id, $o );
									}
								}
							} // end font

							elseif ( 'video' == $type ) {

								$video_options = array( 'mp4', 'webm', 'ogv', 'opacity', 'img' );

								foreach ( $video_options as $s ) {

									$o = $field_id . '_' . $s;

									if ( isset( $_POST[ $o ] ) ) {

										$video_bg_data = $_POST[ $o ];

										if ( 'img' == $o ) {

											if ( is_numeric( $_POST[ $o ] ) ) {
												$video_bg_data = esc_url( $_POST[ $o ] );
											} else {
												$video_bg_data = absint( $_POST[ $o ] );
											}

										} else {
											$data = esc_url( $video_bg_data );
										}

										update_post_meta( $post_id, $o, $video_bg_data );

									} else {

										delete_post_meta( $post_id, $o );
									}
								}

							} // end video

							elseif ( 'repeatable' == $type ) {

								if ( isset( $_POST[ $field_id ] ) ) {
									
									$new = is_array( $_POST[ $field_id ] ) ? $_POST[ $field_id ] : array();

									update_post_meta( $post_id, $field_id, $new );
								}
							} // end repeatable

							else {
								$old = get_post_meta( $post_id, $field_id, true );
								$new = '';

								if ( isset( $_POST[ $field_id ] ) ) {

									if ( 'int' == $type ) {
										
										$new = absint( $_POST[ $field_id ] );

									} elseif ( 'url' == $type ) {
										
										$new = esc_url( $_POST[ $field_id ] );
									
									} elseif ( 'editor' == $type ) {
										
										$new = $_POST[ $field_id ];
									} else {
										
										$new = sanitize_text_field( $_POST[ $field_id ] );
									}
								}


								if ( $new && $new != $old ) {

									update_post_meta( $post_id, $field_id, $new );

								} elseif ( '' == $new && $old ) {

									delete_post_meta( $post_id, $field_id, $old );
								}
							}
						} // end foreach
					}
				}
			}
		}
	} // end class

} // end class exists check