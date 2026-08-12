<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class ProductController extends Controller
{
     public function index()
    {
        $products = products::query()
            ->with([
                'category',
                'details',
                'inventory',
            ])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function show(Products $product)
    {
        $product->load([
            'category',
            'details',
            'inventory',
        ]);

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'image' => [
                'nullable',
                'string',
                'max:255',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'brand' => [
                'nullable',
                'string',
                'max:255',
            ],

            'weight' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'unit' => [
                'nullable',
                'string',
                'max:50',
            ],

            'origin' => [
                'nullable',
                'string',
                'max:255',
            ],

            'ingredients' => [
                'nullable',
                'string',
            ],

            'nutrition' => [
                'nullable',
                'string',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'low_stock_limit' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $product = DB::transaction(function () use ($validated) {

            $product = Products::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'image' => $validated['image'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $product->details()->create([
                'brand' => $validated['brand'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'unit' => $validated['unit'] ?? null,
                'origin' => $validated['origin'] ?? null,
                'ingredients' => $validated['ingredients'] ?? null,
                'nutrition' => $validated['nutrition'] ?? null,
            ]);

            $product->inventory()->create([
                'quantity' => $validated['quantity'],
                'low_stock_limit' => $validated['low_stock_limit'],
            ]);

            return $product;
        });

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $product->load([
                'category',
                'details',
                'inventory',
            ]),
        ], 201);
    }

    public function update(
        Request $request,
        products $product
    ) {
        $validated = $request->validate([
            'category_id' => [
                'sometimes',
                'required',
                'exists:categories,id',
            ],

            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'price' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
            ],

            'image' => [
                'nullable',
                'string',
                'max:255',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug(
                $validated['name']
            );
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => $product->fresh()->load([
                'category',
                'details',
                'inventory',
            ]),
        ]);
    }

    public function destroy(Products $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }

    public function updateStock(
        Request $request,
        products $product
    ) {
        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'low_stock_limit' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $product->inventory()->updateOrCreate(
            [
                'product_id' => $product->id,
            ],
            [
                'quantity' => $validated['quantity'],
                'low_stock_limit' => $validated['low_stock_limit'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully.',
            'data' => $product->fresh()->load('inventory'),
        ]);
    }
}
