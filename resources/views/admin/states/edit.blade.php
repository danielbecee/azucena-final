@extends('layouts.admin')

@section('title', 'Editar Estado')

@section('header', 'Editar Estado')

@section('actions')
<a href="{{ route('admin.states.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Volver
</a>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-body">
        <form action="{{ route('admin.states.update', $state) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre del estado <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $state->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $state->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="color" class="form-label">Color <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $state->color) }}" title="Elegir color" required>
                            <input type="text" class="form-control @error('color') is-invalid @enderror" id="color-hex" value="{{ old('color', $state->color) }}" readonly>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">Selecciona un color para identificar visualmente este estado.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="is_default" class="form-label">Estado por defecto</label>
                        <div class="form-check">
                            <input class="form-check-input @error('is_default') is-invalid @enderror" type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default', $state->is_default) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_default">
                                Marcar como estado por defecto
                            </label>
                            <div class="form-text">Los nuevos pedidos se crearán con este estado.</div>
                            @error('is_default')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Vista previa</label>
                        <div class="p-3 border rounded">
                            <span class="badge py-2 px-3 w-100 text-white" id="preview-badge" style="background-color: {{ old('color', $state->color) }};">
                                {{ old('name', $state->name) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Estado
                </button>
                <a href="{{ route('admin.states.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Actualizar vista previa del color y el texto hexadecimal
        const colorInput = document.getElementById('color');
        const colorHexInput = document.getElementById('color-hex');
        const nameInput = document.getElementById('name');
        const previewBadge = document.getElementById('preview-badge');
        
        function updatePreview() {
            const color = colorInput.value;
            const name = nameInput.value || 'Estado';
            
            colorHexInput.value = color;
            previewBadge.style.backgroundColor = color;
            previewBadge.textContent = name;
            
            // Determinar si el texto debe ser blanco o negro según el color de fondo
            const rgb = hexToRgb(color);
            const brightness = Math.round(((rgb.r * 299) + (rgb.g * 587) + (rgb.b * 114)) / 1000);
            previewBadge.style.color = (brightness > 125) ? 'black' : 'white';
        }
        
        function hexToRgb(hex) {
            const shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
            hex = hex.replace(shorthandRegex, function(m, r, g, b) {
                return r + r + g + g + b + b;
            });

            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }
        
        colorInput.addEventListener('input', updatePreview);
        nameInput.addEventListener('input', updatePreview);
        
        // Inicializar la vista previa
        updatePreview();
    });
</script>
@endsection
