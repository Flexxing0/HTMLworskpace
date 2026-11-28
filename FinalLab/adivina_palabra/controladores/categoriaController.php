<?php

require_once __DIR__ . '/../modelo/Sesion.php';
require_once __DIR__ . '/../dao/CategoriaDAO.php';

Sesion::exigirAdmin('../index.php', '../vistas/menuView.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    header('Location: ../vistas/adminCategoriasView.php');
    exit;
}

if (isset($_POST['btnGuardarCategoria']))
{
    $idForm = isset($_POST['hdnIdCategoria']) ? (int) $_POST['hdnIdCategoria'] : 0;
    $nombreIngresado = isset($_POST['txtNombreCategoria']) ? trim($_POST['txtNombreCategoria']) : '';

    if ($idForm > 0)
    {
        $resultado = CategoriaDAO::modificar($idForm, $nombreIngresado);
        if ($resultado['ok'])
        {
            Sesion::guardarFlash('cat_exito', $resultado['mensaje']);
        }
        else
        {
            Sesion::guardarFlash('cat_error', $resultado['mensaje']);
            Sesion::guardarFlash('cat_id_editar', $idForm);
            Sesion::guardarFlash('cat_nombre', $nombreIngresado);
        }
    }
    else
    {
        $resultado = CategoriaDAO::alta($nombreIngresado);
        if ($resultado['ok'])
        {
            Sesion::guardarFlash('cat_exito', $resultado['mensaje']);
        }
        else
        {
            Sesion::guardarFlash('cat_error', $resultado['mensaje']);
            Sesion::guardarFlash('cat_id_editar', 0);
            Sesion::guardarFlash('cat_nombre', $nombreIngresado);
        }
    }
}
else if (isset($_POST['btnBajaCategoria']))
{
    $idBaja = isset($_POST['hdnIdBaja']) ? (int) $_POST['hdnIdBaja'] : 0;
    $resultado = CategoriaDAO::bajaLogica($idBaja);
    if ($resultado['ok'])
    {
        Sesion::guardarFlash('cat_exito', $resultado['mensaje']);
    }
    else
    {
        Sesion::guardarFlash('cat_error', $resultado['mensaje']);
    }
}

header('Location: ../vistas/adminCategoriasView.php');
exit;
?>
