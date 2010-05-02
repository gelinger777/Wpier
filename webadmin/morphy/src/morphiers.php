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

interface phpMorphy_Morphier_Interface {
	function getBaseForm($word);
	function getAllForms($word);
	function getAllFormsWithGramInfo($word);
	function getPseudoRoot($word);
};

class phpMorphy_Morphier_Empty implements phpMorphy_Morphier_Interface {
	function getBaseForm($word) { return false; }
	function getAllForms($word) { return false; }
	function getAllFormsWithGramInfo($word) { return false; }
	function getPseudoRoot($word) { return false; }
}
 
/**
 * Base class for all morphiers
 * @abstract 
 */
abstract class phpMorphy_Morphier_Base implements phpMorphy_Morphier_Interface {
	protected
		$graminfo,
		$fsa,
		$root_trans;
	
	function phpMorphy_Morphier_Base(phpMorphy_Fsa_Interface $fsa, phpMorphy_GramInfo_Interace $graminfo) {
		$this->fsa = $fsa;
		$this->graminfo = $graminfo;
		$this->root_trans = $fsa->getRootTrans();
	}
	
	function getBaseForm($word) {
		if(false === ($annot = $this->findWord($word))) {
			return false;
		}
		
		return $this->composeForms($word, $annot, true);
	}
	
	function getPseudoRoot($word) {
		if(false === ($annots = $this->findWord($word))) {
			return false;
		}
		
		$result = array();
		
		foreach($annots as $annot) {
			list($base) = $this->getBaseAndPrefix(
				$word,
				$annot['cplen'],
				$annot['plen'],
				$annot['flen']
			);
			
			$result[$base] = 1;
		}
		
		return array_keys($result);
	}
	
	
	function getAllForms($word) {
		if(false === ($annot = $this->findWord($word))) {
			return false;
		}
		
		return $this->composeForms($word, $annot, false);
	}
	
	function getAllFormsWithGramInfo($word) {
		if(false === ($annots = $this->findWord($word))) {
			return false;
		}
		
		$result = $this->composeGramInfos($annots, 'all');
		
		$i = 0;
		foreach($annots as $annot) {
			$current =& $result[$i];
			
			$forms = $this->composeForms($word, array($annot), false);

			if(false !== $forms) {
				$current['common'] = $annot['ancode'];
				$current['forms'] = $forms;
			} else {
				unset($result[$i]);
			}
			
			unset($current);
			$i++;
		}
		
		return count($result) ? $result : false;
	}
	
	function getFsa() { return $this->fsa; }
	function getGramInfo() { return $this->graminfo; }
	
	protected function composeGramInfos($annots, $key) {
		$result = array();
		
		foreach($annots as $annot) {
			$result[] = array(
				$key => $this->graminfo->readAncodes($annot)
			);
		}
		
		return $result;
	}
	
	protected function composeForms($word, $annots, $onlyBase) {
		$result = array();
		
		foreach($annots as $annot) {
			list($base, $prefix) = $this->getBaseAndPrefix(
				$word,
				$annot['cplen'],
				$annot['plen'],
				$annot['flen']
			);
			
			// read flexia
			$flexias = $this->graminfo->readFlexiaData($annot, $onlyBase);
			
			for($i = 0, $c = count($flexias); $i < $c; $i += 2) {
				$result[$prefix . $flexias[$i] . $base . $flexias[$i + 1]] = 1;
			}
		}
		
		return array_keys($result);
	}
	
	protected function getBaseAndPrefix($word, $cplen, $plen, $flen) {
		if($flen) {
			$base = substr($word, $cplen + $plen, -$flen);
		} else {
			if($cplen || $plen) {
				$base = substr($word, $cplen + $plen);
			} else {
				$base = $word;
			}
		}
		
		$prefix = $cplen ? substr($word, 0, $cplen) : '';
		
		return array($base, $prefix);
	}
	
	// abstract methods
	abstract protected function findWord($word);
	abstract protected function decodeAnnot($annotRaw);
	abstract protected function getAnnotSize();
};

// TODO: This can`t extends phpMorphy_Morphier_Base, refactor it!
abstract class phpMorphy_Morphier_Dict extends phpMorphy_Morphier_Base {
	protected
		$predict,
		$single_morphier,
		$bulk_morphier;
	
	function phpMorphy_Morphier_Dict(phpMorphy_Fsa_Interface $fsa, phpMorphy_GramInfo_Interace $graminfo, phpMorphy_Morphier_Interface $predict) {
		parent::phpMorphy_Morphier_Base($fsa, $graminfo);
		$this->predict = $predict;
		
		$this->single_morphier = $this->createSingle($fsa, $graminfo);
	}
	
	function getBaseForm($word) { return $this->invoke('getBaseForm', $word); }
	function getAllForms($word) { return $this->invoke('getAllForms', $word); }
	function getPseudoRoot($word) { return $this->invoke('getPseudoRoot', $word); }
	
	protected function invoke($method, $word) {
		if(!is_array($word)) {
			return $this->single_morphier->$method($word);
		} else {
			if(!isset($this->bulk_morphier)) {
				$this->bulk_morphier = $this->createBulk(
					$this->fsa,
					$this->graminfo,
					$this->predict
				);
			}
			
			return $this->bulk_morphier->$method($word);
		}
	}
	
	protected function createSingle(phpMorphy_Fsa_Interface $fsa, phpMorphy_GramInfo_Interace $graminfo, phpMorphy_Morphier_Interface $predict) {
		return new phpMorphy_Morphier_DictSingle($fsa, $graminfo, $predict);
	}
	
	protected function createBulk(phpMorphy_Fsa_Interface $fsa, phpMorphy_GramInfo_Interace $graminfo, phpMorphy_Morphier_Interface $predict) {
		return new phpMorphy_Morphier_DictBulk($fsa, $graminfo, $predict);
	}
}

abstract class phpMorphy_Morphier_Common extends phpMorphy_Morphier_Base {
	protected function getAnnotSize() { return 15; }
	
	protected function decodeAnnot($annotRaw) {
		$result = array();
		
		$len = strlen($annotRaw);
		if($len % 15 != 0 || !$len) {
			throw new phpMorphy_Exception("Invalid annot with $len length given");
		}
		
		for($i = 0, $c = strlen($annotRaw); $i < $c; $i += 15) {
			$result[] = unpack(
				'Voffset/vbase_size/vall_size/vancodes_size/a2ancode/Cflen/Cplen/Ccplen',
				substr($annotRaw, $i, 15)
			);
		}
		
		return $result;
	}
}

class phpMorphy_Morphier_DictBulk extends phpMorphy_Morphier_Common {
	protected $predict;
	
	function phpMorphy_Morphier_DictBulk(phpMorphy_Fsa_Interface $fsa, phpMorphy_GramInfo_Interace $graminfo, phpMorphy_Morphier_Interface $predict) {
		parent::phpMorphy_Morphier_Common($fsa, $graminfo);
		$this->predict = $predict;
	}
	
	function getBaseForm($words) {
		return $this->invoke('getBaseForm', $words, true, false);
	}
	
	function getAllForms($words) {
		return $this->invoke('getAllForms', $words, false, false);
	}
	
	function getPseudoRoot($words) {
		return $this->invoke('getPseudoRoot', $words, true, true);
	}
	
	function getAllFormsWithGramInfo($words) {
		$raw_annots = $this->findWord($words);
		
		$result = array();
		if(isset($raw_annots[''])) {
			if($this->predict) {
				foreach($raw_annots[''] as $word) {
					$result[$word] = $this->predict->getAllFormsWithGramInfo($word);
				}
			} else {
				foreach($raw_annots[''] as $word) {
					$result[$word] = false;
				}
			}
		}
		
		foreach($raw_annots as $annot_raw => $words) {
			if(!strlen($annot_raw)) continue;
			
			$annot_chunks = str_split($annot_raw, $this->getAnnotSize());
			$annot_decoded = $this->decodeAnnot($annot_raw);
			
			foreach($words as $word) {
				$i = 0;
				foreach($annot_chunks as $chunk) {
					$forms = $this->composeForms(
						array($chunk => array($word)),
						false,
						false
					);
					
					$forms = $forms[$word];
					
					$result[$word][] = array(
						'forms' => $forms,
						'common' => $annot_decoded[$i]['ancode'],
						'all' => $this->graminfo->readAncodes($annot_decoded[$i])
					);
					
					$i++;
				}
			}
		}
		
		
		return $result;
	}
	
	protected function invoke($method, $words, $onlyBase, $pseudoRoot) {
		$annots = $this->findWord($words);
		
		// TODO: Ugly hack!
		$result = $this->composeForms($annots, $onlyBase, $pseudoRoot);
		
		if(isset($annots[''])) {
			if($this->predict) {
				foreach($annots[''] as $word) {
					$result[$word] = $this->predict->$method($word);
				}
			} else {
				foreach($annots[''] as $word) {
					$result[$word] = false;
				}
			}
		}

		return $result;
	}
	
	/*
	protected function findWord_slow($words) {
		$tree = $this->buildPrefixTree($words);
		
		$annots = array();
		$unknown_words_annot = '';
		
		$walk_calls = 0;
		$N = 0;
		
		for($keys = array_keys($tree), $i = 0, $c = count($keys); $i < $c; $i++) {
			$prefix = $keys[$i];
			$suffixes = $tree[$prefix];
			
			// find prefix
			$prefix_result = $this->fsa->walk($this->root_trans, $prefix, false);
			$prefix_trans = $prefix_result['last_trans'];
			$prefix_found = $prefix_result['result'];
			
			for($j = 0, $jc = count($suffixes); $j < $jc; $j++) {
				$suffix = $suffixes[$j];
				$word = $prefix . $suffix;
				
				if($prefix_found) {
					// find suffix
					$result = $this->fsa->walk($prefix_trans, $suffix, true);
					
					if(!$result['result'] || null === $result['annot']) {
						$annots[$unknown_words_annot][] = $word;
					} else {
						$annots[$result['annot']][] = $word;
					}
				} else {
					$annots[$unknown_words_annot][] = $word;
				}
			}
		}
		
		return $annots;
	}
	*/
	
	protected function findWord($words) {
		$unknown_words_annot = '';
		
		list($labels, $finals, $dests) = $this->buildPatriciaTrie($words);
		
		$annots = array();
		$unknown_words_annot = '';
		$stack = array(0, '', $this->root_trans);
		$stack_idx = 0;
		
		$fsa = $this->fsa;
		
		// TODO: Improve this
		while($stack_idx >= 0) {
			$n = $stack[$stack_idx];
			$path = $stack[$stack_idx + 1] . $labels[$n];
			$trans = $stack[$stack_idx + 2];
			$stack_idx -= 3; // TODO: Remove items from stack? (performance!!!)
			
			$is_final = $finals[$n] > 0;
			
			$result = false;
			if(false !== $trans && $n > 0) {
				$label = $labels[$n];
				
				$result = $fsa->walk($trans, $label, $is_final);
				
				if(strlen($label) == $result['walked']) {
					$trans = $result['word_trans'];
				} else {
					$trans = false;
				}
			}
			
			if($is_final) {
				if(false !== $trans && isset($result['annot'])) {
					$annots[$result['annot']][] = $path;
				} else {
					$annots[$unknown_words_annot][] = $path;
				}
			}
			
			if(false !== $dests[$n]) {
				foreach($dests[$n] as $dest) {
					$stack_idx += 3;
					$stack[$stack_idx] = $dest;
					$stack[$stack_idx + 1] = $path;
					$stack[$stack_idx + 2] = $trans;
				}
			}
		}
		
		return $annots;
	}
	
	protected function composeForms($annotsRaw, $onlyBase, $pseudoRoot) {
		$size_index = $onlyBase ? 'base_size' : 'all_size';
		
		$result = array();
		// process found annotations
		foreach($annotsRaw as $annot_raw => $words) {
			if(strlen($annot_raw) == 0) continue;
			
			foreach($this->decodeAnnot($annot_raw) as $annot) {
				$flexias = $this->graminfo->readFlexiaData($annot, $onlyBase);
				
				$cplen = $annot['cplen'];
				$plen = $annot['plen'];
				$flen = $annot['flen'];
				
				foreach($words as $word) {
					if($flen) {
						$base = substr($word, $cplen + $plen, -$flen);
					} else {
						if($cplen || $plen) {
							$base = substr($word, $cplen + $plen);
						} else {
							$base = $word;
						}
					}
					
					$prefix = $cplen ? substr($word, 0, $cplen) : '';
					
					for($i = 0, $c = count($flexias); $i < $c; $i += 2) {
						if($pseudoRoot) {
							$form = $base;
						} else {
							$form = $prefix . $flexias[$i] . $base . $flexias[$i + 1];
						}
						
						if(!isset($result[$word]) || !in_array($form, $result[$word])) {
							$result[$word][] = $form;
						}
					}
				}
			}
		}
		
		return $result;
	}
	
	protected function buildPatriciaTrie($words) {
		sort($words);
		
		$stack = array();
		$prev_word = '';
		$prev_word_len = 0;
		$prev_lcp = 0;
		
		$state_labels = array();
		$state_finals = array();
		$state_dests = array();
		
		$state_labels[] = '';
		$state_finals = '0';
		$state_dests[] = array();
		
		$node = 0;
		
		foreach($words as $word) {
			if($word == $prev_word) {
				continue;
			}
			
			$word_len = strlen($word);
			// find longest common prefix
			for($lcp = 0, $c = min($prev_word_len, $word_len); $lcp < $c && $word[$lcp] == $prev_word[$lcp]; $lcp++);
			
			if($lcp == 0) {
				$stack = array();
				
				$new_state_id = count($state_labels);
				
				$state_labels[] = $word;
				$state_finals .= '1';
				$state_dests[] = false;
				
				$state_dests[0][] = $new_state_id;
				
				$node = $new_state_id;
			} else {
				$need_split = true;
				$trim_size = 0; // for split
				
				if($lcp == $prev_lcp) {
					$need_split = false;
					$node = $stack[count($stack) - 1];
				} elseif($lcp > $prev_lcp) {
					if($lcp == $prev_word_len) {
						$need_split = false;
					} else {
						$need_split = true;
						$trim_size = $lcp - $prev_lcp;
					}
					
					$stack[] = $node;
				} else {
					$trim_size = strlen($prev_word) - $lcp;
					
					for($stack_size = count($stack) - 1; ;--$stack_size) {
						$trim_size -= strlen($state_labels[$node]);
						
						if($trim_size <= 0) {
							break;
						}
						
						if(count($stack) < 1) {
							throw new phpMorphy_Exception('Infinite loop posible');
						}
						
						$node = array_pop($stack);
					}
					
					$need_split = $trim_size < 0;
					$trim_size = abs($trim_size);
					
					if($need_split) {
						$stack[] = $node;
					} else {
						$node = $stack[$stack_size];
					}
				}
				
				if($need_split) {
					$node_key = $state_labels[$node];
					
					// split
					$new_node_id_1 = count($state_labels);
					$new_node_id_2 = $new_node_id_1 + 1;
					
					// new_node_1
					$state_labels[] = substr($node_key, $trim_size);
					$state_finals .= $state_finals[$node];
					$state_dests[] = $state_dests[$node];
					
					// adjust old node
					$state_labels[$node] = substr($node_key, 0, $trim_size);
					$state_finals[$node] = '0';
					$state_dests[$node] = array($new_node_id_1);
					
					// append new node, new_node_2
					$state_labels[] = substr($word, $lcp);
					$state_finals .= '1';
					$state_dests[] = false;
	
					$state_dests[$node][] = $new_node_id_2;
					
					$node = $new_node_id_2;
				} else {
					$new_node_id = count($state_labels);
					
					$state_labels[] = substr($word, $lcp);
					$state_finals .= '1';
					$state_dests[] = false;
					
					if(false !== $state_dests[$node]) {
						$state_dests[$node][] = $new_node_id;
					} else {
						$state_dests[$node] = array($new_node_id);
					}
					
					$node = $new_node_id;
				}
			}
			
			$prev_word = $word;
			$prev_word_len = $word_len;
			$prev_lcp = $lcp;
		}
		
		return array($state_labels, $state_finals, $state_dests);
	}
	/*
	protected function buildPrefixTree($words) {
		sort($words);
		
		$prefixes = array();
		$prev_word = '';
		
		foreach($words as $word) {
			if($prev_word != $word) {
				for($idx = 0, $c = min(strlen($prev_word), strlen($word)); $idx < $c && $word[$idx] == $prev_word[$idx]; $idx++);
				
				$prefix = substr($word, 0, $idx);
				$rest = substr($word, $idx);
				
				$prefixes[$prefix][] = $rest;
				
				$prev_word = $word;
			}
		}
		
		return $prefixes;
	}
	*/
}

class phpMorphy_Morphier_DictSingle extends phpMorphy_Morphier_Common {
	protected function findWord($word) {
		$result = $this->fsa->walk($this->root_trans, $word);
		
		if(!$result['result'] || null === $result['annot']) {
			return false;
		}
		
		return $this->decodeAnnot($result['annot']);
	}
};

class phpMorphy_Morphier_PredictBySuffix extends phpMorphy_Morphier_Common {
	protected
		$min_suf_len,
		$unknown_len;
	
	function phpMorphy_Morphier_PredictBySuffix(phpMorphy_Fsa_Interface $fsa, phpMorphy_GramInfo_Interace $graminfo, $minimalSuffixLength = 4) {
		parent::phpMorphy_Morphier_Base($fsa, $graminfo);
		
		$this->min_suf_len = $minimalSuffixLength;
	}
	
	protected function findWord($word) {
		$word_len = strlen($word);
		
		for($i = 1, $c = $word_len - $this->min_suf_len; $i < $c; $i++) {
			$result = $this->fsa->walk($this->root_trans, substr($word, $i));
			
			if($result['result'] && null !== $result['annot']) {
				break;
			}
		}

		if($i < $c) {
			//$known_len = $word_len - $i;
			$unknown_len = $i;
			
			
			return $this->fixAnnots(
				$this->decodeAnnot($result['annot']),
				$unknown_len
			);
		} else {
			return false;
		}
	}
	
	protected function fixAnnots($annots, $len) {
		for($i = 0, $c = count($annots); $i < $c; $i++) {
			$annots[$i]['cplen'] = $len;
		}
		
		return $annots;
	}
};

class phpMorphy_PredictMorphier_Collector extends phpMorphy_Fsa_WordsCollector {
	protected
		$used_poses = array(),
		$collected = 0;
	
	function collect($path, $annotRaw) {
		if($this->collected > $this->limit) {
			return false;
		}
		
		$used_poses =& $this->used_poses;
		$annots = $this->decodeAnnot($annotRaw);
		
		for($i = 0, $c = count($annots); $i < $c; $i++) {
			$annot = $annots[$i];
			$annot['cplen'] = $annot['plen'] = 0;
			
			$pos_id = $annot['pos_id'];
			
			if(isset($used_poses[$pos_id])) {
				$result_idx = $used_poses[$pos_id];
				
				if($annot['freq'] > $this->items[$result_idx]['freq']) {
					$this->items[$result_idx] = $annot;
				}
			} else {
				$used_poses[$pos_id] = count($this->items);
				$this->items[] = $annot;
			}
		}
		
		$this->collected++;
		return true;
	}
	
	function clear() {
		parent::clear();
		$this->collected = 0;
		$this->used_poses = array();
	}
	
	function decodeAnnot($annotRaw) {
		$result = array();
		
		$len = strlen($annotRaw);
		if($len % 16 != 0 || !$len) {
			throw new phpMorphy_Exception("Invalid annot with $len length given");
		}
		
		for($i = 0, $c = strlen($annotRaw); $i < $c; $i += 16) {
			$result[] = unpack(
				'Voffset/vbase_size/vall_size/vancodes_size/a2ancode/vfreq/Cflen/Cpos_id',
				substr($annotRaw, $i, 16)
			);
		}
		
		return $result;
	}
};

class phpMorphy_Morphier_PredictByDatabse extends phpMorphy_Morphier_Base {
	protected
		$collector,
		$min_postfix_match;
	
	function phpMorphy_Morphier_PredictByDatabse(phpMorphy_Fsa_Interface $fsa, phpMorphy_GramInfo_Interace $graminfo, $minPostfixMatch = 2, $collectLimit = 32) {
		parent::phpMorphy_Morphier_Base($fsa, $graminfo);
		
		$this->min_postfix_match = $minPostfixMatch;
		$this->collector = $this->createCollector($collectLimit);
	}
	
	protected function findWord($word) {
		$result = $this->fsa->walk($this->root_trans, strrev($word));
		
		if($result['result'] && null !== $result['annot']) {
			$annots = $result['annot'];
		} else {
			if(null === ($annots = $this->determineAnnots($result['last_trans'], $result['walked']))) {
				return false;
			}
		}
		
		if(!is_array($annots)) {
			$annots = $this->collector->decodeAnnot($annots);
		}


		return $this->fixAnnots($annots);
	}
	
	/*
	TODO: б¤Ґ« вм, Є®Ј¤  Ўг¤Ґв Ј®в®ў ­®ўл© Є®¬ЇЁ«Ґа б«®ў ап (б form_no)
	protected function composeForms($word, $annots, $onlyBase) {
		$result = array();
		$word_len = strlen($word);
		
		foreach($annots as $annot) {
			$flen = $annot['flen'];
			$plen = $annot['plen'];
			$cplen = $annot['cplen'];

			if($flen + $plen + $cplen <= $word_len) {
				list($base, $prefix) = $this->getBaseAndPrefix($word, $cplen, $plen, $flen);

				// TODO: нв® ­ҐўҐа­® Ё ЄаЁў® (­г¦Ґ­ form_no)
				$word_suffix = $flen ? substr($word, -$flen) : '';
			    $word_prefix = substr($word, 0, $cplen + $plen);

				// read flexia
				$flexias = $this->graminfo->readFlexiaData($annot, $onlyBase);
			
			    //vd($word, $word_prefix, $word_suffix, $annot, $onlyBase, $flexias);

				for($i = 0, $c = count($flexias); $i < $c; $i += 2) {
					$flexia_prefix = $flexias[$i];
					$flexia_suffix = $flexias[$i + 1];

					if($flexia_suffix == $word_suffix && $flexia_prefix == $word_prefix) {
						$result[$prefix . $flexia_prefix . $base . $flexia_suffix] = 1;
					}
				}
			}
		}

		return count($result) ? array_keys($result) : false;
	}
	*/

	// TODO: Refactor this!!!
	protected function getAnnotSize() { return 16; }
	protected function decodeAnnot($annots) { return $this->collector->decodeAnnot($annots); }
	
	protected function determineAnnots($trans, $matchLen) {
		$annots = $this->fsa->getAnnot($trans);
		
		if(null == $annots && $matchLen >= $this->min_postfix_match) {
			$this->collector->clear();
			
			$this->fsa->collect(
				$trans,
				$this->collector->getCallback()
			);
			
			$annots = $this->collector->getItems();
		}

		return $annots;
	}
	
	protected function fixAnnots($annots) {
		// remove all prefixes?
		for($i = 0, $c = count($annots); $i < $c; $i++) {
			$annots[$i]['cplen'] = $annots[$i]['plen'] = 0;
		}

		return $annots;
	}
	
	protected function createCollector($limit) {
		return new phpMorphy_PredictMorphier_Collector($limit);
	}
};

class phpMorphy_Morphier_Decorator implements phpMorphy_Morphier_Interface {
	protected $morphier;
	
	function phpMorphy_Morphier_Decorator(phpMorphy_Morphier_Interface $morphier) {
		$this->morphier = $morphier;
	}
	
	function getBaseForm($word) { return $this->morphier->getBaseForm($word); }
	function getAllForms($word) { return $this->morphier->getAllForms($word); }
	function getAllFormsWithGramInfo($word) { return $this->morphier->getAllFormsWithGramInfo($word); }
	function getPseudoRoot($word) { return $this->morphier->getPseudoRoot($word); }
	
	function getFsa() { return $this->morphier->getFsa(); }
	function getGramInfo() { return $this->morphier->getGramInfo(); }

	function getInner() { return $this->morphier; }
}

class phpMorphy_Morphier_WithGramTab extends phpMorphy_Morphier_Decorator {
	protected
		$gramtab,
		$file_name;
	
	function phpMorphy_Morphier_WithGramTab(phpMorphy_Morphier_Interface $morphier, phpMorphy_GramTab $gramtab) {
		parent::phpMorphy_Morphier_Decorator($morphier);
		$this->gramtab = $gramtab;
	}
	
	function getAllFormsWithGramInfo($word) {
		if(false !== ($result = $this->morphier->getAllFormsWithGramInfo($word))) {
			$this->postprocessItems($result);
		}
		
		return $result;
	}
	
	protected function postprocessItems(&$result) {
		for($i = 0, $c = count($result); $i < $c; $i++) {
			$res =& $result[$i];
			$res['common'] = $this->gramtab->resolve($res['common']);
			
			$res_all =& $res['all'];
			for($j = 0, $jc = count($res_all); $j < $jc; $j++) {
				$res_all[$j] = $this->gramtab->resolve($res_all[$j]);
			}
		}
	}
};

class phpMorphy_Morphier_WithGramTabBulk extends phpMorphy_Morphier_WithGramTab {
	protected function postprocessItems(&$result) {
		foreach($result as $key => &$value) {
			if(false !== $value) {
				parent::postprocessItems($value);
			}
		}
	}
}

class phpMorphy_Morphier_Chain implements phpMorphy_Morphier_Interface {
	protected
		$morphiers = array();
	
	function getMorphiers() { return $this->morphiers; }
	function add(phpMorphy_Morphier_Interface $morphier) { $this->morphiers[] = $morphier; }
	
	function getBaseForm($word) {
		return $this->invoke('getBaseForm', $word);
	}
	
	function getAllForms($word) {
		return $this->invoke('getAllForms', $word);
	}
	
	function getAllFormsWithGramInfo($word) {
		return $this->invoke('getAllFormsWithGramInfo', $word);
	}
	
	function getPseudoRoot($word) {
		return $this->invoke('getPseudoRoot', $word);
	}
	
	protected function invoke($method, $word) {
		for($i = 0, $c = count($this->morphiers); $i < $c; $i++) {
			if(false !== ($result = $this->morphiers[$i]->$method($word))) {
				return $result;
			}
		}
		
		return false;
	}
};
