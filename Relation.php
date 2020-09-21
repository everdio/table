<?php 
namespace Modules\Table {
    use \Components\Validator;
    final class Relation extends \Components\Validation {
        public function __construct($table, array $tables, string $join = NULL, array $joins = []) {             
            foreach ($tables as $thatTable) {
                if ($thatTable instanceof \Components\Core\Mapper && (isset($table->relations) && in_array((string) $thatTable, $table->relations)) && (isset($table->keys) && array_key_exists(array_search((string) $thatTable, $table->relations), $table->keys))) {              
                    $joins[(string) $thatTable] = $this->_join($thatTable, $table, $table->keys[array_search((string) $thatTable, $table->relations)], $join);
                }
            }
            
            
            
            foreach ($tables as $thatTable) {
                if ($thatTable instanceof \Components\Core\Mapper) {              
                    foreach ($tables as $thisTable) {
                       if ($thisTable instanceof \Components\Core\Mapper && !array_key_exists((string) $thisTable, $joins) && (isset($thisTable->relations) && in_array((string) $thatTable, $thisTable->relations)) && (isset($thisTable->keys) && array_key_exists(array_search((string) $thatTable, $thisTable->relations), $thisTable->keys))) {
                           $joins[(string) $thisTable] = $this->_join($thisTable, $thatTable, $thisTable->keys[array_search((string) $thatTable, $thisTable->relations)], $join);
                       }                    
                    }
                }
            }
            
            parent::__construct(implode(PHP_EOL, $joins), [new Validator\IsEmpty, new Validator\IsString\Contains(["JOIN"])]);
        }        
                
        private function _join(\Components\Core\Mapper $thisTable, \Components\Core\Mapper $thatTable, string $parameter, string $join = NULL) : string {            
            return (string) sprintf("%sJOIN`%s`.`%s`ON`%s`.`%s`.`%s`=`%s`.`%s`.`%s`", $join, $thisTable->database, $thisTable->table, $thisTable->database, $thisTable->table, $thisTable->getField($parameter), $thatTable->database, $thatTable->table, $thisTable->getField($parameter));
        }
    }
}