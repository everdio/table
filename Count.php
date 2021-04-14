<?php 
namespace Modules\Table {
    use \Component\Validator\IsString;
    final class Count extends \Component\Validation {
        public function __construct() {
            parent::__construct("SELECT count(*) ", [new IsString]);
        }
    }
}