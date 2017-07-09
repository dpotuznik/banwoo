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

<?php
	//Create an instance of our package class...
	$banwooListZip = new Banwoo_list_zip();
	//Fetch, prepare, sort, and filter our data...
	$banwooListZip->prepare_items();

	?>
	<div class="wrap">

		<div id="icon-users" class="icon32"><br/></div>
		<h2>List Table Test</h2>

		<div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
			<p>This page demonstrates the use of the <tt><a href="http://codex.wordpress.org/Class_Reference/WP_List_Table" target="_blank" style="text-decoration:none;">WP_List_Table</a></tt> class in plugins.</p>
			<p>For a detailed explanation of using the <tt><a href="http://codex.wordpress.org/Class_Reference/WP_List_Table" target="_blank" style="text-decoration:none;">WP_List_Table</a></tt>
				class in your own plugins, you can view this file <a href="<?php echo admin_url( 'plugin-editor.php?plugin='.plugin_basename(__FILE__) ); ?>" style="text-decoration:none;">in the Plugin Editor</a> or simply open <tt style="color:gray;"><?php echo __FILE__ ?></tt> in the PHP editor of your choice.</p>
			<p>Additional class details are available on the <a href="http://codex.wordpress.org/Class_Reference/WP_List_Table" target="_blank" style="text-decoration:none;">WordPress Codex</a>.</p>
		</div>

		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<form id="movies-filter" method="get">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<!-- Now we can render the completed list table -->
			<?php $banwooListZip->display() ?>
		</form>

	</div>


</div>