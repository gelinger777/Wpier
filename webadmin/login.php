<?
require "inc/function.php";
include "location/".$_CONFIG["ADMIN_LOCATION"]."/prog.inc";
$RUN=0;

if(isset($MAINFRAME) && isset($_GET["logindefault"])) {
	include "editorhtml/logindefault.php";
}

// Если в конфиге разрешен гостевой вход, ищем юзера с правами которого пускаются гости
if(isset($_CONFIG["GUEST_ENTER"]) && $_CONFIG["GUEST_ENTER"] && isset($_GET["guest"])){
   $db->query("SELECT id, adminlogin,adminname FROM settings WHERE adminlogin='".AddSlashes($_GET["guest"])."' and admingroup='".intval($_CONFIG["GUEST_ENTER"])."'");
   if($db->next_record()) {
	  $_SESSION['adminlogin']=$db->Record["adminlogin"];
	  $_SESSION['adminname']=$db->Record["adminlogin"];
	  $_SESSION['AdminID']=$db->Record["id"];
	  $_SESSION['AdminGuest']=1;
	  $_SESSION["LoginStartTime"]=mktime();
	  header("Location: ./");
      exit;
   }
}

function OK() {
  global $db;
  $db->query("INSERT INTO onlineusers (usr,tm) VALUES ('".$_SESSION['adminlogin']."','".mktime()."')");
  $db->query("UPDATE settings SET AdminUID='".session_id()."' WHERE AdminLogin='".$_SESSION['adminlogin']."'");
  header("Location: ./");
  exit;
}

//brudforce
$cnt=0;
$tm=0;

$LogOwerLogin=0;
$LogErrorLogin=0;
if (isSet($_POST["login"]) and isSet($_POST["passwd"])) {

  $db->query("SELECT tm,cnt FROM brudforce WHERE ip='".$_SERVER["REMOTE_ADDR"]."'");
  if($db->next_record()) {
    if(($db->Record["tm"]+60)<mktime()) {
    // если последняя попытка с этого ip была более минуты назад, сбрасываем адрес
       $db->query("DELETE FROM brudforce WHERE ip='".$_SERVER["REMOTE_ADDR"]."'");
    } else {
       $cnt=$db->Record["cnt"]+1;
       $tm=$db->Record["tm"];
       $db->query("UPDATE brudforce SET cnt=cnt+1, tm='".mktime()."' WHERE ip='".$_SERVER["REMOTE_ADDR"]."'");
    }
  }

  if(($tm+$cnt)<mktime()) { // проверим по брудфорсу

	$db->query("SELECT id, adminlogin,adminuid,adminname FROM settings WHERE adminlogin='".AddSlashes($_POST["login"])."' and adminpassword='".wpier_hash($_POST["passwd"])."'");
	if ($db->next_record()) {
	  $_SESSION['adminlogin']=$db->Record["adminlogin"];
	      $_SESSION['adminname']=$db->Record["adminlogin"];
	      $_SESSION['AdminID']=$db->Record["id"];
	      $_SESSION['AdminGuest']=0;

	      $_SESSION['AdminLocation']=$_POST["location"];

	      $_SESSION["LoginStartTime"]=mktime();
	  if($db->Record["adminuid"]!= session_id()) {
	    $db->query("SELECT usr FROM onlineusers WHERE usr='".$_SESSION['adminlogin']."' and tm>".(mktime()-30));
	    if($db->next_record()) {
	      $LogOwerLogin=1;
	      $_SESSION=array();;
	    } else {
	      OK();
	    }
	  } else {
	    OK();
	  }
	} else {
		if(!$cnt) {
			$cnt=1;
			// в случае неудачного логина добавим запись в брудфорс
			$db->query("INSERT INTO brudforce (ip,tm,cnt) VALUES ('".$_SERVER["REMOTE_ADDR"]."','".mktime()."',1)");
		}
		$LogErrorLogin=1;
	}
  }


}
if(isset($_POST["mode"]) && $_POST["mode"]==1) exit;

$log=1;
$i=strpos($_SERVER["HTTP_USER_AGENT"],"MSIE");
if($i) {
	$i+=4;
	$v="";
	while($i<strlen($_SERVER["HTTP_USER_AGENT"]) && $_SERVER["HTTP_USER_AGENT"][$i]!=";") {
		$v.=$_SERVER["HTTP_USER_AGENT"][$i++];
	}
	$v=intval(trim($v));
	if($v<7) {
		$log=0;
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
  <title>Wpier - Admin console</title>
  <META HTTP-EQUIV="Expires" CONTENT="Mon, 06 Jan 1990 00:00:01 GMT">
	<link href="./img/main.css" rel="stylesheet" type="text/css">  
<link rel="shortcut icon" href="./favicon.ico" />
  <SCRIPT LANGUAGE="JavaScript">
   <!--
	 if (self.parent.frames.length != 0)
       self.parent.location="login.php"

   // -->
   </SCRIPT>
   <style>
   td {
     font-family:Arial;
     font-size:12px;
   }
   #copy {
     font-family:Arial;
     font-size:11px;
     color:#7a7a7a;
     padding-right:20px;
   }
   #central {
     background:#d4e1f2;
     border-top:3px solid #99bbe8;
     border-bottom:3px solid #99bbe8;
   }
   </style>
   <?if($LogOwerLogin){?>
   <SCRIPT LANGUAGE="JavaScript">
   alert('<?=DLG("User %s% is working now",array("s"=>$_POST["login"]))?>');
   </SCRIPT>
   <?}?>

</head>

<body style="margin:0;padding:0;" <?if($log){?>onload="window.setTimeout(function(){document.getElementById('sendb').disabled=false;},<?=($cnt? $cnt."000":"1")?>)"<?}?>>

<table border=0 height="100%" width="100%"><tr><td>

<table border=0 height="150" width="100%" id="central"><tr><td align="center">

  <?
  if(!$log) {?>
  <p><?=DLG('The system requires browsers FF3+,GH, Safari, Opera9.6, IE7+')?></p>
  <?}else{?>

  <table><form method="post">

  <?if($LogErrorLogin) {?><tr><td colspan=2 style="color:red"><?=DLG("Login error")?></td></tr><?}?>

  <tr>
   <td ><?=DLG('Login')?>:</td>
   <td ><input type="text" size="16" value="" name="login"></td>
  </tr>

  <tr>
   <td ><?=DLG('Password')?>:</td>
   <td ><input type="password" size="16" value="" name="passwd">
   <?if(isset($_CONFIG["GUEST_ENTER"]) && $_CONFIG["GUEST_ENTER"]){?><a href="?guest=guest"><?=DLG("Guest enter")?></a><?}?>
   </td>
  </tr>

  <tr>
   <td ><?=DLG('Location')?>:</td>
   <td ><select name="location">
   <?
   $d = dir("location");
   while (false !== ($entry = $d->read())) if(($entry[0]!=".")) {
   	echo "<option value='$entry'".($entry==$_CONFIG["ADMIN_LOCATION"]? " selected":"").">$entry</option>";
   }
   ?>
   </select></td>
  </tr>

  <tr>
   <td></td>
   <td>
   <input type="submit" id="sendb" name="Login Now" value="  <?=DLG("Enter")?>  " class="button" border="1" disabled="true">
   <br>
  </td>
  </tr> </form>
  </table>
 <?}?>
</td></tr></table>
<div id="copy" align="right">&copy;2006-<?=date("Y")?> <a href="mailto: maximtushev@gmail.com">MT</a></div>



</td></tr></table>

</body>
</html>

