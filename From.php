<?php 
namespace Modules\Table {
    use \Components\Validator;
    final class From extends \Components\Validation {
        public function __construct(array $mappers, array $from = []) {
            foreach ($mappers as $mapper) {               
                if ($mapper instanceof \Components\Core) {
                    $from[] = sprintf("`%s`.`%s`", $mapper->database, $mapper->table);
                }
            }                
            
            parent::__construct(sprintf("FROM%s", implode(",", $from)), [new Validator\IsString]);
        }
    }
}