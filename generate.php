<?php

echo (string) sprintf("Library %s", sprintf($this->model["namespace"], $this->labelize($this->pdo["database"]))) . PHP_EOL;    
ob_flush();   

$pdo = new \PDO($this->pdo["dsn"], $this->pdo["username"], $this->pdo["password"]);

$stm = $pdo->prepare(sprintf("SHOW TABLES FROM`%s`", $this->pdo["database"]));
$stm->execute();    

foreach ($stm->fetchAll(\PDO::FETCH_COLUMN) as $table) {
    $model = new \Modules\Table\Model;
    $model->label = $this->labelize($table);
    $model->class = $this->labelize($table);
    $model->namespace = $this->model["namespace"];
    $model->dsn = $this->pdo["dsn"];
    $model->username = $this->pdo["username"];
    $model->password = $this->pdo["password"];
    $model->database = $this->pdo["database"];
    $model->table = $table;
    $model->use = "\Modules\Table\Pdo";
    $model->setup();
    echo (string) sprintf("%s\%s", $model->namespace, $model->class) . PHP_EOL;    
    ob_flush();   
}