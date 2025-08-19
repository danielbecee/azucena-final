/**
 * Script principal compartido en toda la aplicación
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar todos los tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Inicializar todos los popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Función para añadir clase "is-invalid" a los campos con errores de validación
    const validateForms = function() {
        const forms = document.querySelectorAll('.needs-validation');
        
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    };
    
    // Ejecutar validación en formularios que lo necesiten
    validateForms();
    
    // Manejar eventos de confirmación para operaciones destructivas
    const confirmButtons = document.querySelectorAll('.confirm-action');
    if (confirmButtons.length > 0 && typeof bootstrap !== 'undefined') {
        confirmButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!window.confirm(this.dataset.confirmMessage || '¿Está seguro de realizar esta acción?')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    }
});
