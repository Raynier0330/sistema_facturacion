<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = $_POST['host'];
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $nombre_bd = 'facturacion_larubia';

    // Crear conexión
    $conn = new mysqli($host, $usuario, $contrasena);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Crear base de datos
    $sql = "CREATE DATABASE IF NOT EXISTS $nombre_bd";
    if ($conn->query($sql) === TRUE) {
        echo "Base de datos creada con éxito<br>";
    } else {
        echo "Error al crear la base de datos: " . $conn->error . "<br>";
    }

    // Seleccionar la base de datos
    $conn->select_db($nombre_bd);

    // Crear tablas
    $sql = "
    CREATE TABLE IF NOT EXISTS clientes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        codigo VARCHAR(20) UNIQUE,
        nombre VARCHAR(100)
    );

    CREATE TABLE IF NOT EXISTS facturas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        numero VARCHAR(20) UNIQUE,
        fecha DATE,
        codigo_cliente VARCHAR(20),
        nombre_cliente VARCHAR(100),
        total DECIMAL(10, 2),
        comentario TEXT
    );

    CREATE TABLE IF NOT EXISTS detalles_factura (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_factura INT,
        nombre_articulo VARCHAR(100),
        cantidad INT,
        precio DECIMAL(10, 2),
        total DECIMAL(10, 2),
        FOREIGN KEY (id_factura) REFERENCES facturas(id)
    );
    ";

    if ($conn->multi_query($sql) === TRUE) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
        echo "Tablas creadas con éxito<br>";
    } else {
        echo "Error al crear las tablas: " . $conn->error . "<br>";
    }

    // Insertar 10 clientes de ejemplo
    $sql = "INSERT INTO clientes (codigo, nombre) VALUES
    ('CLI001', 'Juan Pérez'),
    ('CLI002', 'María García'),
    ('CLI003', 'Carlos Rodríguez'),
    ('CLI004', 'Ana Martínez'),
    ('CLI005', 'Luis González'),
    ('CLI006', 'Laura Sánchez'),
    ('CLI007', 'Pedro Ramírez'),
    ('CLI008', 'Sofía López'),
    ('CLI009', 'Miguel Fernández'),
    ('CLI010', 'Isabel Díaz')";

    if ($conn->query($sql) === TRUE) {
        echo "10 clientes de ejemplo insertados con éxito<br>";
    } else {
        echo "Error al insertar clientes: " . $conn->error . "<br>";
    }

    // Guardar configuración
    $config = "<?php\n";
    $config .= "\$host = '$host';\n";
    $config .= "\$usuario = '$usuario';\n";
    $config .= "\$contrasena = '$contrasena';\n";
    $config .= "\$nombre_bd = '$nombre_bd';\n";
    file_put_contents('config.php', $config);

    echo "Instalación completada. Puedes acceder a tu sistema ahora.";
    $conn->close();
} else {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Instalador del Sistema de Facturación La Rubia</title>
</head>
<body>
    <h2>Instalador del Sistema de Facturación La Rubia</h2>
    <form method="post">
        Host: <input type="text" name="host" value="localhost"><br>
        Usuario: <input type="text" name="usuario"><br>
        Contraseña: <input type="password" name="contrasena"><br>
        <input type="submit" value="Instalar">
    </form>
</body>
</html>
<?php
}
