<!DOCTYPE html>
<html lang="es">
<head>
    <title>Document</title>
</head>
<body>
    <section>
        <article>
            <?php
            require_once 'funciones.php';
            if ($_SERVER['REQUEST_METHOD']==='POST')
            {
                if(isset($_POST['tipoInmueble']) && isset($_POST['domicilio']) && isset($_POST['dormitorio']) && isset($_POST['mejoras']) && isset($_POST['banios']))
                {
                    $tipo = $_POST['tipoInmueble'];
                    $dom = $_POST['domicilio'];
                    $dorm = $_POST['dormitorio'];
                    $mejoras = $_POST['mejoras'];
                    $banios = $_POST['banios'];
                    $obs = '';
                    if (isset($_POST['obs']))
                    {
                        $obs = $_POST['obs'];
                    }
                    cargaDatos($tipo,$dom,$dorm,$mejoras,$banios,$obs);
                    muestraTablas();
                }
            } else {
                
                formularioIngreso();
                }
            ?>
        </article>
    </section>
</body>
</html>