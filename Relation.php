<?php 
namespace Modules\Table {
    use \Component\Validator;
    use \Component\Core\Adapter\Mapper;
    final class Relation extends \Component\Validation {
        public function __construct(Mapper $onTable, array $tables, string $join = NULL, string $operator = "AND", array $joins = []) {
            foreach ($tables as $joinTable) {   
                if ($joinTable instanceof Mapper && !$onTable instanceof $joinTable) {
                    foreach ($onTable->keys as $onKey => $joinKey) {
                        if (isset($joinTable->primary) && in_array($joinKey, $joinTable->primary)) {
                            $filter = new Filter($joinTable, $operator);
                            $joins[] = sprintf("%s JOIN`%s`.`%s`ON`%s`.`%s`.`%s`=`%s`.`%s`.`%s`", strtoupper((!$join && isset($onTable->get($onKey)->IS_EMPTY) ? "LEFT" : $join)), $joinTable->database, $joinTable->table, $joinTable->database, $joinTable->table, $joinTable->getField($onTable->getKey($onKey)), $onTable->database, $onTable->table, $onTable->getField($onKey)) . ($filter->isValid() ? strtoupper($operator) . $filter->execute() : false);
                        }
                    }
                }
            }
      
            parent::__construct(implode(PHP_EOL, $joins), [new Validator\IsEmpty, new Validator\IsString\Contains(["JOIN"])]);
        }        
                
        private function _join(Mapper $joinTable, Mapper $onTable, string $parameter, string $join = NULL) : string {       
            return (string) sprintf("%s JOIN`%s`.`%s`ON`%s`.`%s`.`%s`=`%s`.`%s`.`%s`", strtoupper($join), $joinTable->database, $joinTable->table, $joinTable->database, $joinTable->table, $joinTable->getField($onTable->getKey($parameter)), $onTable->database, $onTable->table, $onTable->getField($parameter));
        }
    }
}