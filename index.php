<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba Técnica</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form id="formulario">
        <div class="caja">
            <h2> Formulario de Producto</h2>
            <div class="fila">
                <div class="columna">
                    <label for="Codigo">Código:</label><br>
                    <input type="text" id="Codigo" name="Codigo" placeholder="Ej. PROD01K" >
                </div>
                <div class="columna">
                    <label for="Nombre">Nombre:</label><br>
                    <input type="text" id="Nombre" name="Nombre" placeholder="Ej. Set Comedor" >
                </div>
            </div>
            <div class="fila">
                <div class="columna">
                    <label for="Bodega">Bodega:</label><br>
                    <select id="Bodega" name="Bodega" >
                        <option value=""></option></select>
                </div>
                <div class="columna">
                    <label for="Sucursal">Sucursal:</label><br>
                    <select id="Sucursal" name="Sucursal"  >
                        <option value=""></option></select>
                </div>
            </div>
            <div class="fila">
                <div class="columna">
                    <label for="Moneda">Moneda:</label><br>
                    <select id="Moneda" name="Moneda" >
                        <option value=""></option>
                        <option value="Ejemplo">Ejemplo</option></select>
                </div>
                <div class="columna">
                    <label for="Precio">Precio:</label><br>
                    <input type="text" id="Precio" name="Precio" placeholder="Ej. 1600" >
                </div>
            </div>


            <div>
                <label>Material del Producto:</label><br>
                <input type="checkbox" id="Plastico" name="Material[]" value="Plástico" >
                <label for="Plastico">Plastico</label>
                <input type="checkbox" id="Metal" name="Material[]" value="Metal">
                <label for="Metal">Metal</label>
                <input type="checkbox" id="Madera" name="Material[]" value="Madera" >
                <label for="Madera">Madera</label>
                <input type="checkbox" id="Vidrio" name="Material[]" value="Vidrio" >
                <label for="Vidrio">Vidrio</label>
                <input type="checkbox" id="Textil" name="Material[]" value="Textil" >
                <label for="Textil">Textil</label>
            </div>
            <div>
                <label for="Descripcion">Descripción:</label><br>
                <textarea id="Descripcion" name="Descripcion" placeholder="Descripcion del producto, entre 10 y 1000 caracteres" ></textarea>
            </div>
                <button type="submit" class="btn-guardar">Guardar Producto</button>
        </div>
    </form>
    <script src="app.js"></script>
</body>