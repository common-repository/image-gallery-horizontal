<?php
/*
Plugin Name: Image Gallery Horizontal
Plugin URL: http://beautiful-module.com/demo/image-gallery-horizontal-bar
Description: Responsive Image Gallery Horizontal
Version: 1.0
Author: Module Express
Author URI: http://beautiful-module.com
Contributors: Module Express
*/
/*
 * Register CPT sp_gallery.horizontal.bar
 *
 */
if(!class_exists('Image_Gallery_Horizontal')) {
	class Image_Gallery_Horizontal {

		function __construct() {
		    if(!function_exists('add_shortcode')) {
		            return;
		    }
			add_action ( 'init' , array( $this , 'ighb_responsive_gallery_setup_post_types' ));

			/* Include style and script */
			add_action ( 'wp_enqueue_scripts' , array( $this , 'ighb_register_style_script' ));
			
			/* Register Taxonomy */
			add_action ( 'init' , array( $this , 'ighb_responsive_gallery_taxonomies' ));
			add_action ( 'add_meta_boxes' , array( $this , 'ighb_rsris_add_meta_box_gallery' ));
			add_action ( 'save_post' , array( $this , 'ighb_rsris_save_meta_box_data_gallery' ));
			register_activation_hook( __FILE__, 'ighb_responsive_gallery_rewrite_flush' );


			// Manage Category Shortcode Columns
			add_filter ( 'manage_responsive_ighb_slider-category_custom_column' , array( $this , 'ighb_responsive_gallery_category_columns' ), 10, 3);
			add_filter ( 'manage_edit-responsive_ighb_slider-category_columns' , array( $this , 'ighb_responsive_gallery_category_manage_columns' ));
			require_once( 'ighb_gallery_admin_settings_center.php' );
		    add_shortcode ( 'sp_gallery.horizontal.bar' , array( $this , 'ighb_responsivegallery_shortcode' ));
		}


		function ighb_responsive_gallery_setup_post_types() {

			$responsive_gallery_labels =  apply_filters( 'master_gallery_image_labels', array(
				'name'                => 'Image Gallery Horizontal',
				'singular_name'       => 'Image Gallery Horizontal',
				'add_new'             => __('Add New', 'master_gallery_image'),
				'add_new_item'        => __('Add New Image', 'master_gallery_image'),
				'edit_item'           => __('Edit Image', 'master_gallery_image'),
				'new_item'            => __('New Image', 'master_gallery_image'),
				'all_items'           => __('All Image', 'master_gallery_image'),
				'view_item'           => __('View Image', 'master_gallery_image'),
				'search_items'        => __('Search Image', 'master_gallery_image'),
				'not_found'           => __('No Image found', 'master_gallery_image'),
				'not_found_in_trash'  => __('No Image found in Trash', 'master_gallery_image'),
				'parent_item_colon'   => '',
				'menu_name'           => __('Image Gallery Horizontal', 'master_gallery_image'),
				'exclude_from_search' => true
			) );


			$responsiveslider_args = array(
				'labels' 			=> $responsive_gallery_labels,
				'public' 			=> true,
				'publicly_queryable'		=> true,
				'show_ui' 			=> true,
				'show_in_menu' 		=> true,
				'query_var' 		=> true,
				'capability_type' 	=> 'post',
				'has_archive' 		=> true,
				'hierarchical' 		=> false,
				'menu_icon'   => 'dashicons-format-gallery',
				'supports' => array('title','editor','thumbnail')
				
			);
			register_post_type( 'master_gallery_image', apply_filters( 'sp_faq_post_type_args', $responsiveslider_args ) );

		}
		
		function ighb_register_style_script() {
		    wp_enqueue_style( 'css_responsiveimgslider',  plugin_dir_url( __FILE__ ). 'css/responsiveimgslider.css' );
			/*   REGISTER ALL CSS FOR SITE */
			wp_enqueue_style( 'css_main',  plugin_dir_url( __FILE__ ). 'css/gallerybar.css' );

			/*   REGISTER ALL JS FOR SITE */			
			wp_enqueue_script( 'js_jssor.core', plugin_dir_url( __FILE__ ) . 'js/jssor.core.js', array( 'jquery' ));
			wp_enqueue_script( 'js_jssor.utils', plugin_dir_url( __FILE__ ) . 'js/jssor.utils.js', array( 'jquery' ));
			wp_enqueue_script( 'js_jssor.slider', plugin_dir_url( __FILE__ ) . 'js/jssor.slider.js', array( 'jquery' ));
			
		}
		
		
		function ighb_responsive_gallery_taxonomies() {
		    $labels = array(
		        'name'              => _x( 'Category', 'taxonomy general name' ),
		        'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
		        'search_items'      => __( 'Search Category' ),
		        'all_items'         => __( 'All Category' ),
		        'parent_item'       => __( 'Parent Category' ),
		        'parent_item_colon' => __( 'Parent Category:' ),
		        'edit_item'         => __( 'Edit Category' ),
		        'update_item'       => __( 'Update Category' ),
		        'add_new_item'      => __( 'Add New Category' ),
		        'new_item_name'     => __( 'New Category Name' ),
		        'menu_name'         => __( 'Gallery Category' ),
		    );

		    $args = array(
		        'hierarchical'      => true,
		        'labels'            => $labels,
		        'show_ui'           => true,
		        'show_admin_column' => true,
		        'query_var'         => true,
		        'rewrite'           => array( 'slug' => 'responsive_ighb_slider-category' ),
		    );

		    register_taxonomy( 'responsive_ighb_slider-category', array( 'master_gallery_image' ), $args );
		}

		function ighb_responsive_gallery_rewrite_flush() {  
				ighb_responsive_gallery_setup_post_types();
		    flush_rewrite_rules();
		}


		function ighb_responsive_gallery_category_manage_columns($theme_columns) {
		    $new_columns = array(
		            'cb' => '<input type="checkbox" />',
		            'name' => __('Name'),
		            'gallery_hozizontal_shortcode' => __( 'Gallery Category Shortcode', 'hozizontal_slick_slider' ),
		            'slug' => __('Slug'),
		            'posts' => __('Posts')
					);

		    return $new_columns;
		}

		function ighb_responsive_gallery_category_columns($out, $column_name, $theme_id) {
		    $theme = get_term($theme_id, 'responsive_ighb_slider-category');

		    switch ($column_name) {      
		        case 'title':
		            echo get_the_title();
		        break;
		        case 'gallery_hozizontal_shortcode':
					echo '[sp_gallery.horizontal.bar cat_id="' . $theme_id. '"]';			  	  

		        break;
		        default:
		            break;
		    }
		    return $out;   

		}

		/* Custom meta box for slider link */
		function ighb_rsris_add_meta_box_gallery() {
			add_meta_box('custom-metabox',__( 'LINK URL', 'link_textdomain' ),array( $this , 'ighb_rsris_gallery_box_callback' ),'master_gallery_image');			
		}
		
		function ighb_rsris_gallery_box_callback( $post ) {
			wp_nonce_field( 'ighb_rsris_save_meta_box_data_gallery', 'rsris_meta_box_nonce' );
			$value = get_post_meta( $post->ID, 'rsris_ihgb_link', true );
			echo '<input type="url" id="rsris_ihgb_link" name="rsris_ihgb_link" value="' . esc_attr( $value ) . '" size="25" /><br />';
			echo 'ie http://www.google.com';
		}
		
		function ighb_rsris_save_meta_box_data_gallery( $post_id ) {
			if ( ! isset( $_POST['rsris_meta_box_nonce'] ) ) {
				return;
			}
			if ( ! wp_verify_nonce( $_POST['rsris_meta_box_nonce'], 'ighb_rsris_save_meta_box_data_gallery' ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( isset( $_POST['post_type'] ) && 'master_gallery_image' == $_POST['post_type'] ) {

				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return;
				}
			} else {

				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
			}
			if ( ! isset( $_POST['rsris_ihgb_link'] ) ) {
				return;
			}
			$link_data = sanitize_text_field( $_POST['rsris_ihgb_link'] );
			update_post_meta( $post_id, 'rsris_ihgb_link', $link_data );
		}
		
		/*
		 * Add [sp_gallery.horizontal.bar] shortcode
		 *
		 */
		function ighb_responsivegallery_shortcode( $atts, $content = null ) {
			
			extract(shortcode_atts(array(
				"limit"  => '',
				"cat_id" => '',
				"autoplay" => '',
				"autoplay_interval" => ''
			), $atts));
			
			if( $limit ) { 
				$posts_per_page = $limit; 
			} else {
				$posts_per_page = '-1';
			}
			if( $cat_id ) { 
				$cat = $cat_id; 
			} else {
				$cat = '';
			}
			
			if( $autoplay ) { 
				$autoplay_slider = $autoplay; 
			} else {
				$autoplay_slider = 'true';
			}	 	
			
			if( $autoplay_interval ) { 
				$autoplay_intervalslider = $autoplay_interval; 
			} else {
				$autoplay_intervalslider = '4000';
			}
						

			ob_start();
			// Create the Query
			$post_type 		= 'master_gallery_image';
			$orderby 		= 'post_date';
			$order 			= 'DESC';
						
			 $args = array ( 
		            'post_type'      => $post_type, 
		            'orderby'        => $orderby, 
		            'order'          => $order,
		            'posts_per_page' => $posts_per_page,  
		           
		            );
			if($cat != ""){
		            	$args['tax_query'] = array( array( 'taxonomy' => 'responsive_ighb_slider-category', 'field' => 'id', 'terms' => $cat) );
		            }        
		      $query = new WP_Query($args);

			$post_count = $query->post_count;
			$i = 1;

			if( $post_count > 0) :
			?>
				<div id="ighb_slider1_container" style="position: relative; top: 0px; left: 0px; width: 800px;
					height: 456px; background: #191919; overflow: hidden;">

					<!-- Loading Screen -->
					<div u="loading" style="position: absolute; top: 0px; left: 0px;">
						<div style="filter: alpha(opacity=70); opacity:0.7; position: absolute; display: block;
							background-color: #000000; top: 0px; left: 0px;width: 100%;height:100%;">
						</div>
						<div class="exc-loading-background">
						</div>
					</div>

					<div u="slides" style="cursor: move; position: absolute; left: 0px; top: 0px; width: 800px; height: 356px; overflow: hidden;">
						<?php								
							while ($query->have_posts()) : $query->the_post();
								include('designs/design-1.php');
								
							$i++;
							endwhile;									
						?>						
					</div>
					
					<span u="arrowleft" class="jssord05l" style="width: 40px; height: 40px; top: 158px; left: 8px;">
					</span>
					<span u="arrowright" class="jssord05r" style="width: 40px; height: 40px; top: 158px; right: 8px">
					</span>

					<div u="thumbnavigator" class="jssort01" style="position: absolute; width: 800px; height: 100px; left:0px; bottom: 0px;">
					
						<div u="slides" style="cursor: move;">
							<div u="prototype" class="p" style="position: absolute; width: 74px; height: 74px; top: 0; left: 0;">
								<div class=w><thumbnailtemplate style="width: 100%; height: 100%; border: none;position:absolute; top: 0; left: 0;"></thumbnailtemplate></div>
								<div class=c>
								</div>
							</div>
						</div>
					</div>
				</div>
	
				<?php
				endif;
				// Reset query to prevent conflicts
				wp_reset_query();
			?>							
			<script type="text/javascript">
			jQuery(document).ready(function ($) {

				var _SlideshowTransitions = [
				//Fade in L
					{$Duration: 1200, $During: { $Left: [0.3, 0.7] }, $FlyDirection: 1, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleHorizontal: 0.3, $Opacity: 2 }
				//Fade out R
					, { $Duration: 1200, $SlideOut: true, $FlyDirection: 2, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleHorizontal: 0.3, $Opacity: 2 }
				//Fade in R
					, { $Duration: 1200, $During: { $Left: [0.3, 0.7] }, $FlyDirection: 2, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleHorizontal: 0.3, $Opacity: 2 }
				//Fade out L
					, { $Duration: 1200, $SlideOut: true, $FlyDirection: 1, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleHorizontal: 0.3, $Opacity: 2 }

				//Fade in T
					, { $Duration: 1200, $During: { $Top: [0.3, 0.7] }, $FlyDirection: 4, $Easing: { $Top: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleVertical: 0.3, $Opacity: 2 }
				//Fade out B
					, { $Duration: 1200, $SlideOut: true, $FlyDirection: 8, $Easing: { $Top: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleVertical: 0.3, $Opacity: 2 }
				//Fade in B
					, { $Duration: 1200, $During: { $Top: [0.3, 0.7] }, $FlyDirection: 8, $Easing: { $Top: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleVertical: 0.3, $Opacity: 2 }
				//Fade out T
					, { $Duration: 1200, $SlideOut: true, $FlyDirection: 4, $Easing: { $Top: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleVertical: 0.3, $Opacity: 2 }

				//Fade in LR
					, { $Duration: 1200, $Cols: 2, $During: { $Left: [0.3, 0.7] }, $FlyDirection: 1, $ChessMode: { $Column: 3 }, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleHorizontal: 0.3, $Opacity: 2 }
				//Fade out LR
					, { $Duration: 1200, $Cols: 2, $SlideOut: true, $FlyDirection: 1, $ChessMode: { $Column: 3 }, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleHorizontal: 0.3, $Opacity: 2 }
				//Fade in TB
					, { $Duration: 1200, $Rows: 2, $During: { $Top: [0.3, 0.7] }, $FlyDirection: 4, $ChessMode: { $Row: 12 }, $Easing: { $Top: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleVertical: 0.3, $Opacity: 2 }
				//Fade out TB
					, { $Duration: 1200, $Rows: 2, $SlideOut: true, $FlyDirection: 4, $ChessMode: { $Row: 12 }, $Easing: { $Top: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleVertical: 0.3, $Opacity: 2 }

				//Fade in LR Chess
					, { $Duration: 1200, $Cols: 2, $During: { $Top: [0.3, 0.7] }, $FlyDirection: 4, $ChessMode: { $Column: 12 }, $Easing: { $Top: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleVertical: 0.3, $Opacity: 2 }
				//Fade out LR Chess
					, { $Duration: 1200, $Cols: 2, $SlideOut: true, $FlyDirection: 8, $ChessMode: { $Column: 12 }, $Easing: { $Top: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleVertical: 0.3, $Opacity: 2 }
				//Fade in TB Chess
					, { $Duration: 1200, $Rows: 2, $During: { $Left: [0.3, 0.7] }, $FlyDirection: 1, $ChessMode: { $Row: 3 }, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleHorizontal: 0.3, $Opacity: 2 }
				//Fade out TB Chess
					, { $Duration: 1200, $Rows: 2, $SlideOut: true, $FlyDirection: 2, $ChessMode: { $Row: 3 }, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleHorizontal: 0.3, $Opacity: 2 }

				//Fade in Corners
					, { $Duration: 1200, $Cols: 2, $Rows: 2, $During: { $Left: [0.3, 0.7], $Top: [0.3, 0.7] }, $FlyDirection: 5, $ChessMode: { $Column: 3, $Row: 12 }, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Top: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleHorizontal: 0.3, $ScaleVertical: 0.3, $Opacity: 2 }
				//Fade out Corners
					, { $Duration: 1200, $Cols: 2, $Rows: 2, $During: { $Left: [0.3, 0.7], $Top: [0.3, 0.7] }, $SlideOut: true, $FlyDirection: 5, $ChessMode: { $Column: 3, $Row: 12 }, $Easing: { $Left: $JssorEasing$.$EaseInCubic, $Top: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $ScaleHorizontal: 0.3, $ScaleVertical: 0.3, $Opacity: 2 }

				//Fade Clip in H
					, { $Duration: 1200, $Delay: 20, $Clip: 3, $Assembly: 260, $Easing: { $Clip: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $Opacity: 2 }
				//Fade Clip out H
					, { $Duration: 1200, $Delay: 20, $Clip: 3, $SlideOut: true, $Assembly: 260, $Easing: { $Clip: $JssorEasing$.$EaseOutCubic, $Opacity: $JssorEasing$.$EaseLinear }, $Opacity: 2 }
				//Fade Clip in V
					, { $Duration: 1200, $Delay: 20, $Clip: 12, $Assembly: 260, $Easing: { $Clip: $JssorEasing$.$EaseInCubic, $Opacity: $JssorEasing$.$EaseLinear }, $Opacity: 2 }
				//Fade Clip out V
					, { $Duration: 1200, $Delay: 20, $Clip: 12, $SlideOut: true, $Assembly: 260, $Easing: { $Clip: $JssorEasing$.$EaseOutCubic, $Opacity: $JssorEasing$.$EaseLinear }, $Opacity: 2 }
					];

				var options = {
					$AutoPlay: <?php if($autoplay_slider == "false") { echo 'false';} else { echo 'true'; } ?>,                                    //[Optional] Whether to auto play, to enable slideshow, this option must be set to true, default value is false
					$AutoPlayInterval: <?php echo $autoplay_intervalslider; ?>,                            //[Optional] Interval (in milliseconds) to go for next slide since the previous stopped if the slider is auto playing, default value is 3000
					$PauseOnHover: 3,                                //[Optional] Whether to pause when mouse over if a slider is auto playing, 0 no pause, 1 pause for desktop, 2 pause for touch device, 3 pause for desktop and touch device, default value is 3

					$DragOrientation: 3,                                //[Optional] Orientation to drag slide, 0 no drag, 1 horizental, 2 vertical, 3 either, default value is 1 (Note that the $DragOrientation should be the same as $PlayOrientation when $DisplayPieces is greater than 1, or parking position is not 0)
					$ArrowKeyNavigation: true,   			            //[Optional] Allows keyboard (arrow key) navigation or not, default value is false
					$SlideDuration: 800,                                //Specifies default duration (swipe) for slide in milliseconds

					$SlideshowOptions: {                                //[Optional] Options to specify and enable slideshow or not
						$Class: $JssorSlideshowRunner$,                 //[Required] Class to create instance of slideshow
						$Transitions: _SlideshowTransitions,            //[Required] An array of slideshow transitions to play slideshow
						$TransitionsOrder: 1,                           //[Optional] The way to choose transition to play slide, 1 Sequence, 0 Random
						$ShowLink: true                                    //[Optional] Whether to bring slide link on top of the slider when slideshow is running, default value is false
					},

					$DirectionNavigatorOptions: {                       //[Optional] Options to specify and enable direction navigator or not
						$Class: $JssorDirectionNavigator$,              //[Requried] Class to create direction navigator instance
						$ChanceToShow: 1                               //[Required] 0 Never, 1 Mouse Over, 2 Always
					},

					$ThumbnailNavigatorOptions: {                       //[Optional] Options to specify and enable thumbnail navigator or not
						$Class: $JssorThumbnailNavigator$,              //[Required] Class to create thumbnail navigator instance
						$ChanceToShow: 2,                               //[Required] 0 Never, 1 Mouse Over, 2 Always

						$ActionMode: 1,                                 //[Optional] 0 None, 1 act by click, 2 act by mouse hover, 3 both, default value is 1
						$SpacingX: 6,                                   //[Optional] Horizontal space between each thumbnail in pixel, default value is 0
						$DisplayPieces: 10,                             //[Optional] Number of pieces to display, default value is 1
						$ParkingPosition: 360                          //[Optional] The offset position to park thumbnail
					}
				};

				var jssor_slider1 = new $JssorSlider$("ighb_slider1_container", options);
				//responsive code begin
				//you can remove responsive code if you don't want the slider scales while window resizes
				function ScaleSlider() {
					var parentWidth = jssor_slider1.$Elmt.parentNode.clientWidth;
					if (parentWidth)
						jssor_slider1.$SetScaleWidth(Math.max(Math.min(parentWidth, 800), 300));
					else
						window.setTimeout(ScaleSlider, 30);
				}

				ScaleSlider();

				if (!navigator.userAgent.match(/(iPhone|iPod|iPad|BlackBerry|IEMobile)/)) {
					$(window).bind('resize', ScaleSlider);
				}
				//responsive code end
			});
			</script>
			<?php
			return ob_get_clean();
		}		
	}
}
	
function ighb_master_gallery_images_load() {
        global $mfpd;
        $mfpd = new Image_Gallery_Horizontal();
}
add_action( 'plugins_loaded', 'ighb_master_gallery_images_load' );