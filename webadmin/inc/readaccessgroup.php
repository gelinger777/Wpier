<?
if($AdminLogin=="root") {
// У рута должны быть все права
	unset($ADMINGROUP);
} else {
	$db->query("SELECT GrpAccess FROM usergroups WHERE id='$ADMINGROUP'");
	if($db->next_record()) $ADMINGROUP=unserialize($db->Record[0]);
	$ACCESS_DETAILS=array();
	if(isset($ADMINGROUP) && is_array($ADMINGROUP)) foreach($ADMINGROUP as $key=>$val) {
		foreach($val as $k=>$v) $ACCESS_DETAILS[$v]="y";
	}
	$ACCESS=0;

	//print_r($ACCESS_DETAILS);exit;
}
?>