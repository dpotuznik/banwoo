<div class="wrap">
	<h1>Bangood Import file</h1>

<?php
	//Create an instance of our package class...
	$banwooListZip = new Banwoo_list_zip();
	//Fetch, prepare, sort, and filter our data...
	$banwooListZip->dislpay_insert_form( $_GET[ 'id' ]  );

?>