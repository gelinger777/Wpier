<?php
 /**
 * This file is part of phpMorphy library
 *
 * Copyright c 2007-2008 Kamaev Vladimir <heromantor@users.sourceforge.net>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the
 * Free Software Foundation, Inc., 59 Temple Place - Suite 330,
 * Boston, MA 02111-1307, USA.
 */

interface phpMorphy_GramInfo_Interace {
	/**
	 * Returns langugage for graminfo file
	 * @return string
	 */
	function getLanguage();
	
	/**
	 * Return codepage(encoding) for graminfo file
	 * @return string
	 */
	function getCodepage();
	
	/**
	 * Reads graminfo header
	 *
	 * @param int $offset
	 * @return array
	 */
	function readGramInfoHeader($offset);
	
	/**
	 * Read ancodes section for header retrieved with readGramInfoHeader
	 *
	 * @param array $info
	 * @return array
	 */
	function readAncodes($info);
	
	/**
	 * Read flexias section for header retrieved with readGramInfoHeader
	 *
	 * @param array $info
	 * @param bool $onlyBase when TRUE then only base(first) form flexia returned
	 * @return array
	 */
	function readFlexiaData($info, $onlyBase);
	
	/**
	 * Read all graminfo headers offsets, which can be used latter for readGramInfoHeader method
	 * @return array
	 */
	function readAllGramInfoOffsets();
}
 
abstract class phpMorphy_GramInfo implements phpMorphy_GramInfo_Interace {
	const HEADER_SIZE = 128;
	
	protected
		$resource,
		$header;
	
	protected function phpMorphy_GramInfo($resource, $header) {
		$this->resource = $resource;
		$this->header = $header;
	}
	
	static function create(phpMorphy_Storage $storage) {
		$header = phpMorphy_GramInfo::readHeader(
			$storage->read(0, self::HEADER_SIZE)
		);
		
		if(!phpMorphy_GramInfo::validateHeader($header)) {
			throw new phpMorphy_Exception('Invalid graminfo format');
		}
		
		$storage_type = phpMorphy_GramInfo::getStorageString($storage->getType());
		$file_path = dirname(__FILE__) . "/access/graminfo_{$storage_type}.php";
		$clazz = 'phpMorphy_GramInfo_' . ucfirst($storage_type);
		
		require_once($file_path);
		return new $clazz($storage->getResource(), $header);
	}
	
	function getLanguage() {
		return $this->header['lang'];
	}
	
	function getCodepage() {
		return $this->header['codepage'];
	}
	
	static protected function readHeader($headerRaw) {
		$header = unpack(
			'Vver/Vis_be/Vflex_count/Vflex_offset/Vflex_size',
			$headerRaw
		);
		
		$offset = 20;
		$len = ord(substr($headerRaw, $offset++, 1));
		$header['lang'] = rtrim(substr($headerRaw, $offset, $len));
		
		$len = ord(substr($headerRaw, $offset++, 1));
		$header['codepage'] = rtrim(substr($headerRaw, $offset, $len));
		
		return $header;
	}
	
	static protected function validateHeader($header) {
		if(
			2 != $header['ver'] &&
			0 == $header['is_be']
		) {
			return false;
		}
		
		return true;
	}
	
	static protected function getStorageString($type) {
		$types_map = array(
			PHPMORPHY_STORAGE_FILE => 'file',
			PHPMORPHY_STORAGE_MEM => 'mem',
			PHPMORPHY_STORAGE_SHM => 'shm'
		);
		
		if(!isset($types_map[$type])) {
			throw new phpMorphy_Exception('Unsupported storage type ' . $storage->getType());
		}
		
		return $types_map[$type];
	}
};

class phpMorphy_GramInfo_Decorator implements phpMorphy_GramInfo_Interace {
	protected $info;
	
	function phpMorphy_GramInfo_Decorator(phpMorphy_GramInfo_Interace $info) {
		$this->info = $info;
	}
	
	function readGramInfoHeader($offset) { return $this->info->readGramInfoHeader($offset); }
	function readAncodes($info) { return $this->info->readAncodes($info); }
	function readFlexiaData($info, $onlyBase) { return $this->info->readFlexiaData($info, $onlyBase); }
	function readAllGramInfoOffsets() { return $this->info->readAllGramInfoOffsets(); }
	
	function getLanguage()  { return $this->info->getLanguage(); }
	function getCodepage()  { return $this->info->getCodepage(); }
}

class phpMorphy_GramInfo_RuntimeCaching extends phpMorphy_GramInfo_Decorator {
	protected
		$flexia_all = array(),
		$flexia_base = array();
	
	function readFlexiaData($info, $onlyBase) {
		$offset = $info['offset'];
		
		if($onlyBase) {
			if(!isset($this->flexia_base[$offset])) {
				$this->flexia_base[$offset] = $this->info->readFlexiaData($info, $onlyBase);
			}
			
			return $this->flexia_base[$offset];
		} else {
			if(!isset($this->flexia_all[$offset])) {
				$this->flexia_all[$offset] = $this->info->readFlexiaData($info, $onlyBase);
			}
			
			return $this->flexia_all[$offset];
		}
	}
}
