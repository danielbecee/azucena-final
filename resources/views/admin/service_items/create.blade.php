@extends('layouts.admin')

@section('title', 'Nuevo Servicio')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">Nuevo Servicio</h1>
            <p class="text-muted">Crear un nuevo servicio específico dentro de una categoría</p>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.service_items.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="service_category_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                    <select class="form-select @error('service_category_id') is-invalid @enderror" id="service_category_id" name="service_category_id" required>
                        <option value="">Seleccione una categoría</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (old('service_category_id', request('category')) == $category->id) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre del Servicio <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                        value="{{ old('name') }}" required maxlength="100">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" 
                        name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Breve descripción del servicio (opcional)</div>
                </div>
                
                <div class="mb-3">
                    <label for="price" class="form-label">Precio <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" 
                            name="price" value="{{ old('price', '0.00') }}" step="0.01" min="0" required>
                        <span class="input-group-text">€</span>
                    </div>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.service_items.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Guardar Servicio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
