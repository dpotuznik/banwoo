<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       www.onestepmda.com
 * @since      1.0.0
 *
 * @package    Banwoo
 * @subpackage Banwoo/admin/partials
 */
?>
<?php
if ( isset( $_GET['m'] ) )
{
	if ($_GET['m'] == 1) {
		?>
		<div id='message' class='updated fade'><p><strong>Le fichier à bien été reçus.</strong>
			</p></div>
		<?php
	}else if ($_GET['m'] == 0) {
			?>
			<div id='message' class='fail fade'><p><strong>Le fichier n'a pas été envoyer.</strong>
				</p></div>
			<?php

	}else if ($_GET['m'] == 'deleteok') {
	?>
	<div id='message' class='fail fade'><p><strong>Le fichier à été effacer.</strong>
		</p></div>
	<?php

	}else if ($_GET['m'] == 'bdeleleok') {
		?>
		<div id='message' class='fail fade'><p><strong>Les fichier séléctionner ont été effacer.</strong>
			</p></div>
		<?php

	}

}

if ( isset( $_GET['sub'] ) ){

	// on  affiche le formulaire de d'ajout de produits
	if ('proccess' == $_GET['sub'] ) include plugin_dir_path( __FILE__ ).'banwoo-admin-display-proccess.php';


}else{

	// on affiche la liste par default
	include plugin_dir_path( __FILE__ ).'banwoo-admin-display-default.php';
}



?>
