<?php

/**
 * Copyright (C) 2002-2004 Oliver Hitz <oliver@net-track.ch>
 *
 * $Id: Template.inc,v 1.6 2004/03/08 13:55:01 oli Exp $
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston,
 * MA  02111-1307, USA.
 */

class Template
{

  var $structure;
  var $content;

  var $filename = "";

  function Template($filename)
  {
    $this->filename = $filename;

    $this->structure = array();
    $this->structure["name"] = "main";
    $this->structure["body"] = $this->loadfile($this->filename);
    $this->structure["blocks"] = array();
    $this->structure["values"] = array();
    $this->parse($this->structure);
    $this->content = array();
    $this->content["blocks"] = array();
    $this->content["values"] = array();
  }

  function parse(&$structure)
  {
    while (preg_match("|<!--\ startBlock\(([^),]+)(,([^)]+))?\)\ -->|si", $structure["body"], $m)) {
      $name = $m[1];
      $sort = isset($m[3])?$m[3]:null;

      $block = array();
      $block["name"] = $m[1];
      $block["sort"] = isset($m[3])?$m[3]:null;
      $block["blocks"] = array();
      $block["used"] = 0;
      $reg = "|<!--\ startBlock\(".$name."(,[^)]*)?\)\ -->(.*)<!--\ endBlock\(".$name."\)\ -->|si";
      if (!preg_match($reg, $structure["body"], $m)) {
	$this->error("block `".$name."' does not have startBlock() AND endBlock().");
      }
      $reg2 = "|<!--\ startBlock\(".$name."(,[^)]*)?\)\ -->(.*)<!--\ emptyBlock\(".$name."\)\ -->(.*)<!--\ endBlock\(".$name."\)\ -->|si";

      if (preg_match($reg2, $structure["body"], $m2)) {
	$block["body"] = $m2[2];
	$block["empty"] = $m2[3];
	$structure["body"] = preg_replace($reg2, "{block:".$name."}", $structure["body"]);
      } else {
	$block["body"] = $m[2];
	$structure["body"] = preg_replace($reg, "{block:".$name."}", $structure["body"]);
      }
      $this->parse($block);
     
      $structure["blocks"][$name] = $block;
    }
  }

  function showContent()
  {
    print "<ul>";
    $this->_showContent($this->content);
    print "</ul>";
  }

  function _showContent(&$content)
  {
    print "<li>values: (".implode(",", $content["values"]).")</li>";
    print "<li>blocks:";
    print "<ul>";
    reset ($content["blocks"]);
    while (list($name, $blocks) = each($content["blocks"])) {
      print "<li>name: ".$name;
      print "<ul>";
      for ($i = 0; $i < count($blocks); $i++) {
	$this->_showContent($blocks[$i]);
      }
      print "</ul>";
      print "</li>";
    }
    print "</ul>";
    print "</li>";
  }


  function showStructure()
  {
    print "<ul>";
    $this->_showStructure($this->structure);
    print "</ul>";
  }

  function _showStructure(&$structure)
  {
    print "<li>name: ".$structure["name"]."</li>";
    print "<li>sort: ".$structure["sort"]."</li>";
    print "<li>body: ".htmlentities($structure["body"])."</li>";
    print "<li>blocks:";
    print "<ul>";
    reset ($structure["blocks"]);
    while (list($blockname, $block) = each($structure["blocks"])) {
      $this->_showStructure($block);
    }
    print "</ul>";
    print "</li>";
  }

  function toString()
  {
    return preg_replace(array("|&#x7b;|", "|&#x7d;|", "|&#x3a;|", "|&#x7c;|"),
			array("{", "}", ":", "|"),
			$this->_toString($this->structure, $this->content, 0));
  }

  function _toString(&$structure, &$content, $count, $max = -1)
  {
    global $HTTP_SERVER_VARS;

    $txt = $structure["body"];

    // Replace all user variables
    reset($content["values"]);
    while (list($key, $val) = each($content["values"])) {
      if (!is_numeric($key)) {
	if (!is_string($val)) {
	  $val = "".$val;
	}
 	$val = preg_replace(array("|{|", "|}|", "|:|", "/\|/"),
			    array("&#x7b;", "&#x7d;", "&#x3a;", "&#x7c;"),
			    $val);
	$txt = preg_replace("|{".$key."}|si", $val, $txt);
      }
    }

    // Replace all predefined variables
    $txt = preg_replace("|{sid}|", session_id(), $txt);
    $txt = preg_replace("|{script}|", $HTTP_SERVER_VARS["SCRIPT_NAME"], $txt);
    $txt = preg_replace("|{count:1}|si", "".($count+1), $txt);
    $txt = preg_replace("|{count:0}|si", "".($count), $txt);

    // Clear unused variables
    $txt = preg_replace("|{[a-zA-Z_-]+}|", "", $txt);

    while (true) {
      if (preg_match("|{quotenl:([^{}]+)}|si", $txt, $m)) {
	$v = preg_replace("|\n|", "\\n", $content["values"][$m[1]]);
	$v = preg_replace("|\r|", "", $v);
	$txt = preg_replace("|{quotenl:".$m[1]."}|si", $v, $txt);
      } else if (preg_match("|{quotejs:([^{}]+)}|si", $txt, $m)) {
	$v = preg_replace("|\"|", "\\\"", $content["values"][$m[1]]);
	$v = preg_replace("|\n|", "\\n", $v);
	$v = preg_replace("|\r|", "", $v);
	$txt = preg_replace("|{quotejs:".$m[1]."}|si", $v, $txt);
      } else if (preg_match("|{quotexml:([^{}]+)}|si", $txt, $m)) {
	$v = preg_replace("|&|", "&amp;", $content["values"][$m[1]]);
	$v = preg_replace("|<|", "&lt;", $v);
	$v = preg_replace("|>|", "&gt;", $v);
	$txt = preg_replace("|{quotexml:".$m[1]."}|si", $v, $txt);
      } else if (preg_match("|{csv:([^{}]*)}|si", $txt, $m)) {
	$q = "\"".preg_replace("|\"|", "\"\"", $content["values"][$m[1]])."\"";
	$q = preg_replace("|\n|", "\\n", $q);
	$q = preg_replace("|\r|", "", $q);
	$txt = preg_replace("|{csv:".$m[1]."}|si", $q, $txt);
      } else if (preg_match("|{base64:([^{}]+)}|si", $txt, $m)) {
	$v = base64_encode($m[1]);
	$txt = preg_replace("|{base64:".$this->quotere($m[1])."}|si", $v, $txt);
      } else if (preg_match("|{url:([^{}]+)}|si", $txt, $m)) {
	$txt = preg_replace("|{url:".$this->quotere($m[1])."}|si", urlencode($content["values"][$m[1]]), $txt);
      } else if (preg_match("|{format:([^:{}]+):([^{}]+)}|si", $txt, $m)) {
	$txt = preg_replace("|{format:".$m[1].":".$m[2]."}|si", sprintf($m[2], $content["values"][$m[1]]), $txt);
      } else if (preg_match("|{date:([^:{}]+):([^{}]+)}|si", $txt, $m)) {
	$txt = preg_replace("|{date:".$m[1].":".$m[2]."}|si", date($m[2], $content["values"][$m[1]]), $txt);
      } else if (preg_match("|{ifequal:([^:{}]+):([^:{}]*):([^{}]+)}|si", $txt, $m)) {
	if ($m[1] == $m[2]) {
	  $txt = preg_replace("|{ifequal:".$this->quotere($m[1]).":".$this->quotere($m[2]).":([^{}]+)}|si", "\\1", $txt);
	} else {
	  $txt = preg_replace("|{ifequal:".$this->quotere($m[1]).":".$this->quotere($m[2]).":([^{}]+)}|si", "", $txt);
	}
      } else if (preg_match("|{ifnequal:([^:{}]+):([^:{}]*):([^{}]+)}|si", $txt, $m)) {
	if ($m[1] != $m[2]) {
	  $txt = preg_replace("|{ifnequal:".$this->quotere($m[1]).":".$this->quotere($m[2]).":([^{}]+)}|si", "\\1", $txt);
	} else {
	  $txt = preg_replace("|{ifnequal:".$this->quotere($m[1]).":".$this->quotere($m[2]).":([^{}]+)}|si", "", $txt);
	}
      } else if (preg_match("|{ifeq:([^:{}]+):([^:{}]*):([^{}]+)}|si", $txt, $m)) {
	if ($content["values"][$m[1]] == $m[2]) {
	  $txt = preg_replace("|{ifeq:".$m[1].":".$this->quotere($m[2]).":([^{}]+)}|si", "\\1", $txt);
	} else {
	  $txt = preg_replace("|{ifeq:".$m[1].":".$this->quotere($m[2]).":([^{}]+)}|si", "", $txt);
	}
      } else if (preg_match("|{ifne:([^:{}]+):([^:{}]*):([^{}]+)}|si", $txt, $m)) {
	if ($content["values"][$m[1]] != $m[2]) {
	  $txt = preg_replace("|{ifne:".$m[1].":".$this->quotere($m[2]).":([^{}]+)}|si", "\\1", $txt);
	} else {
	  $txt = preg_replace("|{ifne:".$m[1].":".$this->quotere($m[2]).":([^{}]+)}|si", "", $txt);
	}
      } else if (preg_match("|{ifset:([^:{}]+):([^{}]*)}|si", $txt, $m)) {
	if (isset($content["values"][$m[1]]) && $content["values"][$m[1]] != "" && $content["values"][$m[1]] != "0") {
	  $txt = preg_replace("|{ifset:".$m[1].":([^{}]*)}|si", "\\1", $txt);
	} else {
	  $txt = preg_replace("|{ifset:".$m[1].":([^{}]*)}|si", "", $txt);
	}
      } else if (preg_match("|{ifnotset:([^:{}]+):([^{}]+)}|si", $txt, $m)) {
	if ($content["values"][$m[1]] == "" || $content["values"][$m[1]] == "0") {
	  $txt = preg_replace("|{ifnotset:".$m[1].":([^{}]+)}|si", "\\1", $txt);
	} else {
	  $txt = preg_replace("|{ifnotset:".$m[1].":([^{}]+)}|si", "", $txt);
	}
      } else if (preg_match("|{iflast:([^{}]+)}|si", $txt, $m)) {
	if ($count == $max-1) {
	  $txt = preg_replace("|{iflast:".$this->quotere($m[1])."}|si", $m[1], $txt);
	} else {
	  $txt = preg_replace("|{iflast:".$this->quotere($m[1])."}|si", "", $txt);
	}
      } else if (preg_match("|{ifnotlast:([^{}]+)}|si", $txt, $m)) {
	if ($count == $max-1) {
	  $txt = preg_replace("|{ifnotlast:".$this->quotere($m[1])."}|si", "", $txt);
	} else {
	  $txt = preg_replace("|{ifnotlast:".$this->quotere($m[1])."}|si", $m[1], $txt);
	}
      } else if (preg_match("|{ifnotfirst:([^{}]+)}|si", $txt, $m)) {
	if ($count == 0) {
	  $txt = preg_replace("|{ifnotfirst:".$this->quotere($m[1])."}|si", "", $txt);
	} else {
	  $txt = preg_replace("|{ifnotfirst:".$this->quotere($m[1])."}|si", $m[1], $txt);
	}
      } else if (preg_match("|{ifoddposition:([^{}]+)}|si", $txt, $m)) {
	if ($count % 2 == 1) {
	  $txt = preg_replace("|{ifoddposition:".$this->quotere($m[1])."}|si", $m[1], $txt);
	} else {
	  $txt = preg_replace("|{ifoddposition:".$this->quotere($m[1])."}|si", "", $txt);
	}
      } else if (preg_match("|{ifevenposition:([^{}]+)}|si", $txt, $m)) {
	if ($count % 2 == 0) {
	  $txt = preg_replace("|{ifevenposition:".$this->quotere($m[1])."}|si", $m[1], $txt);
	} else {
	  $txt = preg_replace("|{ifevenposition:".$this->quotere($m[1])."}|si", "", $txt);
	}
      } else if (preg_match("|{block:([^{}]+)}|si", $txt, $m)) {
	$blockname = $m[1];
	$block = $structure["blocks"][$blockname];
	if ($block["sort"] != "" && count($content["blocks"][$blockname]) > 0) {
	  $this->compareField = $block["sort"];
	  usort($content["blocks"][$blockname], array($this, "compare"));
	}
  	$rep = "";
	if (isset($content["blocks"][$blockname]) && count($content["blocks"][$blockname]) > 0) {
	  for ($i = 0; $i < count($content["blocks"][$blockname]); $i++) {
	    $rep .= $this->_toString($block,
				     $content["blocks"][$blockname][$i],
				     $i,
				     count($content["blocks"][$blockname]));
	  }
	} else if (isset($block["empty"])) {
	  $rep = $block["empty"];
	}
	$txt = preg_replace("|{block:".$blockname."}|si", $rep, $txt);
      } else {
	break;
      }
    }
      
    return $txt;
  }

  function quotere($r)
  {
    return preg_replace(array("/\"/", "/\|/", "/\(/", "/\)/"),
			array("\\\"", "\\|", "\\(", "\\)"),
			$r);
  }

  var $compareField;

  function compare($a, $b)
  {
    $an = $a["values"][$this->compareField];
    $bn = $b["values"][$this->compareField];

    return strcoll(strtolower($an), strtolower($bn));
  }

  function setVar($variable, $value)
  {
    if (is_array($value)) {
      if (is_array($value[0])) {
	foreach ($value as $v) {
	  $this->gotoNext($variable);
	  $this->setVar($variable, $v);
	}
      } else {
	if ($variable != "") {
	  $variable = $variable.".";
	}
	foreach ($value as $k => $v) {
	  $this->_setVar($this->content, $variable.$k, $v);
	}
      }
    } else {
      // Convert $value to a string
      $value = $value."";
      $this->_setVar($this->content, $variable, $value);
    }
  }

  function _setVar(&$content, $variable, $value)
  {
    if (preg_match("|([^\.]+)\.(.*)|si", $variable, $m)) {
      $parent = $m[1];
      $var    = $m[2];

      if (empty($content["blocks"][$parent])) {
	$content["blocks"][$parent] = array();
	$content["blocks"][$parent][0] = array();
      }
      $i = count($content["blocks"][$parent])-1;
      $this->_setVar($content["blocks"][$parent][$i], $var, $value);
    } else {
      if (empty($content["values"])) {
	$content["values"] = array();
      }
      $content["values"][$variable] = $value;
    }
  }

  function gotoNext($variable)
  {
    $this->_gotoNext($this->content, $variable);
  }

  function _gotoNext(&$content, $variable)
  {
    if (preg_match("|([^\.]+)\.(.*)|si", $variable, $m)) {
      $parent = $m[1];
      $var    = $m[2];

      if (empty($content["blocks"][$parent])) {
	$content["blocks"][$parent] = array();
	$content["blocks"][$parent][0] = array();
      }
      $i = count($content["blocks"][$parent])-1;
      $this->_gotoNext($content["blocks"][$parent][$i], $var);
    } else {
      if (empty($content["blocks"][$variable])) {
	$content["blocks"][$variable] = array();
      }
      $content["blocks"][$variable][] = array("values" => array(),
					      "blocks" => array());
    }
  }

  function loadfile($filename)
  {
    $fd = fopen($filename, "r");
    $content = fread($fd, filesize($filename));
    fclose($fd);
    return $content;
  }

  function error($message)
  {
    die($this->filename.": ".$message);
  }
}
