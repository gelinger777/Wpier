<?
if(isset($_SESSION) && count($_SESSION)) setcookie("cook_Session", serialize($_SESSION),0, "/");
?>