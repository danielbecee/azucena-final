<!-- Modal para registrar pagos -->
<div class="modal fade" id="pagoModal" tabindex="-1" aria-labelledby="pagoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pagoModalLabel">Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Cliente:</strong> <span id="pago_cliente_nombre"></span>
                </div>
                <div class="mb-3">
                    <strong>Concepto:</strong> <span id="pago_pedido_concepto"></span>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Total:</strong> <span id="pago_pedido_total"></span>
                    </div>
                    <div class="col-6">
                        <strong>Pendiente:</strong> <span id="pago_pedido_pendiente"></span>
                    </div>
                </div>
                
                <form id="registrarPagoForm" action="" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Cantidad a pagar</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" class="form-control" id="payment_amount" 
                                name="amount" required>
                            <span class="input-group-text">€</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Método de pago</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia bancaria</option>
                            <option value="bizum">Bizum</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Fecha del pago</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" 
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="document.getElementById('registrarPagoForm').submit();">
                    Registrar Pago
                </button>
            </div>
        </div>
    </div>
</div>
