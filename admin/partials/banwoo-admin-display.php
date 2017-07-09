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
if ( isset( $_GET['m'] ) && $_GET['m'] == '1' )
{
	if ($_GET['m'] == 1) {
		?>
		<div id='message' class='updated fade'><p><strong>Le fichier à bien été reçus.</strong>
			</p></div>
		<?php
	}else{
			?>
			<div id='message' class='fail fade'><p><strong>Le fichier n'a pas été envoyer.</strong>
				</p></div>
			<?php

	}

}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<h2>Bangood Importer</h2>


	<!-- Le type d'encodage des données, enctype, DOIT être spécifié comme ce qui suit -->
	<form enctype="multipart/form-data" action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
		<input type="hidden" name="action" value="banwoo_save_file" />
		<?php wp_nonce_field( 'banwoo_verify' ); ?>
		<!-- MAX_FILE_SIZE doit précéder le champ input de type file -->
		<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
		<!-- Le nom de l'élément input détermine le nom dans le tableau $_FILES -->
		Envoyez ce fichier : <input name="userfile" type="file" />
		<input type="submit" value="Envoyer le fichier" />
	</form>
</div>