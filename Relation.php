<?php 
namespace Modules\Table {
    use \Components\Validator;
    use \Components\Core\Adapter\Mapper;
    final class Relation extends \Components\Validation {
        public function __construct(Mapper $table, array $tables, string $join = NULL, string $operator = "and", array $joins = []) {
            foreach ($tables as $parentTable) {   
                if (isset($table->parents) && $parentTable instanceof Mapper && in_array((string) $parentTable, $table->parents)){
                    $joins[(string) $parentTable] = $this->_join($parentTable, $table, array_search((string) $parentTable, $table->parents), $join);
                }
            }

            foreach ($tables as $childTable) {
                if ($childTable instanceof Mapper && !array_key_exists((string) $childTable, $joins) && isset($childTable->parents)) {
                    foreach ($tables as $parentTable) {
                        if ($parentTable instanceof Mapper  && in_array((string) $parentTable, $childTable->parents)) {              
                            $joins[(string) $childTable] = $this->_join($childTable, $parentTable, array_search((string) $parentTable, $childTable->parents), $join);
                        }
                    }
                }
            }
            
            foreach ($tables as $table) {
                if ($table instanceof Mapper && array_key_exists((string) $table, $joins) && isset($table->mapping) &&  $table->isNormal($table->mapping)) {
                    $filter = new Filter($table, $operator);
                    if ($filter->isValid()) {
                        $joins[(string) $table] .= "AND" . $filter->execute();
                    }
                }
            }
            
            parent::__construct(implode(PHP_EOL, $joins), [new Validator\IsEmpty, new Validator\IsString\Contains(["JOIN"])]);
        }        
                
        private function _join(\Components\Core\Adapter\Mapper $childTable, \Components\Core\Adapter\Mapper $parentTable, string $key, string $join = NULL) : string {            
            return (string) sprintf("%sJOIN`%s`.`%s`ON`%s`.`%s`.`%s`=`%s`.`%s`.`%s`", $join, $childTable->database, $childTable->table, $childTable->database, $childTable->table, $childTable->getField($childTable->getForeign($key)), $parentTable->database, $parentTable->table, $parentTable->getField($parentTable->getForeign($key)));
        }
    }
}