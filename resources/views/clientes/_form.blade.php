<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">Nome *</label>
        <input type="text" name="nome" class="form-control"
               value="{{ old('nome', $cliente->nome ?? '') }}" required>
        @error('nome') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Empresa</label>
        <input type="text" name="empresa" class="form-control"
               value="{{ old('empresa', $cliente->empresa ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control"
               value="{{ old('email', $cliente->email ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Telefone</label>
        <input type="text" name="telefone" class="form-control"
               value="{{ old('telefone', $cliente->telefone ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">WhatsApp</label>
        <input type="text" name="whatsapp" class="form-control"
               value="{{ old('whatsapp', $cliente->whatsapp ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="ativo" {{ old('status', $cliente->status ?? 'ativo') === 'ativo' ? 'selected' : '' }}>
                Ativo
            </option>
            <option value="inativo" {{ old('status', $cliente->status ?? '') === 'inativo' ? 'selected' : '' }}>
                Inativo
            </option>
        </select>
    </div>
</div>