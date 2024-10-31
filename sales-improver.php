<?php

/**
 * Plugin Name:       Sales Improver
 * Plugin URI:        https://www.webgardeners.nl
 * Description:       WordPress plugin to improve sales using WooCommerce. The plugin shows automated text to the product based on statistics.
 * Version:           1.0.0
 * Author:            Webgardeners
 * Author URI:        https://www.webgardeners.nl
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sales_improver
 * Domain Path:       /lang
 */

define('SALES_IMPROVER_VERSION', '1.0');
define('SALES_IMPROVER_NAME', 'Sales Improver');
define('SALES_IMPROVER_SLUG', 'sales_improver');
define('SALES_IMPROVER_BASENAME', dirname(plugin_basename(__FILE__)));
define('SALES_IMPROVER_LICENSE_SITE', 'https://www.webgardeners.nl');


class SalesImprover {

    public $defaults = [
        'type' =>  'views',
        'options' => [
            'views' => [
                'message' => 'Viewed %amount%x %interval%',
                'interval' => 'today',
                'where'   => 'all',
                'top'   => 5
            ],
            'sales' => [
                'message' => 'Sold %amount%x %interval%',
                'interval' => 'today',
                'where'   => 'all',
                'top'   => 5
            ],
            'traffic' => [
                'message' => '%amount% people are looking at this product',
                //'interval' => 'today',
                'where'   => 'all',
                'top'   => 5,
                'when' => 5
            ],
            'ratings' => [
                'message' => 'Customers rated this product with %rating% /5',
                'interval' => 'today',
                'where'   => 'all',
                'top'   => 5,
                'when' => 5
            ]
        ],
    ];

    public function __construct(){

    }

    public function get_option(){
        return get_option(SALES_IMPROVER_SLUG, $this->defaults);
    }

    public function get_license(){
        return get_option(SALES_IMPROVER_SLUG . '_license');
    }

    function recursive_sanitize($array) {
        foreach ( $array as $key => &$value ) {
            if ( is_array( $value ) ) {
                $value = $this->recursive_sanitize($value);
            } else {
                $value = sanitize_text_field( $value );
            }
        }
        return $array;
    }

    public function update_option(){
        $options = $this->recursive_sanitize($_POST[ SALES_IMPROVER_SLUG ]);

        if(!empty( $options )){
            update_option(  SALES_IMPROVER_SLUG, $options, true );
            echo "<div class='updated'><p><strong>Settings Saved!</strong></p></div>";
        }
    }

    public function sales_improver_license(){
        $response = wp_remote_post(SALES_IMPROVER_LICENSE_SITE . '/wp-json/sales_improver_license/v2/validate', [
            'body' => [
                'domain'    => parse_url(home_url())['host']
            ]
        ]);

	    if ( is_wp_error( $response ) ) {
		    die("Something went wrong: " . $response->get_error_message());
	    }

	    $license = json_decode(wp_remote_retrieve_body( $response ), TRUE );
	    update_option(SALES_IMPROVER_SLUG. '_license', $license, true);

	    return $license;
	}

    public function wp_enqueue_scripts(){

        $this->view_increment();
        //wp_enqueue_style(  'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css', [], SALES_IMPROVER_VERSION, 'all' );
        wp_enqueue_style(  SALES_IMPROVER_SLUG, plugin_dir_url(__FILE__) . 'assets/public/style.css', [], SALES_IMPROVER_VERSION, 'all' );

        wp_enqueue_script('jquery');
        wp_enqueue_script( SALES_IMPROVER_SLUG, plugin_dir_url(__FILE__) . 'assets/public/script.js', [ 'jquery' ], SALES_IMPROVER_VERSION, true );
    }

    public function admin_enqueue_scripts(){
        //wp_enqueue_style(  'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css', [], SALES_IMPROVER_VERSION, 'all' );
        wp_enqueue_style(  SALES_IMPROVER_SLUG, plugin_dir_url(__FILE__) . 'assets/admin/style.css', [], SALES_IMPROVER_VERSION, 'all' );

        wp_enqueue_script('jquery');
        wp_enqueue_script( SALES_IMPROVER_SLUG, plugin_dir_url(__FILE__) . 'assets/admin/script.js', [ 'jquery' ], SALES_IMPROVER_VERSION, true );
    }

    public function register_activation_hook(){
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // Views
        $table = $wpdb->prefix . SALES_IMPROVER_SLUG . '_views';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
			id			bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    		post_id		bigint(20) NOT NULL,
    		timestamp   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
		);";
        dbDelta($sql);

        add_action('sales_improver_license',[$this, 'sales_improver_license']);
        if (! wp_next_scheduled ( 'sales_improver_license' )) {
            wp_schedule_event( time(), 'daily', 'sales_improver_license');
        }
        $this->sales_improver_license();
    }

    public function register_deactivation_hook(){
        wp_clear_scheduled_hook( 'sales_improver_license');
    }

    public function plugins_loaded(){
        load_plugin_textdomain(
            SALES_IMPROVER_SLUG,
            false,
            SALES_IMPROVER_BASENAME . '/lang'
        );
    }

    public function total_sales($product){
        return get_post_meta($product,'total_sales', true);
    }

    public function init(){
        register_activation_hook( __FILE__, [$this, 'register_activation_hook'] );
        register_deactivation_hook( __FILE__, [$this, 'register_deactivation_hook']);

        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'settings_link']);

        add_action( 'plugins_loaded', [$this, 'plugins_loaded'] );
        add_action( 'wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
        add_action( 'admin_enqueue_scripts', [$this, 'admin_enqueue_scripts'] );

        add_action( 'sales_improver_license', [$this, 'sales_improver_license']);

        add_action('admin_menu', [$this, 'admin_menu']);
        add_action( 'admin_notices', [$this, 'trial_notice']);
        add_action( 'admin_notices', [$this, 'license_notice']);

        add_action( 'admin_init', [$this, 'admin_init']);

        add_action('woocommerce_after_shop_loop_item', [$this, 'woocommerce_after_shop_loop_item']);
        add_action( 'wp_ajax_license_notice', [$this, 'ajax_license_notice']);
    }

    public function settings_link( $links ) {
        $links[] = '<a href="' .
            admin_url( 'admin.php?page=' . SALES_IMPROVER_SLUG ) .
            '">' . __('Settings') . '</a>';
        return $links;
    }

    public function admin_init(){

        register_setting(
            SALES_IMPROVER_SLUG,
            SALES_IMPROVER_SLUG
        );

        /*add_settings_section(
            SALES_IMPROVER_SLUG,
            'Sales',
            [ $this, 'sales_page' ],
            SALES_IMPROVER_SLUG
        );*/
    }

    public function admin_menu() {
        add_submenu_page( 'woocommerce', SALES_IMPROVER_NAME, SALES_IMPROVER_NAME, 'manage_options', SALES_IMPROVER_SLUG, [$this, 'sales_page'] );
    }

    public function sales_page() {
        $license = $this->get_license();
        $today = wp_date('Y-m-d', strtotime('now'));
        $expiration = wp_date('Y-m-d', strtotime($license['expiration']));
        if ( ($license['type'] ?? 'trial') == 'trial') $options['type'] = 'views';

        include_once __DIR__ . "/template/setting.php";
    }

    public function trial_notice(){
        $license = $this->get_license();
        if( ($license['type'] ?? 'trial') == 'trial'){
            include_once __DIR__ . "/template/trial_notice.php";
        }

    }

    public function license_notice(){
        $screen = get_current_screen();
        if ( $screen->id == ('woocommerce_page_' . SALES_IMPROVER_SLUG)){
            $license = get_option(  SALES_IMPROVER_SLUG . '_license');
            include_once __DIR__ . "/template/license_notice.php";
        }
    }

    public function ajax_license_notice(){
        $email = sanitize_email($_POST['email']);
        $response = wp_remote_post(SALES_IMPROVER_LICENSE_SITE . '/wp-json/sales_improver_license/v2/subscribe', [
            'body' => [
                'email'     => $email,
                'domain'    => parse_url(home_url())['host']
            ]
        ]);

        if ( is_wp_error( $response ) ) {
            die("Something went wrong: " . $response->get_error_message());
        }

        $license = json_decode(wp_remote_retrieve_body( $response ), TRUE );
        update_option(SALES_IMPROVER_SLUG. '_license', $license, true);
        die('success');
    }

    public function top_rated($n){
        $query = array(
            'posts_per_page' => $n,
            'no_found_rows'  => 1,
            'post_status'    => 'publish',
            'post_type'      => 'product',
            'meta_key'       => '_wc_average_rating',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'meta_query'     => WC()->query->get_meta_query(),
            'tax_query'      => WC()->query->get_tax_query(),
        );

        $r = new WP_Query( $query );
        $posts = [];

        if ( $r->have_posts() ) {
            while( $r->have_posts() ) {
                $r->the_post();
                $posts[] = get_the_ID();
            }
        }
        return $posts;
    }

    public function sale_count($after){
        $all_orders = wc_get_orders(
            array(
                'limit' => -1,
                'status' => array_map( 'wc_get_order_status_name', wc_get_is_paid_statuses() ),
                'date_created' => '>=' . $after,
                //'date_created' => ' >=' . $after,// ' <' . ( time() - HOUR_IN_SECONDS ),
                'return' => 'ids',
            )
        );

        $products = [];
        foreach ( $all_orders as $all_order ) {
            $order = wc_get_order( $all_order );
            $items = $order->get_items();
            foreach ( $items as $item ) {
                $product_id = $item->get_product_id();

                if( isset($products[$product_id]) ){
                    $products[$product_id] += absint( $item['qty'] );
                } else {
                    $products[$product_id] = absint( $item['qty'] );
                }
            }
        }

        arsort($products);
        return $products;
    }

    public function woocommerce_after_shop_loop_item(){

        $license = $this->get_license();

        $expiration = wp_date('Y-m-d', strtotime($license['expiration']));
        $today = wp_date('Y-m-d', strtotime('now'));

	    global $product, $wpdb;
	    $option  = $this->get_option();
	    $type = $option['type'];
	    $option = $option['options'][$type];
	    $message = false;

        if( ($license['type'] ?? 'trial') == 'trial' ) $type = 'views';


		if($type == 'views'){
			$today = wp_date('Y-m-d', strtotime('now'));
			$week = wp_date('Y-m-d', strtotime('-1 week'));
			$month = wp_date('Y-m-d', strtotime('-1 month'));

			$views = $this->view_count(${$option['interval']}, ($option['where'] == 'top') ? $option['top'] : false);

			if(array_key_exists($product->id, $views) ){
				$message = str_replace(
					['%amount%', '%interval%'],
					[$views[$product->id], $option['interval']],
					$option['message']
				);
			}

		}elseif($type == 'sales'){

			$today = wp_date('Y-m-d', strtotime('now'));
			$week = wp_date('Y-m-d', strtotime('-1 week'));
			$month = wp_date('Y-m-d', strtotime('-1 month'));
			$sale_count = $this->sale_count(${$option['interval']});

			if(($option['where'] != 'all')){
				$sale_count = array_slice($sale_count, 0 , $option['top'], true);
			}

			if(array_key_exists($product->id, $sale_count) ){
				$message = str_replace(
					['%amount%', '%interval%'],
					[$sale_count[$product->id], $option['interval']],
					$option['message']
				);
			}
		}elseif($type == 'traffic'){

			$now = wp_date('Y-m-d H:i:s', strtotime('-3 minute'));
			$views = $this->view_count($now, ($option['where'] == 'top') ? $option['top'] : false);

			if(
				array_key_exists($product->id, $views) &&
				(empty($option['when']) || ($views[$product->id] > $option['when']) )
			){
				$message = str_replace(
					'%amount%',
					$views[$product->id],
					$option['message']
				);
			}
		}elseif($type == 'ratings'){
			$avg_rating = $product->get_average_rating();

			if(
				( $avg_rating > $option['when'] ) &&
				(
					( $option['where'] == 'all' ) ||
					( in_array($product->id, $this->top_rated($option['top']))  )
				)
			){
				$message = str_replace('%rating%', $avg_rating, $option['message']);
			}
		}

		if($message): ?>
			<div class="sales-improver-notice">
				<?php echo esc_attr($message) ?>
			</div>
		<?php endif;
    }

    public function view_increment()
    {
        global $post;

        if (is_single($post->ID) && ($post->post_type == 'product')) {
            global $wpdb;
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            $table = $wpdb->prefix . SALES_IMPROVER_SLUG . '_views';
            $wpdb->insert(
                $table, [
                    'post_id' => $post->ID,
                    'timestamp' => wp_date('Y-m-d H:i:s')
                ]
            );
        }
    }

    public function view_count($after, $limit = false){
        global $wpdb;
        $table = $wpdb->prefix . SALES_IMPROVER_SLUG . '_views';
        $result = $wpdb->get_results("SELECT post_id, COUNT(post_id) as views FROM  $table WHERE timestamp > '$after' GROUP BY post_id ORDER BY views DESC" . ( ($limit) ? (" LIMIT " . $limit) : ""  ) . ";", ARRAY_A);

        $results = [];
        foreach($result as $r){
            $results[ $r['post_id'] ] = $r['views'];
        }
        return $results;

    }

}

$plugin = new SalesImprover();
$plugin->init();