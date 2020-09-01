<?php
namespace Modules\Table {
    use \Components\Validator;
    final class Values extends \Components\Validation {
        public function __construct(\Components\Core $table, array $values = []) {
            foreach ($table->mapping as $parameter) {
                if (isset($table->{$parameter}) && !($table->get($parameter)->hasType(Validator\IsString\IsDatetime::TYPE) || $table->get($parameter)->hasType(Validator\IsString\IsDatetime\Timestamp::TYPE))) {
                    if ($table->get($parameter)->hasType(Validator\IsEmpty::TYPE) && empty($table->{$parameter})) {
                        $values[$parameter] = "NULL";
                    } elseif ($table->get($parameter)->hasType(Validator\IsInteger::TYPE) || $table->get($parameter)->hasType(Validator\IsNumeric::TYPE)) {
                        $values[$parameter] = $table->{$parameter};
                    } elseif ($table->get($parameter)->hasType(Validator\IsString::TYPE) || $table->get($parameter)->hasType(Validator\IsString\IsDateTime\IsDate::TYPE)) {
                        $values[$parameter] = sprintf("'%s'", addslashes($table->{$parameter}));
                    } elseif ($table->get($parameter)->hasType(Validator\IsArray::TYPE)) {
                        $values[$parameter] = sprintf("'%s'", implode(",", $table->{$parameter}));
                    }
                }
            }

            parent::__construct(implode(",", $values), array(new Validator\IsString));
        }
    }
}