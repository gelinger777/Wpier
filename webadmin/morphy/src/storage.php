<?php
define('PHPMORPHY_STORAGE_FILE',	'file');
define('PHPMORPHY_STORAGE_MEM',		'mem');
define('PHPMORPHY_STORAGE_SHM',		'shm');

abstract class phpMorphy_Storage {
	protected
		$file_name,
		$resource;
	
	// private ctor
	protected function phpMorphy_Storage($fileName) {
		$this->file_name = $fileName;
		$this->resource = $this->open($fileName);
	}
	
	// static
	static function create($type, $fileName) {
		// TODO: This ugly refactor latter
		switch($type) {
			case PHPMORPHY_STORAGE_FILE: 
			case PHPMORPHY_STORAGE_MEM:
			case PHPMORPHY_STORAGE_SHM: break;
			default:
				throw new phpMorphy_Exception("Invalid storage type $type specified");
		}
		
		$clazz = 'phpMorphy_Storage_' . ucfirst(strtolower($type));
		
		return new $clazz($fileName);
	}
	
	function getFileName() { return $this->file_name; }
	function getResource() { return $this->resource; }
	
	abstract function getFileSize();
	abstract function getType();
	abstract function read($offset, $len);
	abstract protected function open($fileName);
};

class phpMorphy_Storage_File extends phpMorphy_Storage {
	function getType() { return PHPMORPHY_STORAGE_FILE; }
	
	function getFileSize() {
		if(false === ($stat = fstat($this->resource))) {
			throw new phpMorphy_Exception('Can`t invoke fstat for ' . $this->file_name . ' file');
		}
		
		return $stat['size'];
	}
	
	function read($offset, $len) {
		fseek($this->resource, $offset);
		return fread($this->resource, $len);
	}
	
	function open($fileName) {
		if(false === ($fh = fopen($fileName, 'rb'))) {
			throw new phpMorphy_Exception("Can`t open $this->file_name file");
		}
		
		return $fh;
	}
}

class phpMorphy_Storage_Mem extends phpMorphy_Storage {
	function getType() { return PHPMORPHY_STORAGE_MEM; }
	
	function getFileSize() {
		return strlen($this->resource);
	}
	
	function read($offset, $len) {
		return substr($this->resource, $offset, $len);
	}
	
	function open($fileName) {
		if(false === ($string = file_get_contents($fileName))) {
			throw new phpMorphy_Exception("Can`t read $fileName file");
		}
		
		return $string;
	}
}

class phpMorphy_Storage_Shm extends phpMorphy_Storage {
	protected
		$manager,
		$file_size;
	
	function getFileSize() {
		return $this->file_size;
	}
	
	function getType() { return PHPMORPHY_STORAGE_SHM; }
	
	function read($offset, $len) {
		return shmop_read($this->resource, $offset, $len);
	}
	
	function open($fileName) {
		$this->manager = $this->createManager($fileName);
		
		$result = $this->manager->get();
		
		$this->file_size = $result->getFileSize();
		
		return $result->getShmId();
	}
	
	protected function createManager($file) {
		require_once(PHPMORPHY_DIR . '/shm_utils.php');
		return new ShmFileManager($file, 'f');
	}
}
