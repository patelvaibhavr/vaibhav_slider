<?php
/**
 * Plugin Name: Vaibhav_Slider
 * Plugin URI: http://vaibhavpatel.in/
 * Description: Image Slider
 * Version: 1.1.1
 * Author: Vaibhav Patel
 * Author URI: http://vaibhavpatel.in/
 * Text Domain: http://vaibhavpatel.in/
 * License: GPL2
 */
/*
 * Copyright 2014 - 2015 Vaibhav Patel (email : patelvaibhavr@yahoo.com)
 */
defined ( 'ABSPATH' ) or die ( "No script kiddies please!" );

// Admin Vaibhav Slider Admin Menu And Setting Menu
add_action ( 'admin_menu', 'vaibhav_slider' );
function vaibhav_slider() {
	global $post;
	global $vaibhav_slider_hook;
	add_menu_page('VaibhavSlider', 'Vaibhav Slider', 'manage_options', 'vaibhav_slider', 'vaibhav_slider_menu', 'dashicons-images-alt');
	$vaibhav_slider_hook = add_options_page ( 'Vaibhav_Slider', 'Vaibhav Slider', 'manage_options', 'vaibhav_slider', 'vaibhav_slider_menu' );
}

// Add Option in Setting Menu
function vaibhav_slider_menu() {
	?>
<div class="vp_loader">
	<img src="<?php echo plugins_url('lib/css/loader.GIF', __FILE__); ?>" />
</div>
<div class="vaibhav_slider_admin vp_container">
	<h2>Upload Images</h2><h4> (Shortcode : [vaibhav_slider] For Add Slider in your Page)</h4>
	<span><strong>Note : </strong>Slide Image Minimum Size => width = 100px	& height = 100px</span>
	<hr />
	<form id="f1" class="vp_left"  enctype="multipart/form-data" method="POST" action="#"  name="media_upload">
		<input type="hidden" name="action" id="action" value="vp_ajax_upload" />
		<input type="button" name="UploadImage" id="uploadImage" class="vp_btn" value="Upload Image" />
         <span id="imageSrc">No Image Is Selected..</span> <input type="file" name="url" id="slide1" /> 
         <input type="submit" name="Upload" id="upload" class="vp_btn"	value="Add Slide" />
	</form>
	<input type="button" name="change_order" id="change_order"
		class="vp_btn vp_right" value="Change Slide Order" />
         <input	type="button" name="save" id="save" class="vp_btn vp_right" value="Save Slider" />
	<div class="vp_clear"></div>
	<hr />
	<div class="vp_msgBox"></div>
	<div class="vp_clear"></div>
    <br/>    
	<div class="table connectedSortable  vp_block" id="table"></div>
    <br/>
</div>
<?php
}

// Add Scripts vaibhavSlider hook
add_action ( 'admin_enqueue_scripts', 'vaibhav_slider_script' );
function vaibhav_slider_script($hook) {
	global $vaibhav_slider_hook;
	if ($hook != $vaibhav_slider_hook) {
		return;
	}
	all_scripts ();
}
function all_scripts() {
	wp_enqueue_style ( 'vaibhav_images_style', plugins_url ( 'lib/css/vp_images_style.css', __FILE__ ), array (), null, 'all' );
	wp_enqueue_script ( 'vaibhavslider_jquery', plugin_dir_url ( __FILE__ ) . 'lib/js/jquery-1.10.2.js', array ('jquery'), '1.0.0', false );
	wp_enqueue_script ( 'vaibhavslider_jquery_ui', plugin_dir_url ( __FILE__ ) . 'lib/js/jquery-ui.js', array ('vaibhavslider_jquery'), '1.0.0', false );
	wp_enqueue_script ( 'vaibhavslider_script', plugin_dir_url ( __FILE__ ) . 'lib/js/script.js', array ('vaibhavslider_jquery_ui'), '1.0.0', false );
	wp_enqueue_script ( 'vaibhavslider_ajax_script', plugin_dir_url ( __FILE__ ) . 'lib/js/ajax_script.js', array ('vaibhavslider_script'), '1.0.0', true );
}

add_action ( 'wp_ajax_vp_ajax_upload', 'ajax_vp_ajax_upload' );
function ajax_vp_ajax_upload() {
	$image = $_FILES ['url'];
	$getimagesize = getimagesize ( $_FILES ['url'] ['tmp_name'] );
	if ($getimagesize [0] >= 100 && $getimagesize [1] >= 100) {
		$upload_overrides = array ('test_form' => false);
		$movefile = wp_handle_upload ( $image, $upload_overrides );
		if ($movefile) {
			$my_post = array ();
			$my_post ['post_content'] = $movefile ['url'];
			$my_post ['guid'] = $movefile ['url'];
			$my_post ['post_mime_type'] = $movefile ['type'];
			$my_post ['post_status'] = 'publish';
			$my_post ['post_type'] = 'vaibhav_slider';
			$id = wp_insert_post ( $my_post );
			$order = get_option ( 'vaibhavslider_display_order' );
			$order = getOrder($order,$id);
			update_option ( 'vaibhavslider_display_order', $order );
			echo "0";
		} else {
			echo "1";
		}
	} else {
		echo "2";
	}
	die ();
}

function getOrder($order,$id){
	if ($order != "") {
		$order = $order . "," . $id;
	} else {
		$order = $order . $id;
	}
	return $order;
}

add_action ( 'wp_ajax_vaibhavslider_ajax_images_list', 'vaibhavslider_ajax_images_list' );
function vaibhavslider_ajax_images_list() {
	$order = get_option ( 'vaibhavslider_display_order' );
	
	if ($order != "") {
		$order = explode ( ',', $order );
                $cnt1=sizeof( $order );
		for($i = 0; $i < $cnt1; $i ++) {
			$vaibhavslider_post = get_post ( $order [$i] );
			?>
<br/>
<div class="row vp_image_row vp_inline_block" id='<?php echo "ID_".$order[$i]; ?>'>

	<label class="vp_left vp_slide_label">Image-<?php echo $i+1;?></label>
     <input	type="button" onclick="deleteImage(<?php echo $order[$i];?>)" name="delete_image" class="vp_btn vp_delete_btn" value="X" />
	<div class="vp_clear"></div>

	<div class="column vp_block">
		<div class="wrap">
			<img class="fake" src="<?php echo $vaibhavslider_post->post_content;?>"/>
			<div class="img_wrap">
				<img class="normal"	src="<?php echo $vaibhavslider_post->post_content;?>"/>
			</div>
		</div>
             <div class="wrap2">
             	<div class="wrap3"></div>
             </div>
	</div>
	<div class="column vp_block vp_top">
		<input type="hidden" name="id" value="1"> <input type="hidden"	name="order" value="1">
	</div>
</div>
<?php
		}
	}
	die ();
}

add_action ( 'wp_ajax_vaibhavslider_ajax_update_order', 'vaibhavslider_ajax_update_order' );
function vaibhavslider_ajax_update_order() {
	$newOrder = $_POST ['ID'];
	$displayorder = "";
        $cnt2=sizeof( $newOrder );
	for($i = 0; $i < $cnt2; $i ++) {
		$displayorder = getOrder($displayorder,$newOrder [$i]);
	}
	update_option ( 'vaibhavslider_display_order', $displayorder );
	die ();
}

add_shortcode ( 'vaibhav_slider', 'vaibhav_slider_Shortcode' );
function vaibhav_slider_Shortcode() {
	all_scripts ();
	wp_enqueue_style ( 'vaibhavslider_bootstrap_style', plugins_url ( '/lib/css/bootstrap.min.css', __FILE__ ), array (), null, 'all' );
	wp_enqueue_script ( 'vaibhavslider_bootstrap_jq_script', plugin_dir_url ( __FILE__ ) . 'lib/js/jquery.min.js', array ('vaibhavslider_script'), '1.0.0', false );
	wp_enqueue_script ( 'vaibhavslider_bootstrap_script', plugin_dir_url ( __FILE__ ) . 'lib/js/bootstrap.min.js', array ('vaibhavslider_bootstrap_jq_script'), '1.0.0', false );
	$order = get_option ( 'vaibhavslider_display_order' );
	$slide = '<div id="change" class="vertical-slider carousel vertical slide" data-ride="carousel" data-interval="4000"><ol class="carousel-indicators">';
	if ($order != "") {
		$order = explode ( ',', $order );
                $cnt3=sizeof( $order );
		for($i = 0; $i < $cnt3; $i ++) {
			if ($i == 0) {
				$slide .= '<li data-target="#change" data-slide-to="0" class="active"></li>';
			} else {
				$slide .= '<li data-target="#change" data-slide-to="' . $i . '"></li>';
			}
		}
	}
	$slide .= '</ol><div class="carousel-inner" role="listbox">';
	$order = get_option ( 'vaibhavslider_display_order' );
	if ($order != "") {
		$order = explode ( ',', $order );
                $cnt4=sizeof( $order );
		for($i = 0; $i < $cnt4; $i ++) {
			$vaibhavslider_post = get_post ( $order [$i] );
			if ($i == 0) {
				$slide .= '<div class="item active">
      									<img src="' . $vaibhavslider_post->post_content . '" alt="" >
										<div class="carousel-caption"></div>
								</div>';
			} else {
				$slide .= '<div class="item">
										<img src="' . $vaibhavslider_post->post_content . '" alt="">
										<div class="carousel-caption"></div>
							</div>';
			}
		}
	}
	$slide .= '</div>
  							<a class="left carousel-control" href="#change" role="button" data-slide="prev">
    								<span class="glyphicon glyphicon-circle-arrow-down"></span>
  							</a>
  							<a class="right carousel-control" href="#change" role="button" data-slide="next">
    								<span class="glyphicon glyphicon-circle-arrow-up"></span>
  							</a>
				</div>';
	return $slide;
}

add_action ( 'wp_ajax_vaibhavslider_delete_image', 'vaibhavslider_delete_imagePost' );
function vaibhavslider_delete_imagePost() {
	$postID = $_POST ['id'];
	$order = get_option ( 'vaibhavslider_display_order' );
	$updateorder = "";
	if ($order != "") {
		$order = explode ( ',', $order );
                $cnt5=sizeof( $order );
		for($i = 0; $i < $cnt5; $i ++) {
			if ($order [$i] != $postID) {
				$updateorder = getOrder($updateorder,$order [$i]);
			}
		}
	}
	update_option ( 'vaibhavslider_display_order', $updateorder );
	wp_delete_post ( $postID );
	echo "Post Order => " . $updateorder;
	die ();
}
?>
