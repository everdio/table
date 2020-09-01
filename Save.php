<?php
namespace Modules\Table {
    use \Components\Validator;
    final class Save extends \Components\Validation {
        public function __construct(\COmponents\Core  $table) {
            $insert = new Insert($table);
            $values = new Values($table);
            $update = new Update($table);            
            parent::__construct(sprintf("INSERT INTO`%s`.`%s`(%s)VALUES(%s)ON DUPLICATE KEY UPDATE%s", $table->database, $table->table, $insert->execute(), $values->execute(), $update->execute()), [new Validator\IsString]);
        }
    }
}

