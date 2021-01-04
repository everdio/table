<?php 
namespace Modules\Table {
    use \Components\Validator\IsString;
    final class Count extends \Components\Validation {
        public function __construct() {
            parent::__construct("SELECT count(*) ", [new IsString]);
        }
    }
}