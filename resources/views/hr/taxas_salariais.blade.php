@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">
                <i class="fas fa-percent text-primary me-2"></i> Taxas Sociais de Segurança Social (INSS)
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                Decreto Presidencial — Descontos do Trabalhador (3%) e Encargo Patronal (8%).
            </p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem;">
            <h4 style="margin: 0 0 0.5rem; font-weight: 700; color: #2563eb;">Desconto do Trabalhador (INSS)</h4>
            <h2 style="margin: 0; font-size: 2rem; font-weight: 800; color: #0f172a;">3,00 %</h2>
            <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem;">Dedução automática no vencimento do colaborador.</p>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem;">
            <h4 style="margin: 0 0 0.5rem; font-weight: 700; color: #16a34a;">Contribuição Patronal (Empresa)</h4>
            <h2 style="margin: 0; font-size: 2rem; font-weight: 800; color: #0f172a;">8,00 %</h2>
            <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem;">Encargo social suportado pela entidade patronal.</p>
        </div>
    </div>
</div>
@endsection
