<?php

require '../../includes/funciones.php';
$auth = estaAutenticado();

if (!$auth) {
    header('Location: /');
}

// Validar la url por id
$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /admin');
    exit;
}

// Bdd
require '../../includes/config/database.php';
$db = conectarDB();

// Consultar para obtener propiedades
$consulta = "SELECT * FROM propiedades WHERE id = ${id}";
$resultado = mysqli_query($db, $consulta);
$propiedad = mysqli_fetch_assoc($resultado);

// Consultar para obtener vendedores
$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);

// Arreglo con msj de errores
$errores = [];

$titulo = $propiedad['titulo'];
$precio = $propiedad['precio'];
$descripcion = $propiedad['descripcion'];
$habitaciones = $propiedad['habitaciones'];
$bano = $propiedad['bano'];
$estacionamiento = $propiedad['estacionamiento'];
$vendedorid = $propiedad['vendedorID'];
$imagenPropiedad = $propiedad['imagen'];

// Ejecutar el codigo despues q el user envia el form

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = mysqli_real_escape_string($db, $_POST['titulo']);
    $precio = mysqli_real_escape_string($db, $_POST['precio']);
    $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
    $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones']);
    $bano = mysqli_real_escape_string($db, $_POST['bano']);
    $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento']);
    $vendedorid = mysqli_real_escape_string($db, $_POST['vendedor']);
    $creado = date('Y/m/d');
    $imagen = $_FILES['imagen'];
    $medida = 1000 * 1000;

    if (!$titulo) {
        $errores[] = "Debes añadir un titulo";
    }
    if (!$precio) {
        $errores[] = "Debes añadir un precio";
    }
    if (strlen($descripcion) < 50) {
        $errores[] = "Debes añadir una descripción y min 50 caracteres";
    }
    if (!$habitaciones) {
        $errores[] = "Debes agg una habitaciones";
    }
    if (!$bano) {
        $errores[] = "Debes agg un baño";
    }
    if (!$estacionamiento) {
        $errores[] = "Debes agg un estacionamiento";
    }
    if (!$vendedorid) {
        $errores[] = "Debes selecionar un vendedor";
    }
    if ($imagen['size'] > $medida) {
        $errores[] = "La imagen es muy pesada";
    }

    // Revisar q el array este vacio
    if (empty($errores)) {
        // Crear carpeta
        $carpetaImagenes = '../../imagenes/';
        if (!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
        }

        $nombreImagen = '';

        // SUBIDA DE ARCHIVOS
        if ($imagen['name']) {
            // Eliminar la imagen previa
            unlink($carpetaImagenes . $propiedad['imagen']);

            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
            move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);
        } else {
            $nombreImagen = $propiedad['imagen'];
        }

        // INSERTAR EN LA BDD
        $query = "UPDATE propiedades SET titulo = '$titulo', precio = '$precio', imagen = '$nombreImagen',
        descripcion = '$descripcion', habitaciones = $habitaciones, bano = $bano, estacionamiento = $estacionamiento,
        vendedorID = $vendedorid WHERE id = $id";

        $resultado = mysqli_query($db, $query);

        if ($resultado) {
            // Redireccionar al user
            header('Location: /admin?resultado=2');
        }
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
        <fieldset>
            <legend>Información General</legend>

            <label for="titulo">Titulo</label>
            <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo ?>">

            <label for="precio">Precio</label>
            <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" value="<?php echo $precio ?>">

            <label for="imagen">Imagen</label>
            <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">

            <img src="/imagenes/<?php echo $imagenPropiedad ?>" class="imagen-small">

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion"><?php echo $descripcion ?></textarea>

        </fieldset>

        <fieldset>
            <legend>Información Propiedad</legend>

            <label for="habitaciones">Habitaciones</label>
            <input type="number" id="habitaciones" name="habitaciones" placeholder="Ej: 3" min="1" max="9" value="<?php echo $habitaciones ?>">

            <label for="bano">Baños</label>
            <input type="number" id="bano" name="bano" placeholder="Ej: 3" min="1" max="9" value="<?php echo $bano ?>">

            <label for="estacionamiento">Estacionamientos</label>
            <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 3" min="1" max="9" value="<?php echo $estacionamiento ?>">
        </fieldset>

        <fieldset>
            <legend>Vendedor</legend>

            <select name="vendedor">
                <option value="">-- Seleccione --</option>
                <?php while ($vendedor = mysqli_fetch_assoc($resultado)) : ?>
                    <option <?php echo $vendedorid === $vendedor['id'] ? 'selected' : ''; ?> value="<?php echo $vendedor['id']; ?>"><?php echo $vendedor['nombre'] . " " . $vendedor['apellido']; ?></option>
                <?php endwhile; ?>
            </select>
        </fieldset>

        <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
    </form>
</main>

<?php
incluirTemplate('footer');
?>