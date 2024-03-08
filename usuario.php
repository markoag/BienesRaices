<?php

require 'includes/app.php';

$db = conectarDB();

// Crear un email y password
$email = 'taz@gmail.com';
$password = '123456';

// Hashear el password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Crear un usuario
$query = "INSERT INTO usuarios (email, password) VALUES ('$email', '$passwordHash')";

// Agg a la base de datos
mysqli_query($db, $query);