<?php
namespace Modules\Table {
    use \Components\Validator;
    final class Filter extends \Components\Validation {
        public function __construct(\Components\Core $table, string $operator = "and", string $expression = "=", array $operators = []) {
            if (isset($table->primary) && $table->isNormal($table->primary)) {
                foreach($table->restore($table->primary) as $parameter => $value) {
                    $operators[] = sprintf("`%s`.`%s`.`%s`%s %s ", $table->database, $table->table, $table->getField($parameter),  $expression, $value);
                }
            } elseif (isset($table->mapping) && $table->isNormal($table->mapping)) {
                foreach ($table->restore($table->mapping) as $parameter => $value) {
                    if (!empty($value) || !$table->get($parameter)->hasType(Validator\IsEmpty::TYPE)) {
                        if ($table->get($parameter)->hasType(Validator\IsInteger::TYPE) || $table->get($parameter)->hasType(Validator\IsNumeric::TYPE)) {
                            $operators[] = sprintf("`%s`.`%s`.`%s`%s %s ", $table->database, $table->table, $table->getField($parameter), $expression, $value);
                        } elseif ($table->get($parameter)->hasType(Validator\IsDefault::TYPE) || $table->get($parameter)->hasType(Validator\IsString::TYPE) || $table->get($parameter)->hasType(Validator\IsString\IsDateTime\IsDate::TYPE)) {
                            $operators[] = sprintf("`%s`.`%s`.`%s`%s '%s'", $table->database, $table->table, $table->getField($parameter), $expression, $this->sanitize($value));
                        } elseif ($table->get($parameter)->hasType(Validator\IsArray::TYPE)) { 
                            $operators[] = sprintf(" FIND_IN_SET('%s',`%s`.`%s`.`%s`)", implode(",", $value), $table->database, $table->table, $table->getField($parameter));
                        }
                    }
                }               
            }
            
            parent::__construct(implode(strtoupper($operator), $operators), [new Validator\IsString]);
        }
    }
}

