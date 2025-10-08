<?php
$serverName = "localhost\SQLEXPRESS";
$connectionOptions = array(
    "Database" => "GestionEventos",
    "Uid" => "superuser",
    "PWD" => "12345"
);
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn) {
    echo "Conexion exitosa a SQL Server";
} else {
    echo "Error de conexion.<br>";
    die(print_r(sqlsrv_errors(), true));
}
?>