<?php
/* Archivo: clases/Conexion.class.php
 * Centraliza la conexion MySQLi orientada a objetos usando config/config.php.
 */

require_once __DIR__ . '/../config/config.php';

class Conexion
{
    public static function conectar()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try
        {
            $con = new mysqli(BD_HOST, BD_USUARIO, BD_PASSWORD, BD_NOMBRE, BD_PUERTO);
            $con->set_charset('utf8mb4');
            $con->query("SET time_zone = '-03:00'");
            return $con;
        }
        catch (mysqli_sql_exception $e)
        {
            // No se exponen credenciales ni detalles tecnicos al cliente (Unidad 7)
            throw new RuntimeException('No se pudo establecer la conexion con la base de datos.');
        }
    }
}
?>
