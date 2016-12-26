<?php
/**
 * @description LQL it's alternative LQL Processor suport for SQL
 * @author		Antonio Membrides Espinosa
 * @package    	processor
 * @date		15/05/2013
 * @license    	GPL
 * @version    	1.0
 * @require		SQLDriver
 */
namespace Ksike\lql\lib\processor\sql\src;
class Main extends \Ksike\lql\src\server\Processor
{
	public function __construct($jobs=false){
		$this->jobs = $jobs ? $jobs : array(
				'add',
				array('select', 'addSelect', 'delete', 'update', 'insert', 'drop', 'alter', 'create'),
                array('from', 'set', 'ownerTo'),
				array("leftJoin", "innerJoin", "rightJoin"),
				array("where", "andWhere", "orWhere", "addWhereIn", "whereIn"),
				array("groupBy", "having", "orderBy", "limit", "offset")
		);
	}
	public function clear(){
		$this->cache = '';
	}
	public function setting($cache=false){
		$this->cache = $cache ? $cache : $this->cache;
		return $this;
	}
	public function compile($struct, $force=false){
		if($force) $this->cache = '';
		if(empty($this->cache)) $this->cache = $this->generate($struct);
		return $this->cache;
	}
	
	protected function implode($glue, $lst, $quote=false ){
		$out = $this->evaluate(array_shift($lst), $quote);
		foreach($lst as $i){
			$out .= ', '. $this->evaluate($i, $quote);
		}
		return $out;
	}
	protected function evaltype($value, $quote=false){
        switch(gettype($value)) {
            case "boolean":
                return $value ? 'true' : 'false';
            break;
            case "double":
            case "integer":
                return $value;
            break;
            case "string":
                return $quote ? is_string($value) ? "'$value'" : $value : $value;
            break;
            case "NULL":
            case "array":
            case "object":
            case "resource":
            case "unknown type":
                return "''";
            break;
        }
	}
	protected function evaluate($value, $quote=false){
		return is_a($value, 'Ksike\lql\src\server\Main') ? "(" . $value->compile() . ")" : $this->evaltype($value, $quote);
	}
	protected function compare($data){
		$count = count($data);
		$field = $this->evaluate( $data[0] );
		$value = $count > 1 ? $this->evaluate($data[1], true) : '';
		$operator = $count > 2 ? $this->evaluate($data[2]) : ($count > 1 ? '=' : '');
		return "$field $operator $value";
	}
	protected function format($key, $value){
		if(empty($value)) return '';
		$index = strtoupper($key);
		switch($index){
			case "ADD": return is_object($value[0]) ? $value[0]->compile()."; " : (is_string($value[0]) ? $value[0] : ' ') ; break;
			case "ADDSELECT": 	
				$name = is_string($value[1]) ? $value[1] : 'tmp';
				$alias = is_a($value[0], 'Ksike\lql\src\server\Main') ? " AS $name " : (is_string($value[1]) ? " AS $name " : '');
				return ", ".$this->evaluate($value[0]).$alias; 
			break;
			case "ANDWHERE": 
			case "ADDWHERE": return " AND ". $this->compare($value); break;
			case "ORWHERE": return " OR ". $this->compare($value); break;
			case "DELETE": return " DELETE FROM ". $this->evaluate($value[0]); break;
			case "WHERE": return " WHERE ". $this->compare($value); break;
			case "ANDWHEREIN": 
                $list = '';
                foreach($value[1] as $i){
                    $pre = !empty($list) ? ', ' : '';
                    $list .=  $pre . $this->evaluate($i, true);
                }
                return " AND ".  $this->evaluate($value[0])  . ' IN (' . $list . ' )';
            break;
            case "WHEREIN":
                $list = '';
                foreach($value[1] as $i){
                    $pre = !empty($list) ? ', ' : '';
                    $list .=  $pre . $this->evaluate($i, true);
                }
                return " WHERE ". $this->evaluate($value[0])  . ' IN (' . $list . ' )';
            break;
			case "DROP": 	return " DROP TABLE ".$this->evaluate($value[0]); break;
			case "ALTER": 	return " ALTER TABLE ".$this->evaluate($value[0]); break;
			case "OWNERTO": return " OWNER TO ".$this->evaluate($value[0]); break;
			case "CREATE": 	return "-- falta por implementar el CREATE;";  break;
			case "UPDATE": 
				$alias = isset($value[1]) ? $value[1] : '';
				$table = $this->evaluate($value[0]);
				return " UPDATE $table $alias";  
			break;
            case "SET": 
				if(is_array($value[0])){
					foreach($value[0] as $k=>$i){
						$value[0][$k]= "$i=".$this->evaluate($value[1][$k], true);
					}
					$value = implode(", ", $value[0]);
				}else $value = $value[0] . "=" . $this->evaluate($value[1], true);
				return "SET $value ";
			break;
			case "INSERT": 	
				return 	" INSERT INTO ".$this->evaluate($value[0]);
			break;
			case "VALUES": 	
				return " VALUES( ".$this->implode(", ", $value, true). " )";
			break;	
			case "INTO": 	
				return "( ".implode(", ", $value). " )";
			break;
			case "RIGHTJOIN": 	
				$on = isset($value[1]) ? ' ON '.$this->evaluate($value[1]): '';
				$on .= isset($value[2]) ? ' = '.$this->evaluate($value[2]): '';	
				return " RIGHT JOIN ".$this->evaluate($value[0]).$on; 
			break; 
			case "LEFTJOIN": 	
				$on = isset($value[1]) ? ' ON '.$this->evaluate($value[1]): '';
				$on .= isset($value[2]) ? ' = '.$this->evaluate($value[2]): '';
				return " LEFT JOIN ".$this->evaluate($value[0]).$on; 
			break;
			case "INNERJOIN": 	
				$on = isset($value[1]) ? ' ON '.$this->evaluate($value[1]): '';
				$on .= isset($value[2]) ? ' = '.$this->evaluate($value[2]): '';
				return " INNER JOIN ".$this->evaluate($value[0]).$on; 
			break;
            case "FROM":
                $alias = (isset($value[1]) and is_string($value[1])) ? " AS ".$value[1] : '';
                $alias = is_a($value[0], 'Ksike\lql\src\server\Main') ? (!empty($alias) ? $alias : " AS tmp ") : $alias;
                
                return " FROM ".$this->evaluate($value[0]).$alias;
            break;
			case "GROUPBY": 	return " GROUP BY ".$this->evaluate($value[0]); break;
			default: 			return " ".$index." ".$this->evaluate($value[0]); break;
		}
	}
	protected function generate(&$tmp=false, $jobs=false){
		$str = '';
		$end = false;
		if(!$jobs){
			$jobs = $this->jobs;
			$end = true;
		}
		foreach($jobs as $job){
			if(is_array($job)){
				$str .= $this->generate($tmp, $job);
			}else{
				if(isset($tmp[$job])){
					foreach($tmp[$job] as $item)
						$str .= $this->format($job, $item);
					unset ($tmp[$job]);
				}
			}
		}
		if($end){
			foreach($tmp as $k=>$i){
				foreach($i as $item)
					$str .= $this->format($k, $item);
				unset ($tmp[$k]);
			}
		}
		return $str;
	}
}
