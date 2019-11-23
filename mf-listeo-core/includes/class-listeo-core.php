<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Listeo_Core {

	/**
	 * The single instance of Listeo_Core.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.2.2' ) {
		$this->_version = $version;
		
		$this->_token = 'listeo_core';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		register_activation_hook( $this->file, array( $this, 'install' ) );


		define( 'LISTEO_CORE_ASSETS_DIR', trailingslashit( $this->dir ) . 'assets' );
		define( 'LISTEO_CORE_ASSETS_URL', esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) ) );
		
		$dir = dirname(dirname(dirname( __FILE__)));
		$include_dir = $dir . '/listeo-core/includes/';
		include(  $include_dir . 'class-listeo-core-post-types.php' );
		include(  $include_dir . 'class-listeo-core-meta-boxes.php' );
		include(  'class-listeo-core-listing.php' );
		include(  $include_dir . 'class-listeo-core-reviews.php' );
		include(  'class-listeo-core-submit.php' );
		include(  $include_dir . 'class-listeo-core-shortcodes.php' );
		include(  'class-listeo-core-search.php' );
		include(  $include_dir . 'class-listeo-core-users.php' );
		include(  $include_dir . 'class-listeo-core-bookmarks.php' );
		include(  $include_dir . 'class-listeo-core-activities-log.php' );
		include(  $include_dir . 'class-listeo-core-calendar.php' );
		include(  $include_dir . 'class-listeo-core-emails.php' );
		include(  $include_dir . 'class-listeo-core-messages.php' );
		include(  $include_dir . 'class-listeo-core-bookings-calendar.php' );
		include(  $include_dir . 'class-listeo-core-commissions.php' );
		include(  $include_dir . 'class-listeo-core-payouts.php' );

		//include( 'class-listeo-core-bookings-admin.php' );
		// include( 'class-listeo-core-compare.php' );
		
		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		add_action( 'wp_ajax_handle_dropped_media', array( $this, 'listeo_core_handle_dropped_media' ));
		add_action( 'wp_ajax_nopriv_handle_dropped_media', array( $this, 'listeo_core_handle_dropped_media' ));
		add_action( 'wp_ajax_nopriv_handle_delete_media',  array( $this, 'listeo_core_handle_delete_media' ));
		add_action( 'wp_ajax_handle_delete_media',  array( $this, 'listeo_core_handle_delete_media' ));

		// Load API for generic admin functions
		// if ( is_admin() ) {
		// 	$this->admin = new Listeo_Core_Admin_API();
		// }
		
		$this->post_types 	= Listeo_Core_Post_Types::instance();
		$this->meta_boxes 	= new Listeo_Core_Meta_Boxes();

		remove_action( 'cmb2_admin_init', array( $this->meta_boxes,'listeo_register_taxonomy_metabox' ) );
		add_action( 'cmb2_admin_init', array( $this,'listeo_register_taxonomy_metabox' ) );


		$this->listing 		= new Listeo_Core_Listing();
		$this->listing 		= new Listeo_Core_Reviews();
		$this->submit 		= Listeo_Core_Submit::instance();
		$this->search 		= new Listeo_Core_Search();
		$this->users 		= new Listeo_Core_Users();
		$this->bookmarks 	= new Listeo_Core_Bookmarks();
		$this->activites_log = new Listeo_Core_Activities_Log();
		$this->messages 	= new Listeo_Core_Messages();
		$this->calendar 	= Listeo_Core_Calendar::instance();
		$this->emails 		= Listeo_Core_Emails::instance();
		$this->commissions 	= Listeo_Core_Commissions::instance();
		$this->payouts 		= Listeo_Core_Payouts::instance();
		
		
		
		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
		add_action( 'init', array( $this, 'image_size' ) );
		add_action( 'init', array( $this, 'register_sidebar' ) );
		
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );

		add_filter( 'template_include', array( $this, 'listing_templates' ) );

		add_action( 'plugins_loaded', array( $this, 'init_plugin' ), 13 );

		add_action( 'admin_notices', array( $this, 'google_api_notice' ));

		// Schedule cron jobs
		self::maybe_schedule_cron_jobs();
		

	} // End __construct ()
	  


	function listeo_register_taxonomy_metabox() {

		$prefix = 'listeo_';
	/**
	 * Metabox to add fields to categories and tags
	 */
	$cmb_term = new_cmb2_box( array(
		'id'               => $prefix . 'edit',
		'title'            => esc_html__( 'Listing Taxonomy Meta', 'listeo_core' ), // Doesn't output for term boxes
		'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
		'taxonomies'       => array( 'listing_category' ), // Tells CMB2 which taxonomies should have these fields
		// 'new_term_section' => true, // Will display in the "Add New Category" section
	) );


	$cmb_term->add_field( array(
		'name'           => 'Assign Features for this Category',
		'desc'           => 'Features can be created in Listings -> Features',
		'id'             =>  $prefix . 'taxonomy_multicheck',
		'taxonomy'       => 'listing_feature', //Enter Taxonomy Slug
		'type'           => 'taxonomy_multicheck_hierarchical',
		// Optional :
		'text'           => array(
			'no_terms_text' => 'Sorry, no terms could be found.' // Change default text. Default: "No terms"
		),
		'remove_default' => 'true' // Removes the default metabox provided by WP core. Pending release as of Aug-10-1
	) );

	
		$cmb_term->add_field( array(
		'name'           => 'Category description',
        'id'             =>  $prefix . 'taxonomy_content_description',
		'type'           => 'textarea',
	) );
	}

	/**
	 * Widgets init
	 */
	public function widgets_init() {
		$dir = dirname(dirname(dirname( __FILE__)));
		$include_dir = $dir . '/listeo-core/includes/';		
		include( $include_dir.'class-listeo-core-widgets.php' );
	}


	public function include_template_functions() {

		$dir = dirname(dirname(dirname( __FILE__)));
		$dir = $dir . '/listeo-core/';		

		// include( REALTEO_PLUGIN_DIR.'/listeo-core-template-functions.php' );
		// include( REALTEO_PLUGIN_DIR.'/includes/paid-listings/listeo-core-paid-listings-functions.php' );
		include( $dir.'../mf-listeo-core/listeo-core-template-functions.php' );
		include( $dir.'/includes/paid-listings/listeo-core-paid-listings-functions.php' );
		
	}

	/* handles single listing and archive listing view */
	public static function listing_templates( $template ) {
		$post_type = get_post_type();  
		$custom_post_types = array( 'listing' );
		
		$template_loader = new Listeo_Core_Template_Loader;
		if ( in_array( $post_type, $custom_post_types ) ) {
			
			if ( is_archive() && !is_author() ) {

				$template = $template_loader->locate_template('archive-' . $post_type . '.php');

				return $template;
			}

			if ( is_single() ) {
				$template = $template_loader->locate_template('single-' . $post_type . '.php');
				return $template;
			}
		}

		if( is_tax( 'listing_category' ) ){
			$template = $template_loader->locate_template('archive-listing.php');
		}

		if( is_post_type_archive( 'listing' ) ){

			$template = $template_loader->locate_template('archive-listing.php');

		}

		return $template;
	}

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
		

	} // End enqueue_styles ()



	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		
		// wp_register_script(	'dropzone', esc_url( $this->assets_url ) . 'js/dropzone.js', array( 'jquery' ), $this->_version, true );
		wp_register_script(	'uploads', esc_url( $this->assets_url ) . 'js/uploads.min.js', array( 'jquery' ), $this->_version, true );
		wp_register_script(	'ajaxsearch', esc_url( $this->assets_url ) . 'js/ajax.search.min.js', array( 'jquery' ), $this->_version, true );
		
		wp_register_script( $this->_token . '-leaflet-markercluster', esc_url( $this->assets_url ) . 'js/leaflet.markercluster.js', array( 'jquery' ), $this->_version );
		wp_register_script( $this->_token . '-leaflet-geocoder', 'https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js', array( 'jquery' ), $this->_version );
		wp_register_script( $this->_token . '-leaflet-search', esc_url( $this->assets_url ) . 'js/leaflet-search.src.js', array( 'jquery' ), $this->_version );
		wp_register_script( $this->_token . '-leaflet-bing-layer', esc_url( $this->assets_url ) . 'js/leaflet-bing-layer.min.js', array( 'jquery' ), $this->_version );
		wp_register_script( $this->_token . '-leaflet-tilelayer-here', esc_url( $this->assets_url ) . 'js/leaflet-tilelayer-here.js', array( 'jquery' ), $this->_version );
		wp_register_script( $this->_token . '-leaflet-gesture-handling', esc_url( $this->assets_url ) . 'js/leaflet-gesture-handling.min.js', array( 'jquery' ), $this->_version );
		wp_register_script( $this->_token . '-leaflet', esc_url( $this->assets_url ) . 'js/listeo.leaflet.js', array( 'jquery' ), $this->_version );
 
       
		$mf_listeo_core = esc_url( trailingslashit( plugins_url( '/mf-listeo-core') ) );

		wp_register_script( $this->_token . '-frontend', $mf_listeo_core . 'assets/js/frontend.js', array( 'jquery' ), $this->_version."22" );
		wp_register_script( $this->_token . '-bookings', $mf_listeo_core . 'assets/js/bookings.js', array( 'jquery' ), $this->_version."224521ca" );
		
		wp_register_script(	'markerclusterer', esc_url( $this->assets_url )  . '/js/markerclusterer.js', array( 'jquery' ), $this->_version );
		wp_register_script( 'infobox-min', esc_url( $this->assets_url )  . '/js/infobox.min.js', array( 'jquery' ), $this->_version  );
		wp_register_script( 'jquery-geocomplete-min',esc_url( $this->assets_url )  . '/js/jquery.geocomplete.min.js', array( 'jquery','maps' ), $this->_version  );
		wp_register_script( 'maps', esc_url( $this->assets_url )  . '/js/maps.js', array( 'jquery','listeo-custom','markerclusterer' ), $this->_version  );



		$map_provider = get_option( 'listeo_map_provider');
		$maps_api_key = get_option( 'listeo_maps_api' );
		if($map_provider == 'google') {
			if($maps_api_key) {
				wp_enqueue_script( 'google-maps', 'https://maps.google.com/maps/api/js?key='.$maps_api_key.'&libraries=places' );
				wp_enqueue_script( 'infobox-min' );
				wp_enqueue_script( 'markerclusterer' );
				wp_enqueue_script( 'jquery-geocomplete-min' );	
				wp_enqueue_script( 'maps' );
			
			}	
		} else {
			wp_enqueue_script( 'leaflet.js','https://unpkg.com/leaflet@1.4.0/dist/leaflet.js');
			$map_provider = get_option('listeo_map_provider');
			if( $map_provider == 'bing'){
				wp_enqueue_script('polyfill','https://cdn.polyfill.io/v2/polyfill.min.js?features=Promise');
				wp_enqueue_script($this->_token . '-leaflet-bing-layer');
				
			}
			if( $map_provider = 'here' ){
				wp_enqueue_script($this->_token . '-leaflet-tilelayer-here');
			}

			wp_enqueue_script( $this->_token . '-leaflet-geocoder' );
			wp_enqueue_script( $this->_token . '-leaflet-markercluster' );
			wp_enqueue_script( $this->_token . '-leaflet-gesture-handling' );
			wp_enqueue_script( $this->_token . '-leaflet' );
					}
		
		wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js');

		$_price_min =  $this->get_min_all_listing_price('');
		$_price_max =  $this->get_max_all_listing_price('');


		$ajax_url = admin_url( 'admin-ajax.php', 'relative' );
		$currency = get_option( 'listeo_currency' );
		$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency); 
		
		$localize_array = array(
				'ajax_url'                	=> $ajax_url,
				'is_rtl'                  	=> is_rtl() ? 1 : 0,
				'lang'                    	=> defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : '', // WPML workaround until this is standardized
				'_price_min'		    	=> $_price_min,
				'_price_max'		    	=> $_price_max,
				'currency'		      		=> get_option( 'listeo_currency' ),
				'currency_position'		    => get_option( 'listeo_currency_postion' ),
				'currency_symbol'		    => esc_attr($currency_symbol),
				'submitCenterPoint'		    => get_option( 'listeo_submit_center_point','52.2296756,21.012228700000037' ),
				'centerPoint'		      	=> get_option( 'listeo_map_center_point','52.2296756,21.012228700000037' ),
				'country'		      		=> get_option( 'listeo_maps_limit_country' ),
				'upload'					=> admin_url( 'admin-ajax.php?action=handle_dropped_media' ),
  				'delete'					=> admin_url( 'admin-ajax.php?action=handle_delete_media' ),
  				'color'						=> get_option('pp_main_color','#274abb' ), 
  				'dictDefaultMessage'		=> esc_html__("Drop files here to upload","listeo_core"),
				'dictFallbackMessage' 		=> esc_html__("Your browser does not support drag'n'drop file uploads.","listeo_core"),
				'dictFallbackText' 			=> esc_html__("Please use the fallback form below to upload your files like in the olden days.","listeo_core"),
				'dictFileTooBig' 			=> esc_html__("File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.","listeo_core"),
				'dictInvalidFileType' 		=> esc_html__("You can't upload files of this type.","listeo_core"),
				'dictResponseError'		 	=> esc_html__("Server responded with {{statusCode}} code.","listeo_core"),
				'dictCancelUpload' 			=> esc_html__("Cancel upload","listeo_core"),
				'dictCancelUploadConfirmation' => esc_html__("Are you sure you want to cancel this upload?","listeo_core"),
				'dictRemoveFile' 			=> esc_html__("Remove file","listeo_core"),
				'dictMaxFilesExceeded' 		=> esc_html__("You can not upload any more files.","listeo_core"),
				'areyousure' 				=> esc_html__("Are you sure?","listeo_core"),
				'maxFiles' 					=> get_option('listeo_max_files',10),
				'maxFilesize' 				=> get_option('listeo_max_filesize',2),
				'clockformat' 				=> (get_option('listeo_clock_format','12') == '24') ? true : false,
				'prompt_price'				=> esc_html__('Set price for this date','listeo_core'),
				'menu_price'				=> esc_html__('Price (optional)','listeo_core'),
				'menu_desc'					=> esc_html__('Description','listeo_core'),
				'menu_title'				=> esc_html__('Title','listeo_core'),
				"applyLabel"				=> esc_html__( "Apply",'listeo_core'),
		        "cancelLabel" 				=> esc_html__( "Cancel",'listeo_core'),
		        "clearLabel" 				=> esc_html__( "Clear",'listeo_core'),
		        "fromLabel"					=> esc_html__( "From",'listeo_core'),
		        "toLabel" 					=> esc_html__( "To",'listeo_core'),
		        "customRangeLabel" 			=> esc_html__( "Custom",'listeo_core'),
		        "mmenuTitle" 				=> esc_html__( "Menu",'listeo_core'),
		        "pricingTooltip" 			=> esc_html__( "Click to make this item bookable in booking widget",'listeo_core'),
		        "today" 					=> esc_html__( "Today",'listeo_core'),
		        "yesterday" 				=> esc_html__( "Yesterday",'listeo_core'),
		        "last_7_days" 				=> esc_html__( "Last 7 Days",'listeo_core'),
		        "last_30_days" 				=> esc_html__( "Last 30 Days",'listeo_core'),
		        "this_month" 				=> esc_html__( "This Month",'listeo_core'),
		        "last_month" 				=> esc_html__( "Last Month",'listeo_core'),
		        "map_provider" 				=> get_option('listeo_map_provider','osm'),
		        "mapbox_access_token" 		=> get_option('listeo_mapbox_access_token'),
		        "mapbox_retina" 			=> get_option('listeo_mapbox_retina'),
		        "bing_maps_key" 			=> get_option('listeo_bing_maps_key'),
		        "thunderforest_api_key" 	=> get_option('listeo_thunderforest_api_key'),
		        "here_app_id" 				=> get_option('listeo_here_app_id'),
		        "here_app_code" 			=> get_option('listeo_here_app_code'),
		        "maps_reviews_text" 		=> esc_html__('reviews','listeo_core'),
		        "maps_noreviews_text" 		=> esc_html__('Not rated yet','listeo_core'),
  				"day_short_su" => esc_html_x("Su", 'Short for Sunday', 'listeo_core'),
	            "day_short_mo" => esc_html_x("Mo", 'Short for Monday','listeo_core'),
	            "day_short_tu" => esc_html_x("Tu", 'Short for Tuesday','listeo_core'),
	            "day_short_we" => esc_html_x("We", 'Short for Wednesday','listeo_core'),
	            "day_short_th" => esc_html_x("Th", 'Short for Thursday','listeo_core'),
	            "day_short_fr" => esc_html_x("Fr", 'Short for Friday','listeo_core'),
	            "day_short_sa" => esc_html_x("Sa", 'Short for Saturday','listeo_core'),
	            "radius_state" => get_option('listeo_radius_state')


			);
		$criteria_fields = listeo_get_reviews_criteria();
		
		$loc_critera = array();
		foreach ($criteria_fields as $key => $value) {
			$loc_critera[] = $key;
		};
		if(!empty($loc_critera)){
			$localize_array['review_criteria'] = implode(',',$loc_critera);	
		}
		
		wp_localize_script(  $this->_token . '-frontend', 'listeo_core', $localize_array);

		wp_enqueue_script( 'jquery-ui-core' );
		
		wp_enqueue_script( 'jquery-ui-autocomplete' );

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'uploads' );
		wp_enqueue_script( 'ajaxsearch' );
		
		wp_enqueue_script( $this->_token . '-frontend' );
		wp_enqueue_script( $this->_token . '-bookings' );
	
		
	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles ( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts ( $hook = '' ) {
		
		wp_register_script( $this->_token . '-settings', esc_url( $this->assets_url ) . 'js/settings' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-settings' );
		$mf_listeo_core = esc_url( trailingslashit( plugins_url( '/mf-listeo-core') ) );
		wp_register_script( $this->_token . '-admin', $mf_listeo_core. '/assets/js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version."2322" );
		wp_enqueue_script( $this->_token . '-admin' );
		

		$map_provider = get_option( 'listeo_map_provider');
		$maps_api_key = get_option( 'listeo_maps_api' );
		if($map_provider == 'google') {
			if($maps_api_key) {
				wp_enqueue_script( 'google-maps', 'https://maps.google.com/maps/api/js?key='.$maps_api_key.'&libraries=places' );	
				wp_register_script( $this->_token . '-admin-maps', esc_url( $this->assets_url ) . 'js/admin.maps' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
				wp_enqueue_script( $this->_token . '-admin-maps' );
			
			}
		} else {
			wp_enqueue_script( 'leaflet.js','https://unpkg.com/leaflet@1.4.0/dist/leaflet.js');
			wp_enqueue_script( 'leaflet-geocoder','https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js' );
			wp_register_script( $this->_token . '-admin-leaflet', esc_url( $this->assets_url ) . 'js/admin.leaflet' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
			wp_enqueue_script( $this->_token . '-admin-leaflet' );
			
		}
		wp_enqueue_script('jquery-ui-datepicker');
		$convertedData = listeo_date_time_wp_format();
        // add converented format date to javascript
        wp_localize_script(  $this->_token . '-admin', 'wordpress_date_format', $convertedData );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		$dir = dirname(dirname(dirname( __FILE__)));
		$include_dir = $dir . '/listeo-core/';		

		load_plugin_textdomain( 'listeo_core', false, $include_dir . '/languages/' );

	} // End load_localisation ()

	//subscription
	public function init_plugin() {

		$dir = dirname(dirname(dirname( __FILE__)));
		$include_dir = $dir . '/listeo-core/';		

		include( $include_dir.'includes/paid-listings/class-listeo-core-paid-subscriptions.php' );

		if ( class_exists( 'WC_Product_Subscription' ) ) {
			
			include_once( $include_dir.'includes/paid-listings/class-listeo-core-paid-subscriptions-product.php' );
			include_once( $include_dir.'includes/paid-listings/class-wc-product-listing-package-subscription.php' );

		}
	}

	/**
	 * Adds image sizes
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function image_size () {
		add_image_size('listeo-gallery', 1200, 0, true);
		add_image_size('listeo-listing-grid', 520, 397, true);
		add_image_size('listeo_core-avatar', 590, 590, true);
		add_image_size('listeo_core-preview', 200, 200, true);

	} // End load_localisation ()

	public function register_sidebar () {

		register_sidebar( array(
			'name'          => esc_html__( 'Single listing sidebar', 'listeo_core' ),
			'id'            => 'sidebar-listing',
			'description'   => esc_html__( 'Add widgets here.', 'listeo_core' ),
			'before_widget' => '<div id="%1$s" class="listing-widget widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title margin-bottom-35">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Listings sidebar', 'listeo_core' ),
			'id'            => 'sidebar-listings',
			'description'   => esc_html__( 'Add widgets here.', 'listeo_core' ),
			'before_widget' => '<div id="%1$s" class="listing-widget widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title margin-bottom-35">',
			'after_title'   => '</h3>',
		) );		



	} // End load_localisation ()


	function get_min_listing_price($type) {
		global $wpdb;
		$result = $wpdb->get_var(
	    $wpdb->prepare("
	            SELECT min(m2.meta_value + 0)
	            FROM $wpdb->posts AS p
	            INNER JOIN $wpdb->postmeta AS m1 ON ( p.ID = m1.post_id )
				INNER JOIN $wpdb->postmeta AS m2  ON ( p.ID = m2.post_id )
				WHERE
				p.post_type = 'listing'
				AND p.post_status = 'publish'
				AND ( m1.meta_key = '_offer_type' AND m1.meta_value = %s )
				AND ( m2.meta_key = '_price'  ) AND m2.meta_value != ''
	        ", $type )
	    ) ;

	    return $result;
	}	

	function get_max_listing_price($type) {
		global $wpdb;
		$result = $wpdb->get_var(
	    $wpdb->prepare("
	            SELECT max(m2.meta_value + 0)
	            FROM $wpdb->posts AS p
	            INNER JOIN $wpdb->postmeta AS m1 ON ( p.ID = m1.post_id )
				INNER JOIN $wpdb->postmeta AS m2  ON ( p.ID = m2.post_id )
				WHERE
				p.post_type = 'listing'
				AND p.post_status = 'publish'
				AND ( m1.meta_key = '_offer_type' AND m1.meta_value = %s )
				AND ( m2.meta_key = '_price'  ) AND m2.meta_value != ''
	        ", $type )
	    ) ;
	   

	    return $result;
	}	

	function get_min_all_listing_price() {
		global $wpdb;
		$result = $wpdb->get_var(
	    "
	            SELECT min(m2.meta_value + 0)
	            FROM $wpdb->posts AS p
	            INNER JOIN $wpdb->postmeta AS m1 ON ( p.ID = m1.post_id )
				INNER JOIN $wpdb->postmeta AS m2  ON ( p.ID = m2.post_id )
				WHERE
				p.post_type = 'listing'
				AND p.post_status = 'publish'
				AND ( m2.meta_key = '_price'  ) AND m2.meta_value != ''
	        "
	    ) ;

	    return $result;
	}	

	function get_max_all_listing_price() {
		global $wpdb;
		$result = $wpdb->get_var(
	   "
	            SELECT max(m2.meta_value + 0)
	            FROM $wpdb->posts AS p
	            INNER JOIN $wpdb->postmeta AS m1 ON ( p.ID = m1.post_id )
				INNER JOIN $wpdb->postmeta AS m2  ON ( p.ID = m2.post_id )
				WHERE
				p.post_type = 'listing'
				AND p.post_status = 'publish'
				AND ( m2.meta_key = '_price'  ) AND m2.meta_value != ''
	        "
	    ) ;
	   

	    return $result;
	}




	function listeo_core_handle_delete_media(){

	    if( isset($_REQUEST['media_id']) ){
	        $post_id = absint( $_REQUEST['media_id'] );
	        $status = wp_delete_attachment($post_id, true);
	        if( $status )
	            echo json_encode(array('status' => 'OK'));
	        else
	            echo json_encode(array('status' => 'FAILED'));
	    }
	    wp_die();
	}


	function listeo_core_handle_dropped_media() {
	    status_header(200);

	    $upload_dir = wp_upload_dir();
	    $upload_path = $upload_dir['path'] . DIRECTORY_SEPARATOR;
	    $num_files = count($_FILES['file']['tmp_name']);

	    $newupload = 0;

	    if ( !empty($_FILES) ) {
	        $files = $_FILES;
	        foreach($files as $file) {
	            $newfile = array (
	                    'name' => $file['name'],
	                    'type' => $file['type'],
	                    'tmp_name' => $file['tmp_name'],
	                    'error' => $file['error'],
	                    'size' => $file['size']
	            );

	            $_FILES = array('upload'=>$newfile);
	            foreach($_FILES as $file => $array) {
	                $newupload = media_handle_upload( $file, 0 );
	            }
	        }
	    }

	    echo $newupload;    
	    wp_die();
	}

		
		function google_api_notice() {
		
		$map_provider = get_option( 'listeo_map_provider');
		$maps_api_key = get_option( 'listeo_maps_api' );
		if($map_provider == 'google') {

			if(empty($maps_api_key)) {
			    ?>
			    <div class="error notice">
					<p><?php echo esc_html_e('Please configure Google Maps API key to use all Listeo features.') ?> <a href="http://www.docs.purethemes.net/listeo/knowledge-base/getting-google-maps-api-key/"><?php esc_html_e('Check here how to do it.','listeo_core') ?></a></p>
			    	
			        
			    </div>
			    <?php
			}
		}
	}
	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'listeo_core';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main Listeo_Core Instance
	 *
	 * Ensures only one instance of Listeo_Core is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Listeo_Core()
	 * @return Main Listeo_Core instance
	 */
	public static function instance ( $file = '', $version = '1.2.1' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?','listeo_core' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?','listeo_core' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
		$this->init_user_roles();
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

	/**
	* Schedule cron jobs for Listeo_Core events.
	*/
	public static function maybe_schedule_cron_jobs() {
	if ( ! wp_next_scheduled( 'listeo_core_check_for_expired_listings' ) ) {
		wp_schedule_event( time(), 'hourly', 'listeo_core_check_for_expired_listings' );
		}
	}

	function init_user_roles(){
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) && ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
 
		if ( is_object( $wp_roles ) ) {
				remove_role( 'owner' );
				add_role( 'owner', __( 'Owner', 'listeo_core' ), array(
					'read'                 => true,
					'upload_files'         => true,
					'edit_listing'         => true,
					'edit_posts'         => true,
					'read_listing'         => true,
					'delete_listing'       => true,
					'edit_listings'        => true,
					'delete_listings'      => true,
					'edit_listings'        => true,
					'assign_listing_terms' => true,
					
			) );

			$capabilities = array(
				'core' => array(
					'manage_listings'
				),
				'listing' => array(
					"edit_listing",
					"read_listing",
					"delete_listing",
					"edit_listings",
					"edit_others_listings",
					"publish_listings",
					"read_private_listings",
					"delete_listings",
					"delete_private_listings",
					"delete_published_listings",
					"delete_others_listings",
					"edit_private_listings",
					"edit_published_listings",
					"manage_listing_terms",
					"edit_listing_terms",
					"delete_listing_terms",
					"assign_listing_terms"
				));

				add_role( 'guest', __( 'Guest', 'listeo_core' ), array(
						'read'  => true,
				) );

			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->add_cap( 'administrator', $cap );
				}
			}
		}

	}
	
}