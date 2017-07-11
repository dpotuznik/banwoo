<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<h2>Bangood Importer</h2>

<div class="tablenav top">
	<!-- Le type d'encodage des données, enctype, DOIT être spécifié comme ce qui suit -->
	<form enctype="multipart/form-data" action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
		<input type="hidden" name="action" value="banwoo_save_file" />
		<?php wp_nonce_field( 'banwoo_verify' ); ?>
		<!-- MAX_FILE_SIZE doit précéder le champ input de type file -->
		<input class="button action" type="hidden" name="MAX_FILE_SIZE" value="30000" />
		<!-- Le nom de l'élément input détermine le nom dans le tableau $_FILES -->
		Ajouter fichier :
		<label for="file" class="button action">Choisir un fichier</label>
		<input id="file" style="display: none" name="userfile" type="file" />
		<input class="button action" type="submit" value="Envoyer" />
	</form>
</div>
<?php
	//Create an instance of our package class...
	$banwooListZip = new Banwoo_list_zip();
	//Fetch, prepare, sort, and filter our data...
	$banwooListZip->prepare_items();

	?>
	<div class="wrap">

		<div id="icon-users" class="icon32"><br/></div>
		<h2>List des fichier Bangood à traiter</h2>

		<div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
			<p>Téléchargement du fichier zip recupere depuis le centre de téléchargement de bangood qui contient les produit et image </p>
		</div>

		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<form id="movies-filter" method="post">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<!-- Now we can render the completed list table -->
			<?php $banwooListZip->display() ?>
		</form>

	</div>


</div>