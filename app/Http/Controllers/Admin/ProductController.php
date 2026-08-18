<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\UploadService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $products) {}

    public function index()
    {
        return view('admin.products.index', [
            'products' => Product::ordered()->get(),
        ]);
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product([
                'price'      => 0,
                'images'     => [],
                'sizes'      => [],
                'variants'   => [],
                'features'   => [],
                'active'     => true,
                'sort_order' => (int) Product::max('sort_order') + 1,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => ['required', 'string']]);

        $product = Product::create($this->products->clean($request->all()));

        return redirect()->route('admin.products')->with('status', "\"{$product->name}\" যোগ হয়েছে");
    }

    public function edit(int $id)
    {
        return view('admin.products.form', [
            'product' => Product::findOrFail($id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['name' => ['required', 'string']]);

        $product = Product::findOrFail($id);
        $product->update($this->products->clean($request->all(), $id));

        return redirect()->route('admin.products')->with('status', "\"{$product->name}\" আপডেট হয়েছে");
    }

    public function destroy(int $id)
    {
        Product::findOrFail($id)->delete();

        return redirect()->route('admin.products')->with('status', 'প্রোডাক্ট মুছে ফেলা হয়েছে');
    }

    /** ছবি আপলোড — ফর্ম থেকে fetch() দিয়ে, JSON ফেরত */
    public function upload(Request $request, UploadService $uploads)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:12288'], // 12MB
        ]);

        try {
            $url = $uploads->save($request->file('file'), (string) $request->input('prefix', 'img'));

            return response()->json(['ok' => true, 'url' => $url]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
