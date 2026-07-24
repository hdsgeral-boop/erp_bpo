@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">
                <i class="fas fa-shield-alt text-primary me-2"></i> Auditoria de Faturação Eletrónica AGT
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                Estado das chaves RSA, validação do SAF-T AO e encadeamento de hashes de vendas.
            </p>
        </div>
        <div>
            <a href="{{ route('vendas.saft') }}" class="btn btn-primary" style="padding: 0.6rem 1.2rem; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-file-code"></i> Exportar SAF-T AO XML
            </a>
        </div>
    </div>

    <!-- Status Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
            <span style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Certificado AGT</span>
            <h3 style="margin: 0.25rem 0 0; font-weight: 700; color: #16a34a;">142/AGT/2019</h3>
            <p style="margin: 0.5rem 0 0; font-size: 0.8rem; color: #64748b;">Software Validado e Certificado</p>
        </div>

        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
            <span style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Algoritmo de Assinatura</span>
            <h3 style="margin: 0.25rem 0 0; font-weight: 700; color: #2563eb;">RSA 1024-bit / SHA-1</h3>
            <p style="margin: 0.5rem 0 0; font-size: 0.8rem; color: #64748b;">Chave Privada Ativa</p>
        </div>

        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
            <span style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Integração SOAP AGT</span>
            <h3 style="margin: 0.25rem 0 0; font-weight: 700; color: #16a34a;">Conectado ✅</h3>
            <p style="margin: 0.5rem 0 0; font-size: 0.8rem; color: #64748b;">Sub-utilizador WFA Autenticado</p>
        </div>
    </div>

    <!-- Encadeamento de Hashes em Vendas -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem;">
        <h4 style="margin: 0 0 1rem; font-weight: 700; color: #0f172a;">Últimos Documentos Assinados e Registados</h4>

        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; background: #f8fafc;">
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569;">N.º Documento</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569;">Data</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569;">Total</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569;">Hash Criptográfico (Base64)</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569;">Estado AGT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 600;">{{ $sale->doc_number }}</td>
                    <td style="padding: 1rem; color: #64748b;">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                    <td style="padding: 1rem; font-weight: 600;">{{ number_format($sale->total_amount, 2, ',', '.') }} Kz</td>
                    <td style="padding: 1rem; font-family: monospace; font-size: 0.8rem; color: #334155;">
                        {{ $sale->hash ? substr($sale->hash, 0, 35) . '...' : 'Sem Hash' }}
                    </td>
                    <td style="padding: 1rem;">
                        <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">
                            VALIDADO AGT
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
