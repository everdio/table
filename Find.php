<?php
namespace Modules\Table {
    final class Find extends \Components\Validation {
        public function __construct(array $validations = [], string $select = NULL, string $from = NULL, array $relations = [], array $operators = [], string $orderby = NULL, string $query = NULL) {                  
            foreach ($validations as $validation) {
                if ($validation instanceof \Components\Validation && $validation->isValid()) {
                    if ($validation instanceof Select || $validation instanceof Count) {
                        $select = $validation->execute();
                    } elseif ($validation instanceof From) {
                        $from = $validation->execute();
                    } elseif ($validation instanceof Filter) {
                        $operators[] = $validation->execute();
                    } elseif ($validation instanceof Relation) {
                        $relations[] = $validation->execute();
                    } elseif ($validation instanceof OrderBy) {
                        $orderby = $validation->execute();
                    } else { 
                        $query .= $validation->execute();
                    }
                }
            }
            
            parent::__construct($select . $from . implode(false, $relations) . (sizeof($operators) ? sprintf("WHERE%s", implode(false, $operators)) : false) . $orderby . $query, [new \Components\Validator\IsString\Contains(["SELECT", "FROM"])]);
        }
    }
}

