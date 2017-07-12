<?php

class Banwoo_Option_Page {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}


	public function banwoo_add_admin_menu() {

		add_submenu_page( 'banwoo', 'Banggood Importer Settings', 'Settings', 'manage_options', 'banwoo-settings', array($this,'banwoo_options_page') );

	}


public function banwoo_settings_init() {

		register_setting( 'pluginPage', 'banwoo_settings' );

		add_settings_section(
			'banwoo_pluginPage_section',
			__( 'Entrée les clefs d\'api de woocommmerce', 'beygoo' ),
			array($this,'banwoo_settings_section_callback'),
			'pluginPage'
		);

		add_settings_field(
			'banwoo_api_key',
			__( 'Clé client', 'beygoo' ),
			array($this,'banwoo_api_key_render'),
			'pluginPage',
			'banwoo_pluginPage_section'
		);

		add_settings_field(
			'banwoo_api_secret',
			__( 'Secret client', 'beygoo' ),
			array($this,'banwoo_api_secret_render'),
			'pluginPage',
			'banwoo_pluginPage_section'
		);

		add_settings_field(
			'banwoo_strip_desc',
			__( 'Enleve les images du texte', 'beygoo' ),
			array($this,'banwoo_strip_desc_render'),
			'pluginPage',
			'banwoo_pluginPage_section'
		);
		add_settings_field(
			'banwoo_strip_style_script',
			__( 'Enleve les tag Style et script', 'beygoo' ),
			array($this,'banwoo_strip_style_script_render'),
			'pluginPage',
			'banwoo_pluginPage_section'
		);


	}


	public function banwoo_api_key_render() {

		$options = get_option( 'banwoo_settings' );
		?>
		<input type='text' name='banwoo_settings[banwoo_api_key]'
		       value='<?php echo $options['banwoo_api_key']; ?>'>
		<?php

	}


	public function banwoo_api_secret_render() {

		$options = get_option( 'banwoo_settings' );
		?>
		<input type='text' name='banwoo_settings[banwoo_api_secret]'
		       value='<?php echo $options['banwoo_api_secret']; ?>'>
		<?php

	}


	public function banwoo_strip_desc_render() {

		$options = get_option( 'banwoo_settings' );
		?>
		<input type='checkbox'
		       name='banwoo_settings[banwoo_strip_desc]' <?php checked( $options['banwoo_strip_desc'], 1 ); ?>
		       value='1'>
		<?php

	}
	public function banwoo_strip_style_script_render() {

		$options = get_option( 'banwoo_settings' );
		?>
		<input type='checkbox'
		       name='banwoo_settings[banwoo_strip_style_script]' <?php checked( $options['banwoo_strip_style_script'], 1 ); ?>
		       value='1'>
		<?php

	}


	public function banwoo_settings_section_callback() {

		echo __( 'Entrée les clefs d\'api de woocommmerce', 'beygoo' );

	}


	public function banwoo_options_page() {

		?>
		<div class="wrap">
		<h1>Banggood Importer Settings</h1>

		<form action='options.php' method='post'>



			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>

		</form>
		</div>
		<?php

	}
}