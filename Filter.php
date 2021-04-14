<?php
namespace Modules\Table {
    use \Component\Validator;
    final class Filter extends \Component\Validation {
        public function __construct(\Component\Core $table, string $operator = "and", string $expression = "=", array $operators = []) {
            if (isset($table->primary) && $table->isNormal($table->primary)) {
                foreach($table->restore($table->primary) as $parameter => $value) {
                    if (isset($table->get($parameter)->IS_NUMERIC)){
                        $operators[] = sprintf("`%s`.`%s`.`%s`%s %s ", $table->database, $table->table, $table->getField($parameter), $expression, $value);
                    } else {
                        $operators[] = sprintf("`%s`.`%s`.`%s`%s '%s' ", $table->database, $table->table, $table->getField($parameter), $expression, $value);
                    }
                }
            }
            
            if (isset($table->mapping) && $table->isNormal($table->mapping)) {
                foreach ($table->restore($table->mapping) as $parameter => $value) {
                    if (!empty($value) || !isset($table->get($parameter)->IS_EMPTY)) {
                        if (substr($table->getField($parameter), 0, 1) == '@') {
                            $expression = sprintf(":%s", $expression);
                            $column = $table->getField($parameter);
                        } else {
                            $column = sprintf("`%s`.`%s`.`%s`", $table->database, $table->table, $table->getField($parameter));    
                        }
                        if (isset($table->get($parameter)->IS_NUMERIC)) {
                            $operators[] = sprintf("%s %s %s ", $column, $expression, $value);
                        } elseif (isset($table->get($parameter)->IS_STRING) || isset($table->get($parameter)->IS_DEFAULT) || isset($table->get($parameter)->IS_DATE)) { 
                            $operators[] = sprintf("%s %s '%s'", $column, $expression, $this->sanitize($value));                            
                        } elseif (isset($table->get($parameter)->IS_ARRAY)) {
                            $operators[] = sprintf(" CONCAT(\",\", %s, \",\") REGEXP \",(%s),\"", $column, implode("|", $value));
                        }
                    }
                }               
            }
            
            parent::__construct(implode(strtoupper($operator), $operators), [new Validator\IsString]);
        }
    }
}

