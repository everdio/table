<?php 
namespace Modules\Table {
    use \Components\Validator;
    final class Select extends \Components\Validation {
        public function __construct(array $mappers, array $select = []) {
            foreach ($mappers as $mapper) {               
                if ($mapper instanceof \Components\Core\Mapper && isset($mapper->mapping)) {
                    foreach ($mapper->inter($mapper->mapping) as $parameter) {
                        $select[$parameter] = sprintf("`%s`.`%s`.`%s`AS`%s`", $mapper->database, $mapper->table, $mapper->getField($parameter), $parameter);
                    }
                }
            }                
                        
            parent::__construct(sprintf("SELECT%s", implode(",", $select)), [new Validator\IsString]);
        }
    }
}