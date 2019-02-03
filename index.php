<?

ini_set('date.timezone', 'Europe/Warsaw');
error_reporting(E_ALL);
define('EC_DEBUG', 1);

include 'encoo.database.php';
include 'encoo.searchengine.php';

$cogs = array('tables' => array('ec_users'), 'fields' => array(array('id', 'name')));

$SE = new EnCooSE(json_encode($cogs));

if(isset($_GET['query']) && strlen($_GET['query'])) {

	$arr = $SE->sortArray( $SE->search($_GET['query']), 'id' );

	echo "<pre>";
	print_r(json_encode($arr));

	echo "</pre>";
}