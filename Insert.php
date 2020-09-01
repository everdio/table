<?php
namespace Modules\Table {
    use \Components\Validator;
    final class Insert extends \Components\Validation {
        public function __construct(\Components\Core $table, array $values = NULL) {
            foreach ($table->mapping as $parameter) {                
                if (isset($table->{$parameter}) && (!$table->get($parameter)->hasType(Validator\IsString\IsDatetime::TYPE) || $table->get($parameter)->hasType(Validator\IsString\IsDatetime\Timestamp::TYPE))) {
                    $values[$parameter] = sprintf("`%s`.`%s`.`%s`", $table->database, $table->table, $table->getField($parameter));
                }
            }
            
            parent::__construct(implode(",", $values), array(new Validator\IsString));
        }
    }
}