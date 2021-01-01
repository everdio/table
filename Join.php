<?php
namespace Modules\Table {
    class Join extends \Components\Validation {
        public function __construct(\Components\Core $table, string $key, string $join = NULL) {            
            parent::__construct(sprintf("%sJOIN`%s`.`%s`ON`%s`.`%s`.`%s`=`%s`.`%s`.`%s`", $join, $table->parents[$key]::construct()->database, $table->parents[$key]::construct()->table, $table->database, $table->table,  $table->getField($table->keys[$key]), $table->database, $table->parents[$key]::construct()->table, $table->getField($table->keys[$key])), [new \Components\Validator\IsString]);            
        }
    }
}
