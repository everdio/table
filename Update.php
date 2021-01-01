<?php
namespace Modules\Table {
    use \Components\Validator;
    final class Update extends \Components\Validation {
        public function __construct(\Components\Core $table, array $values = []) {
            foreach ($table->mapping as $parameter) {
                if (isset($table->{$parameter}) && !($table->get($parameter)->hasTypes([Validator\IsString\IsDatetime::TYPE, Validator\IsString\IsDatetime\Timestamp::TYPE]))) {
                    if ($table->get($parameter)->hasTypes([Validator\IsEmpty::TYPE]) && empty($table->{$parameter}) && $table->{$parameter} !== 0) {
                        $values[$parameter] = sprintf("`%s`.`%s`.`%s`=%s ", $table->database, $table->table, $table->getField($parameter), "NULL");
                    } elseif ($table->get($parameter)->hasTypes([Validator\IsInteger::TYPE, Validator\IsNumeric::TYPE])) {
                        $values[$parameter] = sprintf("`%s`.`%s`.`%s`=%s ", $table->database, $table->table, $table->getField($parameter), $table->{$parameter});
                    } elseif ($table->get($parameter)->hasTypes([Validator\IsDefault::TYPE, Validator\IsString::TYPE, Validator\IsString\IsDateTime\IsDate::TYPE])) {
                        $values[$parameter] = sprintf("`%s`.`%s`.`%s`='%s'",$table->database, $table->table, $table->getField($parameter), $this->sanitize($table->{$parameter}));
                    } elseif ($table->get($parameter)->hasTypes([Validator\IsArray::TYPE])) {
                        $values[$parameter] = sprintf("`%s`.`%s`.`%s`='%s'", $table->database, $table->table, $table->getField($parameter), implode(",", $table->{$parameter}));
                    }                    
                }
            }
     
            parent::__construct(implode(",", $values), array(new Validator\IsString));
        }

    }
}