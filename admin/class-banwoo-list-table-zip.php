<?php
/*
Plugin Name: Custom List Table Example
Plugin URI: http://www.mattvanandel.com/
Description: A highly documented plugin that demonstrates how to create custom List Tables using official WordPress APIs.
Version: 1.4.1
Author: Matt van Andel
Author URI: http://www.mattvanandel.com
License: GPL2
*/
/*  Copyright 2015  Matthew Van Andel  (email : matt@mattvanandel.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



/* == NOTICE ===================================================================
 * Please do not alter this file. Instead: make a copy of the entire plugin, 
 * rename it, and work inside the copy. If you modify this plugin directly and 
 * an update is released, your changes will be lost!
 * ========================================================================== */


require plugin_dir_path( __FILE__ ) . '../vendor/autoload.php';

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use League\Csv\Reader;


/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary. In this tutorial, we are
 * going to use the WP_List_Table class directly from WordPress core.
 *
 * IMPORTANT:
 * Please note that the WP_List_Table class technically isn't an official API,
 * and it could change at some point in the distant future. Should that happen,
 * I will update this plugin with the most current techniques for your reference
 * immediately.
 *
 * If you are really worried about future compatibility, you can make a copy of
 * the WP_List_Table class (file path is shown just below) to use and distribute
 * with your plugins. If you do that, just remember to change the name of the
 * class to avoid conflicts with core.
 *
 * Since I will be keeping this tutorial up-to-date for the foreseeable future,
 * I am going to work with the copy of the class provided in WordPress core.
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}




/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 *
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 *
 * Our theme for this list table is going to be movies.
 */
class Banwoo_list_zip extends WP_List_Table {

    /** ************************************************************************
     * Normally we would be querying data from a database and manipulating that
     * for use in your list table. For this example, we're going to simplify it
     * slightly and create a pre-built array. Think of this as the data that might
     * be returned by $wpdb->query()
     *
     * In a real-world scenario, you would make your own custom query inside
     * this class' prepare_items() method.
     *
     * @var array
     **************************************************************************/
	private $zipFolder;
	private $proccessFolder;
	private $zipFiles;
	private $proccessFiles;
	private $woocommerce;

    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'zip',     //singular name of the listed records
            'plural'    => 'zip',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

	    $this->zipFolder =  plugin_dir_path( __FILE__ ) .'../banggood_zip';
	    $this->proccessFolder =  plugin_dir_path( __FILE__ ) .'../banggood_proccess';
	    $this->zipFiles = $this->get_list_zip_files();
	    $this->proccessFiles = $this->get_list_proccess_files();

	    $this->woocommerce = new Client(
		    'https://beygoo.com',
		    'ck_90f67edaf907294c6393deaa7e0c3cdf1c836bf7',
		    'cs_cc41467c16713ce01b5b2d4d396c53ecbc6038a2',
		    [
			    'wp_api' => true,
			    'version' => 'wc/v2',
			    'query_string_auth' => true,
			    'verify_ssl' => false
		    ]
	    );


    }


	public function get_list_zip_files() {
		$arr = array();
		$directory =  $this->zipFolder;
		$files = array_diff(scandir($directory), array('..', '.'));
		$id = 1;
		foreach ($files as $file){
			$arr[$id]=array(
				'ID'        => $id,
				'title'     => $file ,
				'csv_image'    => ''
			);
			$id++;
		}
		return $arr;
	}


	public function get_list_proccess_files() {
		$arr = array();
		$list =array();
		$directory =  $this->proccessFolder;
		$files = array_diff(scandir($directory), array('..', '.'));

		foreach ($files as $file){
			$src = str_replace('_product_image.csv','',$file);
			$src = str_replace('_product_info.csv','',$src);
			$list[$src]=1;
		}

		$id = 1;
		foreach ($list as $k => $tmp){
			if (file_exists($directory.'/'.$k.'_product_image.csv') && file_exists($directory.'/'.$k.'_product_info.csv')) {
				$arr[ $id ] = array(
					'ID'          => $id,
					'title'       => $k.'_product_info.csv',
					'csv_image' => $k.'_product_image.csv'
				);
				$id ++;
			}
		}
		return $arr;
	}

    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title()
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as
     * possible.
     *
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     *
     * For more detailed insight into how columns are handled, take a look at
     * WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
        switch($column_name){
            case 'csv_image':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes

        }
    }


    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     *
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     *
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_title($item){

	    // create a nonce
	    $captcha = wp_create_nonce( 'banwoo_protect' );

        //Build row actions
        $actions = array(
            'proccess'      => sprintf('<a href="?page=%s&action=%s&id=%s&captcha=%s">Proccess</a>',$_REQUEST['page'],'proccess',$item['ID'],$captcha),
            'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s&captcha=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID'],$captcha),
        );

        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['title'],
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }


    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ 'bulk-list',  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }

    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
            'bulk-delete'    => 'Delete',
            'bulk-proccess'    => 'Proccess'
        );
        return $actions;
    }

    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     *
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 5;


        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();


        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);


        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();


        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        $data = $this->proccessFiles;


        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');


        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         *
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         *
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/


        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($data);


        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);



        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;


        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => 'Product Info File',
            'csv_image'    => 'Product Image File'
        );
        return $columns;
    }

    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
     * you will need to register it here. This should return an array where the
     * key is the column that needs to be sortable, and the value is db column to
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     *
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',false),     //true means it's already sorted
            'csv_image'    => array('csv_image',false)
        );
        return $sortable_columns;
    }

    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
	        // In our file that handles the request, verify the nonce.
	        $nonce = esc_attr( $_REQUEST['captcha'] );

	        if ( ! wp_verify_nonce( $nonce, 'banwoo_protect' ) ) {
		        die( 'Go get a life script kiddies' );
	        }
	        else {
		        $this->delete_zip( absint( $_GET['id'] ) );
		       // wp_die('Items deleted (or they would be if we had items to delete)!');
		        wp_redirect(  admin_url( 'admin.php?page=banwoo&m=deleleok' ) );
		        exit;
	        }


        }

        //Detect when a bulk action is being triggered...
        if( 'proccess'===$this->current_action() ) {
	        wp_redirect(  admin_url( 'admin.php?page=banwoo&sub=proccess&id='.absint( $_GET['id'] )) );
        }

	    // BULK VERSION

	    //Detect when a bulk action is being triggered...
	    if( 'bulk-delete'===$this->current_action() ) {

		    $delete_ids = esc_sql( $_POST['bulk-list'] );

		    // loop over the array of record IDs and delete them
		    foreach ( $delete_ids as $id ) {
			    $this->delete_zip( $id );

		    }

		    wp_redirect(  admin_url( 'admin.php?page=banwoo&m=bdeleleok' ) );
		    exit;
	    }

	    //Detect when a bulk action is being triggered...
	    if( 'bulk-proccess'===$this->current_action() ) {
		    wp_redirect(  admin_url( 'admin.php?page=banwoo&sub=proccess' ) );
		    exit;
	    }


    }

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

			$zip = \Comodojo\Zip\Zip::open($uploadfile);
			$zip->extract($this->proccessFolder);
			$this->proccessFiles = $this->get_list_proccess_files();
			unlink($uploadfile);
			$status=1;
		} else {
			$status=0;
		}


		wp_redirect(  admin_url( 'admin.php?page=banwoo&m='.$status ) );
		exit;
	}

	public function delete_zip( $id ) {
		unlink($this->proccessFolder.'/'.$this->proccessFiles[$id]['title']);
		unlink($this->proccessFolder.'/'.$this->proccessFiles[$id]['csv_image']);
	}


	public function dislpay_insert_form( $id  ) {

		$input_product_csv = Reader::createFromPath($this->proccessFolder.'/'.$this->proccessFiles[$id]['title']);
		$input_image_csv = Reader::createFromPath($this->proccessFolder.'/'.$this->proccessFiles[$id]['csv_image']);

		//get at maximum 25 rows starting from the 801st row
		$res = $input_product_csv->setOffset(1)->setLimit(25)->fetch();
		$imageList = $input_image_csv->setOffset(1)->setLimit(100)->fetch();
		$image_list_array = array();
		foreach ($imageList as $image){
			if (! is_array($image_list_array[ $image[0] ])) $image_list_array[ $image[0] ] = array();
			array_push($image_list_array[ $image[0] ], $image[1]);
		}

		?>
	<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
		<input type="hidden" name="action" value="banwoo_import_file" />
		<?php wp_nonce_field( 'banwoo_verify' ); ?>

		<table class="wp-list-table widefat fixed posts">
			<thead>
			<tr>
				<th width="3%"></th>
				<th width="7%"><?php _e('id', 'pippinw'); ?></th>
				<th width="70%"><?php _e('title', 'pippinw'); ?></th>
				<th width="70%"><?php _e('slug', 'pippinw'); ?></th>
				<th width="20%"><?php _e('price', 'pippinw'); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
			$data_array = $res;
			if( !empty( $data_array ) ) :

				foreach( $data_array as $row ) : ?>
					<tr>
						<td width="40%"><input type="checkbox" name="beygoo[import][]; ?>]" value="<?php echo $row[0]; ?>"></td>
						<td width="10%"><?php echo $row[0]; ?>

						<input type="hidden" name="beygoo[weight][<?php echo $row[0]; ?>]"  value="<?php echo sanitize_text_field($row[5]); ?>">
						<input type="hidden" name="beygoo[desc][<?php echo $row[0]; ?>]"  value="<?php echo esc_textarea($row[6]); ?>">
						<input type="hidden" name="beygoo[id][<?php echo $row[0]; ?>]"   value="<?php echo sanitize_text_field($row[0]); ?>">
						<input type="hidden" name="beygoo[url][<?php echo $row[0]; ?>]"  value="<?php echo esc_js($row[7]); ?>">
						<?php
							// import image
						foreach ($image_list_array[ $row[0] ] as $image) { ?>
							<input type="hidden" name="beygoo[images][<?php echo $row[0]; ?>][]"  value="<?php echo sanitize_text_field($image); ?>">
						<?php }
								?>
						<input type="hidden" name="beygoo[house][<?php echo $row[0]; ?>]"  value="<?php echo $row[8]; ?>"></td>
						<td width="40%"><input type="text" name="beygoo[title][<?php echo $row[0]; ?>]"  style="width:100%" value="<?php echo sanitize_text_field($row[1]); ?>"></td>
						<td width="40%"><input type="text" name="beygoo[slug][<?php echo $row[0]; ?>]"  style="width:100%" value="<?php echo sanitize_title_with_dashes($row[1]); ?>"></td>
						<td width="10%"><input type="text" name="beygoo[price][<?php echo $row[0]; ?>]"  class="" value="<?php echo sanitize_text_field($row[4]); ?>"></td>
					</tr>
					<?php
				endforeach;
			else : ?>
				<tr>
					<td colspan="3"><?php _e('No data found', 'pippinw'); ?></td>
				</tr>
				<?php
			endif;
			?>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Import"></p>
		</form>

<?php
	}


	public function import_banwoo_into_woocommerce() {

		if ( !current_user_can( 'manage_options' ) )
		{
			wp_die( 'You are not allowed to be on this page.' );
		}
		// Check that nonce field
		check_admin_referer( 'banwoo_verify' );


		$beygoo = $_POST['beygoo'];

		// si il n'y a rien a importer on sort
		if (!isset($beygoo['import'])) return true;


		foreach ($beygoo['import'] as $product_id) {

			$images = array();
			$pos_image = 0;

			foreach ( $beygoo['images'][$product_id] as $image){

				array_push($images,array('src'=>$image,'position'=>$pos_image ));
				$pos_image++;

			}

			$data = [
				'name'              => $beygoo['title'][$product_id],
				'slug'              => $beygoo['slug'][$product_id],
				'sku'               => $beygoo['id'][$product_id],
				'type'              => 'simple',
				'regular_price'     => $beygoo['price'][$product_id],
				'description'       => $beygoo['desc'][$product_id],
				'short_description' => '',
				'categories'        => [
					[
						'id' => 9
					],
					[
						'id' => 14
					]
				],
				'images'            => $images,
				'meta_data'         => [
					[
						'key'   => 'banwoo_url',
						'value' => $beygoo['url'][$product_id]
					]
				]
			];
		echo '<pre>';
//		var_dump($data);
			try{

				print_r($this->woocommerce->post('products', $data));

			}catch(HttpClientException $e){
				echo 'le produit '.$beygoo['id'][$product_id].' existe déjà<br>';

//				print_r($e);

				print_r($e->getMessage() . PHP_EOL);

				print_r('Code: ' . $e->getResponse()->getCode() . PHP_EOL);

//				print_r('Body: ' . $e->getResponse()->getBody() . PHP_EOL);

			}

			echo '</pre>';


		}

		wp_die('end');



	}
}