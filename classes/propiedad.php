<?php

namespace App;

class Propiedad
{
    // Base de datos
    protected static $db;
    protected static $columnasDB = [
        'id',
        'titulo',
        'precio',
        'imagen',
        'descripcion',
        'habitaciones',
        'bano',
        'estacionamiento',
        'creado',
        'vendedorID'
    ];

    // Errores
    protected static $errores = [];


    public $id;
    public $titulo;
    public $precio;
    public $imagen;
    public $descripcion;
    public $habitaciones;
    public $bano;
    public $estacionamiento;
    public $creado;
    public $vendedorID;

    // Definir la conexion a la base de datos
    public static function setDB($database)
    {
        self::$db = $database;
    }

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? '';
        $this->titulo = $args['titulo'] ?? '';
        $this->precio = $args['precio'] ?? '';
        $this->imagen = $args['imagen'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->habitaciones = $args['habitaciones'] ?? '';
        $this->bano = $args['bano'] ?? '';
        $this->estacionamiento = $args['estacionamiento'] ?? '';
        $this->creado = date('Y/m/d');
        $this->vendedorID = $args['vendedorID'] ?? 1;
    }

    public function guardar()
    {
        if (isset($this->id)) {
            $this->actualizar();
        } else {
            $this->crear();
        }
    }
    public function crear()
    {
        // Sanitizar los datos
        $atributos = $this->sanitizaAtributos();

        // INSERTAR EN LA BDD
        $query = "INSERT INTO propiedades ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (' ";
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";

        $resultado = self::$db->query($query);

        return $resultado;
    }

    public function actualizar()
    {
        // Sanitizar los datos
        $atributos = $this->sanitizaAtributos();

        $valores = [];
        foreach ($atributos as $key => $value) {
            $valores[] = "{$key} = '{$value}'";
        }

        // INSERTAR EN LA BDD
        $query = "UPDATE propiedades SET ";
        $query .= join(', ', $valores);
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1";

        $resultado = self::$db->query($query);

        if ($resultado) {
            // Redireccionar al user
            header('Location: /admin?resultado=2');
        }
    }

    // Identificar y unir los atributos de la clase
    public function atributos()
    {
        $atributos = [];
        foreach (self::$columnasDB as $columna) {
            if ($columna === 'id')
                continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizaAtributos()
    {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach ($atributos as $key => $value) {
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }

    // Subida de archivos
    public function setImagen($imagen)
    {
        // Eliminar la imagen previa
        if (isset($this->id)) {
            $existeArchivo = file_exists(CARPETA_IMAGENES . $this->imagen);
            if ($existeArchivo) {
                unlink(CARPETA_IMAGENES . $this->imagen);
            }
        }
        // Asignar al atributo de imagen el nombre de la imagen
        if ($imagen) {
            $this->imagen = $imagen;
        }
    }

    // Valida los datos
    public static function getErrores()
    {
        return self::$errores;
    }

    public function validar()
    {
        if (!$this->titulo) {
            self::$errores[] = "Debes añadir un titulo";
        }
        if (!$this->precio) {
            self::$errores[] = "Debes añadir un precio";
        }
        if (strlen($this->descripcion) < 50) {
            self::$errores[] = "Debes añadir una descripción y min 50 caracteres";
        }
        if (!$this->habitaciones) {
            self::$errores[] = "Debes agg una habitaciones";
        }
        if (!$this->bano) {
            self::$errores[] = "Debes agg un baño";
        }
        if (!$this->estacionamiento) {
            self::$errores[] = "Debes agg un estacionamiento";
        }
        if (!$this->vendedorID) {
            self::$errores[] = "Debes selecionar un vendedor";
        }
        if (!$this->imagen) {
            self::$errores[] = "Debes seleccionar una imagen";
        }

        return self::$errores;
    }

    // Listar todas las propiedades
    public static function all()
    {
        $query = "SELECT * FROM propiedades";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Buscar una propiedad por su id
    public static function find($id)
    {
        $query = "SELECT * FROM propiedades WHERE id = $id";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }
    public static function consultarSQL($query)
    {
        // Consultar la base de datos
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = self::crearObjeto($registro);
        }

        // Liberar la memoria
        $resultado->free();

        // Retornar los resultados
        return $array;
    }

    protected static function crearObjeto($registro)
    {
        $objeto = new self;

        foreach ($registro as $key => $value) {
            if (property_exists($objeto, $key)) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    // Sincronizar el objeto en memoria con los datos de la BDD
    public function sincronizar($args = [])
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }
}
