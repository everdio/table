<?php
namespace Modules\Table {
    use \Component\Validator;
    final class Insert extends \Component\Validation {
        public function __construct(\Component\Core $table, array $values = NULL) {
            foreach ($table->mapping as $parameter) {                
                if (isset($table->{$parameter}) && (!$table->get($parameter)->hasTypes([Validator\IsString\IsDatetime::TYPE, Validator\IsString\IsDatetime\Timestamp::TYPE]))) {
                    $values[$parameter] = sprintf("`%s`.`%s`.`%s`", $table->database, $table->table, $table->getField($parameter));
                }
            }
            
            parent::__construct(implode(",", $values), array(new Validator\IsString));
        }
    }
}