/* JavaScript para el panel de administración */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    
    // Inicializar popovers de Bootstrap
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    
    // Hacer que el menú seleccionado actualmente aparezca como activo
    const currentPath = window.location.pathname;
    
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        const href = link.getAttribute('href');
        if (href && currentPath.includes(href) && href !== '/admin/dashboard') {
            link.classList.add('active');
            
            // Si el enlace está dentro de un menú colapsable, expandirlo
            const parentCollapse = link.closest('.collapse');
            if (parentCollapse) {
                parentCollapse.classList.add('show');
                const collapseToggle = document.querySelector(`[href="#${parentCollapse.id}"]`);
                if (collapseToggle) {
                    collapseToggle.setAttribute('aria-expanded', 'true');
                }
            }
        }
    });
    
    // Soporte para confirmaciones de eliminación con SweetAlert si está disponible
    if (typeof Swal !== 'undefined') {
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('delete-confirm')) {
                e.preventDefault();
                
                const form = e.target.closest('form');
                const itemName = e.target.getAttribute('data-item-name') || 'este elemento';
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Realmente deseas eliminar ${itemName}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed && form) {
                        form.submit();
                    }
                });
            }
        });
    }
});
