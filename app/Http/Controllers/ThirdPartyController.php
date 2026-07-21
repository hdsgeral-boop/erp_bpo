<?php

namespace App\Http\Controllers;

use App\Models\ThirdParty;
use App\Models\Company;
use App\Http\Requests\StoreThirdPartyRequest;
use App\Http\Requests\UpdateThirdPartyRequest;
use App\Services\ThirdPartyService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;
use App\Models\Attachment;

class ThirdPartyController extends Controller
{
    protected $thirdPartyService;

    public function __construct(ThirdPartyService $thirdPartyService)
    {
        $this->thirdPartyService = $thirdPartyService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type'); // 'customer', 'supplier', or null for all
        
        $thirdParties = $this->thirdPartyService->getAllPaginated(15, $search, $type);
        
        return view('entities.index', compact('thirdParties', 'search', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();
        return view('entities.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreThirdPartyRequest $request)
    {
        $data = $request->validated();
        
        // Em um cenário multi-empresa real, isto viria do contexto do user logado.
        // Aqui garantimos que tem company_id, ou usamos a primeira empresa como fallback se não vier no request.
        if (empty($data['company_id'])) {
            $company = Company::first();
            if (!$company) {
                return back()->withInput()->with('error', 'Tem de criar pelo menos uma Empresa no sistema antes de criar entidades.');
            }
            $data['company_id'] = $company->id;
        }

        try {
            $thirdParty = $this->thirdPartyService->createThirdParty($data);

            // Handle Attachments
            $this->handleAttachments($request, $thirdParty);

            return redirect()->route('entidades.index')->with('success', 'Entidade criada com sucesso.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $entidade = $this->thirdPartyService->getById($id);
        $entidade->load('attachments', 'company');
        return view('entities.show', compact('entidade'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $entidade = $this->thirdPartyService->getById($id);
        $companies = Company::all();
        return view('entities.edit', compact('entidade', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateThirdPartyRequest $request, string $id)
    {
        $data = $request->validated();

        if (empty($data['company_id'])) {
            $data['company_id'] = $this->thirdPartyService->getById($id)->company_id;
        }

        try {
            $thirdParty = $this->thirdPartyService->updateThirdParty($id, $data);

            // Handle Attachments
            $this->handleAttachments($request, $thirdParty);

            return redirect()->route('entidades.index')->with('success', 'Entidade atualizada com sucesso.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->thirdPartyService->deleteThirdParty($id);
            return redirect()->route('entidades.index')->with('success', 'Entidade removida com sucesso.');
        } catch (Exception $e) {
            return back()->with('error', 'Não foi possível remover a entidade: ' . $e->getMessage());
        }
    }

    /**
     * Remove a specific attachment.
     */
    public function destroyAttachment(string $id, string $attachmentId)
    {
        $attachment = Attachment::findOrFail($attachmentId);
        
        // Verifica se o anexo pertence a esta entidade
        if ($attachment->attachable_type === ThirdParty::class && $attachment->attachable_id == $id) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
            return back()->with('success', 'Anexo removido com sucesso.');
        }

        return back()->with('error', 'Acesso negado ao anexo.');
    }

    /**
     * Handle the upload of attachments.
     */
    protected function handleAttachments(Request $request, ThirdParty $thirdParty)
    {
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/third_parties/' . $thirdParty->id, 'public');
                
                $thirdParty->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }
    }
}
