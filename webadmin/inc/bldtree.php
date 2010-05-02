<?
$dirTree=array();
				$secTree=array();
				$db->query("SELECT id, pid, ".$LANG."title as title, dir,menu, cod, hiddenLink, hideleft  FROM catalogue$FinSuf  ORDER BY id");
				while($db->next_record()) {
					$secTree[$db->Record["id"]]=$db->Record["pid"];
					if(!isset($dirTree[$db->Record["pid"]])) $dirTree[$db->Record["pid"]]=array();
					$dirTree[$db->Record["pid"]][$db->Record["id"]]=array(
					$db->Record["title"],
					$db->Record["dir"],
					$db->Record["menu"],
					$db->Record["cod"],
					$db->Record["hideleft"]					
					);
				}
				
				$i=$CurrentId;
				$listDir=array();
				
				while(isset($secTree[$i])) {
					$listDir[]=$i;
					$i=$secTree[$i];
				}
				$list=array();
				for($i=0;$i<3;$i++) {
					if(isset($listDir[count($listDir)-1-$i])) $list[]=$listDir[count($listDir)-1-$i];
				}
				$listDir=$list;

				$list="";$list1="";				
				foreach($dirTree[0] as $k=>$v) if($v[4]!="1") {
					// Верхнее меню ------------------------
					if($v[2]) {
						$s=str_replace("%title%",$v[0], $MENU);
						$list.=str_replace("%dir%",$v[1], $s);
						$s=str_replace("%title%",$v[0], $MENUB);
						$list1.=str_replace("%dir%",$v[1], $s);
					}
	
					if(isset($listDir[0]) && $listDir[0]==$k && isset($dirTree[$k])) {
						$l1="";$l2="";make_out("headItem",$v[0]);
						foreach($dirTree[$k] as $kk=>$vv) if($vv[4]!="1") {
							if(isset($listDir[1]) && $listDir[1]==$kk && isset($dirTree[$kk])) {
								$l2="";$l3="";
								make_out("level1Item",$vv[0]);
								make_out("level1cod",$vv[3]);
								foreach($dirTree[$kk] as $kkk=>$vvv) if($vvv[4]!="1") {
									if(isset($listDir[2]) && $listDir[2]==$kkk && isset($dirTree[$kkk])) {
										$l3="";
										make_out("level2Item",$vvv[0]);
										make_out("level2cod",$vvv[3]);
										foreach($dirTree[$kkk] as $kkkk=>$vvvv) if($vvvv[4]!=1) {
											$s=str_replace("%title%",$vvvv[0], $LMENU3);
											$s=str_replace("%dcod%",$vvvv[3], $s);
											$l3.=str_replace("%dir%","/".$v[1]."/".$vv[1]."/".$vvv[1]."/".$vvvv[1], $s);
										}
									}
									$s=str_replace("%title%",$vvv[0], $LMENU2);
									$s=str_replace("%dcod%",$vvv[3], $s);
									$s=str_replace("%dir%","/".$v[1]."/".$vv[1]."/".$vvv[1]."/", $s);
									$l2.=str_replace("%LMENU3%",$l3, $s);
									$l3="";
								}
							}
							if($vv[1]!="games") {
								$s=str_replace("%title%",$vv[0], $LMENU1);
								$s=str_replace("%dcod%",$vv[3], $s);
								$s=str_replace("%dir%","/".$v[1]."/".$vv[1]."/", $s);
								$l1.=str_replace("%LMENU2%",$l2, $s);
								$l2="";
							}
						}
					}					
				}
?>