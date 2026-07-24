@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">
                <i class="fas fa-list-ul text-primary me-2"></i> Infotipos e Estruturas de Cadastro de RH
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                Definições de dados pessoais, agregados familiares e habilitações literárias.
            </p>
        </div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; background: #f8fafc;">
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Código Infotipo</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Nome do Registo</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Descrição</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 700; color: #2563eb;">IT 0001</td>
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;">Atribuição Organizacional</td>
                    <td style="padding: 1rem; color: #64748b;">Empresa, departamento, centro de custo e cargo</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 700; color: #2563eb;">IT 0002</td>
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;">Dados Pessoais & BI/NIF</td>
                    <td style="padding: 1rem; color: #64748b;">Documentos de identificação e estado civil</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
