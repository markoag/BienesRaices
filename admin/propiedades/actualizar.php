<?php

use App\Propiedad;
use App\Vendedor;
use Intervention\Image\ImageManager as Image;

require '../../includes/app.php';
estaAutenticado();

// Validar la url por id
$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /admin');
    exit;
}

// Consultar para obtener propiedades
$propiedad = Propiedad::find($id);

// Obterner vendedores
$vendedores = Vendedor::all();

// Arreglo con msj de errores
$errores = Propiedad::getErrores();

// Ejecutar el codigo despues q el user envia el form

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Asignar los atributos
    $args = $_POST['propiedad'];

    $propiedad->sincronizar($args);

    // Validacion
    $errores = $propiedad->validar();

    // SUBIDA DE ARCHIVOS
    // Generar nombre unico
    $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

    if ($_FILES['propiedad']['tmp_name']['imagen']) {
        $image = Image::gd()->read($_FILES['propiedad']['tmp_name']['imagen']);
        $image->resize(800, 600);
        $propiedad->setImagen($nombreImagen);
    }

    // Revisar q el array este vacio
    if (empty($errores)) {
        // Almacenar la imagen
        if ($_FILES['propiedad']['tmp_name']['imagen']) {
            $image->save(CARPETA_IMAGENES . $nombreImagen);
        }

        $resultado = $propiedad->guardar();
    }
}


incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Actualizar Propiedad</h1>

    <a href="/admin" class="boton boton-verde">Volver</a>

    <?php foreach ($errores as $error) :  ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario" method="POST" enctype="multipart/form-data">
        <?php include '../../includes/templates/formulario_propiedades.php'; ?>

        <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
    </form>
</main>

<?php
incluirTemplate('footer');
?>