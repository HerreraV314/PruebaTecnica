<?php
// =========================================================================
// CONFIGURACIÓN INICIAL Y CONEXIÓN
// =========================================================================
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'BASE_DATOS1'; // Nombre de tu base de datos
$user = 'postgres';      // Usuario de PostgreSQL
$password = 'admin';     // Contraseña de PostgreSQL

try {
    // 1. Iniciamos la conexión a PostgreSQL mediante PDO
    $pdo = new PDO("pgsql:host=$host;port=5433;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // =========================================================================
    // MITAD 1: MÉTODO POST - GUARDAR UN NUEVO PRODUCTO
    // =========================================================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Recolección y limpieza de los datos recibidos del formulario
        $codigo = trim($_POST['Codigo'] ?? '');
        $nombre = trim($_POST['Nombre'] ?? '');
        $bodega = trim($_POST['Bodega'] ?? '');
        $sucursal = trim($_POST['Sucursal'] ?? '');
        $moneda = trim($_POST['Moneda'] ?? '');
        $precio = trim($_POST['Precio'] ?? '');
        $descripcion = trim($_POST['Descripcion'] ?? '');
        $materiales = $_POST['Material'] ?? []; 

        // --- A. VALIDACIÓN DE UNICIDAD DEL CÓDIGO ---
        $stmtCheck = $pdo->prepare("SELECT id FROM productos WHERE codigo = :codigo");
        $stmtCheck->execute([':codigo' => $codigo]);
        
        if ($stmtCheck->rowCount() > 0) {
            echo json_encode([
                'exito' => false, 
                'tipo_error' => 'duplicado', 
                'mensaje' => 'El código del producto ya está registrado.'
            ]);
            exit; 
        }

        // --- B. INSERCIÓN DEL PRODUCTO Y SUS MATERIALES (TRANSACCIÓN) ---
        // Iniciamos transacción para asegurar que no se guarden datos a medias
        $pdo->beginTransaction();

        // 1. Guardar en la tabla principal de productos
        $sqlProducto = "INSERT INTO productos (codigo, nombre, bodega, sucursal, moneda, precio, descripcion) 
                        VALUES (:codigo, :nombre, :bodega, :sucursal, :moneda, :precio, :descripcion) 
                        RETURNING id";
                        
        $stmtInsert = $pdo->prepare($sqlProducto);
        $stmtInsert->execute([
            ':codigo' => $codigo,
            ':nombre' => $nombre,
            ':bodega' => $bodega,
            ':sucursal' => $sucursal,
            ':moneda' => $moneda,
            ':precio' => $precio,
            ':descripcion' => $descripcion
        ]);

        // Obtenemos el ID autoincremental que PostgreSQL acaba de generar
        $producto_id = $stmtInsert->fetchColumn();

        // 2. Guardar los múltiples materiales en la tabla secundaria
        $sqlMaterial = "INSERT INTO producto_materiales (producto_id, material) VALUES (:producto_id, :material)";
        $stmtMaterial = $pdo->prepare($sqlMaterial);

        foreach ($materiales as $material) {
            $stmtMaterial->execute([
                ':producto_id' => $producto_id,
                ':material' => $material
            ]);
        }

        // Confirmamos todos los cambios en la base de datos
        $pdo->commit();
        
        // Respuesta de éxito al frontend
        echo json_encode(['exito' => true]);
        exit; 
    }

    // =========================================================================
    // MITAD 2: MÉTODO GET - OBTENER DATOS DINÁMICOS PARA LOS SELECTS
    // =========================================================================
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        
        if (isset($_GET['bodega']) && $_GET['bodega'] !== '') {
            // Caso A: Obtener sucursales dependientes de la bodega seleccionada
            $stmt = $pdo->prepare("SELECT nombre FROM sucursales WHERE bodega_nombre = :bodega ORDER BY nombre ASC");
            $stmt->execute([':bodega' => $_GET['bodega']]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } elseif (isset($_GET['monedas'])) {
            // Caso B: Obtener el catálogo de monedas
            $stmt = $pdo->query("SELECT id, nombre FROM monedas ORDER BY nombre ASC");
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } else {
            // Caso C: Obtener el catálogo de bodegas (Por defecto)
            $stmt = $pdo->query("SELECT id, nombre FROM bodegas ORDER BY nombre ASC");
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Enviamos la lista correspondiente al frontend
        echo json_encode($resultados);
        exit; 
    }

} catch (PDOException $e) {
    // --- MANEJO SEGURO DE ERRORES ---
    // Si la transacción falló a la mitad, revertimos todo
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Devolvemos el error en formato JSON para que JavaScript pueda leerlo
    echo json_encode([
        'exito' => false, 
        'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}
?>