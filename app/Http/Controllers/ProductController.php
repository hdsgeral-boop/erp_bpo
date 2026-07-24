<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use App\Models\ProductCategory;
use App\Models\Attachment;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function indexView(Request $request)
    {
        return $this->index($request);
    }

    public function categoriesView(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $categories = ProductCategory::where('company_id', $companyId)->get();
        return view('product_categories.index', compact('categories'));
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        
        $products = Product::where('company_id', $companyId)
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"))
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->paginate(15);
            
        $categories = ProductCategory::where('company_id', $companyId)->orderBy('name')->get();

        return view('inventory.products.index', compact('products', 'categories', 'search', 'categoryId'));
    }

    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();
        return view('inventory.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        
        if (empty($data['company_id'])) {
            $company = Company::first();
            if (!$company) {
                return back()->withInput()->with('error', 'Tem de criar pelo menos uma Empresa no sistema.');
            }
            $data['company_id'] = $company->id;
        }

        $data['is_inventory'] = $request->has('is_inventory');
        $data['is_asset'] = $request->has('is_asset');
        $data['is_blocked'] = $request->has('is_blocked');
        $data['stock_qty'] = 0; // Starts at zero

        $product = $this->productRepository->create($data);
        $this->handleAttachments($request, $product);

        return redirect()->route('inventario.artigos.index')->with('success', 'Artigo criado com sucesso.');
    }

    public function show(string $id)
    {
        $product = $this->productRepository->findWithDetails((int)$id);
        return view('inventory.products.show', compact('product'));
    }

    public function edit(string $id)
    {
        $product = $this->productRepository->findOrFail((int)$id);
        $categories = ProductCategory::orderBy('name')->get();
        return view('inventory.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, string $id)
    {
        $data = $request->validated();
        
        $data['is_inventory'] = $request->has('is_inventory');
        $data['is_asset'] = $request->has('is_asset');
        $data['is_blocked'] = $request->has('is_blocked');

        $this->productRepository->update((int)$id, $data);
        $product = $this->productRepository->findOrFail((int)$id);
        $this->handleAttachments($request, $product);

        return redirect()->route('inventario.artigos.index')->with('success', 'Artigo atualizado com sucesso.');
    }

    public function destroy(string $id)
    {
        $product = $this->productRepository->findOrFail((int)$id);
        // Não apagar se tiver stock
        if ($product->stock_qty > 0) {
            return back()->with('error', 'Não é possível remover artigos com stock positivo.');
        }
        
        $this->productRepository->delete((int)$id);
        return redirect()->route('inventario.artigos.index')->with('success', 'Artigo removido.');
    }

    public function destroyAttachment(string $id, string $attachmentId)
    {
        $attachment = Attachment::findOrFail($attachmentId);
        
        if ($attachment->attachable_type === Product::class && $attachment->attachable_id == $id) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
            return back()->with('success', 'Anexo removido.');
        }

        return back()->with('error', 'Acesso negado.');
    }

    protected function handleAttachments(Request $request, Product $product)
    {
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/products/' . $product->id, 'public');
                
                $product->attachments()->create([
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
