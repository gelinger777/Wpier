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

interface phpMorphy_GramTab_Builder_Interace {
	/**
	 * Build gramifo string from part of speech and grammems string
	 *
	 * @param string $pos Part of speech string
	 * @param string $grammems Grammems string
	 * @return mixed
	 */
	function build($pos, $grammems);
	
	/**
	 * @param string $grammems
	 * @return mixed
	 */
	function processGrammems($grammems);
	
	/**
	 * Join several graminfo strings into one
	 *
	 * @param array $strings
	 * @return mixed
	 */
	function join($strings);
}

interface phpMorphy_GramTab_Interface {
	/**
	 * @param string $ancodes
	 * @return mixed
	 */
	function resolve($ancodes);
	
	/**
	 * @param string $ancode
	 * @return void
	 */
	function resolveOne($ancode, &$pos, &$grammems);
	
	/**
	 * @param string $ancodes
	 * @return int
	 */
	function getFormsCount($ancodes);
	
	/**
	 * @param string $ancodes
	 * @return array
	 */
	function splitAncodes($ancodes);
}

class phpMorphy_GramTab_StringBuilder implements phpMorphy_GramTab_Builder_Interace {
	function build($pos, $grammems) {
		if($pos) {
			return "$pos $grammems";
		} else {
			return $grammems;
		}
	}
	
	function processGrammems($grammems) {
		return $grammems;
	}
	
	function join($strings) {
		return implode(';', $strings);
	}
};

class phpMorphy_GramTab_ArrayBuilder implements phpMorphy_GramTab_Builder_Interace {
	function build($pos, $grammems) {
		return array(
			'grammems' => explode(',', $grammems),
			'pos' => $pos
		);
	}
	
	function processGrammems($grammems) {
		return explode(',', $grammems);
	}
	
	function join($strings) {
		return $strings;
	}
};

class phpMorphy_GramTab implements phpMorphy_GramTab_Interface {
	protected
		$index,
		$poses,
		$grammems,
		$builder;
	
	function phpMorphy_GramTab($raw, phpMorphy_GramTab_Builder_Interace $builder) {
		$this->builder = $builder;
		
		$data = $this->prepare($raw);
		
		if(
			!is_array($data) ||
			!isset($data['index']) ||
			!isset($data['grammems']) ||
			!isset($data['poses'])
		) {
			throw new phpMorphy_Exception("Broken gramtab data");
		}
		
		$this->index = $data['index'];
		$this->grammems = $data['grammems'];
		$this->poses = $data['poses'];
	}
	
	function getFormsCount($ancodes) {
		return strlen($ancodes) / 2;
	}
	
	function splitAncodes($ancodes) {
		return str_split($ancodes, 2);
	}
	
	function resolve($ancodes) {
		$result = array();
		
		if($ancodes) {
			foreach(str_split($ancodes, 2) as $ancode) {
				// make "$result[] = $this->resolveOne($ancode);" inline
				$index = $this->index[$ancode];
				
				$result[] = $this->builder->build(
					$this->poses[$index & 0xFF],
					$this->grammems[$index >> 8]
				);
				
			}
		}
		
		return $this->builder->join($result);
	}
	
	function resolveOne($ancode, &$pos, &$grammems) {
		$index = $this->index[$ancode];
		
		$pos = $this->poses[$index & 0xFF];
		$grammems = $this->builder->processGrammems($this->grammems[$index >> 8]);
		/*
		return $this->builder->build(
			$this->poses[$index & 0xFF],
			$this->grammems[$index >> 8]
		);
		*/
	}
	
	protected function prepare($data) {
		return unserialize($data);
	}
};
