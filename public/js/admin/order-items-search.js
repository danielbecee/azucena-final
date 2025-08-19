/**
 * Azucena Admin - Búsqueda y gestión de productos/servicios en pedidos
 * Este archivo maneja la búsqueda en tiempo real de productos y servicios
 * para añadirlos al pedido, así como la gestión de los items seleccionados.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos DOM
    const productoSearch = document.getElementById('producto_busqueda');
    const servicioSearch = document.getElementById('servicio_busqueda');
    const resultadosProducto = document.getElementById('resultadosProductoBusqueda');
    const resultadosServicio = document.getElementById('resultadosServicioBusqueda');
    const productoFeedback = document.getElementById('productoFeedback');
    const servicioFeedback = document.getElementById('servicioFeedback');
    const itemsTableBody = document.getElementById('itemsTableBody');
    const noItemsRow = document.getElementById('noItemsRow');
    const itemsDataContainer = document.getElementById('itemsDataContainer');
    const subtotalAmount = document.getElementById('subtotalAmount');
    const taxAmount = document.getElementById('taxAmount');
    const totalAmount = document.getElementById('totalAmount');
    
    // Contador para generar IDs únicos
    let itemCounter = 0;
    
    // Arreglo para almacenar todos los items añadidos
    let items = [];
    
    // Función para buscar productos
    function searchProducts(query) {
        if (query.length < 2) {
            resultadosProducto.style.display = 'none';
            return;
        }
        
        // Mostrar spinner mientras se carga
        resultadosProducto.innerHTML = '<div class="text-center p-2"><span class="spinner-border spinner-border-sm text-primary" role="status"></span> Buscando...</div>';
        resultadosProducto.style.display = 'block';
        
        // Hacer la petición AJAX
        fetch(`/admin/products/search?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    resultadosProducto.style.display = 'none';
                    productoFeedback.style.display = 'block';
                    return;
                }
                
                productoFeedback.style.display = 'none';
                resultadosProducto.innerHTML = '';
                
                // Crear los elementos de resultado
                data.forEach(product => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action py-2';
                    item.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${product.name}</strong>
                                <br><small class="text-muted">${product.reference || 'Sin referencia'}</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">${parseFloat(product.price).toFixed(2)} €</span>
                        </div>
                    `;
                    
                    // Manejar el clic en el producto
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        addItem('product', product);
                        productoSearch.value = '';
                        resultadosProducto.style.display = 'none';
                    });
                    
                    resultadosProducto.appendChild(item);
                });
                
                resultadosProducto.style.display = 'block';
            })
            .catch(error => {
                console.error('Error al buscar productos:', error);
                resultadosProducto.innerHTML = '<div class="text-center p-2 text-danger">Error al buscar productos</div>';
            });
    }
    
    // Función para buscar servicios
    function searchServices(query) {
        if (query.length < 2) {
            resultadosServicio.style.display = 'none';
            return;
        }
        
        // Mostrar spinner mientras se carga
        resultadosServicio.innerHTML = '<div class="text-center p-2"><span class="spinner-border spinner-border-sm text-primary" role="status"></span> Buscando...</div>';
        resultadosServicio.style.display = 'block';
        
        // Hacer la petición AJAX
        fetch(`/admin/services/search?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    resultadosServicio.style.display = 'none';
                    servicioFeedback.style.display = 'block';
                    return;
                }
                
                servicioFeedback.style.display = 'none';
                resultadosServicio.innerHTML = '';
                
                // Crear los elementos de resultado
                data.forEach(service => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action py-2';
                    item.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${service.name}</strong>
                                <br><small class="text-muted">${service.description || 'Sin descripción'}</small>
                            </div>
                            <span class="badge bg-success rounded-pill">${parseFloat(service.price).toFixed(2)} €</span>
                        </div>
                    `;
                    
                    // Manejar el clic en el servicio
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        addItem('service', service);
                        servicioSearch.value = '';
                        resultadosServicio.style.display = 'none';
                    });
                    
                    resultadosServicio.appendChild(item);
                });
                
                resultadosServicio.style.display = 'block';
            })
            .catch(error => {
                console.error('Error al buscar servicios:', error);
                resultadosServicio.innerHTML = '<div class="text-center p-2 text-danger">Error al buscar servicios</div>';
            });
    }
    
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
        const totalAmountInput = document.getElementById('total_amount');
        if (totalAmountInput) {
            totalAmountInput.value = total.toFixed(2);
        } else {
            // Crear el input si no existe
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'total_amount';
            input.id = 'total_amount';
            input.value = total.toFixed(2);
            itemsDataContainer.appendChild(input);
        }
    }
    
    // Función para actualizar los campos ocultos para el envío
    function updateHiddenFields() {
        // Limpiar contenedor
        itemsDataContainer.innerHTML = '';
        
        // Añadir campo para el total
        const totalInput = document.createElement('input');
        totalInput.type = 'hidden';
        totalInput.name = 'total_amount';
        totalInput.id = 'total_amount';
        totalInput.value = items.reduce((acc, item) => acc + (item.price * item.quantity), 0) * 1.21;
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
    
    // Event listener para buscar productos en tiempo real
    productoSearch.addEventListener('input', function() {
        const query = this.value.trim();
        productoFeedback.style.display = 'none';
        searchProducts(query);
    });
    
    // Event listener para buscar servicios en tiempo real
    servicioSearch.addEventListener('input', function() {
        const query = this.value.trim();
        servicioFeedback.style.display = 'none';
        searchServices(query);
    });
    
    // Cerrar resultados al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!productoSearch.contains(e.target) && !resultadosProducto.contains(e.target)) {
            resultadosProducto.style.display = 'none';
        }
        
        if (!servicioSearch.contains(e.target) && !resultadosServicio.contains(e.target)) {
            resultadosServicio.style.display = 'none';
        }
    });
    
    // Inicialización
    updateTotals();
    updateHiddenFields();
});
