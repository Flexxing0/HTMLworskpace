<?php
/* Archivo: dao/PalabraDAO.php
 * Data Access Object para la tabla palabras: listado filtrado, sorteo
 * aleatorio por categoria/dificultad y ABM con baja logica siempre (A-15).
 */

require_once __DIR__ . '/../db/conexion.php';
require_once __DIR__ . '/../modelo/Palabra.php';
require_once __DIR__ . '/CategoriaDAO.php';

class PalabraDAO
{
    // Lista palabras activas pertenecientes a categorias activas (con filtros opcionales)
    public static function listarActivas($idCategoria = null, $nivel = null)
    {
        $con = Conexion::conectar();
        $palabras = array();

        $query = 'SELECT p.id, p.palabra, p.longitud, p.id_categoria, p.activa, c.nombre AS nombre_categoria
                  FROM palabras p
                  INNER JOIN categorias c ON c.id = p.id_categoria
                  WHERE p.activa = 1 AND c.activa = 1';

        $tipos = '';
        $valores = array();

        if ($idCategoria !== null && (int) $idCategoria > 0)
        {
            $query .= ' AND p.id_categoria = ?';
            $tipos .= 'i';
            $valores[] = (int) $idCategoria;
        }

        if ($nivel !== null && $nivel !== '')
        {
            $rango = Palabra::getRangoLongitudPorNivel($nivel);
            if ($rango !== null)
            {
                $query .= ' AND p.longitud BETWEEN ? AND ?';
                $tipos .= 'ii';
                $valores[] = $rango[0];
                $valores[] = $rango[1];
            }
        }

        $query .= ' ORDER BY c.nombre ASC, p.palabra ASC';

        $stmt = $con->prepare($query);
        if ($tipos !== '')
        {
            $stmt->bind_param($tipos, ...$valores);
        }
        $stmt->execute();
        $resu = $stmt->get_result();

        while ($regi = $resu->fetch_object())
        {
            $obj = new Palabra();
            $obj->setId($regi->id);
            $obj->setPalabra($regi->palabra);
            $obj->setLongitud($regi->longitud);
            $obj->setIdCategoria($regi->id_categoria);
            $obj->setNombreCategoria($regi->nombre_categoria);
            $obj->setActiva($regi->activa);
            $palabras[] = $obj;
        }

        $resu->free();
        $stmt->close();
        $con->close();

        return $palabras;
    }

    // Busca una palabra activa por su ID
    public static function buscarActivaPorId($id)
    {
        $idInt = (int) $id;
        $con = Conexion::conectar();
        $obj = null;

        $query = 'SELECT p.id, p.palabra, p.longitud, p.id_categoria, p.activa, c.nombre AS nombre_categoria
                  FROM palabras p
                  INNER JOIN categorias c ON c.id = p.id_categoria
                  WHERE p.id = ? AND p.activa = 1 AND c.activa = 1
                  LIMIT 1';
        $stmt = $con->prepare($query);
        $stmt->bind_param('i', $idInt);
        $stmt->execute();
        $resu = $stmt->get_result();

        if ($regi = $resu->fetch_object())
        {
            $obj = new Palabra();
            $obj->setId($regi->id);
            $obj->setPalabra($regi->palabra);
            $obj->setLongitud($regi->longitud);
            $obj->setIdCategoria($regi->id_categoria);
            $obj->setNombreCategoria($regi->nombre_categoria);
            $obj->setActiva($regi->activa);
        }

        $resu->free();
        $stmt->close();
        $con->close();

        return $obj;
    }

    // Selecciona al azar una palabra activa que cumpla categoria y dificultad (RF-07)
    public static function obtenerAleatoria($idCategoria, $nivel)
    {
        $idCat = (int) $idCategoria;
        $rango = Palabra::getRangoLongitudPorNivel($nivel);
        if ($idCat <= 0 || $rango === null)
        {
            return null;
        }

        $con = Conexion::conectar();
        $obj = null;

        $query = 'SELECT p.id, p.palabra, p.longitud, p.id_categoria, p.activa, c.nombre AS nombre_categoria
                  FROM palabras p
                  INNER JOIN categorias c ON c.id = p.id_categoria
                  WHERE p.id_categoria = ?
                    AND p.activa = 1
                    AND c.activa = 1
                    AND p.longitud BETWEEN ? AND ?
                  ORDER BY RAND()
                  LIMIT 1';
        $stmt = $con->prepare($query);
        $stmt->bind_param('iii', $idCat, $rango[0], $rango[1]);
        $stmt->execute();
        $resu = $stmt->get_result();

        if ($regi = $resu->fetch_object())
        {
            $obj = new Palabra();
            $obj->setId($regi->id);
            $obj->setPalabra($regi->palabra);
            $obj->setLongitud($regi->longitud);
            $obj->setIdCategoria($regi->id_categoria);
            $obj->setNombreCategoria($regi->nombre_categoria);
            $obj->setActiva($regi->activa);
        }

        $resu->free();
        $stmt->close();
        $con->close();

        return $obj;
    }

    // Alta de palabra: exige categoria activa, evita duplicados (A-14) y reactiva si estaba inactiva (A-15)
    public static function alta($palabraRaw, $idCategoria)
    {
        $val = Palabra::validarFormato($palabraRaw);
        if (!$val['ok'])
        {
            return $val;
        }

        $palabra = $val['valor'];
        $longitud = $val['longitud'];
        $idCat = (int) $idCategoria;

        if (CategoriaDAO::buscarActivaPorId($idCat) === null)
        {
            return array('ok' => false, 'mensaje' => 'La categoria seleccionada no existe o esta dada de baja.');
        }

        $con = Conexion::conectar();
        try
        {
            $stmtDup = $con->prepare('SELECT id, activa FROM palabras WHERE palabra = ? AND id_categoria = ? LIMIT 1');
            $stmtDup->bind_param('si', $palabra, $idCat);
            $stmtDup->execute();
            $resuDup = $stmtDup->get_result();
            $existente = $resuDup->fetch_object();
            $resuDup->free();
            $stmtDup->close();

            if ($existente)
            {
                if ((int) $existente->activa === 1)
                {
                    $con->close();
                    return array('ok' => false, 'mensaje' => 'Esa palabra ya existe en la categoria seleccionada.');
                }

                // Reactiva palabra dada de baja logica previamente (A-15)
                $idExistente = (int) $existente->id;
                $stmtUpd = $con->prepare('UPDATE palabras SET activa = 1, longitud = ? WHERE id = ?');
                $stmtUpd->bind_param('ii', $longitud, $idExistente);
                $stmtUpd->execute();
                $stmtUpd->close();
                $con->close();

                return array('ok' => true, 'mensaje' => 'La palabra existia dada de baja en esta categoria y fue reactivada.');
            }

            $stmtIns = $con->prepare('INSERT INTO palabras (palabra, longitud, id_categoria, activa) VALUES (?, ?, ?, 1)');
            $stmtIns->bind_param('sii', $palabra, $longitud, $idCat);
            $stmtIns->execute();
            $stmtIns->close();
            $con->close();

            return array('ok' => true, 'mensaje' => 'Palabra agregada correctamente.');
        }
        catch (mysqli_sql_exception $e)
        {
            $con->close();
            return array('ok' => false, 'mensaje' => 'No se pudo guardar la palabra.');
        }
    }

    // Modificacion de una palabra activa (recalcula longitud automaticamente)
    public static function modificar($id, $palabraRaw, $idCategoria)
    {
        $idInt = (int) $id;
        if (self::buscarActivaPorId($idInt) === null)
        {
            return array('ok' => false, 'mensaje' => 'La palabra seleccionada no existe o esta dada de baja.');
        }

        $val = Palabra::validarFormato($palabraRaw);
        if (!$val['ok'])
        {
            return $val;
        }

        $palabra = $val['valor'];
        $longitud = $val['longitud'];
        $idCat = (int) $idCategoria;

        if (CategoriaDAO::buscarActivaPorId($idCat) === null)
        {
            return array('ok' => false, 'mensaje' => 'La categoria seleccionada no existe o esta dada de baja.');
        }

        $con = Conexion::conectar();
        try
        {
            $stmtDup = $con->prepare('SELECT id FROM palabras WHERE palabra = ? AND id_categoria = ? AND id <> ? LIMIT 1');
            $stmtDup->bind_param('sii', $palabra, $idCat, $idInt);
            $stmtDup->execute();
            $resuDup = $stmtDup->get_result();
            $hayDuplicado = ($resuDup->num_rows > 0);
            $resuDup->free();
            $stmtDup->close();

            if ($hayDuplicado)
            {
                $con->close();
                return array('ok' => false, 'mensaje' => 'Ya existe esa palabra en la categoria seleccionada.');
            }

            $stmtUpd = $con->prepare('UPDATE palabras SET palabra = ?, longitud = ?, id_categoria = ? WHERE id = ? AND activa = 1');
            $stmtUpd->bind_param('siii', $palabra, $longitud, $idCat, $idInt);
            $stmtUpd->execute();
            $stmtUpd->close();
            $con->close();

            return array('ok' => true, 'mensaje' => 'Palabra modificada correctamente.');
        }
        catch (mysqli_sql_exception $e)
        {
            $con->close();
            return array('ok' => false, 'mensaje' => 'No se pudo modificar la palabra.');
        }
    }

    // Baja logica de palabra: siempre marca activa = 0, nunca elimina fisicamente (A-15)
    public static function bajaLogica($id)
    {
        $idInt = (int) $id;
        if (self::buscarActivaPorId($idInt) === null)
        {
            return array('ok' => false, 'mensaje' => 'La palabra seleccionada no existe o ya esta dada de baja.');
        }

        $con = Conexion::conectar();
        try
        {
            $stmt = $con->prepare('UPDATE palabras SET activa = 0 WHERE id = ? AND activa = 1');
            $stmt->bind_param('i', $idInt);
            $stmt->execute();
            $stmt->close();
            $con->close();

            return array('ok' => true, 'mensaje' => 'Palabra dada de baja correctamente.');
        }
        catch (mysqli_sql_exception $e)
        {
            $con->close();
            return array('ok' => false, 'mensaje' => 'No se pudo dar de baja la palabra.');
        }
    }
}
?>
