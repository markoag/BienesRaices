<?php
require '../../includes/app.php';

use App\Vendedor;

estaAutenticado();

$vendedor = new Vendedor();

// Arreglo con msj de errores
$errores = Vendedor::getErrores();

// Ejecutar el codigo despues q el user envia el form

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear una nueva instancia
    $vendedor = new Vendedor($_POST['vendedor']);

    $errores = $vendedor->validar();

    if (empty($errores)) {
        // Guardar en la base de datos
        $vendedor->guardar();
    }
}

incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Crear Vendedor</h1>

    <a href="/admin" class="boton boton-verde">Volver</a>

    <?php foreach ($errores as $error): ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario" method="POST" action="/admin/vendedores/crear.php">
        <?php include '../../includes/templates/formulario_vendedores.php'; ?>

        <input type="submit" value="Crear vendedor" class="boton boton-verde">
    </form>
</main>

<?php
incluirTemplate('footer');
?>