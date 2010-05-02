<?
if(isset($_GET["f"]) && $_GET["f"]) {
  include "../autorisation.php";
  if(file_exists($_SERVER["IPR_DIR"].$_GET["f"])) echo 1;
}
?>