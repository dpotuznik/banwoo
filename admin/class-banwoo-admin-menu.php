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
class Banwoo_Admin_Menu {

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

	//add_action( 'admin_menu', 'my_admin_menu' );

	public function add_banggood_admin_menu() {
		add_menu_page( 'Bangood Import Product', 'Bangood Import', 'manage_options', 'banwoo' , array($this, 'banggood_admin_page'), 'dashicons-tickets', 6  );
	}


	public function banggood_admin_page(){
		// Show file
		require_once plugin_dir_path( __FILE__ ) . '/partials/banwoo-admin-display.php';
	}


	/*
	 * Va sauver les zip fournis par bangood pour crrer les produit
	 */


	public function process_banwoo_save_file(){
		if ( !current_user_can( 'manage_options' ) )
		{
			wp_die( 'You are not allowed to be on this page.' );
		}
		// Check that nonce field
		check_admin_referer( 'banwoo_verify' );


		$uploaddir = plugin_dir_path( __FILE__ ) .'../banggood_zip/';
		$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);


		if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
			$status=1;
		} else {
			$status=0;
		}

		/*
		echo '<pre>';
		echo 'Voici quelques informations de débogage :';
		print_r($_FILES);

		echo '</pre>';
		*/


		wp_redirect(  admin_url( 'admin.php?page=banwoo&m='.$status ) );
		exit;
	}


}
