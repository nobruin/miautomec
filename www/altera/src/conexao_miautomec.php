<?php
/**
 * Created by Marluce Almeida
 * Date: 08/06/2015
 * Time: 16:35
 */
try {
    $conn = ibase_connect(getenv('DB_PATH'),getenv('DB_USER'), getenv('DB_PASSWORD'));
    //$conn = ibase_connect('192.168.43.121:C:\DB\MIAUTOMEC.FDB','SYSDBA','masterkey');
} catch (\Throwable $th) {
    echo $th;
}

?>