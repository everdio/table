<?php
namespace Modules\Table {
    use \Component\Validator;
    final class Values extends \Component\Validation {
        public function __construct(\Component\Core $table, array $values = []) {
            foreach ($table->mapping as $parameter) {
                if (isset($table->{$parameter}) && !($table->get($parameter)->hasTypes([Validator\IsString\IsDatetime::TYPE, Validator\IsString\IsDatetime\Timestamp::TYPE]))) {
                    if ($table->get($parameter)->hasTypes([Validator\IsEmpty::TYPE]) && empty($table->{$parameter}) && $table->{$parameter} !== 0) {
                        $values[$parameter] = "NULL";
                    } elseif ($table->get($parameter)->hasTypes([Validator\IsInteger::TYPE, Validator\IsNumeric::TYPE])) {
                        $values[$parameter] = $table->{$parameter};
                    } elseif ($table->get($parameter)->hasTypes([Validator\IsDefault::TYPE, Validator\IsString::TYPE, Validator\IsString\IsDateTime\IsDate::TYPE])) {
                        $values[$parameter] = sprintf("'%s'", $this->sanitize($table->{$parameter}));
                    } elseif ($table->get($parameter)->hasTypes([Validator\IsArray::TYPE])) {
                        $values[$parameter] = sprintf("'%s'", implode(",", $table->{$parameter}));
                    } 
                }
            }

            parent::__construct(implode(",", $values), array(new Validator\IsDefault));
        }
    }
}