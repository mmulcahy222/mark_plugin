<?php 
/*
Plugin Name: Mark's Plugin
Description: Enables Google Maps in Advanced Custom Fields & Other tasks
Version:     20170612
Author:      the_goon
Text Domain: mark_plugin
*/


include("shortcodes.php");



////////////////////
//
//  GOOGLE MAPS
//
////////////////////
function my_acf_google_map_api( $api ){
	$api['key'] = '';
	return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');
function pan_to_center(){
	$post_type = get_post_type();
	$screen = get_current_screen();
	if((strstr( $_SERVER['REQUEST_URI'], 'post.php' )||strstr( $_SERVER['REQUEST_URI'], 'post-new.php' ))&&$post_type == 'show')
	{
	echo '<script>var timer = setInterval(center_map, 500); function center_map() {map = acf.fields.google_map.map; try {lat = map.getCenter().lat(); if(lat == 39) {clearInterval(timer); console.log("map centered correctly"); return; } map.setCenter({lat: 39, lng: -94}); map.setZoom(4); } catch(err) {console.log("No lat() function exists"); } }</script>';
	}
}
add_action('admin_footer', 'pan_to_center', 35,1);






////////////////////
//
//  PLUGIN FOR SHOW ADMIN VENUE
//
////////////////////
add_action('wp_ajax_look', 'find_venues',45,1);
add_action('wp_ajax_nopriv_look', 'find_venues',45,1);
add_action('admin_footer', 'se_wp_admin',45,1);
function find_venues() {
	global $wpdb;
	$args = array('post_type' => 'show','nopaging' => true, 'meta_key' => 'venue', "orderby"=>"venue", 'order'=>'ASC');
	$custom_posts = get_posts($args);
	$nodes = array();
	$venues = array();
	$i = 0;
	foreach ($custom_posts as $post) 
	{
		if(!in_array($post->venue, $venues))
		{
			$venues[] = $post->venue;
			$nodes[$i]['venue'] = "{$post->venue} ({$post->show_address})";
			$nodes[$i]['show_address'] = $post->show_address;
			$i += 1;
		}
	}
	echo json_encode($nodes);
	die();
}
function se_wp_admin() {


	?>
	<script>

		var venues = [];
		var addresses = [];
		var node = '';
		jQuery.ajax({
			url: "<?php echo admin_url('admin-ajax.php'); ?>?action=look",
			type: 'GET',
		})
		.done(function(r) {
			console.log("venue autocomplete");
			r = JSON.parse(r);
			for(var i = 0; i < r.length; i++) 
			{

				node = r[i];
				venues[i] = node.venue;
				addresses[node.venue] = node.show_address;	
			};
		});
		setTimeout(set_autocomplete,5000);
		function set_autocomplete(){
			//CHOSE AUTOCOMPLETE OPTION
			jQuery( "#acf-field-venue" ).autocomplete({ 
				source: venues,
				select: function(e,ui)
				{
					venue = ui.item.value;
					address = addresses[venue];
					jQuery("#acf-field-show_address").val(address);
					jQuery("#acf-google_map .has-value h4").html(address);
					jQuery("#acf-google_map .search").val(address);
					map = acf.fields.google_map.map;
					geocoder = new google.maps.Geocoder();
					geocoder.geocode( { 'address': address}, function(results, status) {
						  if (status == 'OK') {
							lng_v = results["0"].geometry.location.lng();
							lat_v = results["0"].geometry.location.lat();
							map.setCenter({lat: lat_v, lng: lng_v});
							map.setZoom(14);
						  } else {
							alert('Geocode was not successful for the following reason: ' + status);
						  }
						});	
					
				}
			});
			//LEFT ADDRESS FIELD
			jQuery("#acf-field-show_address").on("blur",function(){
				address = jQuery("#acf-field-show_address").val();
				jQuery("#acf-google_map .has-value h4").html(address);
				geocoder = new google.maps.Geocoder();
				geocoder.geocode( { 'address': address}, function(results, status) {
					  if (status == 'OK') {
						lng_v = results["0"].geometry.location.lng();
						lat_v = results["0"].geometry.location.lat();
						map.setCenter({lat: lat_v, lng: lng_v});
						map.setZoom(14);
					  } else {
						alert('Geocode was not successful for the following reason: ' + status);
					  }
				});	
			})
		};
		

	</script>
<?php 

}




////////////////////
//
//  NON ELEMENTOR DETAIL PAGES
//
////////////////////
// function non_elementor_css()
// {
// 	$id = get_the_id();
// 	$elementor_data = get_post_meta($id, '_elementor_data',true);
// 	$pt = get_post_type();
// 	if(empty($elementor_data)&&$pt == 'show'&&!is_admin())
// 	{
// 		wp_enqueue_style( 'single_show_non_elementor', get_template_directory_uri() . '/single_show.css?v=' . time() );
// 	}
// }
// add_action('wp_head','non_elementor_css');
// add_action('admin_head','non_elementor_css');




////////////////////
//
//  MAKE NO SIDEBAR & FULL WIDTH
//
////////////////////
function no_sidebar_full_width($id) 
{
	if(strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' )&&is_admin())
	{
		add_post_meta($id,'_generate-sidebar-layout-meta','no-sidebar',true);
		add_post_meta($id,'_generate-full-width-content','true',true);
	}
}	
add_action( 'wp_insert_post', 'no_sidebar_full_width',22,2);

















////////////////////
//
//  OVERRIDE THE EDIT TO ELEMENTOR BUTTON BEHAVIOR (NOTE: Only works on strumandspirits)
//
////////////////////
function override_edit_to_elementor()
{
	$post_type = get_post_type();
	if($post_type == 'show'&&is_admin()&&(strstr( $_SERVER['REQUEST_URI'], 'post-new.php' )))
	{
	?>
	<script>
		jQuery(document).ready(function(){  
			elementor_page_link_el = jQuery("#elementor-go-to-edit-page-link");
			elementor_button_el = jQuery('#elementor-switch-mode-button');
			form_el = jQuery("#post");
			elementor_page_link = elementor_page_link_el.attr("href");
			elementor_button_el.unbind();
			elementor_button_el.off();
			elementor_button_el.click = '';
			elementor_button_el.on("click",function(e){
				jQuery("#post");
				e.preventDefault();
				// alert(elementor_page_link);
				// alert(jQuery("#post_ID").val());
				jQuery.ajax({
					url: 'post.php',
					type: 'POST',
					data: jQuery("#post").serialize().replace("content=", "content=[view_3]"),
				})
				.done(function() {
					window.location.href = elementor_page_link;
				});
				return false;
			});
		});
		
	</script>
	<?php
	}
}
add_action('shutdown', 'override_edit_to_elementor', 400000,1);







?>