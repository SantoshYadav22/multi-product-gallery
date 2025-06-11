<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('images')->latest()->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'files.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);
        if ($request->hasFile('files') && count($request->file('files')) > 5) {
            if ($request->wantsJson()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'You can only upload up to 5 images',
                    ],
                    422,
                );
            }
        }

        $product = Product::create($request->only('name', 'price'));

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $image) {
                $path = $image->store('product_images', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }
        return response()->json([
            'success' => true,
            'redirect' => route('products.index'),
        ]);
    }

    public function edit(Product $product)
    {
        return view('products.create', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'files.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $existingCount = $product->images()->count();
        $newCount = $request->hasFile('files') ? count($request->file('files')) : 0;

        if ($existingCount + $newCount > 5) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Total images cannot exceed 5',
                ],
                422,
            );
        }

        $product->update($request->only('name', 'price'));

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $image) {
                $path = $image->store('product_images', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        return response()->json([
            'success' => true,
            'redirect' => route('products.index'),
        ]);
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            if (\Storage::disk('public')->exists($image->image_path)) {
                \Storage::disk('public')->delete($image->image_path);
            }
             $image->delete();
        }

        $product->delete();
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }

    public function destroyImage($id)
    {
        $image = ProductImage::findOrFail($id);
        if (\Storage::disk('public')->exists($image->image_path)) {
            \Storage::disk('public')->delete($image->image_path);
        }
        $image->delete();

        return response()->json(['success' => true]);
    }

    public function showCartItems()
    {
        // $user = User::with(['cartItems.product'])->findOrFail($userId);
         $users = \App\Models\User::with('cartItems.product')->get();
        return view('cart.index', compact('users'));
    }
}
 