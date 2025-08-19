{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}"> 
  <title>@yield('title') – Modistería Azucena</title>

  {{-- Bootstrap CSS (CDN) --}}
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
    crossorigin="anonymous"
  >
  
  {{-- Font Awesome para iconos --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
  
  {{-- Estilos básicos compartidos --}}
  <link rel="stylesheet" href="/assets/css/searchable-select.css">
  <link rel="stylesheet" href="/assets/css/main.css">
  <link rel="stylesheet" href="/assets/css/admin.css">

  @stack('styles')
</head>
<body class="bg-light text-dark">
  <!-- Botón para mostrar el menú en dispositivos móviles -->
  <button class="sidebar-toggle-btn d-md-none" type="button" id="sidebarToggle">
    <i class="fas fa-bars"></i>
  </button>
  
  <!-- Overlay para dispositivos móviles cuando el menú está abierto -->
  <div class="sidebar-backdrop d-md-none"></div>

  <div class="d-flex flex-column flex-md-row">
    {{-- Sidebar por defecto --}}
    <div class="sidebar-container d-md-block">
      @hasSection('sidebar')
        @yield('sidebar')
      @else
      <div class="sidebar bg-dark text-light py-4 px-3">
        <div class="d-flex align-items-center mb-4 px-2">
          <img src="/img/logo.png" alt="Logo" height="40" class="me-2">
          <h4 class="text-light mb-0">Panel de Admin</h4>
        </div>
        <ul class="nav flex-column">
          <li class="nav-item mb-2">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
              <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
          </li>
          <li class="nav-item mb-2">
            <a href="{{ route('admin.orders.index') }}" class="nav-link">
              <i class="fas fa-shopping-cart me-2"></i> Pedidos
            </a>
          </li>
          <li class="nav-item mb-2">
            <a href="{{ route('admin.customers.index') }}" class="nav-link">
              <i class="fas fa-users me-2"></i> Clientes
            </a>
          </li>
          <!-- Gestión de servicios -->
          <li class="nav-item mb-2">
            <a href="#serviciosCollapse" data-bs-toggle="collapse" class="nav-link d-flex justify-content-between align-items-center">
              <span><i class="fas fa-concierge-bell me-2"></i> Servicios</span>
              <i class="fas fa-chevron-down small"></i>
            </a>
            <div class="collapse ps-3" id="serviciosCollapse">
              <ul class="nav flex-column border-start border-secondary border-opacity-50 ps-2 ms-2">
                <li class="nav-item">
                  <a href="{{ route('admin.service_categories.index') }}" class="nav-link py-2">
                    <i class="fas fa-tags me-2"></i> Categorías
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.service_items.index') }}" class="nav-link py-2">
                    <i class="fas fa-list me-2"></i> Servicios
                  </a>
                </li>
              </ul>
            </div>
          </li>
          <!-- Gestión de estados -->
          <li class="nav-item mb-2">
            <a href="{{ route('admin.states.index') }}" class="nav-link">
              <i class="fas fa-flag me-2"></i> Estados
            </a>
          </li>
          <!-- Reportes -->
          <li class="nav-item mb-2">
            <a href="{{ route('admin.reports.sales') }}" class="nav-link">
              <i class="fas fa-chart-line me-2"></i> Reportes de Ventas
            </a>
          </li>
          <li class="nav-item mt-3">
            <a href="{{ route('logout') }}" class="btn btn-outline-danger btn-sm w-100" 
              onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </li>
        </ul>
      </div>
      @endif
    </div>

    <main class="flex-grow-1 p-4 content-wrapper">
      <!-- Sección para acciones/botones de cabecera -->
      @hasSection('actions')
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">@yield('header', 'Panel de Administración')</h1>
        <div>
          @yield('actions')
        </div>
      </div>
      @endif
      
      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif
      
      @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif
      
      @yield('content')
    </main>
  </div>

  {{-- Bootstrap JS Bundle (CDN) --}}
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"
  ></script>

  {{-- SweetAlert2 para confirmaciones --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  {{-- Scripts compartidos --}}
  <script src="/assets/js/searchable-select.js"></script>
  <script src="/assets/js/main.js"></script>
  <script src="/assets/js/admin.js"></script>
  
  <script>
    // Funcionalidad de sidebar en dispositivos móviles
    document.addEventListener('DOMContentLoaded', function() {
      const body = document.body;
      const sidebarToggle = document.getElementById('sidebarToggle');
      const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
      
      if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
          body.classList.toggle('mobile-sidebar-open');
        });
      }
      
      if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', function() {
          body.classList.remove('mobile-sidebar-open');
        });
      }
    });
  </script>

  @stack('scripts')
    <!-- Scripts personalizados -->
</body>
</html>
