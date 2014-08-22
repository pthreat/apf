<?php 

	$prevPath	=	get_include_path();

	set_include_path(__DIR__);

	require	"Adapter.class.php";
	require	"Table.class.php";
	require	"Query.class.php";
	require	"Select.class.php";
	require	"select/Result.class.php";
	require	"select/Row.class.php";
	require	"Update.class.php";
	require	"Insert.class.php";
	require	"Delete.class.php";
	require	"Replace.class.php";
	
	set_include_path($prevPath);

?>
