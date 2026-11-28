<?php

require_once __DIR__ . '/../db/conexion.php';
require_once __DIR__ . '/../modelo/Categoria.php';

class CategoriaDAO
{

    public static function listarActivas()
    {
        $con = Conexion::conectar();
        $categorias = array();

        $query = 'SELECT c.id, c.nombre, c.activa, COUNT(p.id) AS cantidad_palabras
                  FROM categorias c
                  LEFT JOIN palabras p ON p.id_categoria = c.id AND p.activa = 1
                  WHERE c.activa = 1
                  GROUP BY c.id, c.nombre, c.activa
                  ORDER BY c.nombre ASC';
        $resu = $con->query($query);
        while ($regi = $resu->fetch_object())
        {
            $cat = new Categoria();
            $cat->setId($regi->id);
            $cat->setNombre($regi->nombre);
            $cat->setActiva($regi->activa);
            $cat->setCantidadPalabrasActivas($regi->cantidad_palabras);
            $categorias[] = $cat;
        }

        $resu->free();
        $con->close();

        return $categorias;
    }

    public static function buscarActivaPorId($id)
    {
        $idInt = (int) $id;
        $con = Conexion::conectar();
        $categoria = null;

        $query = 'SELECT id, nombre, activa FROM categorias WHERE id = ? AND activa = 1 LIMIT 1';
        $stmt = $con->prepare($query);
        $stmt->bind_param('i', $idInt);
        $stmt->execute();
        $resu = $stmt->get_result();

        if ($regi = $resu->fetch_object())
        {
            $categoria = new Categoria();
            $categoria->setId($regi->id);
            $categoria->setNombre($regi->nombre);
            $categoria->setActiva($regi->activa);
        }

        $resu->free();
        $stmt->close();
        $con->close();

        return $categoria;
    }

    public static function contarPalabrasActivas($idCategoria)
    {
        $idInt = (int) $idCategoria;
        $con = Conexion::conectar();

        $query = 'SELECT COUNT(*) AS total FROM palabras WHERE id_categoria = ? AND activa = 1';
        $stmt = $con->prepare($query);
        $stmt->bind_param('i', $idInt);
        $stmt->execute();
        $resu = $stmt->get_result();
        $regi = $resu->fetch_object();
        $total = $regi ? (int) $regi->total : 0;

        $resu->free();
        $stmt->close();
        $con->close();

        return $total;
    }

    public static function alta($nombreRaw)
    {
        $val = Categoria::validarNombre($nombreRaw);
        if (!$val['ok'])
        {
            return $val;
        }
        $nombre = $val['valor'];

        $con = Conexion::conectar();
        try
        {
            $query = 'SELECT id, activa FROM categorias WHERE nombre = ? LIMIT 1';
            $stmt = $con->prepare($query);
            $stmt->bind_param('s', $nombre);
            $stmt->execute();
            $resu = $stmt->get_result();
            $existente = $resu->fetch_object();
            $resu->free();
            $stmt->close();

            if ($existente)
            {
                if ((int) $existente->activa === 1)
                {
                    $con->close();
                    return array('ok' => false, 'mensaje' => 'Ya existe una categoria activa con ese nombre.');
                }

                $idExistente = (int) $existente->id;
                $stmtUpd = $con->prepare('UPDATE categorias SET activa = 1, nombre = ? WHERE id = ?');
                $stmtUpd->bind_param('si', $nombre, $idExistente);
                $stmtUpd->execute();
                $stmtUpd->close();
                $con->close();

                return array('ok' => true, 'mensaje' => 'La categoria existia dada de baja y fue reactivada correctamente.');
            }

            $stmtIns = $con->prepare('INSERT INTO categorias (nombre, activa) VALUES (?, 1)');
            $stmtIns->bind_param('s', $nombre);
            $stmtIns->execute();
            $stmtIns->close();
            $con->close();

            return array('ok' => true, 'mensaje' => 'Categoria agregada correctamente.');
        }
        catch (mysqli_sql_exception $e)
        {
            $con->close();
            return array('ok' => false, 'mensaje' => 'No se pudo guardar la categoria.');
        }
    }

    public static function modificar($id, $nombreRaw)
    {
        $idInt = (int) $id;
        $val = Categoria::validarNombre($nombreRaw);
        if (!$val['ok'])
        {
            return $val;
        }
        $nombre = $val['valor'];

        if (self::buscarActivaPorId($idInt) === null)
        {
            return array('ok' => false, 'mensaje' => 'La categoria seleccionada no existe o esta dada de baja.');
        }

        $con = Conexion::conectar();
        try
        {
            $stmtDup = $con->prepare('SELECT id FROM categorias WHERE nombre = ? AND id <> ? LIMIT 1');
            $stmtDup->bind_param('si', $nombre, $idInt);
            $stmtDup->execute();
            $resuDup = $stmtDup->get_result();
            $hayDuplicado = ($resuDup->num_rows > 0);
            $resuDup->free();
            $stmtDup->close();

            if ($hayDuplicado)
            {
                $con->close();
                return array('ok' => false, 'mensaje' => 'Ya existe otra categoria con ese nombre.');
            }

            $stmtUpd = $con->prepare('UPDATE categorias SET nombre = ? WHERE id = ? AND activa = 1');
            $stmtUpd->bind_param('si', $nombre, $idInt);
            $stmtUpd->execute();
            $stmtUpd->close();
            $con->close();

            return array('ok' => true, 'mensaje' => 'Categoria modificada correctamente.');
        }
        catch (mysqli_sql_exception $e)
        {
            $con->close();
            return array('ok' => false, 'mensaje' => 'No se pudo modificar la categoria.');
        }
    }

    public static function bajaLogica($id)
    {
        $idInt = (int) $id;
        if (self::buscarActivaPorId($idInt) === null)
        {
            return array('ok' => false, 'mensaje' => 'La categoria seleccionada no existe o ya esta dada de baja.');
        }

        $palabrasAsociadas = self::contarPalabrasActivas($idInt);
        if ($palabrasAsociadas > 0)
        {
            return array(
                'ok' => false,
                'mensaje' => 'No se puede dar de baja la categoria porque tiene ' . $palabrasAsociadas . ' palabra(s) activa(s) asociada(s).'
            );
        }

        $con = Conexion::conectar();
        try
        {
            $stmt = $con->prepare('UPDATE categorias SET activa = 0 WHERE id = ? AND activa = 1');
            $stmt->bind_param('i', $idInt);
            $stmt->execute();
            $stmt->close();
            $con->close();

            return array('ok' => true, 'mensaje' => 'Categoria dada de baja correctamente.');
        }
        catch (mysqli_sql_exception $e)
        {
            $con->close();
            return array('ok' => false, 'mensaje' => 'No se pudo dar de baja la categoria.');
        }
    }
}
?>
