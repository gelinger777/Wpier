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

if(!defined('PHPMORPHY_DIR')) {
	define('PHPMORPHY_DIR', dirname(__FILE__));
}

require_once(PHPMORPHY_DIR . '/fsa/fsa.php');
require_once(PHPMORPHY_DIR . '/graminfo/graminfo.php');
require_once(PHPMORPHY_DIR . '/morphiers.php');
require_once(PHPMORPHY_DIR . '/storage.php');

class phpMorphy_Exception extends Exception { }

interface phpMorphy_FilesBundle_Interface {
	/**
	 * Returns common automat file name
	 * @return string
	 */
	function getCommonAutomatFile();
	
	/**
	 * Returns predict automat file name
	 * @return string
	 */
	function getPredictAutomatFile();
	
	/**
	 * Returns gram info file name
	 * @return string
	 */
	function getGramInfoFile();
	
	/**
	 * Returns gramtab file name
	 * @return string
	 */
	function getGramTabFile();
}

class phpMorphy_FilesBundle implements phpMorphy_FilesBundle_Interface {
	protected
		$dir,
		$lang;

	function phpMorphy_FilesBundle($dirName, $lang) {
		$this->dir = $dirName;
		$this->lang = $lang;
	}

	function getCommonAutomatFile() {
		return $this->genFileName('%s/common_aut.%s.bin');
	}

	function getPredictAutomatFile() {
		return $this->genFileName('%s/predict_aut.%s.bin');
	}

	function getGramInfoFile() {
		return $this->genFileName('%s/morph_data.%s.bin');
	}
	
	function getGramTabFile() {
		return $this->genFileName('%s/gramtab.%s.bin');
	}

	protected function genFileName($fmt) {
		return sprintf($fmt, $this->dir, strtolower($this->lang));		
	}
};

class phpMorphy {
	protected
		$options,
		$common_fsa,
		$graminfo,
		$gramtab,
		$single_morphier,
		$bulk_morphier,
		$predict_morphier;
	
	function phpMorphy(phpMorphy_FilesBundle_Interface $filesBundle, array $options = null) {
		$options = $this->repairOptions($options);
		$this->options = $options;
		$this->bundle = $filesBundle;
		
		$common_fsa = $this->createFsa(
			$this->createStorage(
				$options['storage'],
				$filesBundle->getCommonAutomatFile()
			)
		);
		
		$this->common_fsa = $common_fsa;
		
		$graminfo = $this->createGramInfo(
			$this->createStorage(
				$options['storage'],
				$filesBundle->getGramInfoFile()
			)
		);
		
		$this->graminfo = $graminfo;
		
		$extra_morphiers = array();
		
		if($options['predict_by_suffix']) {
			$extra_morphiers[] = $this->createPredictBySuffixMorphier(
				$common_fsa,
				$graminfo
			);
		}
		
		if($options['predict_by_db']) {
			$extra_morphiers[] = $this->createPredictByDatabaseMorphier(
				$this->createFsa(
					$this->createStorage(
						$options['storage'],
						$filesBundle->getPredictAutomatFile()
					)
				),
				$graminfo
			);
		}
		
		$predict_morphier = null;
		$single_morphier = null;
		$standalone_morphier = $this->createSingleMorphier($common_fsa, $graminfo);
		
		if(($count = count($extra_morphiers))) {
			if($count > 1) {
				$predict_morphier = $this->createChainMorphier();
				$single_morphier = $this->createChainMorphier();
				
				$single_morphier->add($standalone_morphier);
				
				for($i = 0, $c = count($extra_morphiers); $i < $c; $i++) {
					$predict_morphier->add($extra_morphiers[$i]);
					$single_morphier->add($extra_morphiers[$i]);
				}
			} else {
				$predict_morphier = $extra_morphiers[0];
				
				$single_morphier = $this->createChainMorphier();
				$single_morphier->add($standalone_morphier);
				$single_morphier->add($predict_morphier);
			}
		} else {
			$single_morphier = $standalone_morphier;
		}
		
		if($options['with_gramtab']) {
			$this->gramtab = $this->createGramTab(
				$options['storage'],
				$filesBundle->getGramTabFile()
			);

			if(0) {
				$this->single_morphier = $this->createGramTabMorphier(
					$single_morphier,
					$this->gramtab
				);
			} else {
				$this->single_morphier = $single_morphier;
			}
		} else {
			$this->single_morphier = $single_morphier;
		}
		
		$this->predict_morphier = $predict_morphier;
	}
	
	function getSingleMorphier() { return $this->single_morphier; }
	
	function getBulkMorphier() {
		if(!isset($this->bulk_morphier)) {
			$bulk_morphier = $this->createBulkMorphier(
				$this->common_fsa,
				$this->graminfo,
				$this->predict_morphier
			);
			
			if($this->options['with_gramtab']) {
				$this->bulk_morphier = $this->createGramTabMorphierBulk(
					$bulk_morphier,
					$this->gramtab
				);
			} else {
				$this->bulk_morphier = $bulk_morphier;
			}
		}
		
		return $this->bulk_morphier;
	}
	
	function getBaseForm($word) {
		if(!is_array($word)) {
			return $this->single_morphier->getBaseForm($word);
		} else {
			$bulker = $this->getBulkMorphier();
			return $bulker->getBaseForm($word);
		}
	}
	
	function getAllForms($word) {
		if(!is_array($word)) {
			return $this->single_morphier->getAllForms($word);
		} else {
			$bulker = $this->getBulkMorphier();
			return $bulker->getAllForms($word);
		}
	}
	
	function getPseudoRoot($word) {
		if(!is_array($word)) {
			return $this->single_morphier->getPseudoRoot($word);
		} else {
			$bulker = $this->getBulkMorphier();
			return $bulker->getPseudoRoot($word);
		}
	}
	
	function getAllFormsWithGramInfo($word) {
		if(!is_array($word)) {
			return $this->single_morphier->getAllFormsWithGramInfo($word);
		} else {
			$bulker = $this->getBulkMorphier();
			return $bulker->getAllFormsWithGramInfo($word);
		}
	}
	
	function getCodepage() {
		return $this->graminfo->getCodepage();
	}
	
	function getGramTab() {
		return $this->gramtab;
	}
	
	protected function repairOptions($options) {
		$default = array(
		 	'storage' => PHPMORPHY_STORAGE_FILE,
			'with_gramtab' => false,
			'predict_by_suffix' => false,
			'predict_by_db' => false,
		);
		
		$result = array();
		settype($options, 'array');
		
		foreach($default as $k => $v) {
			if(array_key_exists($k, $options)) {
				$result[$k] = $options[$k];
			} else {
				$result[$k] = $v;
			}
		}
		
		return $result;
	}
	
	protected function createGramTabMorphierBulk($morphier, $gramtab) {
		return new phpMorphy_Morphier_WithGramTabBulk($morphier, $gramtab);
	}
	
	protected function createGramTabMorphier($morphier, $gramtab) {
		return new phpMorphy_Morphier_WithGramTab($morphier, $gramtab);
	}
	
	protected function readGramTab($storageType, $fileName) {
		$storage = $this->createStorage($storageType, $fileName);
		return $storage->read(0, $storage->getFileSize());
	}
	
	protected function createGramTab($storageType, $fileName) {
		require_once(PHPMORPHY_DIR . '/gramtab.php');
		
		return new phpMorphy_GramTab(
			$this->readGramTab($storageType, $fileName),
			$this->createGramTabBuilder()
		);
	}
	
	protected function createGramTabBuilder() {
		//return new phpMorphy_GramTab_ArrayBuilder();
		return new phpMorphy_GramTab_StringBuilder();
	}
	
	protected function createGramInfo($storage) {
		return new phpMorphy_GramInfo_RuntimeCaching(
			phpMorphy_GramInfo::create($storage)
		);
	}
	
	protected function createFsa($storage) {
		return phpMorphy_Fsa::create($storage);
	}
	
	protected function createStorage($type, $fileName) {
		return phpMorphy_Storage::create($type, $fileName);
	}
	
	protected function createChainMorphier() {
		return new phpMorphy_Morphier_Chain();
	}
	
	protected function createSingleMorphier($fsa, $graminfo) {
		return new phpMorphy_Morphier_DictSingle($fsa, $graminfo);
	}
	
	protected function createBulkMorphier($fsa, $graminfo, $predict) {
		if(null === $predict) {
			$predict = new phpMorphy_Morphier_Empty();
		}

		return new phpMorphy_Morphier_DictBulk($fsa, $graminfo, $predict);
	}
	
	protected function createPredictBySuffixMorphier($fsa, $graminfo) {
		return new phpMorphy_Morphier_PredictBySuffix($fsa, $graminfo);
	}
	
	protected function createPredictByDatabaseMorphier($fsa, $graminfo) {
		return new phpMorphy_Morphier_PredictByDatabse($fsa, $graminfo);
	}
};
