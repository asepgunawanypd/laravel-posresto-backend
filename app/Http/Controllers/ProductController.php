<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // $products = Product::paginate(10);
        // Retrieve the search query from the request
        $search = $request->get('search', '');

        $products = Product::with('category')
            ->where('name', 'like', '%' . $search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);


        return view('pages.products.index', compact('products', 'search'));
    }

    public function create()
    {
        $categories = DB::table('categories')->get();
        return view('pages.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'is_favorite' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Optional image validation
        ]);

        // Create a new product
        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        //$product->price = (int) $request->price;
        $product->price = $request->input('price');
        $product->category_id = $request->category_id;
        $product->stock = (int) $request->stock;
        $product->status = $request->status;
        $product->is_favorite = $request->is_favorite;
        $product->save();

        // Save image if present
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = $product->id . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/products', $filename);
            $product->image = 'storage/products/' . $filename;
            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }


    // public function store(Request $request)
    // {
    //     //validate request
    //     $request->validate([
    //         'name' => 'required',
    //         'description' => 'required',
    //         'price' => 'required|numeric',
    //         'category_id' => 'required',
    //         'stock' => 'required|numeric',
    //         'status' => 'required|boolean',
    //         'is_favorite' => 'required|boolean',
    //     ]);

    //     $product = new Product;
    //     $product->name = $request->name;
    //     $product->description = $request->description;
    //     $product->price = $request->price;
    //     $product->category_id = $request->category_id;
    //     $product->stock = $request->stock;
    //     $product->status = $request->status;
    //     $product->is_favorite = $request->is_favorite;
    //     // $product->image = $filename;
    //     $product->save();

    //     //save image
    //     if ($request->hasFile('image')) {
    //         $image = $request->file('image');
    //         $image->storeAs('public/products', $product->id . '.' . $image->getClientOriginalExtension());
    //         $product->image = 'storage/products/' . $product->id . '.' . $image->getClientOriginalExtension();
    //         $product->save();
    //     }

    //     return redirect()->route('products.index')->with('success', 'Product created successfully');
    // }

    public function show($id)
    {
        return view('pages.products.show');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = DB::table('categories')->get();
        return view('pages.products.edit', compact('product', 'categories'));
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'description' => 'required',
    //         'price' => 'required|numeric',
    //         'category_id' => 'required',
    //         'stock' => 'required|numeric',
    //         'status' => 'required|boolean',
    //         'is_favorite' => 'required|boolean',
    //     ]);

    //     $product = Product::find($id);
    //     $product->name = $request->name;
    //     $product->description = $request->description;
    //     $product->price = $request->price;
    //     $product->category_id = $request->category_id;
    //     $product->stock = $request->stock;
    //     $product->status = $request->status;
    //     $product->is_favorite = $request->is_favorite;
    //     $product->save();

    //     //save image
    //     if ($request->hasFile('image')) {
    //         $image = $request->file('image');
    //         $image->storeAs('public/products', $product->id . '.' . $image->getClientOriginalExtension());
    //         $product->image = 'storage/products/' . $product->id . '.' . $image->getClientOriginalExtension();
    //         $product->save();
    //     }

    //     return redirect()->route('products.index')->with('success', 'Product updated successfully');
    // }

    public function update(Request $request, $id)
    {
        // Validate request
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'is_favorite' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Find and update the product
        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->description = $request->description;
        // $product->price = (int) $request->price;
        $product->price = $request->input('price');
        $product->category_id = $request->category_id;
        $product->stock = (int) $request->stock;
        $product->status = $request->status;
        $product->is_favorite = $request->is_favorite;
        $product->save();

        // Save image if present
        if ($request->hasFile('image')) {
            // Delete the previous image if it exists
            if ($product->image && Storage::exists($product->image)) {
                Storage::delete($product->image);
            }

            // Store the new image
            $image = $request->file('image');
            $filename = $product->id . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/products', $filename);
            $product->image = 'storage/products/' . $filename;
            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }


    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product delete successfully');
    }
}
