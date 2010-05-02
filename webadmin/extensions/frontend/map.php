<?
parse_blk("map.htm","ITEM");

$db->query("SELECT ".$LANG."title as title,id,pid,dir FROM catalogue$FinSuf WHERE map='1' ORDER BY indx");
$tree=array();
while($db->next_record()) $tree[intval($db->Record["pid"])][intval($db->Record["id"])]=array("dir"=>$db->Record["dir"],"title"=>$db->Record["title"]);
$lvl=0;
$list="";

function ShowTree($ParentID,$dir) { 
global $tree,$ITEM; 
    $list="";
    $n=($ParentID? "":1);

    if(isset($tree[$ParentID])) {
        foreach($tree[$ParentID] as $k=>$v) {
            $v["n"]=($n? $n++:"");
            $v["dir"]=$dir.$v["dir"]."/";
            $v["sub"]=ShowTree($k,$v["dir"]); 
            $list.= sendAr2blk($v,$ITEM);
        }
    }
    return $list;
}

send2blk("ITEM",ShowTree(0,"/"));
?>