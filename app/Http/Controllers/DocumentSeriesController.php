<?php

namespace App\Http\Controllers;

use App\Models\DocumentSeries;
use App\Models\Company;
use App\Http\Requests\StoreDocumentSeriesRequest;
use App\Http\Requests\UpdateDocumentSeriesRequest;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class DocumentSeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $company_id = $request->input('company_id');
        
        $query = DocumentSeries::with('company');
        
        if ($search) {
            $query->where('document_type', 'like', "%{$search}%")
                  ->orWhere('identifier', 'like', "%{$search}%");
        }
        
        if ($company_id) {
            $query->where('company_id', $company_id);
        }
        
        $documentSeries = $query->orderBy('company_id')->orderBy('document_type')->orderBy('identifier')->paginate(15);
        $companies = Company::orderBy('name')->get();
        
        return view('config.document_series.index', compact('documentSeries', 'search', 'companies', 'company_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();
        // Tipos de documentos pré-definidos (podem ser configurados de outra forma, aqui é exemplo fixo)
        $documentTypes = [
            'FT' => 'Fatura',
            'FS' => 'Fatura Simplificada',
            'FR' => 'Fatura-Recibo',
            'NC' => 'Nota de Crédito',
            'ND' => 'Nota de Débito',
            'RC' => 'Recibo',
            'GT' => 'Guia de Transporte',
            'GR' => 'Guia de Remessa',
            'OR' => 'Orçamento',
            'EC' => 'Encomenda'
        ];
        
        return view('config.document_series.create', compact('companies', 'documentTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentSeriesRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');
        $data['is_default'] = $request->has('is_default');
        
        try {
            // Se esta é a série por defeito, remover a flag das outras do mesmo tipo na mesma empresa
            if ($data['is_default']) {
                DocumentSeries::where('company_id', $data['company_id'])
                             ->where('document_type', $data['document_type'])
                             ->update(['is_default' => false]);
            }

            DocumentSeries::create($data);

            return redirect()->route('config.document-series.index')->with('success', 'Série documental criada com sucesso.');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry
                return back()->withInput()->with('error', 'Já existe uma série com este identificador para este tipo de documento nesta empresa.');
            }
            return back()->withInput()->with('error', 'Erro ao criar a série documental: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentSeries $documentSeries)
    {
        $documentSeries->load('company');
        return view('config.document_series.show', compact('documentSeries'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentSeries $documentSeries)
    {
        $companies = Company::all();
        $documentTypes = [
            'FT' => 'Fatura',
            'FS' => 'Fatura Simplificada',
            'FR' => 'Fatura-Recibo',
            'NC' => 'Nota de Crédito',
            'ND' => 'Nota de Débito',
            'RC' => 'Recibo',
            'GT' => 'Guia de Transporte',
            'GR' => 'Guia de Remessa',
            'OR' => 'Orçamento',
            'EC' => 'Encomenda'
        ];
        
        return view('config.document_series.edit', compact('documentSeries', 'companies', 'documentTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentSeriesRequest $request, DocumentSeries $documentSeries)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');
        $data['is_default'] = $request->has('is_default');
        
        try {
            // Se esta é a série por defeito, remover a flag das outras do mesmo tipo na mesma empresa
            if ($data['is_default']) {
                DocumentSeries::where('company_id', $data['company_id'])
                             ->where('document_type', $data['document_type'])
                             ->where('id', '!=', $documentSeries->id)
                             ->update(['is_default' => false]);
            }

            $documentSeries->update($data);

            return redirect()->route('config.document-series.index')->with('success', 'Série documental atualizada com sucesso.');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return back()->withInput()->with('error', 'Já existe uma série com este identificador para este tipo de documento nesta empresa.');
            }
            return back()->withInput()->with('error', 'Erro ao atualizar a série documental: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentSeries $documentSeries)
    {
        if ($documentSeries->current_number > 0) {
            return back()->with('error', 'Não é possível eliminar esta série pois já existem documentos emitidos (Numerador > 0).');
        }

        $documentSeries->delete();

        return redirect()->route('config.document-series.index')->with('success', 'Série documental eliminada com sucesso.');
    }
}
