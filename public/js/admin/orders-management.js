/**
 * Sistema de gestión de pedidos para Azucena
 * 
 * Este archivo contiene toda la funcionalidad para:
 * - Búsqueda y selección de clientes
 * - Búsqueda y selección de productos
 * - Búsqueda y selección de servicios
 * - Gestión dinámica de ítems de pedidos
 * - Cálculo de totales
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // REFERENCIAS A ELEMENTOS DEL DOM
    // ========================================
    const nuevoPedidoForm = document.getElementById('nuevoPedidoForm');
    
    // Referencias para clientes
    const clienteBusqueda = document.getElementById('cliente_busqueda');
    const buscarClienteBtn = document.getElementById('buscarClienteBtn');
    const resultadosClienteBusqueda = document.getElementById('resultadosClienteBusqueda');
    const clienteSeleccionado = document.getElementById('clienteSeleccionado');
    const clienteNombre = document.getElementById('clienteNombre');
    const clienteDetalles = document.getElementById('clienteDetalles');
    const customerIdInput = document.getElementById('customer_id');
    
    // Referencias para productos
    const productoBusqueda = document.getElementById('producto_busqueda');
    const resultadosProductoBusqueda = document.getElementById('resultadosProductoBusqueda');
    const productoFeedback = document.getElementById('productoFeedback');
    
    // Referencias para servicios
    const servicioBusqueda = document.getElementById('servicio_busqueda');
    const resultadosServicioBusqueda = document.getElementById('resultadosServicioBusqueda');
    const servicioFeedback = document.getElementById('servicioFeedback');
    
    // Referencias para la tabla de items
    const itemsTableBody = document.getElementById('itemsTableBody');
    const noItemsRow = document.getElementById('noItemsRow');
    const itemsDataContainer = document.getElementById('itemsDataContainer');
    
    // Referencias para totales
    const subtotalAmount = document.getElementById('subtotalAmount');
    const taxAmount = document.getElementById('taxAmount');
    const totalAmount = document.getElementById('totalAmount');
    const totalAmountInput = document.getElementById('total_amount');
    const paidAmountInput = document.getElementById('paid_amount');
    
    // Variables globales
    let itemCounter = 0;
    let items = [];
    
    // ========================================
    // BÚSQUEDA Y SELECCIÓN DE CLIENTES
    // ========================================
    
    // Búsqueda de clientes cuando se escribe
    clienteBusqueda.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length < 2) {
            resultadosClienteBusqueda.style.display = 'none';
            return;
        }
        
        searchClientes(query);
    });
    
    // Búsqueda de clientes con botón
    buscarClienteBtn.addEventListener('click', function() {
        const query = clienteBusqueda.value.trim();
        if (query.length < 2) return;
        
        searchClientes(query);
    });
    
    // Función para buscar clientes
    function searchClientes(query) {
        // Mostrar indicador de carga
        resultadosClienteBusqueda.innerHTML = '<div class="text-center p-2"><span class="spinner-border spinner-border-sm text-primary" role="status"></span> Buscando clientes...</div>';
        resultadosClienteBusqueda.style.display = 'block';
        
        // Hacer petición AJAX
        fetch(`/admin/clientes/search?query=${encodeURIComponent(query)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la petición: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                resultadosClienteBusqueda.innerHTML = '';
                
                if (data.length === 0) {
                    resultadosClienteBusqueda.innerHTML = '<div class="p-2 text-center">No se encontraron clientes</div>';
                    return;
                }
                
                // Crear elementos para cada cliente
                data.forEach(cliente => {
                    const nombreCompleto = cliente.first_name + (cliente.last_name ? ' ' + cliente.last_name : '');
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action';
                    item.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${nombreCompleto}</strong>
                                <br><small>${cliente.email}</small>
                            </div>
                            <span class="badge bg-light text-dark">${cliente.phone || 'Sin teléfono'}</span>
                        </div>
                    `;
                    
                    // Evento para seleccionar cliente
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        selectCliente(cliente);
                    });
                    
                    resultadosClienteBusqueda.appendChild(item);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                resultadosClienteBusqueda.innerHTML = `<div class="p-2 text-center text-danger">Error: ${error.message}</div>`;
            });
    }
    
    // Función para seleccionar cliente
    function selectCliente(cliente) {
        // Guardar ID en campo oculto
        customerIdInput.value = cliente.id;
        
        // Mostrar datos del cliente seleccionado
        const nombreCompleto = cliente.first_name + (cliente.last_name ? ' ' + cliente.last_name : '');
        clienteNombre.textContent = nombreCompleto;
        
        let detalles = `Email: ${cliente.email}`;
        if (cliente.phone) detalles += ` | Teléfono: ${cliente.phone}`;
        clienteDetalles.textContent = detalles;
        
        // Mostrar el área de cliente seleccionado
        clienteSeleccionado.style.display = 'block';
        
        // Ocultar resultados
        resultadosClienteBusqueda.style.display = 'none';
        
        // Limpiar campo de búsqueda
        clienteBusqueda.value = '';
    }
    
    // ========================================
    // BÚSQUEDA Y SELECCIÓN DE PRODUCTOS
    // ========================================
    
    // Búsqueda de productos cuando se escribe
    productoBusqueda.addEventListener('input', function() {
        const query = this.value.trim();
        productoFeedback.style.display = 'none';
        
        if (query.length < 2) {
            resultadosProductoBusqueda.style.display = 'none';
            return;
        }
        
        searchProductos(query);
    });
    
    // Función para buscar productos
    function searchProductos(query) {
        // Mostrar indicador de carga
        resultadosProductoBusqueda.innerHTML = '<div class="text-center p-2"><span class="spinner-border spinner-border-sm text-primary" role="status"></span> Buscando productos...</div>';
        resultadosProductoBusqueda.style.display = 'block';
        
        // Hacer petición AJAX
        fetch(`/admin/productos/search?query=${encodeURIComponent(query)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la petición: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                resultadosProductoBusqueda.innerHTML = '';
                
                if (data.length === 0) {
                    productoFeedback.style.display = 'block';
                    resultadosProductoBusqueda.style.display = 'none';
                    return;
                }
                
                // Crear elementos para cada producto
                data.forEach(producto => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action py-2';
                    item.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${producto.name}</strong>
                                <br><small class="text-muted">${producto.reference || 'Sin referencia'}</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">${parseFloat(producto.price).toFixed(2)} €</span>
                        </div>
                    `;
                    
                    // Evento para seleccionar producto
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        addItem('product', producto);
                        productoBusqueda.value = '';
                        resultadosProductoBusqueda.style.display = 'none';
                    });
                    
                    resultadosProductoBusqueda.appendChild(item);
                });
                
                resultadosProductoBusqueda.style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                resultadosProductoBusqueda.innerHTML = `<div class="p-2 text-center text-danger">Error: ${error.message}</div>`;
            });
    }
    
    // ========================================
    // BÚSQUEDA Y SELECCIÓN DE SERVICIOS
    // ========================================
    
    // Búsqueda de servicios cuando se escribe
    servicioBusqueda.addEventListener('input', function() {
        const query = this.value.trim();
        servicioFeedback.style.display = 'none';
        
        if (query.length < 2) {
            resultadosServicioBusqueda.style.display = 'none';
            return;
        }
        
        searchServicios(query);
    });
    
    // Función para buscar servicios
    function searchServicios(query) {
        // Mostrar indicador de carga
        resultadosServicioBusqueda.innerHTML = '<div class="text-center p-2"><span class="spinner-border spinner-border-sm text-primary" role="status"></span> Buscando servicios...</div>';
        resultadosServicioBusqueda.style.display = 'block';
        
        // Hacer petición AJAX
        fetch(`/admin/servicios/search?query=${encodeURIComponent(query)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la petición: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                resultadosServicioBusqueda.innerHTML = '';
                
                if (data.length === 0) {
                    servicioFeedback.style.display = 'block';
                    resultadosServicioBusqueda.style.display = 'none';
                    return;
                }
                
                // Crear elementos para cada servicio
                data.forEach(servicio => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action py-2';
                    item.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${servicio.name}</strong>
                                <br><small class="text-muted">${servicio.description || 'Sin descripción'}</small>
                            </div>
                            <span class="badge bg-success rounded-pill">${parseFloat(servicio.price).toFixed(2)} €</span>
                        </div>
                    `;
                    
                    // Evento para seleccionar servicio
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        addItem('service', servicio);
                        servicioBusqueda.value = '';
                        resultadosServicioBusqueda.style.display = 'none';
                    });
                    
                    resultadosServicioBusqueda.appendChild(item);
                });
                
                resultadosServicioBusqueda.style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                resultadosServicioBusqueda.innerHTML = `<div class="p-2 text-center text-danger">Error: ${error.message}</div>`;
            });
    }
    
    // ========================================
    // GESTIÓN DE ITEMS DEL PEDIDO
    // ========================================
    
    // Función para añadir un item a la tabla
    function addItem(type, item) {
        // Incrementar contador
        itemCounter++;
        
        // Crear un ID único para este item
        const itemId = `item_${itemCounter}`;
        
        // Ocultar el mensaje de "no hay items"
        if (noItemsRow) {
            noItemsRow.style.display = 'none';
        }
        
        // Crear nueva fila en la tabla
        const row = document.createElement('tr');
        row.id = itemId;
        
        const itemType = type === 'product' ? 'Producto' : 'Servicio';
        const itemName = item.name;
        const itemPrice = parseFloat(item.price);
        
        row.innerHTML = `
            <td>
                <span class="badge ${type === 'product' ? 'bg-primary' : 'bg-success'}">${itemType}</span>
            </td>
            <td>
                ${itemName}
                <input type="text" class="form-control form-control-sm mt-1 item-description" placeholder="Descripción adicional...">
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" class="form-control item-price" value="${itemPrice.toFixed(2)}" step="0.01" min="0">
                    <span class="input-group-text">€</span>
                </div>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm item-quantity" value="1" min="1" max="99">
            </td>
            <td class="item-total">${itemPrice.toFixed(2)} €</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        // Añadir el item al arreglo
        items.push({
            id: itemId,
            type: type,
            item_id: item.id,
            name: itemName,
            price: itemPrice,
            quantity: 1,
            description: ''
        });
        
        // Añadir eventos para los inputs
        itemsTableBody.appendChild(row);
        
        // Manejo de cambio de precio
        row.querySelector('.item-price').addEventListener('change', function(e) {
            updateItemPrice(itemId, parseFloat(e.target.value));
        });
        
        // Manejo de cambio de cantidad
        row.querySelector('.item-quantity').addEventListener('change', function(e) {
            updateItemQuantity(itemId, parseInt(e.target.value));
        });
        
        // Manejo de cambio de descripción
        row.querySelector('.item-description').addEventListener('change', function(e) {
            updateItemDescription(itemId, e.target.value);
        });
        
        // Manejo de eliminación
        row.querySelector('.remove-item').addEventListener('click', function() {
            removeItem(itemId);
        });
        
        // Actualizar totales
        updateTotals();
        
        // Actualizar campos ocultos para el envío
        updateHiddenFields();
    }
    
    // Función para actualizar el precio de un item
    function updateItemPrice(itemId, newPrice) {
        // Actualizar en el arreglo
        const item = items.find(i => i.id === itemId);
        if (item) {
            item.price = newPrice;
            
            // Actualizar el total de la fila
            const row = document.getElementById(itemId);
            const totalCell = row.querySelector('.item-total');
            totalCell.textContent = (newPrice * item.quantity).toFixed(2) + ' €';
            
            // Actualizar totales
            updateTotals();
            updateHiddenFields();
        }
    }
    
    // Función para actualizar la cantidad de un item
    function updateItemQuantity(itemId, newQuantity) {
        // Actualizar en el arreglo
        const item = items.find(i => i.id === itemId);
        if (item) {
            item.quantity = newQuantity;
            
            // Actualizar el total de la fila
            const row = document.getElementById(itemId);
            const totalCell = row.querySelector('.item-total');
            totalCell.textContent = (item.price * newQuantity).toFixed(2) + ' €';
            
            // Actualizar totales
            updateTotals();
            updateHiddenFields();
        }
    }
    
    // Función para actualizar la descripción de un item
    function updateItemDescription(itemId, newDescription) {
        // Actualizar en el arreglo
        const item = items.find(i => i.id === itemId);
        if (item) {
            item.description = newDescription;
            updateHiddenFields();
        }
    }
    
    // Función para eliminar un item
    function removeItem(itemId) {
        // Eliminar del arreglo
        items = items.filter(i => i.id !== itemId);
        
        // Eliminar de la tabla
        const row = document.getElementById(itemId);
        row.remove();
        
        // Mostrar el mensaje de "no hay items" si no hay items
        if (items.length === 0 && noItemsRow) {
            noItemsRow.style.display = 'table-row';
        }
        
        // Actualizar totales
        updateTotals();
        updateHiddenFields();
    }
    
    // ========================================
    // CÁLCULO DE TOTALES
    // ========================================
    
    // Función para actualizar los totales
    function updateTotals() {
        // Calcular subtotal
        const subtotal = items.reduce((acc, item) => acc + (item.price * item.quantity), 0);
        
        // Calcular IVA (21%)
        const tax = subtotal * 0.21;
        
        // Calcular total
        const total = subtotal + tax;
        
        // Actualizar en la UI
        subtotalAmount.textContent = subtotal.toFixed(2) + ' €';
        taxAmount.textContent = tax.toFixed(2) + ' €';
        totalAmount.textContent = total.toFixed(2) + ' €';
        
        // Actualizar campo oculto para el total
        if (totalAmountInput) {
            totalAmountInput.value = total.toFixed(2);
        }
    }
    
    // Función para actualizar los campos ocultos para el envío
    function updateHiddenFields() {
        // Limpiar contenedor
        itemsDataContainer.innerHTML = '';
        
        // Añadir campo para el total
        const total = items.reduce((acc, item) => acc + (item.price * item.quantity), 0) * 1.21;
        const totalInput = document.createElement('input');
        totalInput.type = 'hidden';
        totalInput.name = 'total_amount';
        totalInput.id = 'total_amount';
        totalInput.value = total.toFixed(2);
        itemsDataContainer.appendChild(totalInput);
        
        // Añadir campos para cada item
        items.forEach((item, index) => {
            // Tipo
            const typeInput = document.createElement('input');
            typeInput.type = 'hidden';
            typeInput.name = `items[${index}][type]`;
            typeInput.value = item.type;
            itemsDataContainer.appendChild(typeInput);
            
            // ID
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = `items[${index}][id]`;
            idInput.value = item.item_id;
            itemsDataContainer.appendChild(idInput);
            
            // Precio
            const priceInput = document.createElement('input');
            priceInput.type = 'hidden';
            priceInput.name = `items[${index}][price]`;
            priceInput.value = item.price;
            itemsDataContainer.appendChild(priceInput);
            
            // Cantidad
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `items[${index}][quantity]`;
            quantityInput.value = item.quantity;
            itemsDataContainer.appendChild(quantityInput);
            
            // Descripción
            const descriptionInput = document.createElement('input');
            descriptionInput.type = 'hidden';
            descriptionInput.name = `items[${index}][description]`;
            descriptionInput.value = item.description;
            itemsDataContainer.appendChild(descriptionInput);
        });
    }
    
    // ========================================
    // EVENTOS GLOBALES
    // ========================================
    
    // Cerrar resultados al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!clienteBusqueda.contains(e.target) && !resultadosClienteBusqueda.contains(e.target)) {
            resultadosClienteBusqueda.style.display = 'none';
        }
        
        if (!productoBusqueda.contains(e.target) && !resultadosProductoBusqueda.contains(e.target)) {
            resultadosProductoBusqueda.style.display = 'none';
        }
        
        if (!servicioBusqueda.contains(e.target) && !resultadosServicioBusqueda.contains(e.target)) {
            resultadosServicioBusqueda.style.display = 'none';
        }
    });
    
    // Inicialización
    updateTotals();
    updateHiddenFields();
    
    // Validar formulario antes de enviar
    nuevoPedidoForm.addEventListener('submit', function(e) {
        // Verificar que hay cliente seleccionado
        if (!customerIdInput.value) {
            e.preventDefault();
            alert('Por favor, seleccione un cliente para el pedido.');
            return false;
        }
        
        // Verificar que hay items en el pedido
        if (items.length === 0) {
            e.preventDefault();
            alert('Por favor, añada al menos un producto o servicio al pedido.');
            return false;
        }
        
        // Todo correcto, continuar con el envío
        return true;
    });
});
