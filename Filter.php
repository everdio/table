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
                    if (!empty($value) || !$table->get($parameter)->hasTypes([Validator\IsEmpty::TYPE])) {
                        if (substr($table->getField($parameter), 0, 1) == '@') {
                            $expression = sprintf(":%s", $expression);
                            $column = $table->getField($parameter);
                        } else {
                            $column = sprintf("`%s`.`%s`.`%s`", $table->database, $table->table, $table->getField($parameter));    
                        }
                        
                        if ($table->get($parameter)->hasTypes([Validator\IsInteger::TYPE, Validator\IsNumeric::TYPE])) {
                            $operators[] = sprintf("%s %s %s ", $column, $expression, $value);
                        } elseif ($table->get($parameter)->hasTypes([Validator\IsDefault::TYPE, Validator\IsString::TYPE, Validator\IsString\IsDateTime\IsDate::TYPE])) {
                            $operators[] = sprintf("%s %s '%s'", $column, $expression, $this->sanitize($value));
                        } elseif ($table->get($parameter)->hasTypes([Validator\IsArray::TYPE])) { 
                            $operators[] = sprintf(" FIND_IN_SET('%s', %s)", implode(",", $value), $column);
                        }
                    }
                }               
            }
            
            parent::__construct(implode(strtoupper($operator), $operators), [new Validator\IsString]);
        }
    }
}

