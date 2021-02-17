<?php 
namespace Modules\Table {
    use \Components\Validator;
    final class Select extends \Components\Validation {
        public function __construct(array $tables, array $select = []) {
            foreach ($tables as $table) {               
                if ($table instanceof \Components\Core\Adapter\Mapper && isset($table->mapping)) {
                    foreach ($table->inter($table->mapping) as $parameter) {
                        $select[$parameter] = sprintf("%sAS`%s`",(substr($table->getField($parameter), 0, 1) == '@' ? $table->getField($parameter) : sprintf("`%s`.`%s`.`%s`", $table->database, $table->table, $table->getField($parameter))), $parameter);
                    }
                }
            }                
                        
            parent::__construct(sprintf("SELECT%s", implode(",", $select)), [new Validator\IsString]);
        }
    }
}