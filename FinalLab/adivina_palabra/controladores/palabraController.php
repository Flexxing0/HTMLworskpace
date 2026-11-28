<?php
/* Archivo: controladores/palabraController.php
 * Controlador POST del ABM de palabras para el administrador (RF-20, A-06, A-07, A-14, A-15).
 */

require_once __DIR__ . '/../modelo/Sesion.php';
require_once __DIR__ . '/../dao/PalabraDAO.php';

Sesion::exigirAdmin('../index.php', '../vistas/menuView.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    header('Location: ../vistas/adminPalabrasView.php');
    exit;
}

if (isset($_POST['btnGuardarPalabra']))
{
    $idForm = isset($_POST['hdnIdPalabra']) ? (int) $_POST['hdnIdPalabra'] : 0;
    $palabraIngresada = isset($_POST['txtPalabra']) ? trim($_POST['txtPalabra']) : '';
    $categoriaIngresada = isset($_POST['cmbCategoriaPalabra']) ? (int) $_POST['cmbCategoriaPalabra'] : 0;

    if ($idForm > 0)
    {
        $resultado = PalabraDAO::modificar($idForm, $palabraIngresada, $categoriaIngresada);
        if ($resultado['ok'])
        {
            Sesion::guardarFlash('pal_exito', $resultado['mensaje']);
        }
        else
        {
            Sesion::guardarFlash('pal_error', $resultado['mensaje']);
            Sesion::guardarFlash('pal_id_editar', $idForm);
            Sesion::guardarFlash('pal_texto', $palabraIngresada);
            Sesion::guardarFlash('pal_cat', $categoriaIngresada);
        }
    }
    else
    {
        $resultado = PalabraDAO::alta($palabraIngresada, $categoriaIngresada);
        if ($resultado['ok'])
        {
            Sesion::guardarFlash('pal_exito', $resultado['mensaje']);
        }
        else
        {
            Sesion::guardarFlash('pal_error', $resultado['mensaje']);
            Sesion::guardarFlash('pal_id_editar', 0);
            Sesion::guardarFlash('pal_texto', $palabraIngresada);
            Sesion::guardarFlash('pal_cat', $categoriaIngresada);
        }
    }
}
else if (isset($_POST['btnBajaPalabra']))
{
    $idBaja = isset($_POST['hdnIdBaja']) ? (int) $_POST['hdnIdBaja'] : 0;
    $resultado = PalabraDAO::bajaLogica($idBaja);
    if ($resultado['ok'])
    {
        Sesion::guardarFlash('pal_exito', $resultado['mensaje']);
    }
    else
    {
        Sesion::guardarFlash('pal_error', $resultado['mensaje']);
    }
}

header('Location: ../vistas/adminPalabrasView.php');
exit;
?>
