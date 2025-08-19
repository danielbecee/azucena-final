/**
 * Script para gestionar los filtros acumulativos en la página de pedidos
 */
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const filterForm = document.getElementById('filters-form');
    const searchFilters = document.querySelectorAll('.search-filter');
    const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
    
    // Configuración de búsqueda en filtros con muchas opciones
    searchFilters.forEach(function(input) {
        input.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const parentDropdown = this.closest('.dropdown-menu');
            const filterOptions = parentDropdown.querySelectorAll('.form-check');
            
            filterOptions.forEach(function(option) {
                const label = option.querySelector('.form-check-label').textContent.toLowerCase();
                if (label.includes(searchValue)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });
    });
    
    // Actualizar contadores de filtros seleccionados
    function updateFilterCounts() {
        // Estados de pedido
        const orderStateCount = document.querySelector('.order-state-count');
        if (orderStateCount) {
            const selectedOrderStates = document.querySelectorAll('input[name="order_state[]"]:checked').length;
            orderStateCount.textContent = selectedOrderStates;
            orderStateCount.style.display = selectedOrderStates > 0 ? 'inline' : 'none';
        }
        
        // Estados de pago
        const paymentStateCount = document.querySelector('.payment-state-count');
        if (paymentStateCount) {
            const selectedPaymentStates = document.querySelectorAll('input[name="payment_state[]"]:checked').length;
            paymentStateCount.textContent = selectedPaymentStates;
            paymentStateCount.style.display = selectedPaymentStates > 0 ? 'inline' : 'none';
        }
        
        // Clientes
        const customerCount = document.querySelector('.customer-count');
        if (customerCount) {
            const selectedCustomers = document.querySelectorAll('input[name="customer[]"]:checked').length;
            customerCount.textContent = selectedCustomers;
            customerCount.style.display = selectedCustomers > 0 ? 'inline' : 'none';
        }
        
        // Categorías de servicio
        const serviceCategoryCount = document.querySelector('.service-category-count');
        if (serviceCategoryCount) {
            const selectedCategories = document.querySelectorAll('input[name="service_category[]"]:checked').length;
            serviceCategoryCount.textContent = selectedCategories;
            serviceCategoryCount.style.display = selectedCategories > 0 ? 'inline' : 'none';
        }
    }
    
    // Evento para cambios en los checkboxes
    filterCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', updateFilterCounts);
    });
    
    // Manejar cambios en los dropdowns para evitar que se cierren al hacer clic en las opciones
    document.querySelectorAll('.dropdown-menu').forEach(function(dropdown) {
        dropdown.addEventListener('click', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'LABEL') {
                e.stopPropagation();
            }
        });
    });
    
    // Inicialización de contadores
    updateFilterCounts();
    
    // Expandir filtros si hay alguno aplicado
    const hasActiveFilters = 
        document.querySelectorAll('input[name="order_state[]"]:checked').length > 0 ||
        document.querySelectorAll('input[name="payment_state[]"]:checked').length > 0 ||
        document.querySelectorAll('input[name="customer[]"]:checked').length > 0 ||
        document.querySelectorAll('input[name="service_category[]"]:checked').length > 0 ||
        document.querySelector('input[name="date_from"]').value ||
        document.querySelector('input[name="date_to"]').value ||
        document.querySelector('input[name="due_date_from"]').value ||
        document.querySelector('input[name="due_date_to"]').value ||
        document.querySelector('input[name="amount_from"]').value ||
        document.querySelector('input[name="amount_to"]').value;
    
    if (hasActiveFilters) {
        const collapseFilters = document.getElementById('collapseFilters');
        if (collapseFilters && typeof bootstrap !== 'undefined') {
            new bootstrap.Collapse(collapseFilters, {
                toggle: true
            });
        }
    }
    
    // Guardar valores de filtros cuando se cambian
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const numberInputs = document.querySelectorAll('input[type="number"]');
    const selectInputs = document.querySelectorAll('select');
    
    [...dateInputs, ...numberInputs].forEach(function(input) {
        input.addEventListener('change', function() {
            // Si se desea que los filtros se apliquen automáticamente al cambiar, 
            // descomentar la siguiente línea:
            // filterForm.submit();
        });
    });
    
    selectInputs.forEach(function(select) {
        select.addEventListener('change', function() {
            // Si se desea que los filtros se apliquen automáticamente al cambiar, 
            // descomentar la siguiente línea:
            // filterForm.submit();
        });
    });
});
