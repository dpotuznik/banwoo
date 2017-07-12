<div class="wrap">
	<h1>Bangood Import file</h1>

	<div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
		<p><b>NOTE:</b> si il n'est pas possible de selectionner le produit c'est qu'il existe deja ! </p>
	</div>
<br>
<?php



	//Create an instance of our package class...
	$banwooListZip = new Banwoo_list_zip();
	//Fetch, prepare, sort, and filter our data...
	$banwooListZip->dislpay_insert_form( $_GET[ 'id' ]  );

?>
	</div>

