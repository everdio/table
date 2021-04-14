<?php
namespace Modules\Table {
    class Join extends \Component\Validation {
        public function __construct(\Component\Core $table, string $key, string $join = NULL) {            
            parent::__construct(sprintf("%sJOIN`%s`.`%s`ON`%s`.`%s`.`%s`=`%s`.`%s`.`%s`", $join, $table->parents[$key]::construct()->database, $table->parents[$key]::construct()->table, $table->database, $table->table,  $table->getField($table->keys[$key]), $table->database, $table->parents[$key]::construct()->table, $table->getField($table->keys[$key])), [new \Component\Validator\IsString]);            
        }
    }
}
