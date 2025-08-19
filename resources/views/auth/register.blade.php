@extends('layouts.auth')

@section('title', 'Registro')

@section('content')
<div class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
          <h4 class="mb-0">Crear cuenta</h4>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
              <label for="name" class="form-label">Nombre completo</label>
              <input
                type="text"
                class="form-control"
                id="name"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
              >
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
              >
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <input
                type="password"
                class="form-control"
                id="password"
                name="password"
                required
              >
            </div>

            <div class="mb-4">
              <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
              <input
                type="password"
                class="form-control"
                id="password_confirmation"
                name="password_confirmation"
                required
              >
            </div>

            <button type="submit" class="btn btn-success w-100">Registrar</button>
          </form>

          <div class="text-center mt-3">
            <p class="mb-1">¿Ya tienes cuenta?</p>
            <a href="{{ route('login') }}" class="btn btn-outline-primary">Iniciar sesión</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
