<?php

use App\Vendedor;

require '../../includes/app.php';
estaAutenticado();

// Validar la url por id
$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /admin');
    exit;
}

// Consultar para obtener vendedores
$vendedor = Vendedor::find($id);

// Arreglo con msj de errores
$errores = Vendedor::getErrores();

// Ejecutar el codigo despues q el user envia el form

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Asignar los atributos
    $args = $_POST['vendedor'];

    $vendedor->sincronizar($args);

    // Validacion
    $errores = $vendedor->validar();

    // Revisar q el array este vacio
    if (empty($errores)) {
        $vendedor->guardar();
    }
}


incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Actualizar Vendedor</h1>

    <a href="/admin" class="boton boton-verde">Volver</a>

    <?php foreach ($errores as $error) :  ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario" method="POST">
        <?php include '../../includes/templates/formulario_vendedores.php'; ?>

        <input type="submit" value="Actualizar vendedor" class="boton boton-verde">
    </form>
</main>

<?php
incluirTemplate('footer');
?>