@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@section('content')
<div class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Iniciar sesión</h4>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
              >
            </div>

            <div class="mb-4">
              <label for="password" class="form-label">Contraseña</label>
              <input
                type="password"
                class="form-control"
                id="password"
                name="password"
                required
              >
            </div>

            <button type="submit" class="btn btn-primary w-100">Acceder</button>
          </form>

          <div class="text-center mt-3">
            <p class="mb-1">¿No tienes cuenta?</p>
            <a href="{{ route('register') }}" class="btn btn-outline-secondary">Regístrate</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
