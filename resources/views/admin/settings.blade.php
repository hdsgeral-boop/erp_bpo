@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">
                <i class="fas fa-sliders-h text-primary me-2"></i> Definições Globais do Sistema
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                Configuração geral de parâmetros, empresa ativa e integrações fiscais AGT.
            </p>
        </div>
        <div>
            <button class="btn btn-primary" style="padding: 0.6rem 1.2rem; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-save me-1"></i> Guardar Alterações
            </button>
        </div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <h4 style="margin-top: 0; font-weight: 700; color: #0f172a;">Definições de Faturação AGT Angola</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
            <div>
                <label style="display: block; font-weight: 600; font-size: 0.85rem; color: #475569; margin-bottom: 0.4rem;">N.º de Validação do Software AGT</label>
                <input type="text" value="142/AGT/2019" readonly style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #f8fafc; font-weight: 700;">
            </div>
            <div>
                <label style="display: block; font-weight: 600; font-size: 0.85rem; color: #475569; margin-bottom: 0.4rem;">Versão SAF-T AO</label>
                <input type="text" value="1.01_01" readonly style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #f8fafc; font-weight: 700;">
            </div>
        </div>
    </div>
</div>
@endsection
