<?php
require '../../includes/app.php';

use App\Propiedad;
use Intervention\Image\ImageManager as Image;

estaAutenticado();

$db = conectarDB();

$propiedad = new Propiedad();

// Consultar para obtener vendedores
$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);

// Arreglo con msj de errores
$errores = Propiedad::getErrores();

// Ejecutar el codigo despues q el user envia el form

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear una nueva instancia
    $propiedad = new Propiedad($_POST);
    
    
    // SUBIDA DE ARCHIVOS
  

    // Generar nombre unico
    $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

     // Setear la imagen
     // Realizar un resize a la imagen
     if ($_FILES['imagen']['tmp_name']) {
        $image = Image::gd()->read($_FILES['imagen']['tmp_name']);
        $image->resize(800, 600);
        $propiedad->setImagen($nombreImagen);
    }

    $errores = $propiedad->validar();

    if (empty($errores)) {
        // Crear carpeta
        if (!is_dir(CARPETA_IMAGENES)) {
            mkdir(CARPETA_IMAGENES);
        }
        
        // Guardar la imagen en el servidor
        $image->save(CARPETA_IMAGENES . $nombreImagen);

        // Guardar en la base de datos
        $resultado = $propiedad->guardar();

        if ($resultado) {
            // Redireccionar al user
            header('Location: /admin?resultado=1');
        }
    }
}

incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Crear Propiedad</h1>

    <a href="/admin" class="boton boton-verde">Volver</a>

    <?php foreach ($errores as $error) :  ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario" method="POST" action="/admin/propiedades/crear.php" enctype="multipart/form-data">
        <?php include '../../includes/templates/formulario_propiedades.php'; ?>

        <input type="submit" value="Crear Propiedad" class="boton boton-verde">
    </form>
</main>

<?php
incluirTemplate('footer');
?>