<!DOCTYPE html>
<html lang="es">
<head>
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="script.js"></script>
</head>
<body>
    <section>
        <article>
        <label for="idpaises">Selecciona pais</label>
        <div class="select-div">
        <select name="paises" id="idpaises" onChange="muestraCiudades();">
            <option value="0">--</option>
            <?php
            include_once 'paises.php';
            $paises = Paises::getPaisesBD();
            if (count($paises)>0){
                foreach($paises as $pais)
                {
                    echo '<option value="'.$pais->getIdpais().'">'.$pais->getNombrepais().'</option>';
                }
            }
            ?>
        </select>
        </div>
        <div class="tabla-scroll-contenedor">
            <table class="tabla-moderna">
                <thead>
                    <tr>
                        <th>
                            <div id="idpais"></div>
                        </th>
                    </tr>
                </thead>
                <tbody id="idciudades">
                    
                </tbody>
            </table>
        </div>
        </article>
    </section>

</body>
<footer></footer>
</html>