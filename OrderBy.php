<?php 
namespace Modules\Table {
    use \Components\Validator;
    final class OrderBy extends \Components\Validation {
        public function __construct(\Components\Core $table, array $orderby, array $values = []) {
            foreach ($orderby as $order => $parameters) {
                foreach ($table->inter($parameters) as $parameter) {
                    $values[] = sprintf("`%s`.`%s`.`%s` %s", $table->database, $table->table, $table->getField($parameter), strtoupper($order));
                }
            }
            
            parent::__construct("ORDER BY" . implode(",", $values), array(new Validator\IsString\Contains(["DESC","ASC"])), self::STRICT);
        }
    }
}