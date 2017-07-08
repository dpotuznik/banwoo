<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.onestepmda.com
 * @since      1.0.0
 *
 * @package    Banwoo
 * @subpackage Banwoo/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Banwoo
 * @subpackage Banwoo/admin
 * @author     Daniel Potuznik <osf@dpotuznik.com>
 */
class Banwoo_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Banwoo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Banwoo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/banwoo-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Banwoo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Banwoo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/banwoo-admin.js', array( 'jquery' ), $this->version, false );

	}
	/*
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $product_data_tabs       List of all tabs.
	 * */
	public function add_my_custom_product_data_tab( $product_data_tabs )  {
			// First Register the Tab by hooking into the 'woocommerce_product_data_tabs' filter
			//add_filter( 'woocommerce_product_data_tabs', 'add_my_custom_product_data_tab' );
			$product_data_tabs['banggood'] = array(
				'label' => __( 'Banggood', 'woocommerce' ),
				'target' => 'banwoo_product_data',
			);
			return $product_data_tabs;
		}

	// Next provide the corresponding tab content by hooking into the 'woocommerce_product_data_panels' action hook
	// See https://github.com/woothemes/woocommerce/blob/master/includes/admin/meta-boxes/class-wc-meta-box-product-data.php

	// ajoute un url qui permet de stocker l'url du produit
	public	function add_my_custom_product_data_fields() {
			global $woocommerce, $post;
			?>
			<!-- id below must match target registered in above add_my_custom_product_data_tab function -->
			<div id="banwoo_product_data" class="panel woocommerce_options_panel">
				<?php
				woocommerce_wp_text_input(
					array(
						'id'          => 'banwoo_url',
						'label'       => __( 'Banggood Url', 'woocommerce' ),
						'placeholder' => 'http://',
						'desc_tip'    => 'true',
						'description' => __( 'Enter the Banggood product url here.', 'woocommerce' )
					)
				);
				?>
			</div>
			<?php
		}

	// on save le l'url banggood dans la base de donnee
	public function woo_add_custom_general_fields_save( $post_id ){

		// Text Field
		$woocommerce_text_field = $_POST['banwoo_url'];
		if( !empty( $woocommerce_text_field ) )
			update_post_meta( $post_id, 'banwoo_url', esc_attr( $woocommerce_text_field ) );

	}

}
