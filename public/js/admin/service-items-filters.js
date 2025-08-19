/**
 * Script para gestionar los filtros acumulativos en la página de servicios específicos
 */
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const filterForm = document.getElementById('filters-form');
    const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
    
    // Actualizar contador de categorías seleccionadas
    function updateCategoryCount() {
        const categoryCount = document.querySelector('.category-count');
        if (categoryCount) {
            const selectedCategories = document.querySelectorAll('input[name="category[]"]:checked').length;
            categoryCount.textContent = selectedCategories;
            categoryCount.style.display = selectedCategories > 0 ? 'inline' : 'none';
        }
    }
    
    // Evento para cambios en los checkboxes
    filterCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', updateCategoryCount);
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
    updateCategoryCount();
    
    // Expandir filtros si hay alguno aplicado
    const hasActiveFilters = 
        document.querySelectorAll('input[name="category[]"]:checked').length > 0 ||
        document.querySelector('input[name="name"]').value ||
        document.querySelector('input[name="price_from"]').value ||
        document.querySelector('input[name="price_to"]').value ||
        document.querySelector('select[name="sort_field"]').value !== 'name' ||
        document.querySelector('select[name="sort_order"]').value !== 'asc';
    
    if (hasActiveFilters) {
        const collapseFilters = document.getElementById('collapseFilters');
        if (collapseFilters && typeof bootstrap !== 'undefined') {
            new bootstrap.Collapse(collapseFilters, {
                toggle: true
            });
        }
    }
});
