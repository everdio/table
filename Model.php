<?php
namespace Modules\Table {
    use Components\Validation;
    use Components\Validator;
    final class Model extends \Components\Core\Mapper\Model {
        use \Modules\Table\Pdo;
        public function __construct() {
            parent::__construct([
                "dsn" => new Validation(false, array(new Validator\IsString)),
                "username" => new Validation(false, array(new Validator\IsString)),
                "password" => new Validation(false, array(new Validator\IsString)),
                "database" => new Validation(false, array(new Validator\IsString)),
                "table" => new Validation(false, array(new Validator\IsString)),
                "mapping" => new Validation(false, array(new Validator\IsArray)),
                "primary" => new Validation(false, array(new Validator\IsArray)),
                "keys" => new Validation(false, array(new Validator\IsArray)),
                "relations" => new Validation(false, array(new Validator\IsArray))
            ]);
            
            $this->use = "\Modules\Table\Pdo";
        }
        
        public function setup() {
            $columns = $this->prepare(sprintf("SELECT * FROM`information_schema`.`COLUMNS`WHERE`information_schema`.`COLUMNS`.`TABLE_SCHEMA`='%s'AND`information_schema`.`COLUMNS`.`TABLE_NAME`='%s' ORDER BY `ORDINAL_POSITION` ASC", $this->database, $this->table));
            $columns->execute();
            foreach ($columns->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $sample = $this->prepare(sprintf("SELECT `%s` FROM `%s`.`%s`WHERE`%s` <> '' LIMIT 1", $row["COLUMN_NAME"], $this->database, $this->table, $row["COLUMN_NAME"]));
                $value = $this->hydrate(($sample && $sample->execute() ? $sample->fetchColumn() : $row["COLUMN_DEFAULT"]));                
               
                switch ($row["DATA_TYPE"]) {
                    case "char":
                    case "longtext":
                    case "mediumtext":
                    case "tinytext":
                    case "text":
                    case "varchar":                        
                    case "bigint":
                    case "tinyint":
                    case "mediumint":
                    case "smallint":                            
                    case "decimal":
                    case "int":
                        $parameter = new Validation\Parameter($this->labelize($row["COLUMN_NAME"]), (!empty($row["COLUMN_DEFAULT"]) ? $this->hydrate($row["COLUMN_DEFAULT"]) : $value), !empty($row["COLUMN_DEFAULT"]), ($row["IS_NULLABLE"] === "YES" ? false : true), $row["CHARACTER_MAXIMUM_LENGTH"]);     
                        break;
                    case "enum":
                        $parameter = new Validation\Parameter($this->labelize($row["COLUMN_NAME"]), (!empty($row["COLUMN_DEFAULT"]) ? $this->hydrate($row["COLUMN_DEFAULT"]) : false), !empty($row["COLUMN_DEFAULT"]), ($row["IS_NULLABLE"] === "YES" ? false : true), NULL, explode(",", str_replace("'", false, trim($row["COLUMN_TYPE"], "enum()"))));
                        break;
                    case "set":
                        $parameter = new Validation\Parameter($this->labelize($row["COLUMN_NAME"]), explode(",", $row["COLUMN_DEFAULT"]), $row["COLUMN_DEFAULT"], ($row["IS_NULLABLE"] === "YES" ? false : true), NULL, explode(",", str_replace("'", false, trim($row["COLUMN_TYPE"], "set()"))));
                        break;
                    case "date":
                        $parameter = new Validation\Parameter($this->labelize($row["COLUMN_NAME"]), date("Y-m-d"), NULL, ($row["IS_NULLABLE"] === "YES" ? false : true));
                        break;
                    case "datetime":
                    case "timestamp":
                        $parameter = new Validation\Parameter($this->labelize($row["COLUMN_NAME"]), date("Y-m-d H:i:s"), NULL, ($row["IS_NULLABLE"] === "YES" ? false : true));
                        break;
                    default:
                        throw new Event(sprintf("unknown column type %s for `%s`.`%s`", $row["DATA_TYPE"], $this->table, $row["COLUMN_NAME"]));
                }
                                
                $this->add((string) $parameter, $parameter->getValidation($parameter->getValidators()));
                $this->mapping = [$row["COLUMN_NAME"] => (string) $parameter];
            }                    
            
                           
            $keys = $this->prepare(sprintf("SELECT * FROM`information_schema`.`KEY_COLUMN_USAGE`WHERE`information_schema`.`KEY_COLUMN_USAGE`.`TABLE_SCHEMA`='%s'AND`information_schema`.`KEY_COLUMN_USAGE`.`TABLE_NAME`='%s'", $this->database, $this->table));
            $keys->execute();
            foreach ($keys->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                switch($row["CONSTRAINT_NAME"]) {
                    case "PRIMARY":
                        $this->primary = [$row["COLUMN_NAME"] => $this->mapping[$row["COLUMN_NAME"]]];
                        break;
                    default:
                        $this->keys = [$this->labelize($row["CONSTRAINT_NAME"]) => $this->mapping[$row["COLUMN_NAME"]]];
                }
            }
            
            $foreign = $this->prepare(sprintf("SELECT * FROM`information_schema`.`KEY_COLUMN_USAGE`WHERE`information_schema`.`KEY_COLUMN_USAGE`.`TABLE_SCHEMA`='%s'AND`information_schema`.`KEY_COLUMN_USAGE`.`TABLE_NAME`='%s'", $this->database, $this->table));
            $foreign->execute();
            foreach($foreign->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                if ($row["REFERENCED_COLUMN_NAME"]) {
                    $this->relations = [$this->labelize($row["CONSTRAINT_NAME"]) => $this->namespace . "\\" . $this->labelize($row["REFERENCED_TABLE_NAME"])];
                }
            }
            
            $many = $this->prepare(sprintf("SELECT * FROM`information_schema`.`KEY_COLUMN_USAGE`WHERE`information_schema`.`KEY_COLUMN_USAGE`.`TABLE_SCHEMA`='%s'AND`information_schema`.`KEY_COLUMN_USAGE`.`REFERENCED_TABLE_NAME`='%s'", $this->database, $this->table));
            $many->execute();           
            foreach($many->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $this->relations = [$this->labelize($row["CONSTRAINT_NAME"]) => $this->namespace . "\\" . $this->labelize($row["TABLE_NAME"])];
                $this->keys = [$this->labelize($row["CONSTRAINT_NAME"]) => $this->mapping[$row["COLUMN_NAME"]]];
            } 
        }
    }
}