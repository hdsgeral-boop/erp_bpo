<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Http\Requests\StoreDocumentRequest;
use App\Services\DocumentStorageService;
use App\Services\OCR\Contracts\OcrServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiResponse;

class DocumentController extends Controller
{
    use ApiResponse;

    protected $storageService;
    protected $ocrService;

    public function __construct(DocumentStorageService $storageService, OcrServiceInterface $ocrService)
    {
        $this->storageService = $storageService;
        $this->ocrService = $ocrService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = Document::with(['type', 'uploader', 'documentable'])->get();
        return view('sgd.index', compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request)
    {
        $file = $request->file('file');
        
        $metadata = [
            'document_type_id' => $request->document_type_id,
            'documentable_type' => $request->documentable_type,
            'documentable_id' => $request->documentable_id,
        ];

        // 1. Armazenar Ficheiro Físico (Local ou S3)
        $response = $this->storageService->storeDocument($file, $metadata);

        if (!$response['success']) {
            if ($request->wantsJson()) return $this->errorResponse($response['message']);
            return back()->with('error', $response['message']);
        }

        /** @var Document */
        $document = $response['data'];

        // 2. Extração de OCR Assíncrona (Aqui simulada de forma síncrona/rápida, idealmente usaria Laravel Jobs)
        if (in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'pdf'])) {
            try {
                $disk = config('filesystems.default', 'public');
                $physicalPath = Storage::disk($disk)->path($document->file_path);
                
                $text = $this->ocrService->extractText($physicalPath);
                
                $document->update([
                    'ocr_content' => $text,
                    'status' => 'processed'
                ]);
            } catch (\Exception $e) {
                Log::error("OCR falhou para doc {$document->id}: " . $e->getMessage());
                $document->update(['status' => 'error']);
            }
        }

        if ($request->wantsJson()) {
            return $this->successResponse('Documento arquivado com sucesso.', $document, 201);
        }

        return redirect()->route('documents.index')->with('success', 'Documento arquivado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        return view('sgd.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $response = $this->storageService->deleteDocument($document);
        
        if ($response['success']) {
            if (request()->wantsJson()) return $this->successResponse('Documento eliminado.');
            return redirect()->route('documents.index')->with('success', 'Documento eliminado.');
        }

        if (request()->wantsJson()) return $this->errorResponse($response['message']);
        return back()->with('error', $response['message']);
    }
}
