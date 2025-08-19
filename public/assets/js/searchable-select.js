/**
 * Searchable Select - Una alternativa ligera a Select2 en JavaScript puro
 * 
 * Convierte un select normal en uno con búsqueda incorporada
 */

class SearchableSelect {
  constructor(selectElement, options = {}) {
    // Guardar elemento original
    this.originalSelect = selectElement;
    this.options = {
      placeholderText: options.placeholderText || 'Buscar...',
      noResultsText: options.noResultsText || 'No se encontraron resultados',
      maxResults: options.maxResults || 100,
      minSearchChars: options.minSearchChars || 1
    };
    
    // Inicializar
    this.init();
  }
  
  init() {
    // Crear el contenedor principal
    this.container = document.createElement('div');
    this.container.className = 'searchable-select-container';
    
    // Crear input de búsqueda
    this.searchInput = document.createElement('input');
    this.searchInput.type = 'text';
    this.searchInput.className = 'searchable-select-input';
    this.searchInput.placeholder = this.options.placeholderText;
    
    // Crear lista desplegable
    this.dropdown = document.createElement('div');
    this.dropdown.className = 'searchable-select-dropdown';
    this.dropdown.style.display = 'none';
    
    // Crear mensaje "sin resultados"
    this.noResults = document.createElement('div');
    this.noResults.className = 'searchable-select-no-results';
    this.noResults.textContent = this.options.noResultsText;
    this.noResults.style.display = 'none';
    this.dropdown.appendChild(this.noResults);
    
    // Crear elemento para valor seleccionado
    this.selectedItem = document.createElement('div');
    this.selectedItem.className = 'searchable-select-selected';
    
    // Insertar en DOM
    this.originalSelect.parentNode.insertBefore(this.container, this.originalSelect);
    this.container.appendChild(this.selectedItem);
    this.container.appendChild(this.searchInput);
    this.container.appendChild(this.dropdown);
    this.container.appendChild(this.originalSelect);
    
    // Ocultar select original
    this.originalSelect.style.display = 'none';
    
    // Inicializar opciones
    this.populateOptions();
    
    // Añadir eventos
    this.bindEvents();
    
    // Aplicar estilos CSS
    this.applyCss();
    
    // Inicializar con valor actual si existe
    this.updateSelectedItem();
    
    // Configurar observador de mutaciones para detectar cambios externos
    this.observer = this.observeOriginalSelect();
  }
  
  populateOptions() {
    // Limpiar opciones previas
    this.dropdown.querySelectorAll('.searchable-select-option').forEach(el => el.remove());
    
    // Crear opciones a partir del select original
    Array.from(this.originalSelect.options).forEach((option, index) => {
      if (index === 0 && option.value === '') {
        // Skip placeholder option
        return;
      }
      
      const item = document.createElement('div');
      item.className = 'searchable-select-option';
      item.dataset.value = option.value;
      item.textContent = option.text;
      
      if (option.selected) {
        item.classList.add('selected');
      }
      
      this.dropdown.appendChild(item);
    });
  }
  
  bindEvents() {
    // Evento clic en container
    this.container.addEventListener('click', (e) => {
      e.stopPropagation();
      // Abrir dropdown cuando se hace clic en el contenedor
      if (e.target === this.selectedItem || e.target === this.container) {
        this.openDropdown();
        this.searchInput.focus();
      }
    });
    
    // Foco en input de búsqueda
    this.searchInput.addEventListener('focus', () => {
      this.openDropdown();
    });
    
    // Evento para cerrar dropdown al hacer clic fuera
    document.addEventListener('click', () => {
      this.closeDropdown();
    });
    
    // Búsqueda
    this.searchInput.addEventListener('input', () => {
      this.filterOptions();
    });
    
    // Keydown events
    this.searchInput.addEventListener('keydown', (e) => {
      switch (e.key) {
        case 'Escape':
          this.closeDropdown();
          break;
        case 'ArrowDown':
          e.preventDefault();
          this.highlightNextOption();
          break;
        case 'ArrowUp':
          e.preventDefault();
          this.highlightPrevOption();
          break;
        case 'Enter':
          e.preventDefault();
          this.selectHighlightedOption();
          break;
      }
    });
    
    // Clic en opciones
    this.dropdown.addEventListener('click', (e) => {
      const option = e.target.closest('.searchable-select-option');
      if (option) {
        this.selectOption(option);
      }
    });
    
    // Detectar cambios en el select original
    this.originalSelect.addEventListener('change', () => {
      this.updateSelectedItem();
    });
  }
  
  openDropdown() {
    this.dropdown.style.display = 'block';
    this.filterOptions();
  }
  
  closeDropdown() {
    this.dropdown.style.display = 'none';
    this.searchInput.value = '';
    
    // Restaurar valor seleccionado
    this.updateSelectedItem();
  }
  
  filterOptions() {
    const query = this.searchInput.value.toLowerCase().trim();
    const options = this.dropdown.querySelectorAll('.searchable-select-option');
    let foundResults = 0;
    
    options.forEach(option => {
      const text = option.textContent.toLowerCase();
      
      // Solo buscar si hay suficientes caracteres o está vacío (mostrar todo)
      if (query.length === 0 || (query.length >= this.options.minSearchChars && text.includes(query))) {
        option.style.display = 'block';
        foundResults++;
      } else {
        option.style.display = 'none';
      }
      
      // Limitar resultados para rendimiento
      if (foundResults > this.options.maxResults) {
        option.style.display = 'none';
      }
    });
    
    // Mostrar mensaje "sin resultados" si no se encontró nada
    this.noResults.style.display = foundResults === 0 ? 'block' : 'none';
  }
  
  highlightOption(option) {
    if (!option) return;
    
    // Eliminar highlight previo
    this.dropdown.querySelectorAll('.searchable-select-option').forEach(el => {
      el.classList.remove('highlighted');
    });
    
    // Añadir highlight
    option.classList.add('highlighted');
    
    // Scroll para ver la opción
    if (option.offsetTop < this.dropdown.scrollTop) {
      this.dropdown.scrollTop = option.offsetTop;
    } else if (option.offsetTop + option.offsetHeight > this.dropdown.scrollTop + this.dropdown.offsetHeight) {
      this.dropdown.scrollTop = option.offsetTop + option.offsetHeight - this.dropdown.offsetHeight;
    }
  }
  
  highlightNextOption() {
    const options = Array.from(this.dropdown.querySelectorAll('.searchable-select-option')).filter(
      el => el.style.display !== 'none'
    );
    
    if (options.length === 0) return;
    
    const currentHighlight = this.dropdown.querySelector('.searchable-select-option.highlighted');
    
    if (!currentHighlight) {
      this.highlightOption(options[0]);
    } else {
      const currentIndex = options.indexOf(currentHighlight);
      if (currentIndex < options.length - 1) {
        this.highlightOption(options[currentIndex + 1]);
      }
    }
  }
  
  highlightPrevOption() {
    const options = Array.from(this.dropdown.querySelectorAll('.searchable-select-option')).filter(
      el => el.style.display !== 'none'
    );
    
    if (options.length === 0) return;
    
    const currentHighlight = this.dropdown.querySelector('.searchable-select-option.highlighted');
    
    if (!currentHighlight) {
      this.highlightOption(options[options.length - 1]);
    } else {
      const currentIndex = options.indexOf(currentHighlight);
      if (currentIndex > 0) {
        this.highlightOption(options[currentIndex - 1]);
      }
    }
  }
  
  selectHighlightedOption() {
    const highlighted = this.dropdown.querySelector('.searchable-select-option.highlighted');
    if (highlighted) {
      this.selectOption(highlighted);
    }
  }
  
  selectOption(option) {
    const value = option.dataset.value;
    
    // Establecer valor en select original
    this.originalSelect.value = value;
    
    // Disparar evento change
    const event = new Event('change', { bubbles: true });
    this.originalSelect.dispatchEvent(event);
    
    // Actualizar texto seleccionado
    this.updateSelectedItem();
    
    // Cerrar dropdown
    this.closeDropdown();
  }
  
  updateSelectedItem() {
    const selectedOption = this.originalSelect.options[this.originalSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value !== '') {
      this.selectedItem.textContent = selectedOption.text;
      this.selectedItem.style.display = 'block';
      this.searchInput.placeholder = '';
    } else {
      this.selectedItem.textContent = '';
      this.selectedItem.style.display = 'none';
      this.searchInput.placeholder = this.options.placeholderText;
    }
  }
  
  applyCss() {
    // CSS para el contenedor
    this.container.style.position = 'relative';
    this.container.style.width = '100%';
    
    // CSS para el input
    this.searchInput.style.width = '100%';
    this.searchInput.style.padding = '0.375rem 0.75rem';
    this.searchInput.style.border = '1px solid #ced4da';
    this.searchInput.style.borderRadius = '0.25rem';
    this.searchInput.style.lineHeight = '1.5';
    this.searchInput.style.boxSizing = 'border-box';
    
    // CSS para el dropdown
    this.dropdown.style.position = 'absolute';
    this.dropdown.style.top = '100%';
    this.dropdown.style.left = '0';
    this.dropdown.style.width = '100%';
    this.dropdown.style.maxHeight = '250px';
    this.dropdown.style.overflowY = 'auto';
    this.dropdown.style.border = '1px solid #ced4da';
    this.dropdown.style.borderTop = 'none';
    this.dropdown.style.borderRadius = '0 0 0.25rem 0.25rem';
    this.dropdown.style.backgroundColor = '#fff';
    this.dropdown.style.zIndex = '1000';
    this.dropdown.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
    
    // CSS para opciones
    const optionStyle = `
      .searchable-select-option {
        padding: 0.375rem 0.75rem;
        cursor: pointer;
      }
      
      .searchable-select-option:hover,
      .searchable-select-option.highlighted {
        background-color: #f8f9fa;
      }
      
      .searchable-select-option.selected {
        background-color: #e9ecef;
      }
      
      .searchable-select-no-results {
        padding: 0.375rem 0.75rem;
        color: #6c757d;
        font-style: italic;
      }
      
      .searchable-select-selected {
        padding: 0.175rem 0.75rem;
        background-color: #f1f3f5;
        border-radius: 0.25rem;
        margin-bottom: 0.25rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
    `;
    
    // Añadir estilos globales solo una vez
    if (!document.querySelector('#searchable-select-styles')) {
      const styleEl = document.createElement('style');
      styleEl.id = 'searchable-select-styles';
      styleEl.textContent = optionStyle;
      document.head.appendChild(styleEl);
    }
  }
  
  // Método para actualizar opciones (útil cuando cambian los datos)
  refresh() {
    this.populateOptions();
    this.updateSelectedItem();
  }
  
  // Método para manejar cambios externos
  handleExternalChange() {
    this.populateOptions();
    this.updateSelectedItem();
  }
  
  // Observar cambios en el select original
  observeOriginalSelect() {
    // Crear un observador de mutaciones para el select original
    const observer = new MutationObserver(() => {
      this.handleExternalChange();
    });
    
    // Configurar el observador
    observer.observe(this.originalSelect, {
      attributes: true,
      childList: true,
      subtree: true
    });
    
    return observer;
  }
}

// Función para inicializar todos los selects con la clase 'searchable'
function initSearchableSelects() {
  document.querySelectorAll('select.searchable').forEach(select => {
    new SearchableSelect(select);
  });
}

// Auto-inicializar cuando el DOM está listo
document.addEventListener('DOMContentLoaded', initSearchableSelects);

// Exponer globalmente
window.SearchableSelect = SearchableSelect;
window.initSearchableSelects = initSearchableSelects;
