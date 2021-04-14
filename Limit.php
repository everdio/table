<?php 
namespace Modules\Table {
    use \Component\Validator;
    final class Limit extends \Component\Validation {
        public function __construct(int $position, int $limit) {
            parent::__construct(sprintf(" LIMIT %s,%s", $position, $limit),[new Validator\IsString]);
        }
    }
}