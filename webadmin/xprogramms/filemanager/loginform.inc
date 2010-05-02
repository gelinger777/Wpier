<?
$db->query("SELECT attr FROM graphset WHERE id=1");
if($db->next_record() && $db->Record[0]) {
?>
<div style="margin-top:40px">
<!-- Diagramm --

<span style="font-weight: bold;">CURRENT GDR PRICE</span><br>
<a href="/34.html"><img src="/graph_small.php" border="0" alt="Investor tools" /></a>
!-- / Diagramm<br><br> -->
        Use link below to subscribe on <br/>IBS Group site news alerts<br/><br/>
        <a href="%subscrurl%" title="Subscribe" style="discl">%subscrlinktext%</a>
</div>
<?}?>