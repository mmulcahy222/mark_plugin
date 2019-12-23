<?php 





function get_city_and_state($str)
{
	$regex_state_abbreviations = '';
	$state_abbreviations = array (0 => 'AL', 1 => 'AK', 2 => 'AS', 3 => 'AZ', 4 => 'AR', 5 => 'CA', 6 => 'CO', 7 => 'CT', 8 => 'DE', 9 => 'DC', 10 => 'FM', 11 => 'FL', 12 => 'GA', 13 => 'GU', 14 => 'HI', 15 => 'ID', 16 => 'IL', 17 => 'IN', 18 => 'IA', 19 => 'KS', 20 => 'KY', 21 => 'LA', 22 => 'ME', 23 => 'MH', 24 => 'MD', 25 => 'MA', 26 => 'MI', 27 => 'MN', 28 => 'MS', 29 => 'MO', 30 => 'MT', 31 => 'NE', 32 => 'NV', 33 => 'NH', 34 => 'NJ', 35 => 'NM', 36 => 'NY', 37 => 'NC', 38 => 'ND', 39 => 'MP', 40 => 'OH', 41 => 'OK', 42 => 'OR', 43 => 'PW', 44 => 'PA', 45 => 'PR', 46 => 'RI', 47 => 'SC', 48 => 'SD', 49 => 'TN', 50 => 'TX', 51 => 'UT', 52 => 'VT', 53 => 'VI', 54 => 'VA', 55 => 'WA', 56 => 'WV', 57 => 'WI', 58 => 'WY', 59 => 'ON', 60 => "BC", 61 => "AB", 62 => "SK", 63 => "MB", 64 => "QC", 65 => "NS",66 => "NT",67 => "YT",68 => "NB");
	foreach ($state_abbreviations as $state_abbreviation) {
		$regex_state_abbreviations .= "|$state_abbreviation";
	}
	$regex_state_abbreviations = ltrim($regex_state_abbreviations,"|");
	preg_match("/[\w|\s]*?[,|-]?\s?($regex_state_abbreviations)/",$str,$matches);
	if(!empty($matches[0]))
	{
		return trim($matches[0]);
	}
			////////////////////
			//
			//  TRY THE NEXT METHOD OF CITY & STATE EXTRACTION IF REGEX DIDN'T WORK
			//
			////////////////////
	$show_address_field = $str;
	$location_split = explode(',', $show_address_field);
	$state_abbreviation = trim(end($location_split));
	array_pop($location_split);
	$city = trim(end($location_split));
	$location = "$city, $state_abbreviation";
	return $location;
}



////////////////////
//
//  START VIEW #1
//
////////////////////
function view_1( $atts )
{
	$html = '<div class="front-custom-code block block-type-custom-code block-fluid-height" data-alias="TourDates">
	<div class="block-content">
		<h3>Upcoming Shows:</h3>
		<!-- insert custom code -->
		<div class="rsslist">
			<div class="header screen">
				<span class="rssdate title">Date</span><span class="rssvenue title">Venue</span><span class="rsslocation title">Location</span><span class="rssdetails title">More Info</span><span class="rsstickets title">Tickets</span>
				<div class="header mobile"><span class="title">Date / Venue</span></div>
			</div>';

			$the_query = new WP_Query(
				array(
					'post_type' => array('show'),
					'nopaging' => true,
					'meta_key' => 'date_of_show',
					'meta_value'   => date("Ymd"),
					'meta_compare' => '>',
					'orderby' => 'date_of_show',
					'order' => 'ASC'
					)
				);
			if ( $the_query->have_posts() )
			{
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
			//debug
			//echo get_the_title();
			//date
					$date = date("M dS", strtotime(get_field('date_of_show')));
			//location
					$location = get_city_and_state(get_field("show_address"));
			//more info link
					$id = get_the_id();
					$detail_link = get_permalink($id);
			//ticket link
					$ticket_link = get_field('ticket_link');
					$html .= '<div class="rssupcomingshows"><span class="rssdate">'.$date.'</span><span class="rssvenue">'.get_field('venue').'</span><span class="rsslocation">'.$location.'</span><span class="rssdetails"><a href="'.$detail_link.'">Details</a></span><span class="rsstickets"><a href="'.$ticket_link.'" target="_blank">Tickets</a></span></div>';
				}
				/* Restore original Post Data */
				wp_reset_postdata();
			}

			$html .= '</div>
			<div style="margin-top:15px;">
				<h3>Past Shows:</h3>
			</div>
			<div class="rsslist">
				<div class="header screen">
					<span class="rssdate title">Date</span><span class="rssvenue title">Venue</span><span class="rsslocation title">Location</span><span class="rssdetails title">More Info</span>
				</div>
				<div class="header mobile"><span class="title">Date / Venue</span></div>';
		////////////////////
		//
		//  PAST SHOWS
		//
		////////////////////
				$the_query = new WP_Query(
					array(
						'post_type' => array('show'),
						'nopaging' => true,
						'meta_key' => 'date_of_show',
						'meta_value'   => date("Ymd"),
						'meta_compare' => '<',
						'orderby' => 'date_of_show',
						'order' => 'DESC'
						)
					);
				if ( $the_query->have_posts() )
				{
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
				//debug
				//echo get_the_title();
				//date
						$date = date("M dS", strtotime(get_field('date_of_show')));
				//location
						$show_address_field = get_field("show_address");
						$location_split = explode(',', $show_address_field);
						$state_abbreviation = trim(end($location_split));
						array_pop($location_split);
						$city = trim(end($location_split));
						$location = "$city, $state_abbreviation";
				//more info link
						$id = get_the_id();
						$detail_link = get_permalink($id);
				//ticket link
						$ticket_link = get_field('ticket_link');
						$html .= '<div class="rssupcomingshows"><span class="rssdate">'.$date.'</span><span class="rssvenue">'.get_field('venue').'</span><span class="rsslocation">'.$location.'</span><span class="rssdetails"><a href="'.$detail_link.'">Details</a></span><span class="rsstickets"></span></div>';
					}
					/* Restore original Post Data */
					wp_reset_postdata();
				}
				$html .= '</div></div></div>';
				?>
			
	<?php 
	return $html;
}
add_shortcode( 'view_1', 'view_1' );
////////////////////
//
//  END VIEW #1
//
////////////////////





















































////////////////////
//
//  START VIEW #2
//
////////////////////
function view_2( $atts )
{

	$html = '<div class="block front-custom-code block block-type-custom-code block-fluid-height" data-alias="TourDates">
	<div class="single_page_all_shows">
		<h3>Upcoming Shows:</h3>';

		////////////////////
		//
		//  UPCOMING SHOWS
		//
		////////////////////
		$the_query = new WP_Query(
			array(
				'post_type' => array('show'),
				'nopaging' => true,
				'meta_key' => 'date_of_show',
				'meta_value'   => date("Ymd"),
				'meta_compare' => '>',
				'orderby' => 'date_of_show',
				'order' => 'ASC'
				)
			);
		if ( $the_query->have_posts() )
		{
			while ( $the_query->have_posts() ) 
			{
				$the_query->the_post();
				//debug
				// echo get_the_title();
				//image
				$images = get_field('image');
				$thumbnail_url = $images['sizes']['medium'];
				$thumbnail_width = $images['sizes']['medium-width'];
				$thumbnail_height = $images['sizes']['medium-height'];
				//date
				$date = date("M dS, Y", strtotime(get_field('date_of_show')));
				$venue = get_field('venue');
				//location
				$show_address_field = get_field("show_address");
				$location_split = explode(',', $show_address_field);
				$state_abbreviation = trim(end($location_split));
				array_pop($location_split);
				$city = trim(end($location_split));
				$location = "$city, $state_abbreviation";
				//google map (get from google maps data first, and then get from address if no google maps)
				$google_map = get_field('google_map');
				if(!empty($google_map))
				{
					$longitude = $google_map['lng'];
					$latitude = $google_map['lat'];
					$map_display_url = 'https://maps.google.com/maps?q='.$latitude.','.$longitude.'&hl=es;z=11&amp;output=embed';
				}
				else
				{
					$map_display_url = 'https://maps.google.com/maps?q='.$show_address_field.'&hl=es;z=11&amp;output=embed';
				}	
				//more info link
				$id = get_the_id();
				$detail_link = get_permalink($id);
				//ticket link
				$ticket_link = get_field('ticket_link');
				//Title
				$title = "$date - $venue - $location";
				//excerpt
				$excerpt = get_field('excerpt');
				$html .= '<div class="dv-show-listing"><div class="dv-show-img desktop"><a href="' . $detail_link . '"><img alt="" width="'.$thumbnail_width.'" height="200" src="'.$thumbnail_url.'"></a></div> <div class="dv-show-detail"><span class="dv-date">'.$title.'</span><span class="dv-desc">'.$excerpt.'</span><br><span class="dv-links"><a class="grey-button" href="'. $detail_link .'">Details</a>&nbsp;&nbsp;&nbsp;<a class="grey-button" href="'.$ticket_link.'" target="_blank">Tickets</a></span></div> <div class="dv-show-img mobile"><a href="'.$detail_link.'"><img alt="" width="300" height="200" src="'.$thumbnail_url.'"></a></div> <div class="dv-map desktop"><iframe src="'.$map_display_url.'" width="298" height="198" style="border:0" allowfullscreen=""></iframe></div> <div class="dv-map mobile"><iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d13899.761230826558!2d-98.488751!3d29.430541!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xb265a799b3314ad7!2sTobin+Center+for+the+Performing+Arts!5e0!3m2!1sen!2sus!4v1481027893123" width="450" height="150" style="border:0" allowfullscreen=""></iframe></div></div>'; 
			}
			wp_reset_postdata();
		}
		
		$html .= '<h3>Past Shows:</h3>';
		
			////////////////////
			//
			//  PAST SHOWS
			//
			////////////////////
		$the_query = new WP_Query(
			array(
				'post_type' => array('show'),
				'nopaging' => true,
				'meta_key' => 'date_of_show',
				'meta_value'   => date("Ymd"),
				'meta_compare' => '<',
				'orderby' => 'date_of_show',
				'order' => 'DESC'
				)
			);
		if ( $the_query->have_posts() )
		{
			while ( $the_query->have_posts() ) 
			{
				$the_query->the_post();
				//debug
				// echo get_the_title();
				//image
				$images = get_field('image');
				$thumbnail_url = $images['sizes']['medium'];
				$thumbnail_width = $images['sizes']['medium-width'];
				$thumbnail_height = $images['sizes']['medium-height'];
				//date
				$date = date("M dS, Y", strtotime(get_field('date_of_show')));
				$venue = get_field('venue');
				//location
				$show_address_field = get_field("show_address");
				$location_split = explode(',', $show_address_field);
				$state_abbreviation = trim(end($location_split));
				array_pop($location_split);
				$city = trim(end($location_split));
				$location = "$city, $state_abbreviation";
				//google map (get from google maps data first, and then get from address if no google maps)
				$google_map = get_field('google_map');
				if(!empty($google_map))
				{
					$longitude = $google_map['lng'];
					$latitude = $google_map['lat'];
					$map_display_url = 'https://maps.google.com/maps?q='.$latitude.','.$longitude.'&hl=es;z=11&amp;output=embed';
				}
				else
				{
					$map_display_url = 'https://maps.google.com/maps?q='.$show_address_field.'&hl=es;z=11&amp;output=embed';
				}	
				//more info link
				$id = get_the_id();
				$detail_link = get_permalink($id);
				//ticket link
				$ticket_link = get_field('ticket_link');
				//Title
				$title = "$date - $venue - $location";
				//excerpt
				$excerpt = get_field('excerpt');
				$html .= '<div class="dv-show-listing"><div class="dv-show-img desktop"><a href="' . $detail_link . '"><img alt="" width="'.$thumbnail_width.'" height="200" src="'.$thumbnail_url.'"></a></div> <div class="dv-show-detail"><span class="dv-date">'.$title.'</span><span class="dv-desc">'.$excerpt.'</span><br><span class="dv-links"><a class="grey-button" href="'. $detail_link .'">Details</a></span></div> <div class="dv-show-img mobile"><a href="'.$detail_link.'"><img alt="" width="300" height="200" src="'.$thumbnail_url.'"></a></div> <div class="dv-map desktop"><iframe src="'.$map_display_url.'" width="298" height="198" style="border:0" allowfullscreen=""></iframe></div> <div class="dv-map mobile"><iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d13899.761230826558!2d-98.488751!3d29.430541!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xb265a799b3314ad7!2sTobin+Center+for+the+Performing+Arts!5e0!3m2!1sen!2sus!4v1481027893123" width="450" height="150" style="border:0" allowfullscreen=""></iframe></div></div></div></div>'; 
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		}	
		return $html;
	}

	add_shortcode( 'view_2', 'view_2' );
////////////////////
//
//  END VIEW #2
//
////////////////////














////////////////////
//
//  VIEW #3
//
////////////////////
function view_3($id)
{
	//this if statement is for shortcodes
	if(empty($id))
	{
		$id = get_the_id();
	}
	if(empty($id))
	{
		return "No Show";
	}	
	$md = get_post_meta($id);
	$description = get_post_meta($id,'description',true);
	$show_title = get_post_meta($id,'show_title',true);
	$band_or_artist = get_post_meta($id,'band_or_artist',true);
	$date_of_show = get_post_meta($id,'date_of_show',true);
	$date_of_show = date("l, F dS", strtotime($date_of_show));
	$show_time = get_post_meta($id,'show_time',true);
	$show_address = get_post_meta($id,'show_address',true);
	$google_map = get_post_meta($id,'google_map',true);
	$ticket_link = get_post_meta($id,'ticket_link',true);
	preg_match('/^http/',$ticket_link,$matches);
	if(isset($matches[0])&&$matches[0] == null)
	{
		$ticket_link = 'http://' . $ticket_link;
	}
	$ticket_prices = get_post_meta($id,'ticket_prices',true);
	$more_info_link = get_post_meta($id,'more_info_link',true);
	$excerpt = get_post_meta($id,'excerpt',true);
	$discount_code = get_post_meta($id,'discount_code',true);
	$promo_partner_link = get_post_meta($id,'promo_partner_link',true);
	$charity_partner_link = get_post_meta($id,'charity_partner_link',true);
	$charity_partner_link = (!empty($charity_partner_link)) ? $charity_partner_link : "TBA";
	$venue = get_post_meta($id,'venue',true);
	$image_id = get_post_meta($id,'image',true);
	$images = wp_get_attachment_image_src($image_id,'full');
	$image_url = trim($images[0]);
	$image_post = get_post($image_id);
	$image_caption =  isset($image_post->post_content) ? $image_post->post_content : '';
	$current_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$html = '';
	$html .= '<div id="view_3_wrapper">
		
		<div class="wrapper wrapper-fluid wrapper-fixed-grid grid-fluid-24-30-30 responsive-grid" data-alias="">
			<div class="grid-container clearfix">
				<section class="row row-1">
					<section class="column column-1 grid-left-0 grid-width-24">
						<div class="block block-type-content block-fluid-height" data-alias="">
							<div class="block-content">
								<div class="loop">';
		$html .= '<div class="view_3_title">' . $show_title . '</div>';
		$html .= '<div class="date_time">'. $date_of_show . ' @ ' . $show_time . '</div>';	
		if(!empty($image_url))
		{
			$html .= '<div class="view_3_image_div"><a href=""><img class="view_3_image" style="max-width: 425px" src="' . $image_url . '" itemprop="image" title="" style=""></a></div>';
			$html .= '<div class="image_caption">'.$image_caption.'</div>';
		}	
	
		if(!empty($ticket_link)) 
		{
			$html .= '<div class="get_tickets"><a href="' . $ticket_link . '"><strong>Click here to get your tickets!</strong></a><strong></strong></div>';
		}
		$html .= '<div class="venue_address">' . $venue . ', ' . $show_address . '<a target="_blank" href="https://www.google.com/maps?q='.str_replace(' ','+',$show_address).'"><img class="map_icon" src="'.get_template_directory_uri().'/images/icons8-Map-25.png"></img></a></div>';
		
		$html .= '<div class="ticket_prices"><a href="'.$ticket_link.'">Tickets: '.$ticket_prices.'</a></div>';
		$html .= '<div class="description">'.$description.'</div>';
		if(!empty($ticket_link)) 
		{
			$html .= '<div class="get_tickets_bottom"><a href="' . $ticket_link . '">Click here to get your tickets!</a></div>';
		}
		$html .= '<div class="social_grid">'; 
		$html .= '<div class="social_facebook"><a style="color: white" target="_blank" href="https://www.facebook.com/login.php?skip_api_login=1&signed_next=1&next=https://www.facebook.com/sharer.php?u='.$current_link.'&cancel_url=https://www.facebook.com/dialog/return/close?error_code=4201&error_message=User+canceled+the+Dialog+flow#_=_&display=popup&locale=en_US">Share on Facebook</a></div>';
		$html .= '<div class="social_google"><a style="color: white" target="_blank"  target="_blank"  href="https://plus.google.com/share?url='.$current_link.'">Share on Google</a></div>';
		$html .= '<div class="social_twitter"><a style="color: white" target="_blank" href="https://twitter.com/intent/tweet?url='.$current_link.'&text=">Tweet this Show</a></div>';
		$html .= '<div class="social_linkedin"><a style="color: white" target="_blank" href="https://www.linkedin.com/start/join?session_redirect=https://www.linkedin.com/sharing/share-offsite?mini=true&url='.$current_link.'&title=Show%20Detail%20Example%20Page%20-%20Firehouse&summary=&source='.$current_link.'&trk=login_reg_redirect">Share on LinkedIn</a></div>';
		$html .= '</div>';
		$html .= '				
							</div>
						</div>
					</div>
				</section>
			</section>
		</div>
	</div>
	
	</div>';
	return "$html";
}
add_shortcode( 'view_3', 'view_3' );
////////////////////
//
//  Utilize View 3 By Changing Results Post Query (THIS ACCOUNTS FOR NO CONTEXT BEING FILLED)
//
////////////////////
function alter_post($param)
{

	//debug here if this changes
	//C:\makeshift\files\wordpress\machine_of_pwnage\wp-content\plugins\elementor\includes\editor.php
	$param['data'][0]['elements'][0]['elements'][0]['settings']['editor'] = '[view_3]';
	return $param;
}
add_filter( 'elementor/editor/localize_settings', 'alter_post',10,2);
////////////////////
//
//  Make sure context is always [view_3] on front end
//
////////////////////
function context_always_shortcode($html)
{
	$post_type = get_post_type();
	if($post_type == 'show')
	{
		return view_3(get_the_id());
	}
	return $html;
}
add_filter( 'the_content', 'context_always_shortcode',10,2);
////////////////////
//
//  End View #3
//
////////////////////











?>