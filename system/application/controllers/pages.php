<?php
//////////////////////////////////////////////////
/*

 Адаптер публичного раздела Wpier для Сodeigniter

 Автор: Макс Тушев
 Copyright: 2009

 Лицензия: LGPL

 TODO:

 3. Меню редактора в админке (названия боков)
 5. Доработать обработчик "Отказано в доступе" (добавить правильный хедер)

*/
/////////////////////////////////////////////////

// Определим каталог админки
define('_ADMIN_DIR_',dirname(__FILE__)."/../../../"."webadmin");

class Pages extends Controller {

	var $FinSuf="_fin";
	var $DATA=array();
	var $PERM=array(1,1,1,1);
	var $BLOCKS=array(array(),array());

	function Pages() {
		parent::Controller();
	}

	////////////////////////////////////////////////////
	// Основной метод генерации страницы
	////////////////////////////////////////////////////
	
	
	
	
	function index(){
		
		die('Thus Page is removed...');
	}
	
	
	function show($id=FALSE,$page=FALSE) {

		// Переключение в режим предпросмотра в админке
		if(isset($_COOKIE["_WPIER_ADMIN"]) && $_COOKIE["_WPIER_ADMIN"]) {
			session_start();
			if(isset($_SESSION["adminlogin"])) $this->FinSuf="";
			else setcookie("_WPIER_ADMIN","");
		}

		$this->DATA["C"]=&$this;

		parse_str($_SERVER['QUERY_STRING'], $_GET);

		$this->DATA["HTML_FILE"]="";

		// Вычислим Id страницы
		$this->DATA["PageId"]=$this->get_page_id($_SERVER["REQUEST_URI"]);

		// Определим права доступа
		$this->access_check($this->DATA["PageId"]);

		// Прочитаем основную страницу
		$this->read_page($this->DATA["PageId"]);

	}

	////////////////////////////////////////////////////
	// Строим меню
	////////////////////////////////////////////////////
	public function build_menus($n) {
		return $this->db->query("SELECT mainmenu.item as title, catalogue".$this->FinSuf.".dir FROM mainmenu, catalogue".$this->FinSuf." WHERE mainmenu.m$n='1' and mainmenu.page=catalogue".$this->FinSuf.".id ORDER BY mainmenu.id")->result();
	}

	////////////////////////////////////////////////////
	// Строим ссылки на дочки
	////////////////////////////////////////////////////
	public function build_links() {
		$q=$this->db->query("SELECT dir,title FROM catalogue".$this->FinSuf." WHERE pid=".$this->DATA["id"]." and hiddenlink is NULL ORDER BY indx");
		$a=array();
		if($q->num_rows()) {
			foreach($q->result() as $r) {
				$r->dir="./".$r->dir."/";
				$a[]=$r;
			}
		} else {
			$q=$this->db->query("SELECT dir,title FROM catalogue".$this->FinSuf." WHERE pid=".$this->DATA["pid"]." and hiddenlink is NULL ORDER BY indx");
			foreach($q->result() as $r) {
				if($r->dir==$this->DATA["dir"]) $r->sel=1;
				else $r->sel=0;
				$r->dir="../".$r->dir."/";
				$a[]=$r;
			}
		}
		return $a;
	}

	////////////////////////////////////////////////////
	// Вычислим ID страницы из URI
	////////////////////////////////////////////////////
	private function get_page_id($uri) {

		// Уберем get-строку
		$uri=explode("?",$uri);
		$uri=$uri[0];

		$uri_arr=explode("/",$uri);
		array_shift($uri_arr);

		// Если указан физический путь, отпилим лишнее (/index.php/pages/)
		if($uri_arr[0]=="index.php") {
			array_shift($uri_arr);
			array_shift($uri_arr);
		}

		// Найдем последний элемент в адресе
		$l=count($uri_arr)-1;
		// Если в uri 1 элемент и он не пуст, значит обрабатываем адрес типа /1.html (доступ к странице через код)
		if($l==0 && $uri_arr[0]) {
			$x=intval($uri_arr[0]);
			if($uri_arr[0]==$x.".html") {
				if(isset($_GET["prev"])) {
					setcookie("_WPIER_ADMIN",1);
				}
				$this->redirect2page(intval($uri_arr[0]));
			}
		} elseif($uri_arr[$l]) {
		// Если последний элемент не пустой, значит там ссылка на файл (адрес вида /news/12.html)
			$this->DATA["HTML_FILE"]=$uri_arr[$l];
			unset($uri_arr[$l]);
		}
		$this->DATA["RootDir"]=$uri_arr[0];

		// Если ничего не осталось, ищем первую в списке страницу с pid=0
		if(!$uri_arr[0]) {
			$this->db->select('id,dir,title');
			$this->db->order_by("indx", "asc");
			$q = $this->db->get_where('catalogue'.$this->FinSuf, array('pid' => 0), 1, 0);
			foreach ($q->result() as $row) {
			   $this->DATA["RootTitle"]=$row->title;
			   $this->DATA["RootId"]=$row->id;
			   return $row->id;
			}
		}

		// Ищем ID по всему урлу
		$recs=array();
		$q = $this->db->query("SELECT dir, title, id, pid, hiddenlink FROM catalogue".$this->FinSuf."  WHERE dir in ('".join("','",$uri_arr)."') ORDER BY pid");
		foreach ($q->result() as $r) {
			if($this->DATA["RootDir"]==$r->dir && !$r->pid) {
				$this->DATA["RootTitle"]=$r->title;
				$this->DATA["RootId"]=$r->id;
			}
			$recs[]=$r;
		}
		$id=0;
		$this->DATA["BREADCRUMBS"]=array();
		$dir="/";
		foreach($uri_arr as $v) if($v) {
			$log=0;
			foreach($recs as $r) {
				if($r->pid==$id && $r->dir==$v) {
					$this->DATA["BREADCRUMBS"][$dir.$v."/"]=$r->title;
					$id=$r->id;
					$log=1;
					break;
				}
			}
			if(!$log) {
				// Такого пути нет, ошибка 404
				$this->err404();
			}
		}
		return $id;
	}

	////////////////////////////////////////////////////
	// Проверка прав доступа
	////////////////////////////////////////////////////
	private function access_check($id) {


		// Проверяем права доступа на эту страницу
		$logExit=0;
		$q = $this->db->get_where('accesspgpubl', array('pg' => $id));
		foreach ($q->result() as $row) {
			if(isset($_SESSION["usr_group"]) && $r->grp==$_SESSION["usr_group"]) {
				$logExit=0;
				break;
			}
			$logExit=1;
		}
		if($logExit) {
			if(isset($_SESSION["adminlogin"])) {
				if($_SESSION["adminlogin"]!="root") {
					if(isset($_CONFIG["ACCESS_EMPTY_MODE"]) && $_CONFIG["ACCESS_EMPTY_MODE"]) {
						$this->PERM=array(0,0,0);
						$q=$this->db->query("SELECT  rd, ad, ed  FROM accesspgadmins WHERE pg='$id' and grp='".$_SESSION['admingroup']."'");
						if(count($q)) {
							$this->PERM=array($q[0]->rd,$q[0]->ad,$q[0]->ed);
						} else {
							$this->PERM=array(1,1,1);
							$q=$this->db->query("SELECT  rd, ad, ed, grp  FROM accesspgadmins WHERE pg='$id'");
							if(count($q)) $this->PERM=array(0,0,0);
							foreach($q as $r) {
								if($_SESSION['admingroup']==$r->grp) {
									$this->PERM=array($r->rd,$r->ad,$r->ed);
									break;
								}
							}
						}
						if(!$this->PERM[0]) {
							return $this->access_denied();
						}
					}
				} else $this->PERM=array(1,1,1,1);
			} else {
				return $this->access_denied();
			}
		}
		return 1;
	}

	////////////////////////////////////////////////////
	// Читаем основные данные страницы
	////////////////////////////////////////////////////
	private function read_page($id,$header="HTTP/1.1 200 OK") {
		$FinSuf=$this->FinSuf;

		$q=$this->db->query("SELECT
			catalogue$FinSuf.title,
			catalogue$FinSuf.dir,
			catalogue$FinSuf.pid,
			catalogue$FinSuf.id,
			catalogue$FinSuf.wintitle,
			catalogue$FinSuf.windescript,
			catalogue$FinSuf.winkeywords,
			catalogue$FinSuf.mkhtml,
			catalogue$FinSuf.attr,
			templates.tmpfile,
			catalogue$FinSuf.hiddenlink,
			catalogue$FinSuf.gotopage
			FROM catalogue$FinSuf LEFT JOIN templates ON templates.id=catalogue$FinSuf.tpl
			WHERE catalogue$FinSuf.id=$id");

		if(!count($q)) $this->err404();
		foreach ($q->result_array() as $r) {
			if($r["gotopage"]) {
				$q=$this->db->query("SELECT dir FROM catalogue$FinSuf WHERE id='".$r["gotopage"]."' and pid='$id'");
				if(count($q)) {
					$this->redirect("./".$q[0]->dir."/");
				}
			}

			$r["tmpfile"]=substr($r["tmpfile"],strrpos($r["tmpfile"],"/")+1);
			$r["tmpfile"]=substr($r["tmpfile"],0,strrpos($r["tmpfile"],"."));

			$this->DATA=array_merge($this->DATA,$r);

			// Выводим глобальные переменные (копират, теле фоны и т.п.) из модуля "Служебные надписи"
			$q=$this->db->query("SELECT * FROM labels");
			foreach($q->result() as $r) $this->DATA[$r->keyname]=$r->valtext;

			// Читаем блоки
			$this->read_bloks($id);

			// Отправим заголовок
			header($header);

			// Показываем шаблон
			$this->load->view($this->DATA["tmpfile"],$this->DATA);

			if(!$this->FinSuf) $this->echo_admin_log();
		}
	}

	// Покажем лог для админки
	private function echo_admin_log() {
		echo str_replace("\n"," ",str_replace("\r","","<script>try {parent.ChangeCurrentTitle(window,'".$this->DATA["title"]."',[".join(",",$this->BLOCKS[0])."],[".join(",",$this->BLOCKS[1])."],'',".$this->DATA["id"].(count($this->PERM)? ",":"").join(",",$this->PERM).",'".$this->DATA["attr"]."');}catch(e){}</script>"));
	}

	////////////////////////////////////////////////////
	// Обработка блоков
	////////////////////////////////////////////////////
	private function read_bloks($id) {

		$q=$this->db->query("SELECT id,title, text, spec,cpid, access_, nohtml, cmpw, catalogue_id, globalblock, ins2text, nocash FROM content".$this->FinSuf." WHERE catalogue_id='$id' or globalblock!=0 ORDER BY cpid,id");
		$blocks=array();
		$global="";
		foreach ($q->result() as $r) {
			if($r->globalblock && $r->catalogue_id!=$id) {
				$global=$r;
			} else {
				$i=($r->cpid? $r->cpid:$r->id);
				if(!isset($blocks[$i])) $blocks[$i]=array();
				$blocks[$i][]=$r;
			}
		}
		$i=1;
		foreach($blocks as $k=>$v) {
			if(!is_string($global)) $this->DATA["BLOCK$i"]=$this->read_block($global);
			elseif(!isset($this->DATA["BLOCK$i"])) $this->DATA["BLOCK$i"]="";
			foreach($v as $r) {
				$this->DATA["BLOCK$i"].=$this->make_block($r);
			}
			$i++;
		}
	}

	////////////////////////////////////////////////////
	// Генерируем контент из блоков
	////////////////////////////////////////////////////
	private function make_block($r) {
		if($r->spec) {
			$this->BLOCKS[1][]="['".$r->spec."','".$r->spec."',true,'']";
			// Блок модуля
			$f=dirname(__FILE__)."/extensions/".$r->spec.".php";
			if(file_exists($f)) {
				ob_start();
				include $f;
				$s=ob_get_contents();
				ob_end_clean();
				return $s;
			} else {
				if(defined("_ADMIN_DIR_") && file_exists(_ADMIN_DIR_."/extensions/".$r->spec.".php")) {
					return $this->standard_block($r,_ADMIN_DIR_."/extensions/".$r->spec.".php");
				}
				return "Error: module file ".dirname(__FILE__)."/extensions/".$r->spec.".php not found.";
			}
		} else {
			// Текстовый блок
			$this->BLOCKS[0][]="['".$r->title."',".$r->id."]";
			return $r->text;
		}
	}

	////////////////////////////////////////////////////
	// Обработка модулей без публичного кода
	////////////////////////////////////////////////////
	private function standard_block($r,$file) {
		$s=file_get_contents($file);
		$s=substr($s,strpos($s,"//HEAD//")+8);
		$s=substr($s,0,strpos($s,"//ENDHEAD//"));
		ob_start();

		eval($s.$r->cmpw);

		if($this->DATA["HTML_FILE"]) {
			$x=intval($this->DATA["HTML_FILE"]);

		    // Проверим корректность ссылки
		    if($this->DATA["HTML_FILE"]!=$x.".html") $this->err404();

		    // Читаем и выводим запись
		    $r=current($this->db->query("SELECT * FROM ".$PROPERTIES["tbname"]." WHERE ".(isset($PROPERTIES["FIX_ID_TO_COD"])? $PROPERTIES["FIX_ID_TO_COD"]:"id")."=".$x)->result());
		    if($r) $this->load->view("spec/".$PROPERTIES["template_row"],$r);
		    else $this->err404();

		} else {

			$this->load->view("spec/".$PROPERTIES["template_list"],array("LIST"=>$this->db->query("SELECT * FROM ".$PROPERTIES["tbname"]." ".(isset($PROPERTIES["usrwhere"])? $PROPERTIES["usrwhere"]:"")." ORDER BY ".(isset($PROPERTIES["usrorderby"])? $PROPERTIES["usrorderby"]:"id")." LIMIT ".(isset($PROPERTIES["usrorderby"])? $PROPERTIES["step"]:"20"))->result()));

		}

		$s=ob_get_contents();
		ob_end_clean();

		return $s;
	}

	////////////////////////////////////////////////////
	// Редирект
	////////////////////////////////////////////////////
	private function redirect($url) {
		header("Location $url",TRUE,302);
		exit;
	}

	////////////////////////////////////////////////////
	// Обработчик ошибки 404
	////////////////////////////////////////////////////
	private function err404() {
		$q=$this->db->query("SELECT id FROM catalogue".$this->FinSuf." WHERE dir='err404' and pid='0'");
		$r=current($q->result());
		if($r) {
			$this->read_page($r->id,"HTTP/1.0 404 Not Found");
		} else {
			header("HTTP/1.0 404 Not Found");
			$this->load->view("errors/404",array(
				"uri"=>$_SERVER["REQUEST_URI"]
			));
		}
		exit;
	}

	////////////////////////////////////////////////////
	// Редирект на страницу по коду
	////////////////////////////////////////////////////
	private function redirect2page($id) {
		$q=$this->db->query("SELECT pid,dir FROM catalogue".$this->FinSuf." WHERE id=$id");
		$uri="/";
		$r=current($q->result());
		while($r) {
			$uri="/".$r->dir.$uri;
			if(!$r->pid) break;
			$q=$this->db->query("SELECT pid,dir FROM catalogue".$this->FinSuf." WHERE id=".$r->pid);
			$r=current($q->result());
		}

		header("Location: $uri",TRUE,302); // Редиректим с правильным заголовком
		exit;
	}

	////////////////////////////////////////////////////
	// Обработчик отказа в  доступе
	////////////////////////////////////////////////////
	private function access_denied() {
		echo str_replace("\n"," ",str_replace("\r","","<script>try {parent.ChangeCurrentTitle(window,'Нет доступа!',[],[],'',0);}catch(e){}</script>"));
		exit;
	}


}

/* End of file pages.php */
/* Location: ./system/application/controllers/pages.php */