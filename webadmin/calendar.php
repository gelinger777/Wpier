<?
$MONTS=array("","январь","февраль","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Календарь</TITLE>
<META NAME="Author" CONTENT="MaximTushev">
<style>
body, p, li, ul, td {font-size : 11px; font-family :   Arial;}
.clcbt{border:1 solid #4F4F4F;color:#acacac;font-size:10px;background:#E8E8E8;width:16px;height:16px;text-align:center;}
.clcwe{border:1 solid #9B1455;color:#9B1455;font-size:10px;background:#E8E8E8;width:16px;height:16px;text-align:center;}
.dn {font-weight:bold}
.we {font-weight:bold;color:#9B1455}
</style>

<SCRIPT LANGUAGE="JavaScript">
<!--
function gval(s) {
   var o=opener.document.getElementsByName('<?=$_GET["f"]?>');
   o.item(0).value=s;
   window.close();
}
//-->
</SCRIPT>

</HEAD>

<body id="bbody" onload="focus()" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" style="border:;"><div id="calcDIV" style="width:100%;height:100%;border:2 solid #9B1455;background:#ffffff;padding:10px;padding-top:5px;padding-bottom:5px;">
<table width="100%" height="100%" cellspacing=0 cellpadding=2 border=0>
<?
$cm=0;
$curdt=mktime();
if(isset($_GET["cm"])) $cm=intval($_GET["cm"]);

if($cm) 
  $tm=mktime(0,0,0,date("m")+$cm,1,date("Y"));
else 
  $tm=mktime();
$crm=intval(date("m",$tm));
$m=$MONTS[$crm];
$Y=date("y",$tm);

$start=mktime(1,0,0,date("m",$tm),1,date("Y",$tm));
$wstart=date("w",$start);
if(!$wstart) $wstart=7;

$lpd=0;
if(isset($_GET["ld"]) && $_GET["ld"]) 
	$lpd=1;

$frm="d.m.Y";
if(isset($_GET["format"])) $frm=$_GET["format"];
?>
<tr>

<td><?if($cm>0 || $lpd) echo "<a href='?f=".$_GET["f"]."&ld=".$lpd."&cm=".($cm-1)."'>&lt;&lt;</a>";?>&nbsp;</td>

<td colspan="5" align="center"><b><?=$m." '".$Y?></b></td>

<td>&nbsp;<a href='?f=<?=$_GET["f"]?>&ld="<?=$lpd?>"&cm=<?=($cm+1)?>'>&gt;&gt;</a></td></tr>

<tr>
<td class="dn">пн</td>
<td class="dn">вт</td>
<td class="dn">ср</td>
<td class="dn">чт</td>
<td class="dn">пт</td>
<td class="we">сб</td>
<td class="we">вс</td>
</tr>

<tr>
<?
for($i=1;$i<$wstart;$i++) echo '<td width="14%">&nbsp;</td>';

while($crm==intval(date("m",$start))) {
	if(date("w",$start)==1) echo "<tr>";
	$wc=date("w",$start);
	if(!$wc) $wc=7;
	echo '<td width="14%" align="left" valign="top"><div class="'.($wc<6? "clcbt":"clcwe").'" '.(($start<$curdt && !$lpd)? "":'onclick="gval(\''.date($frm,$start).'\')" style="cursor:hand;color:#000000"').'>'.intval(date("d",$start)).'</div></td>';
	if($wc==7) echo "</tr>";
	$start+=24*3600+1;
}
$i=date("w",$start);
if(!$i) $i=7;
if($i>1) {for($i;$i<=7;$i++) echo '<td width="14%">&nbsp;</td>';echo '</tr>';}
?>

</table>
</div>
</BODY>
</HTML>
