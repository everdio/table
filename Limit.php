<?php 
namespace Modules\Table {
    use \Components\Validator;
    final class Limit extends \Components\Validation {
        public function __construct(int $position, int $limit) {
            parent::__construct(sprintf(" LIMIT %s,%s", $position, $limit),[new Validator\IsString]);
        }
    }
}