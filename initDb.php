<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/28 0028
 * Time: 13:37
 */

$db_config = require  "./phinx.php";
$pro_config = $db_config['environments']['production'];
$con = new mysqli($pro_config['host'].':'.$pro_config['port'], $pro_config['user'], $pro_config['pass']);
if (!$con) {
    // die("connect error:".mysqli_connect_error());
} else {
    echo "success connect mysql\n";
}
$sql = "create database  if  not exists {$pro_config['name']} default charset utf8mb4 collate utf8mb4_general_ci";
if ($con->query($sql)) {
    echo  "create default db success";
}
