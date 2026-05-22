document.addEventListener('DOMContentLoaded', function() {
    
    // =========================================================================
    // 1. CAPTURA DE ELEMENTOS PRINCIPALES DEL DOM
    // =========================================================================
    const formulario = document.getElementById('formulario');
    const mensajeDiv = document.getElementById('mensajeRespuesta');
    const selectBodega = document.getElementById('Bodega'); 
    const selectSucursal = document.getElementById('Sucursal'); 
    const selectMoneda = document.getElementById('Moneda'); 

    // =========================================================================
    // 2. CARGA DE DATOS INICIALES AL ABRIR LA PÁGINA (AJAX)
    // =========================================================================
    
    // A. Cargar Bodegas
    if (selectBodega) {
        fetch('proceso.php')
            .then(respuesta => respuesta.json())
            .then(datos => {
                selectBodega.innerHTML = '<option value=""></option>';
                datos.forEach(bodega => {
                    const opcion = document.createElement('option');
                    opcion.value = bodega.nombre; 
                    opcion.textContent = bodega.nombre; 
                    selectBodega.appendChild(opcion);
                });
            })
            .catch(error => console.error('Error al cargar las bodegas:', error));
    }

    // B. Cargar Monedas
    if (selectMoneda) {
        fetch('proceso.php?monedas=true') 
            .then(respuesta => respuesta.json())
            .then(datos => {
                selectMoneda.innerHTML = '<option value=""></option>';
                datos.forEach(moneda => {
                    const opcion = document.createElement('option');
                    opcion.value = moneda.nombre;
                    opcion.textContent = moneda.nombre;
                    selectMoneda.appendChild(opcion);
                });
            })
            .catch(error => console.error('Error al cargar monedas:', error));
    }

    // =========================================================================
    // 3. EVENTOS DINÁMICOS DE LA INTERFAZ
    // =========================================================================
    
    // Selects en cascada: Cargar Sucursales al cambiar la Bodega
    if (selectBodega && selectSucursal) {
        selectBodega.addEventListener('change', function() {
            const bodegaSeleccionada = selectBodega.value;
            selectSucursal.innerHTML = '<option value=""></option>';

            if (bodegaSeleccionada !== "") {
                fetch('proceso.php?bodega=' + encodeURIComponent(bodegaSeleccionada))
                    .then(respuesta => respuesta.json())
                    .then(datos => {
                        datos.forEach(sucursal => {
                            const opcion = document.createElement('option');
                            opcion.value = sucursal.nombre;
                            opcion.textContent = sucursal.nombre;
                            selectSucursal.appendChild(opcion);
                        });
                    })
                    .catch(error => console.error('Error al cargar sucursales:', error));
            }
        });
    }

    // =========================================================================
    // 4. VALIDACIÓN Y ENVÍO DEL FORMULARIO
    // =========================================================================
    formulario.addEventListener('submit', function(evento) {
        // Prevenir la recarga de la página
        evento.preventDefault();

        // --- 4.1. Validación del Código ---
        const codigoInput = document.getElementById('Codigo');
        const codigo = codigoInput.value.trim(); 

        if (codigo === "") {
            alert("El código del producto no puede estar en blanco.");
            return; 
        }
        if (codigo.length < 5 || codigo.length > 15) {
            alert("El código del producto debe tener entre 5 y 15 caracteres.");
            return;
        }
        const regexFormato = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]+$/;
        if (!regexFormato.test(codigo)) {
            alert("El código del producto debe contener letras y números");
            return;
        }

        // --- 4.2. Validación del Nombre ---
        const nombreInput = document.getElementById('Nombre');
        const nombre = nombreInput.value.trim(); 

        if (nombre === "") {
            alert("El nombre del producto no puede estar en blanco.");
            return; 
        }
        if (nombre.length < 2 || nombre.length > 50) {
            alert("El nombre del producto debe tener entre 2 y 50 caracteres.");
            return;
        }

        // --- 4.3. Validación del Precio ---
        const precioInput = document.getElementById('Precio');
        const precio = precioInput.value.trim(); 

        if (precio === "") {
            alert("El precio del producto no puede estar en blanco.");
            return; 
        }
        const regexPrecio = /^\d+(\.\d{1,2})?$/;
        if (!regexPrecio.test(precio) || parseFloat(precio) === 0) {
            alert("El precio del producto debe ser un número positivo con hasta dos decimales.");
            return;
        }

        // --- 4.4. Validación de Materiales (Checkboxes) ---
        const checkboxesMateriales = document.querySelectorAll('input[name="Material[]"]:checked');
        if (checkboxesMateriales.length < 2) {
            alert("Debe seleccionar al menos dos materiales para el producto.");
            return; 
        }

        // --- 4.5. Validación de Bodega ---
        if (selectBodega && selectBodega.value === "") {
            alert("Debe seleccionar una bodega.");
            return; 
        }

        // --- 4.6. Validación de Sucursal ---
        if (selectSucursal && selectSucursal.value === "") {
            alert("Debe seleccionar una sucursal para la bodega seleccionada.");
            return; 
        }

        // --- 4.7. Validación de Moneda ---
        if (selectMoneda && selectMoneda.value === "") {
            alert("Debe seleccionar una moneda para el producto.");
            return; 
        }

        // --- 4.8. Validación de la Descripción ---
        const descripcionInput = document.getElementById('Descripcion');
        const descripcion = descripcionInput.value.trim(); 

        if (descripcion === "") {
            alert("La descripción del producto no puede estar en blanco.");
            return; 
        }
        if (descripcion.length < 10 || descripcion.length > 1000) {
            alert("La descripción del producto debe tener entre 10 y 1000 caracteres.");
            return;
        }

        // =========================================================================
        // 5. ENVÍO DE DATOS AL BACKEND (AJAX - POST)
        // =========================================================================
        if (mensajeDiv) {
            mensajeDiv.textContent = "Procesando...";
            mensajeDiv.style.color = "blue";
        }

        const datosFormulario = new FormData(formulario);

        fetch('proceso.php', {
            method: 'POST',
            body: datosFormulario
        })
        .then(respuesta => respuesta.json())
        .then(data => {
            if (data.exito) {
                alert("¡Producto guardado exitosamente!");
                if (mensajeDiv) mensajeDiv.textContent = "";
                formulario.reset(); 
            } else {
                if (data.tipo_error === "duplicado") {
                    alert("El código del producto ya está registrado.");
                    if (mensajeDiv) mensajeDiv.textContent = "";
                } else {
                    alert("Error: " + data.mensaje);
                    if (mensajeDiv) {
                        mensajeDiv.textContent = "Error al guardar.";
                        mensajeDiv.style.color = "red";
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error AJAX:', error);
            alert("Hubo un error al comunicarse con el servidor.");
            if (mensajeDiv) mensajeDiv.textContent = "";
        });
    });
});