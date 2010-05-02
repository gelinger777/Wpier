<?
include_once "./autorisation.php";

if(isset($_GET["fn"]) && $_GET["fn"]) {
	include $_GET["fn"];
} 

require ("./output_interface.php");
?>