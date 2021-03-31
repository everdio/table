<?php 
namespace Modules\Table {
    use \Components\Validator;
    use \Components\Core\Adapter\Mapper;
    final class Relation extends \Components\Validation {
        public function __construct(Mapper $table, array $tables, string $join = NULL, string $operator = "AND", array $joins = []) {

            foreach ($tables as $parentTable) {   
                if ($parentTable instanceof Mapper && !$table instanceof $parentTable) {
                    $filter = new Filter($parentTable, $operator);
                    $joins[] = $this->_join($parentTable, $table, array_search((string) $parentTable, $table->parents), $join) . ($filter->isValid() ? strtoupper($operator) . $filter->execute() : false);
                }
            }

            foreach ($tables as $childTable) {
                if ($childTable instanceof Mapper) {
                    foreach ($tables as $parentTable) {
                        if ($parentTable instanceof Mapper && !$childTable instanceof $parentTable && $childTable->isParent($parentTable)) {              
                            //$joins[(string) $childTable] = $this->_join($childTable, $parentTable, array_search((string) $parentTable, $childTable->parents), $join);
                        }
                    }
                }
            }

            parent::__construct(implode(PHP_EOL, $joins), [new Validator\IsEmpty, new Validator\IsString\Contains(["JOIN"])]);
        }        
                
        private function _join(\Components\Core\Adapter\Mapper $joinTable, \Components\Core\Adapter\Mapper $onTable, string $parameter, string $join = NULL) : string {       
            return (string) sprintf("%sJOIN`%s`.`%s`ON`%s`.`%s`.`%s`=`%s`.`%s`.`%s`", $join, $joinTable->database, $joinTable->table, $joinTable->database, $joinTable->table, $joinTable->getField($onTable->getKey($parameter)), $onTable->database, $onTable->table, $onTable->getField($parameter));
        }
    }
}