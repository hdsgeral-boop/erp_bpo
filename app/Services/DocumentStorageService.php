<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class DocumentStorageService extends BaseService
{
    /**
     * @var string
     */
    protected $disk;

    public function __construct()
    {
        // Opcionalmente configurável para usar 's3' através do .env (ex: env('FILESYSTEM_DISK', 'public'))
        $this->disk = config('filesystems.default', 'public');
    }

    /**
     * Processa e guarda um ficheiro de forma segura
     */
    public function storeDocument(UploadedFile $file, array $metadata)
    {
        try {
            // Gerar um nome único e seguro para o ficheiro físico
            $extension = $file->getClientOriginalExtension();
            $secureFilename = Str::uuid() . '.' . $extension;
            
            // Definir diretoria baseada no tipo de entidade associada
            $directory = 'documents/' . strtolower(class_basename($metadata['documentable_type'])) . '/' . date('Y/m');

            // Armazenar no File System (Local ou S3)
            $path = $file->storeAs($directory, $secureFilename, $this->disk);

            if (!$path) {
                return $this->response(false, 'Falha ao guardar ficheiro físico no disco.');
            }

            // Gerar metadados para a BD
            $docData = [
                'documentable_type' => $metadata['documentable_type'],
                'documentable_id'   => $metadata['documentable_id'],
                'document_type_id'  => $metadata['document_type_id'],
                'user_id'           => auth()->id(),
                'original_name'     => $file->getClientOriginalName(),
                'file_path'         => $path,
                'mime_type'         => $file->getMimeType(),
                'size'              => $file->getSize(),
                'status'            => 'pending', // Pendente para OCR/IA se necessário
            ];

            // (Opcional) Gerar miniatura se for imagem
            if (str_starts_with($file->getMimeType(), 'image/')) {
                $this->generateThumbnail($path);
            }

            $document = Document::create($docData);

            return $this->response(true, 'Documento guardado com sucesso', $document);
        } catch (\Exception $e) {
            Log::error("Erro no DocumentStorageService: " . $e->getMessage());
            return $this->response(false, 'Erro interno ao processar documento', $e->getMessage());
        }
    }

    /**
     * Remove um documento físico e da BD
     */
    public function deleteDocument(Document $document)
    {
        try {
            if (Storage::disk($this->disk)->exists($document->file_path)) {
                Storage::disk($this->disk)->delete($document->file_path);
            }
            
            // Apaga miniatura se existir
            $thumbPath = str_replace('.', '_thumb.', $document->file_path);
            if (Storage::disk($this->disk)->exists($thumbPath)) {
                Storage::disk($this->disk)->delete($thumbPath);
            }

            // O soft delete do Eloquent trata do resto
            $document->delete();

            return $this->response(true, 'Documento eliminado com sucesso');
        } catch (\Exception $e) {
            Log::error("Erro a eliminar documento: " . $e->getMessage());
            return $this->response(false, 'Erro interno ao eliminar documento');
        }
    }

    /**
     * Gera uma miniatura utilizando Intervention Image 3
     */
    protected function generateThumbnail(string $path)
    {
        try {
            $manager = new ImageManager(new Driver());
            
            $fullPath = Storage::disk($this->disk)->path($path);
            $image = $manager->read($fullPath);
            
            // Redimensionar mantendo a proporção (máx 300px)
            $image->scale(width: 300);
            
            $thumbPath = str_replace('.', '_thumb.', $path);
            $fullThumbPath = Storage::disk($this->disk)->path($thumbPath);
            
            $image->save($fullThumbPath);
            
            return true;
        } catch (\Exception $e) {
            Log::warning("Não foi possível gerar miniatura para {$path}: " . $e->getMessage());
            return false; // Não bloqueia o upload se a miniatura falhar
        }
    }
}
