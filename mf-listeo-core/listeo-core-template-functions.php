<?php
/**
 * Template Functions
 *
 * Template functions for listings
 *
 * @author 		Lukasz Girek
 * @version     1.0
 */


/**
 * Add custom body classes
 */
function listeo_core_body_class( $classes ) {
	$classes   = (array) $classes;
	$classes[] = sanitize_title( wp_get_theme() );

	return array_unique( $classes );
}

add_filter( 'body_class', 'listeo_core_body_class' );


/**
 * Outputs the listing offer type
 *
 * @return void
 */
function the_listing_offer_type( $post = null ) {
	$type = get_the_listing_offer_type( $post );
	$offers = listeo_core_get_offer_types_flat(true);
	if(array_key_exists($type, $offers)) {
		echo '<span class="tag">'.$offers[$type].'</span>';	
	}
}

/**
 * Gets the listing offer type
 *
 * @return string
 */
function get_the_listing_offer_type( $post = null ) {
	$post     = get_post( $post );
	if ( $post->post_type !== 'listing' ) {
		return;
	}
	return apply_filters( 'the_listing_offer_type', $post->_offer_type, $post );
}


function the_listing_type( $post = null ) {
	$type = get_the_listing_type( $post );
	$types = listeo_core_get_listing_types(true);
	if(array_key_exists($type, $types)) {
		echo '<span class="listing-type-badge listing-type-badge-'.$type.'">'.$types[$type].'</span>';	
	}
}
/**
 * Gets the listing  type
 *
 * @return string
 */
function get_the_listing_type( $post = null ) {
	$post     = get_post( $post );
	if ( $post->post_type !== 'listing' ) {
		return;
	}
	return apply_filters( 'the_listing_type', $post->_listing_type, $post );
}

function listeo_get_reviews_criteria(){
	$criteria = array(
		'service' => array(
				'label' => esc_html__('Service','listeo_core'),
				'tooltip' => esc_html__('Quality of customer service and attitude to work with you','listeo_core')
		), 
		'value-for-money' => array(
				'label' => esc_html__('Value for Money','listeo_core'),
				'tooltip' => esc_html__('Overall experience received for the amount spent','listeo_core')
		), 
		'location' => array(
				'label' => esc_html__('Location','listeo_core'),
				'tooltip' => esc_html__('Visibility, commute or nearby parking spots','listeo_core')
		),
		'cleanliness' => array(
				'label' => esc_html__('Cleanliness','listeo_core'),
				'tooltip' => esc_html__('The physical condition of the business','listeo_core')
		), 
	);

	return apply_filters('listeo_reviews_criteria',$criteria);
}

/**
 * Outputs the listing location
 *
 * @return void
 */
function the_listing_address( $post = null ) {
	echo get_the_listing_address( $post );
}

/**
 * get_the_listing_address function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_the_listing_address( $post = null ) {
	$post = get_post( $post );
	if ( $post->post_type !== 'listing' ) {
		return;
	}
	
	$friendly_address = get_post_meta( $post->ID, '_friendly_address', true );
	$address = get_post_meta( $post->ID, '_address', true );
	$output =  (!empty($friendly_address)) ? $friendly_address : $address ;
	
	return apply_filters( 'the_listing_location', $output, $post );
}


/**
 * Outputs the listing price
 *
 * @return void
 */
function the_listing_price( $post = null ) {
	echo get_the_listing_price( $post );
}

/**
 * get_the_listing_price function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_the_listing_price( $post = null ) {
	return Listeo_Core_Listing::get_listing_price( $post );
}


function get_the_listing_price_range( $post = null ) {
	return Listeo_Core_Listing::get_listing_price_range( $post );
}


/**
 * Outputs the listing price per scale
 *
 * @return void
 */
function the_listing_price_per_scale( $post = null ) {
	echo get_the_listing_price_per_scale( $post );
}

function get_the_listing_price_per_scale( $post = null ) {
	return Listeo_Core_Listing::get_listing_price_per_scale( $post );
}

function the_listing_location_link($post = null, $map_link = true ) {

	$address =  get_post_meta( $post, '_address', true );
	$friendly_address =  get_post_meta( $post, '_friendly_address', true );

	if(empty($friendly_address)) { $friendly_address = $address; }
	
	if ( $address ) {
		if ( $map_link ) {
			// If linking to google maps, we don't want anything but text here
			echo apply_filters( 'the_listing_map_link', '<a class="listing-address popup-gmaps" href="' . esc_url( 'https://maps.google.com/maps?q=' . urlencode( strip_tags( $address ) ) . '' ) . '"><i class="fa fa-map-marker"></i>' . esc_html( strip_tags( $friendly_address ) ) . '</a>', $address, $post );
		} else {
			echo wp_kses_post( $friendly_address );
		}
	} 

}


function listeo_core_check_if_bookmarked($id){
	if($id){
		$classObj = new Listeo_Core_Bookmarks;
		return $classObj->check_if_added($id);
	} else {
		return false;
	}
}

function listeo_core_is_featured($id){
	$featured = get_post_meta($id,'_featured',true);
	if(!empty($featured)) {
		return true;
	} else {
		return false;
	}
}



/**
 * Gets the listing title for the listing.
 *
 * @since 1.27.0
 * @param int|WP_Post $post (default: null)
 * @return string|bool|null
 */
function listeo_core_get_the_listing_title( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || 'listing' !== $post->post_type ) {
		return;
	}

	$title = esc_html( get_the_title( $post ) );

	/**
	 * Filter for the listing title.
	 *
	 * @since 1.27.0
	 * @param string      $title Title to be filtered.
	 * @param int|WP_Post $post
	 */
	return apply_filters( 'listeo_core_the_listing_title', $title, $post );
}

function listeo_core_add_tooltip_to_label( $field_args, $field ) {
	// Get default label
	$label = $field->label();
	if ( $label && $field->options( 'tooltip' ) ) {
		$label = substr($label, 0, -9);
		
		// If label and tooltip exists, add it
		$label .= sprintf( ' <i class="tip" data-tip-content="%s"></i></label>',$field->options( 'tooltip' ) );
	}

	return $label;
}

/**
 * Overrides the default render field method
 * Allows you to add custom HTML before and after a rendered field
 *
 * @param  array             $field_args Array of field parameters
 * @param  CMB2_Field object $field      Field object
 */
function listeo_core_render_as_col_12( $field_args, $field ) {

	// If field is requesting to not be shown on the front-end
	if ( ! is_admin() && ! $field->args( 'on_front' ) ) {
		return;
	}

	// If field is requesting to be conditionally shown
	if ( ! $field->should_show() ) {
		return;
	}

	$field->peform_param_callback( 'before_row' );

	echo '<div class="col-md-12">';
	
	// Remove the cmb-row class
	printf( '<div class="custom-class %s">', $field->row_classes() );

	if ( ! $field->args( 'show_names' ) ) {
	
		// If the field is NOT going to show a label output this
		$field->peform_param_callback( 'label_cb' );
	
	} else {

		// Otherwise output something different
		if ( $field->get_param_callback_result( 'label_cb', false ) ) {
			echo $field->peform_param_callback( 'label_cb' );
		}
		
	}

	$field->peform_param_callback( 'before' );
	
	// The next two lines are key. This is what actually renders the input field
	$field_type = new CMB2_Types( $field );
	$field_type->render();

	$field->peform_param_callback( 'after' );

		echo '</div>'; //cmb-row

	echo '</div>';

	$field->peform_param_callback( 'after_row' );

    // For chaining
	return $field;
}
/**
 * Dispays bootstarp column start
 * @param  string $col integer column width
 */
function listeo_core_render_column($col='', $name='') {
	echo '<div class="col-md-'.$col.' form-field-'.$name.'-container">';
}

function listeo_archive_buttons($list_style, $list_top_buttons = null){
	$template_loader = new Listeo_Core_Template_Loader; 
	$data = array( 'buttons' => $list_top_buttons );
	$template_loader->set_template_data( $data )->get_template_part( 'archive/top-buttons' );
}

// function listeo_result_layout_switch($list_style, $layout_switch = null){
// 	if(!isset($layout_switch)){
// 		$layout_switch = 'on';
// 	}
// 	if($list_style != 'compact' && $layout_switch == 'on') {
// 		$template_loader = new Listeo_Core_Template_Loader; 
// 		$template_loader->get_template_part( 'archive/layout-switcher' ); 	
// 	}
	
// }

/* Hooks */
/* Hooks */
//add_action( 'listeo_before_archive', 'listeo_result_sorting', 20 );
add_action( 'listeo_before_archive', 'listeo_archive_buttons', 25 ,2 );
//add_action( 'listeo_before_archive', 'listeo_result_layout_switch', 10, 2 );

/**
 * Return type of listings
 *
 */
function listeo_core_get_listing_types(){
	 $options = array(
        	'apartments' => __( 'Apartments', 'listeo_core' ),
			'houses' 	 => __( 'Houses', 'listeo_core' ),
			'commercial' => __( 'Commercial', 'listeo_core' ),
			'garages' 	 => __( 'Garages', 'listeo_core' ),
			'lots' 		 => __( 'Lots', 'listeo_core' ),
    );
	return apply_filters('listeo_core_get_listing_types',$options);
}


/*add_filter('listeo_core_get_listing_types','add_listing_types_from_option');*/

/**
 * Return type of listings
 *
 */
function listeo_core_get_rental_period(){
	 $options = array(
        	'daily' => __( 'Daily', 'listeo_core' ),
			'weekly' 	 => __( 'Weekly', 'listeo_core' ),
			'monthly' => __( 'Monthly', 'listeo_core' ),
			'yearly' 	 => __( 'Yearly', 'listeo_core' ),
    );
	return apply_filters('listeo_core_get_rental_period',$options);
}

/**
 * Return type of offers
 *
 */

function listeo_core_get_offer_types(){
	$options =  array(
        	'sale' => array( 
        		'name' => __( 'For Sale', 'listeo_core' ),
        		'front' => '1'
        		), 
			'rent' => array( 
        		'name' => __( 'For Rent', 'listeo_core' ),
        		'front' => '1'
        		), 
			'sold' => array( 
        		'name' => __( 'Sold', 'listeo_core' )
        		), 
			'rented' => array( 
        		'name' => __( 'Rented', 'listeo_core' )
        		), 
    );
	return apply_filters('listeo_core_get_offer_types',$options);
}

function listeo_core_get_offer_types_flat($with_all = false){
	$org_offer_types = listeo_core_get_offer_types();

	$options = array();
	foreach ($org_offer_types as $key => $value) {

		if($with_all == true ) {
			$options[$key] = $value['name']; 
		} else {
			if(isset($value['front']) && $value['front'] == 1) {
				$options[$key] = $value['name']; 
			} elseif(!isset($value['front']) && in_array($key, array('sale','rent'))) {
					$options[$key] = $value['name']; 
				
			}
		}
	}
	return $options;
}
function listeo_core_get_options_array($type,$data) {
	$options = array();
	if($type == 'taxonomy'){
		$categories =  get_terms( $data, array(
		    'hide_empty' => false,
		) );	
		$options = array();
		foreach ($categories as $cat) {
			$options[$cat->term_id] = array ( 
				'name'  => $cat->name,
				'slug'  => $cat->slug,
				'id'	=> $cat->term_id,
				);
		}
	}
	return $options;
}
function listeo_core_get_options_array_hierarchical($terms, $selected, $output = '', $parent_id = 0, $level = 0) {
    //Out Template
    $outputTemplate = '<option %SELECED% value="%ID%">%PADDING%%NAME%</option>';

    foreach ($terms as $term) {
        if ($parent_id == $term->parent) {
        	if(is_array($selected)) {
				$is_selected = in_array( $term->slug, $selected ) ? ' selected="selected" ' : '';
			} else {
				$is_selected = selected($selected, $term->slug);
			}
            //Replacing the template variables
            $itemOutput = str_replace('%SELECED%', $is_selected, $outputTemplate);
            $itemOutput = str_replace('%ID%', $term->slug, $itemOutput);
            $itemOutput = str_replace('%PADDING%', str_pad('', $level*12, '&nbsp;&nbsp;'), $itemOutput);
            $itemOutput = str_replace('%NAME%', $term->name, $itemOutput);

            $output .= $itemOutput;
            $output = listeo_core_get_options_array_hierarchical($terms, $selected, $output, $term->term_id, $level + 1);
        }
    }
    return $output;
}

/*$terms = get_terms('taxonomy', array('hide_empty' => false));
$output = get_terms_hierarchical($terms);

echo '<select>' . $output . '</select>';  
*/
/**
 * Returns html for select input with options based on type
 *
 *
 * @param  $type taxonomy
 * @param  $data term
 */	
function get_listeo_core_dropdown( $type, $data='', $name, $class='chosen-select-no-single', $placeholder='Any Type'){
	$output = '<select name="'.esc_attr($name).'" data-placeholder="'.esc_attr($placeholder).'" class="'.esc_attr($class).'">';
	if($type == 'taxonomy'){
		$categories =  get_terms( $data, array(
		    'hide_empty' => false,
		) );	
		
		$output .= '<option>'.esc_html__('Any Type','listeo_core').'</option>';
		foreach ($categories as $cat) { 
			$output .= '<option value='.$cat->term_id.'>'.$cat->name.'</option>';
		}
	}
	$output .= '</select>';
	return $output;
}

/**
 * Returns html for just options input based on data array
 *
 * @param  $data array
 */	
function get_listeo_core_options_dropdown(  $data,$selected ){
	$output = '';

	if(is_array($data)) :
		foreach ($data as $id => $value) {
			if(is_array($selected)) {

				$is_selected = in_array( $value['slug'], $selected ) ? ' selected="selected" ' : '';
				
			} else {
				$is_selected = selected($selected, $id);
			}
			$output .= '<option '.$is_selected.' value="'.esc_attr($value['slug']).'">'.esc_html($value['name']).'</option>';
		}
	endif;
	return $output;
}

function get_listeo_core_options_dropdown_by_type( $type, $data ){
	$output = '';
	if(is_array($data)) :
		foreach ($data as $id => $value) {
			$output .= '<option value="'.esc_attr($id).'">'.esc_html($value).'</option>';
		}
	endif;
	return $output;
}

function get_listeo_core_numbers_dropdown( $number=10 ){
	$output = '';
	$x = 1;
	while($x <= $number) {
		$output .= '<option value="'.esc_attr($x).'">'.esc_html($x).'</option>';
    	$x++;
	} 
	return $output;
}

function get_listeo_core_intervals_dropdown( $min, $max, $step = 100, $name = false ){
	$output = '';
	
	if($min == 'auto'){
		$min = Listeo_Core_Search::get_min_meta_value($name);
	}
	if($max == 'auto'){
		$max = Listeo_Core_Search::get_max_meta_value($name);
	}
	$range = range($min, $max, $step );
	if(sizeof($range) > 30 ) {
		$output = "<option>ADMIN NOTICE: increase your step value in Search Form Editor, having more than 30 steps is not recommended for performence options</option>";
	} else {
		foreach ($range as $number) {
		    $output .= '<option value="'.esc_attr($number).'">'.esc_html(number_format_i18n($number)).'</option>';
		}
	}
	return $output;
}


/**
 * Gets a number of posts and displays them as options
 * @param  array $query_args Optional. Overrides defaults.
 * @return array             An array of options that matches the CMB2 options array
 */
function listeo_core_get_post_options( $query_args ) {

	$args = wp_parse_args( $query_args, array(
		'post_type'   => 'post',
		'numberposts' => -1,
	) );

	$posts = get_posts( $args );

	$post_options = array();
	$post_options[0] = esc_html__('--Choose page--','listeo_core');
	if ( $posts ) {
		foreach ( $posts as $post ) {
          $post_options[ $post->ID ] = $post->post_title;
		}
	}

	return $post_options;
}

/**
 * Gets 5 posts for your_post_type and displays them as options
 * @return array An array of options that matches the CMB2 options array
 */
function listeo_core_get_pages_options() {
	return listeo_core_get_post_options( array( 'post_type' => 'page', ) );
}


function listeo_core_get_listing_packages_as_options() {
	
	$args =  array(
			'post_type'        => 'product',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'date',
			'suppress_filters' => false,
			'tax_query'        => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => array( 'listing_package'),
					'operator' => 'IN',
				),
			),
			
	);

	$posts = get_posts( $args );

	$post_options = array();
	
	if ( $posts ) {
		foreach ( $posts as $post ) {
          $post_options[ $post->ID ] = $post->post_title;
		}
	}

	return $post_options;
}




function listeo_core_agent_name(){
	$fname = get_the_author_meta('first_name');
	$lname = get_the_author_meta('last_name');
	$full_name = '';

	if( empty($fname)){
	    $full_name = $lname;
	} elseif( empty( $lname )){
	    $full_name = $fname;
	} else {
	    //both first name and last name are present
	    $full_name = "{$fname} {$lname}";
	}

	echo $full_name;
}


function listeo_core_ajax_pagination($pages = '', $current = false, $range = 2 ) {
	if(!empty($current)){
    	$paged = $current;
    } else {
    	global $paged;	
    }
    
    $output = false;
    if(empty($paged)) $paged = 1;

    $prev = $paged - 1;
    $next = $paged + 1;
    $showitems = ( $range * 2 )+1;
    $range = 2; // change it to show more links

    if( $pages == '' ){
        global $wp_query;

        $pages = $wp_query->max_num_pages;
        if( !$pages ){
            $pages = 1;
        }
    }

    if( 1 != $pages ){

        
            $output .= '<nav class="pagination margin-top-30"><ul class="pagination">';
                $output .=  ( $paged > 2 && $paged > $range+1 && $showitems < $pages ) ? '<li data-paged="next"><a href="#"><i class="sl sl-icon-arrow-left"></i></a></li>' : '';
                //$output .=  ( $paged > 1 ) ? '<li><a class="previouspostslink" href="#"">'.__('Previous','listeo_core').'</a></li>' : '';
                for ( $i = 1; $i <= $pages; $i++ ) {
                    if ( 1 != $pages &&( !( $i >= $paged+$range+1 || $i <= $paged-$range-1 ) || $pages <= $showitems ) )
                    {
                        if ( $paged == $i ){
                            $output .=  '<li class="current" data-paged="'.$i.'"><a href="#">'.$i.' </a></li>';
                        } else {
                            $output .=  '<li data-paged="'.$i.'"><a href="#">'.$i.'</a></li>';
                        }
                    }
                }
               // $output .=  ( $paged < $pages ) ? '<li><a class="nextpostslink" href="#">'.__('Next','listeo_core').'</a></li>' : '';
                $output .=  ( $paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages ) ? '<li data-paged="prev"><a  href="#"><i class="sl sl-icon-arrow-right"></i></a></li>' : '';
            $output .=  '</ul></nav>';
  

    }
    return $output;
}
function listeo_core_pagination($pages = '', $current = false, $range = 2 ) {
    if(!empty($current)){
    	$paged = $current;
    } else {
    	global $paged;	
    }
    

    if(empty($paged))$paged = 1;

    $prev = $paged - 1;
    $next = $paged + 1;
    $showitems = ( $range * 2 )+1;
    $range = 2; // change it to show more links

    if( $pages == '' ){
        global $wp_query;

        $pages = $wp_query->max_num_pages;
        if( !$pages ){
            $pages = 1;
        }
    }

    if( 1 != $pages ){

        
            echo '<ul class="pagination">';
                echo ( $paged > 2 && $paged > $range+1 && $showitems < $pages ) ? '<li><a href="'.get_pagenum_link(1).'"><i class="sl sl-icon-arrow-left"></i></a></li>' : '';
               // echo ( $paged > 1 ) ? '<li><a class="previouspostslink" href="'.get_pagenum_link($prev).'">'.__('Previous','listeo_core').'</a></li>' : '';
                for ( $i = 1; $i <= $pages; $i++ ) {
                    if ( 1 != $pages &&( !( $i >= $paged+$range+1 || $i <= $paged-$range-1 ) || $pages <= $showitems ) )
                    {
                        if ( $paged == $i ){
                            echo '<li class="current" data-paged="'.$i.'"><a href="'.get_pagenum_link($i).'">'.$i.' </a></li>';
                        } else {
                            echo '<li data-paged="'.$i.'"><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
                        }
                    }
                }
               // echo ( $paged < $pages ) ? '<li><a class="nextpostslink" href="'.get_pagenum_link($next).'">'.__('Next','listeo_core').'</a></li>' : '';
                echo ( $paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages ) ? '<li><a  href="'.get_pagenum_link( $pages ).'"><i class="sl sl-icon-arrow-right"></i></a></li>' : '';
            echo '</ul>';
  

    }
}

function listeo_core_get_post_status($id){
	$status = get_post_status($id);
	switch ($status) {
		case 'publish':
			$friendly_status = esc_html__('Published', 'listeo_core');
			break;		
		case 'pending_payment':
			$friendly_status = esc_html__('Pending Payment', 'listeo_core');
			break;
		case 'expired':
			$friendly_status = esc_html__('Expired', 'listeo_core');
			break;
		case 'draft':
		case 'pending':
			$friendly_status = esc_html__('Pending Approval', 'listeo_core');
			break;
		
		default:
			$friendly_status = $status;
			break;
	}
	return $friendly_status;
	
}

/**
 * Calculates and returns the listing expiry date.
 *
 * @since 1.22.0
 * @param  int $id
 * @return string
 */
function calculate_listing_expiry( $id ) {
	// Get duration from the product if set...
	$duration = get_post_meta( $id, '_duration', true );

	// ...otherwise use the global option
	if ( ! $duration ) {
		$duration = absint( get_option( 'listeo_core_default_duration') );
	}

	if ( $duration ) {
		return date( 'Y-m-d', strtotime( "+{$duration} days", current_time( 'timestamp' ) ) );
	}

	return '';
}

function listeo_core_get_expiration_date($id) {
	$expires = get_post_meta( $id, '_listing_expires', true );
	if(!empty($expires)) {
		if(listeo_core_is_timestamp($expires)) {
			$saved_date = get_option( 'date_format' );
			$new_date = date($saved_date, $expires); 
		} else {
			return $expires;
		}
	}
	return (empty($expires)) ? __('Never/not set','listeo_core') : $new_date ;
}

function listeo_core_is_timestamp($timestamp) {

		$check = (is_int($timestamp) OR is_float($timestamp))
			? $timestamp
			: (string) (int) $timestamp;
		return  ($check === $timestamp)
	        	AND ( (int) $timestamp <=  PHP_INT_MAX)
	        	AND ( (int) $timestamp >= ~PHP_INT_MAX);
	}
	
function listeo_core_get_listing_image($id){
	if(has_post_thumbnail($id)){ 
		return	wp_get_attachment_image_url( get_post_thumbnail_id( $id ),'listeo-listing-grid' );
	} else {
		$gallery = (array) get_post_meta( $id, '_gallery', true );

		$ids = array_keys($gallery);
		if(!empty($ids[0]) && $ids[0] !== 0){ 
			return  wp_get_attachment_image_url($ids[0],'listeo-listing-grid'); 
		} else {
			$placeholder = get_listeo_core_placeholder_image();
			return $placeholder;
		}
	} 
}

add_action('listeo_page_subtitle','listeo_core_my_account_hello');
function listeo_core_my_account_hello(){
	$my_account_page = get_option( 'my_account_page');
	if(is_user_logged_in() && !empty($my_account_page) && is_page($my_account_page)){
		$current_user = wp_get_current_user();
		if(!empty($current_user->user_firstname)){
			$name = $current_user->user_firstname.' '.$current_user->user_lastname;
		} else {
			$name = $current_user->display_name;
		}
		echo "<span>" . esc_html__('Howdy, ','listeo_core') . $name.'!</span>';
	} else {
		global $post;
		$subtitle = get_post_meta($post->ID,'listeo_subtitle',true);
		if($subtitle) {
			echo "<span>".esc_html($subtitle)."</span>";
		}
	}
}



function listeo_core_sort_by_priority( $array = array(), $order = SORT_NUMERIC ) {
	
		if ( ! is_array( $array ) )
			return;

		// Sort array by priority

		$priority = array();

		foreach ( $array as $key => $row ) {

			if ( isset( $row['position'] ) ) {
				$row['priority'] = $row['position'];
				unset( $row['position'] );
			}

			$priority[$key] = isset( $row['priority'] ) ? absint( $row['priority'] ) : false;
		}

		array_multisort( $priority, $order, $array );

		return apply_filters( 'listeo_sort_by_priority', $array, $order );
}


/**
 * CMB2 Select Multiple Custom Field Type
 * @package CMB2 Select Multiple Field Type
 */

/**
 * Adds a custom field type for select multiples.
 * @param  object $field             The CMB2_Field type object.
 * @param  string $value             The saved (and escaped) value.
 * @param  int    $object_id         The current post ID.
 * @param  string $object_type       The current object type.
 * @param  object $field_type_object The CMB2_Types object.
 * @return void
 */
if(!function_exists('cmb2_render_select_multiple_field_type')) {
	function cmb2_render_select_multiple_field_type( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

		$select_multiple = '<select class="widefat" multiple name="' . $field->args['_name'] . '[]" id="' . $field->args['_id'] . '"';
		foreach ( $field->args['attributes'] as $attribute => $value ) {
			$select_multiple .= " $attribute=\"$value\"";
		}
		$select_multiple .= ' />';

		foreach ( $field->options() as $value => $name ) {
			$selected = ( $escaped_value && in_array( $value, $escaped_value ) ) ? 'selected="selected"' : '';
			$select_multiple .= '<option class="cmb2-option" value="' . esc_attr( $value ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
		}

		$select_multiple .= '</select>';
		$select_multiple .= $field_type_object->_desc( true );

		echo $select_multiple; // WPCS: XSS ok.
	}
	add_action( 'cmb2_render_select_multiple', 'cmb2_render_select_multiple_field_type', 10, 5 );


	/**
	 * Sanitize the selected value.
	 */
	function cmb2_sanitize_select_multiple_callback( $override_value, $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $key => $saved_value ) {
				$value[$key] = sanitize_text_field( $saved_value );
			}

			return $value;
		}

		return;
	}
	add_filter( 'cmb2_sanitize_select_multiple', 'cmb2_sanitize_select_multiple_callback', 10, 2 );
}

function listeo_core_array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}


function listeo_core_get_nearby_listings($lat, $lng, $distance, $radius_type){
    global $wpdb;
    if($radius_type=='km') {
    	$ratio = 6371;
    } else {
    	$ratio = 3959;
    }

  	$post_ids = 
			$wpdb->get_results(
				$wpdb->prepare( "
			SELECT DISTINCT
			 		geolocation_lat.post_id,
			 		geolocation_lat.meta_key,
			 		geolocation_lat.meta_value as listingLat,
			        geolocation_long.meta_value as listingLong,
			        ( %d * acos( cos( radians( %f ) ) * cos( radians( geolocation_lat.meta_value ) ) * cos( radians( geolocation_long.meta_value ) - radians( %f ) ) + sin( radians( %f ) ) * sin( radians( geolocation_lat.meta_value ) ) ) ) AS distance 
		       
			 	FROM 
			 		$wpdb->postmeta AS geolocation_lat
			 		LEFT JOIN $wpdb->postmeta as geolocation_long ON geolocation_lat.post_id = geolocation_long.post_id
					WHERE geolocation_lat.meta_key = '_geolocation_lat' AND geolocation_long.meta_key = '_geolocation_long'
			 		HAVING distance < %d

		 	", 
		 	$ratio, 
		 	$lat, 
		 	$lng, 
		 	$lat, 
		 	$distance)
		,ARRAY_A);

    return $post_ids;
 
}


// function to geocode address, it will return false if unable to geocode address
function listeo_core_geocode($address){
 
    // url encode the address
    $address = urlencode($address);
	$api_key = get_option( 'listeo_maps_api_server' );
    // google map geocode api url
    $url = "https://maps.google.com/maps/api/geocode/json?address={$address}&key={$api_key}";
 
    // get the json response
  	$resp_json = wp_remote_get($url);
  	
 	$resp = json_decode( wp_remote_retrieve_body( $resp_json ), true );
 	listeo_write_log($resp);
    // response status will be 'OK', if able to geocode given address 
    if($resp['status']=='OK'){
 
        // get the important data
        $lati = $resp['results'][0]['geometry']['location']['lat'];
        $longi = $resp['results'][0]['geometry']['location']['lng'];
        $formatted_address = $resp['results'][0]['formatted_address'];
         
        // verify if data is complete
        if($lati && $longi && $formatted_address){
         
            // put the data in the array
            $data_arr = array();            
             
            array_push(
                $data_arr, 
                    $lati, 
                    $longi, 
                    $formatted_address
                );
             
            return $data_arr;
             
        }else{
            return false;
        }
         
    }else{
        return false;
    }
}


/**
 * Checks if the user can edit a listing.
 */
function listeo_core_if_can_edit_listing( $listing_id ) {
	$can_edit = true;

	if ( ! is_user_logged_in() || ! $listing_id ) {
		$can_edit = false;
	} else {
		$listing      = get_post( $listing_id );

		if ( ! $listing || ( absint( $listing->post_author ) !== get_current_user_id()  ) ) {
			$can_edit = false;
		}
		
	}

	return apply_filters( 'listeo_core_if_can_edit_listing', $can_edit, $listing_id );
}



//&& ! current_user_can( 'edit_post', $listing_id )


add_filter('submit_listing_form_submit_button_text','listeo_core_rename_button_no_preview');

function listeo_core_rename_button_no_preview(){
	if(get_option('listeo_new_listing_preview' )) {
			return  __( 'Submit', 'listeo_core' );
		} else {
			return  __( 'Preview', 'listeo_core' );
		}
}

function get_listeo_core_placeholder_image(){
	$image_id = get_option('listeo_placeholder_id' );
	if($image_id) {
		$placeholder = wp_get_attachment_image_src($image_id,'listeo-listing-grid');
		return $placeholder[0];
	} else {
		return  plugin_dir_url( __FILE__ )."templates/images/listeo_placeholder.png";
	}
	
}


function listeo_is_rated() {
	return true;
}



function listeo_post_view_count(){
	if ( is_single() ){

		global $post;
		$count_post 	= get_post_meta( $post->ID, '_listing_views_count', true);
		$author_id 		= get_post_field( 'post_author', $post->ID );

		$total_views 	= get_user_meta($author_id,'listeo_total_listing_views',true);

		if( $count_post == ''){
		
			$count_post = 1;
			add_post_meta( $post->ID, '_listing_views_count', $count_post);
			
			$total_views = (int) $total_views + 1;
			update_user_meta($author_id, 'listeo_total_listing_views', $total_views);
			
		} else {
		
			$total_views = (int) $total_views + 1;
			update_user_meta($author_id, 'listeo_total_listing_views', $total_views);

			$count_post = (int)$count_post + 1;
			update_post_meta( $post->ID, '_listing_views_count', $count_post);
		
		}
	}
}
add_action('wp_head', 'listeo_post_view_count');

function listeo_count_user_comments( $args = array() ) {
    global $wpdb;
    $default_args = array(
        'author_id' => 1,
        'approved' => 1,
        'author_email' => '',
    );

    $param = wp_parse_args( $args, $default_args );
    
    $sql = $wpdb->prepare( "SELECT COUNT(comments.comment_ID) 
            FROM {$wpdb->comments} AS comments 
            LEFT JOIN {$wpdb->posts} AS posts
            ON comments.comment_post_ID = posts.ID
            WHERE posts.post_author = %d
            AND comment_approved = %d
            AND comment_author_email NOT IN (%s)
            AND comment_type IN ('comment', '')",
        $param
    );

    return $wpdb->get_var( $sql );
}




if ( ! function_exists( 'listeo_comment_review' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since astrum 1.0
 */
function listeo_comment_review( $comment, $args, $depth ) {
  $GLOBALS['comment'] = $comment;
  global $post;

  switch ( $comment->comment_type ) :
    case 'pingback' :
    case 'trackback' :
  ?>
  <li class="post pingback">
    <p><?php esc_html_e( 'Pingback:', 'listeo_core' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( esc_html__( '(Edit)', 'listeo' ), ' ' ); ?></p>
  <?php
      break;
    default :
      $allowed_tags = wp_kses_allowed_html( 'post' );
      $rating  = get_comment_meta( get_comment_ID(), 'listeo-rating', true ); 
  ?>
  	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
       	<div class="avatar"><?php echo get_avatar( $comment, 70 ); ?></div>
		<div class="comment-content"><div class="arrow-comment"></div>
		
			<div class="comment-by">
				
				<?php if( $comment->user_id === $post->post_author ) { ?>
					<h5><?php esc_html_e('Owner','listeo_core') ?></h5>
				<?php } else {
					printf( '<h5>%s</h5>', get_comment_author_link() ); 
				} ?> 
			<span class="date"> <?php printf( esc_html__( '%1$s at %2$s', 'listeo_core' ), get_comment_date(), get_comment_time() ); ?>
		
			</span>
		
				<div class="star-rating" data-rating="<?php echo esc_attr($rating); ?>"></div>
			</div>
			<?php comment_text(); ?>
			<?php 
	            $photos = get_comment_meta( get_comment_ID(), 'listeo-attachment-id', false );

	            if($photos) : ?>
	            <div class="review-images mfp-gallery-container">
	            	<?php foreach ($photos as $key => $attachment_id) {

	            		$image = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery' );
	            		$image_thumb = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );

	            	 ?>
					<a href="<?php echo esc_attr($image[0]); ?>" class="mfp-gallery"><img src="<?php echo esc_attr($image_thumb[0]); ?>" alt=""></a>
					<?php } ?>
				</div>
				<?php endif; ?>
			<?php $review_rating = get_comment_meta( get_comment_ID(), 'listeo-review-rating', true ); ?>
			<a href="#" id="review-<?php comment_ID(); ?>" data-comment="<?php comment_ID(); ?>" class="rate-review listeo_core-rate-review"><i class="sl sl-icon-like"></i> <?php esc_html_e('Helpful Review ', 'listeo_core'); ?><?php if($review_rating) { echo "<span>".$review_rating."</span>"; } ?></a>
        </div>
    
  <?php
      break;
  endswitch;
}
endif; // ends check for listeo_comment()

function listeo_get_days(){
	$days = array(
		'monday'	=> __('Monday','listeo_core'),
		'tuesday' 	=> __('Tuesday','listeo_core'),
		'wednesday' => __('Wednesday','listeo_core'),
		'thursday' 	=> __('Thursday','listeo_core'),
		'friday' 	=> __('Friday','listeo_core'),
		'saturday' 	=> __('Saturday','listeo_core'),
		'sunday' 	=> __('Sunday','listeo_core'),
	);
	return apply_filters( 'listeo_days_array',$days );
	
}

function listeo_top_comments_only( $clauses )
{
    $clauses['where'] .= ' AND comment_parent = 0';
    return $clauses;
}

function listeo_check_if_review_replied($comment_id,$user_id){

	$author_replies_args = array(
			'user_id' => $user_id,
			'parent'  => $comment_id
		);
	$author_replies = get_comments( $author_replies_args ); 
	return (empty($author_replies)) ? false : true ;
}
function listeo_get_review_reply($comment_id,$user_id){

	$author_replies_args = array(
			'user_id' => $user_id,
			'parent'  => $comment_id
		);
	$author_replies = get_comments( $author_replies_args ); 
	return $author_replies;
}


function listeo_check_if_open($post = ''){
	
	$status = false;
	$has_hours = false;
	if(empty($post)){
		global $post;	
	}
	
	$days = listeo_get_days();
	$storeSchedule = array();
	foreach ($days as $d_key => $value) {
		$open_val = get_post_meta($post->ID, '_'.$d_key.'_opening_hour', true);
		$opening = ($open_val) ? $open_val : '' ;
		$clos_val = get_post_meta($post->ID, '_'.$d_key.'_closing_hour', true);
		$closing = ($clos_val) ? $clos_val : '';
		if(is_numeric(substr($opening, 0, 1))) {
			$has_hours = true;
		}
		$storeSchedule[$d_key] = array(
			'opens' => $opening,
			'closes' => $closing
		);
	}

	$clock_format = get_option('listeo_clock_format');
    //get current  time
    $timeObject = new DateTime(null, liste_get_timezone());
    
    $timestamp 		= $timeObject->getTimeStamp();
    $currentTime 	= $timeObject->setTimestamp($timestamp)->format('Hi');
    $timezone		= get_option('timezone_string');
	

	if(isset($storeSchedule[lcfirst(date('l', $timestamp))])) :
		$day = ($storeSchedule[lcfirst(date('l', $timestamp))]);
		$startTime = $day['opens'];
		$endTime = $day['closes'];
		
		if(!empty($startTime) && is_numeric(substr($startTime, 0, 1)) ) {
			if(substr($startTime, -1)=='M'){
				$startTime = DateTime::createFromFormat('h:i A', $startTime)->format('Hi');			
			} else {
				$startTime = DateTime::createFromFormat('H:i', $startTime)->format('Hi');			
			}
			
	 	} 
	        //create time objects from start/end times and format as string (24hr AM/PM)
		if(!empty($endTime)  && is_numeric(substr($endTime, 0, 1))){
			if(substr($endTime, -1)=='M'){
				$endTime = DateTime::createFromFormat('h:i A', $endTime)->format('Hi');			
			} else {
				$endTime = DateTime::createFromFormat('H:i', $endTime)->format('Hi');
			}
	    } 
	    if($endTime == '0000'){
	    	$endTime = 2400;
	    }
	    
   		if((int)$startTime > (int)$endTime ) {
   			// midnight situation
   			$endTime = 2400 + (int)$endTime;
   		}
   		
        // check if current time is within the range
        if (((int)$startTime < (int)$currentTime) && ((int)$currentTime < (int)$endTime)) {
            $status = TRUE;
        }
    
	endif;
	if($status == false) {
		
		if(isset($storeSchedule[lcfirst(date( 'l', strtotime ( '-1 day' , $timestamp )))])) :

				$day = ($storeSchedule[lcfirst(date('l',(strtotime ( '-1 day' , $timestamp ) )))]);
				
				$startTime = $day['opens'];
				$endTime = $day['closes'];
				if(!empty($startTime) && is_numeric(substr($startTime, 0, 1)) ) {
					if(substr($startTime, -1)=='M'){
						$startTime = DateTime::createFromFormat('h:i A', $startTime)->format('Hi');			
					} else {
						$startTime = DateTime::createFromFormat('H:i', $startTime)->format('Hi');			
					}
					
			 	} 
			        //create time objects from start/end times and format as string (24hr AM/PM)
				if(!empty($endTime)  && is_numeric(substr($endTime, 0, 1))){
					if(substr($endTime, -1)=='M'){
						$endTime = DateTime::createFromFormat('h:i A', $endTime)->format('Hi');			
					} else {
						$endTime = DateTime::createFromFormat('H:i', $endTime)->format('Hi');
					}
			    } 
					if( ((int)$startTime > (int)$endTime) && (int)$currentTime < (int)$endTime ) {
	 					$status = TRUE;

					}
				
				
		endif;
		
	}
   	return $status;
    
}


function liste_get_timezone() {

    $tzstring = get_option( 'timezone_string' );
    $offset   = get_option( 'gmt_offset' );

    //Manual offset...
    //@see http://us.php.net/manual/en/timezones.others.php
    //@see https://bugs.php.net/bug.php?id=45543
    //@see https://bugs.php.net/bug.php?id=45528
    //IANA timezone database that provides PHP's timezone support uses POSIX (i.e. reversed) style signs
    if( empty( $tzstring ) && 0 != $offset && floor( $offset ) == $offset ){
        $offset_st = $offset > 0 ? "-$offset" : '+'.absint( $offset );
        $tzstring  = 'Etc/GMT'.$offset_st;
    }

    //Issue with the timezone selected, set to 'UTC'
    if( empty( $tzstring ) ){
        $tzstring = 'UTC';
    }

    $timezone = new DateTimeZone( $tzstring );
    return $timezone; 
}


function listeo_check_if_has_hours(){
	$status = false;
	$has_hours = false;
	global $post;
	$days = listeo_get_days();
	$storeSchedule = array();
	foreach ($days as $d_key => $value) {
		$open_val = get_post_meta($post->ID, '_'.$d_key.'_opening_hour', true);
		$opening = ($open_val) ? $open_val : '' ;
		$clos_val = get_post_meta($post->ID, '_'.$d_key.'_closing_hour', true);
		$closing = ($clos_val) ? $clos_val : '';
		
		if(is_numeric(substr($opening, 0, 1))) {
			$has_hours = true;
		}
		$storeSchedule[$d_key] = array(
			'opens' => $opening,
			'closes' => $closing
		);
	}
	
	return $has_hours;
}

// function listeo_check_if_open(){
	
// 	$status = false;
// 	$has_hours = false;
// 	global $post;
// 	$days = listeo_get_days();
// 	$storeSchedule = array();
// 	foreach ($days as $d_key => $value) {
// 		$open_val = get_post_meta($post->ID, '_'.$d_key.'_opening_hour', true);
// 		$opening = ($open_val) ? $open_val : '' ;
// 		$clos_val = get_post_meta($post->ID, '_'.$d_key.'_closing_hour', true);
// 		$closing = ($clos_val) ? $clos_val : '';
// 		if(is_numeric(substr($opening, 0, 1))) {
// 			$has_hours = true;
// 		}
// 		$storeSchedule[$d_key] = array(
// 			'opens' => $opening,
// 			'closes' => $closing
// 		);
// 	}
	
// 	if(!$has_hours){
// 		return;
// 	}
	
//     //get current East Coast US time
//     $timeObject = new DateTime();
//     $timestamp 		= $timeObject->getTimeStamp();
//     $currentTime 	= $timeObject->setTimestamp($timestamp)->format('H:i A');
//     $timezone		= get_option('timezone_string');
    
// 	if(isset($storeSchedule[lcfirst(date('l', $timestamp))])) :
// 		$day = ($storeSchedule[lcfirst(date('l', $timestamp))]);
// 		$startTime = $day['opens'];
// 		$endTime = $day['closes'];
		
// 		if(!empty($startTime) && is_numeric(substr($startTime, 0, 1)) ) {
// 	 			$startTime = DateTime::createFromFormat('h:i A', $startTime)->format('H:i A');	

// 	 	} 
// 	        //create time objects from start/end times and format as string (24hr AM/PM)
//         if(!empty($endTime)  && is_numeric(substr($endTime, 0, 1))){
//          	$endTime = DateTime::createFromFormat('h:i A', $endTime)->format('H:i A');	
//         }

//         // check if current time is within the range
//         if (($startTime < $currentTime) && ($currentTime < $endTime)) {
//             $status = TRUE;
            
//         }
// 	endif;
//    return $status;
    
// }


function listeo_get_geo_data($post){ 
	$terms = get_the_terms( $post->ID, 'listing_category' );
	
	if($terms ) {
		$term = array_pop($terms);	
		
		$t_id = $term->term_id;
		// retrieve the existing value(s) for this meta field. This returns an array
		$icon = get_term_meta($t_id,'icon',true);
		if($icon) {
			$icon = '<i class="'.$icon.'"></i>';	
		}	
	}
	if(empty($icon)){
		$icon = get_post_meta( $post->ID, '_icon', true );
	}
	
	if(empty($icon)){
		$icon = '<i class="im im-icon-Map-Marker2"></i>';
	}
	

	ob_start(); ?>

	  	data-title="<?php the_title(); ?>"
    	data-friendly-address="<?php echo esc_attr(get_post_meta( $post->ID, '_friendly_address', true )); ?>" 
    	data-address="<?php the_listing_address(); ?>" 
    	data-image="<?php echo listeo_core_get_listing_image( $post->ID ); ?>" 
    	data-longitude="<?php echo esc_attr( get_post_meta( $post->ID, '_geolocation_lat', true ) ); ?>" 
    	data-latitude="<?php echo esc_attr( get_post_meta( $post->ID, '_geolocation_long', true ) ); ?>"
    	<?php if(!get_option('listeo_disable_reviews')){ ?>
    	data-rating="<?php echo esc_attr( get_post_meta($post->ID, 'listeo-avg-rating', true ) ); ?>"
    	data-reviews="<?php echo esc_attr( listeo_get_reviews_number($post->ID)); ?>"
    	<?php } ?>
    	data-icon="<?php echo esc_attr($icon); ?>"

    <?php 
    return ob_get_clean();
}

	function listeo_get_unread_counter(){
        $user_id = get_current_user_id();
         global $wpdb;

        $result_1  = $wpdb -> get_var( "
        SELECT COUNT(*) FROM `" . $wpdb->prefix . "listeo_core_conversations` 
        WHERE  user_1 = '$user_id' AND read_user_1 = 0
        ");
        $result_2  = $wpdb -> get_var( "
        SELECT COUNT(*) FROM `" . $wpdb->prefix . "listeo_core_conversations` 
        WHERE  user_2 = '$user_id' AND read_user_2 = 0
        ");
        return $result_1+$result_2;
    }


    function listeo_count_posts_by_user($post_author=null,$post_type=array(),$post_status=array()) {
	    global $wpdb;

	    if(empty($post_author))
	        return 0;

	    $post_status = (array) $post_status;
	    $post_type = (array) $post_type;

	    $sql = $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = %d AND ", $post_author );

	    //Post status
	    if(!empty($post_status)){
	        $argtype = array_fill(0, count($post_status), '%s');
	        $where = "(post_status=".implode( " OR post_status=", $argtype).') AND ';
	        $sql .= $wpdb->prepare($where,$post_status);
	    }

	    //Post type
	    if(!empty($post_type)){
	        $argtype = array_fill(0, count($post_type), '%s');
	        $where = "(post_type=".implode( " OR post_type=", $argtype).') AND ';
	        $sql .= $wpdb->prepare($where,$post_type);
	    }

	    $sql .='1=1';
	    $count = $wpdb->get_var($sql);
	    return $count;
	} 

	function listeo_count_gallery_items( $post_id){
		if(!$post_id) { return; }

		$gallery = get_post_meta( $post_id, '_gallery', true );
		
		if(is_array($gallery)){
			return sizeof($gallery);	
		} else {
			return 0;
		}
		
	}

	function listeo_get_reviews_number( $post_id = 0 ) {

	    global $wpdb, $post;

	    $post_id = $post_id ? $post_id : $post->ID;

	    return $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_parent = 0 AND comment_post_ID = $post_id AND comment_approved = 1" );

	}

	function listeo_count_bookings($user_id,$status){
		global $wpdb;
		if( $status == 'approved' ) {
			$status_sql = "AND status IN ('confirmed','paid')";
		} else {
			$status_sql = "AND status='$status'";
		}
		
		$result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE owner_id=$user_id $status_sql", "ARRAY_A" );
		 return $wpdb->num_rows;
	}

	function listeo_count_my_bookings($user_id){
		global $wpdb;
		$result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE NOT comment = 'owner reservations' AND (`bookings_author` = '$user_id') AND (`type` = 'reservation')", "ARRAY_A" );
		
		 return $wpdb->num_rows;
	}


if ( ! function_exists('listeo_write_log')) {
   function listeo_write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}



//cmb2 slots field
function cmb2_render_callback_for_slots( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
	 $clock_format = get_option('listeo_clock_format','12') ?>

		<div class="availability-slots" data-clock-type="<?php echo esc_attr($clock_format); ?>hr">

		<?php 
		$days = array(
			'monday'	=> __('Monday','listeo_core'),
			'tuesday' 	=> __('Tuesday','listeo_core'),
			'wednesday' => __('Wednesday','listeo_core'),
			'thursday' 	=> __('Thursday','listeo_core'),
			'friday' 	=> __('Friday','listeo_core'),
			'saturday' 	=> __('Saturday','listeo_core'),
			'sunday' 	=> __('Sunday','listeo_core'),
			); 

		$field = json_decode( $field->value );
		$int = 0;
		?>

		<?php foreach ($days as $id => $dayname) { 
			?>

			<!-- Single Day Slots -->
			<div class="day-slots">
				<div class="day-slot-headline">
					<?php echo esc_html($dayname); ?>
				</div>


				<!-- Slot For Cloning / Do NOT Remove-->
				<div class="single-slot cloned">
					<div class="single-slot-left">
						<div class="single-slot-time"><?php echo esc_html($dayname); ?></div>
						<button class="remove-slot"><i class="fa fa-close"></i></button>
					</div>

					<div class="single-slot-right">
						<strong><?php echo esc_html('Slots','listeo_core'); ?></strong>
						<div class="plusminus horiz">
							<button></button>
							<input type="number" name="slot-qty" id="slot-qty" value="1" min="1" max="99">
							<button></button> 
						</div>
					</div>
				</div>		
				<!-- Slot For Cloning / Do NOT Remove-->

				<?php if (!isset( $field[$int][0]) ) { ?>
				<!-- No slots -->
				<div class="no-slots"><?php esc_html_e('No slots added','listeo_core'); ?></div>
				<?php } ?>
				<!-- Slots Container -->
				<div class="slots-container">


			<!-- Slots from database loop -->
			<?php if ( isset( $field ) && is_array( $field[$int] ) ) foreach ( $field[$int] as $slot ) { // slots loop
					$slot = explode( '|', $slot);?>	
						<div class="single-slot ui-sortable-handle">
							<div class="single-slot-left">
								<div class="single-slot-time"><?php echo esc_html($slot[0]); ?></div>
								<button class="remove-slot"><i class="fa fa-close"></i></button>
							</div>

							<div class="single-slot-right">
								<strong>Slots</strong>
								<div class="plusminus horiz">
									<button disabled=""></button>
									<input type="number" name="slot-qty" id="slot-qty" value="<?php echo esc_html($slot[1]); ?>" min="1" max="99">
									<button></button> 
								</div>
							</div>
						</div>
				<?php } ?>			
				<!-- Slots from database / End -->		

				</div>
				<!-- Slots Container / End -->
				<!-- Add Slot -->
				<div class="add-slot">
					<div class="add-slot-inputs">
						<input type="time" class="time-slot-start" min="00:00" max="12:59"/>
						<?php if( $clock_format == '12'){ ?>
						<select class="time-slot-start twelve-hr" id="">
							<option><?php esc_html_e('am'); ?></option>
							<option><?php esc_html_e('pm'); ?></option>
						</select>
						<?php } ?>

						<span>-</span>

						<input type="time" class="time-slot-end" min="00:00" max="12:59"/>
						<?php if( $clock_format == '12'){ ?>
						<select class="time-slot-end twelve-hr" id="">
							<option><?php esc_html_e('am'); ?></option>
							<option><?php esc_html_e('pm'); ?></option>
						</select>
						<?php } ?>

					</div>
					<div class="add-slot-btn">
						<button><?php esc_html_e('Add','listeo_core'); ?></button>
					</div>
				</div>
			</div>
		<?php 
		$int++;
		} ?>

		</div>
	
	<?php 
	echo $field_type_object->input( array( 'type' => 'hidden' ) );
}
add_action( 'cmb2_render_slots', 'cmb2_render_callback_for_slots', 10, 5 );

function cmb2_render_callback_for_listeo_calendar( $field, $escaped_value, $object_id, $object_type, $field_type){

	$calendar = new Listeo_Core_Calendar;

	echo $calendar->getCalendarHTML();
	// make sure we specify each part of the value we need.
	$value = wp_parse_args( $field->value, array(
		
		'dates'     => '',
		'price'       => '',
	) );

	echo $field_type->input( array(
			'name'  => $field_type->_name( '[dates]' ),
			'id'    => $field_type->_id( 'dates' ),
			'class'    => 'listeo-calendar-avail',
			'value' => esc_attr($value['dates']),
			'type'  => 'hidden',
		) );
	echo $field_type->input( array(
			'name'  => $field_type->_name( '[price]' ),
			'id'    => $field_type->_id( 'price' ),
			'class'    => 'listeo-calendar-price',
			'value' => esc_attr($value['price']),
			'type'  => 'hidden',
		) ); ?>

<?php
}
add_action( 'cmb2_render_listeo_calendar', 'cmb2_render_callback_for_listeo_calendar', 10, 5 );

function listeo_get_bookable_services($post_id){

	$services = array();
	
	$_menu = get_post_meta( $post_id, '_menu', 1 );
	if($_menu) {
		foreach ($_menu as $menu) { 
		
			if(isset($menu['menu_elements']) && !empty($menu['menu_elements'])) :
				foreach ($menu['menu_elements'] as $item) {
					if(isset($item['bookable'])){

						$services[] = $item;	
					}
				}
			endif;
	
		}
	}
	
	return $services;
}