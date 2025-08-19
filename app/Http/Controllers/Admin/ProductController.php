<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductCreateRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Tag;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('images')->get();
        return view('admin.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $brands = Brand::where('status', 1)->get();
        $allTags = Tag::all();
        return view('admin.product.create', compact('categories', 'brands', 'allTags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCreateRequest $request)
    {
        $product = Product::create([
            'category_id' => $request->category,
            'brand_id' => $request->brand,
            'author_id' => Auth::guard('admin')->user()->id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'sku' => $this->generateSKU(),
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'stock' => $request->stock,
            'status' => $request->status,
        ]);

        /** Handle Multiple Image */
        $imagePaths = $this->handleMultipleFileUpload($request, 'images');
        $mainImageIndex = (int) $request->input('is_main', 0);
        foreach ($imagePaths as $index => $path) {
            Image::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_main'    => $index === $mainImageIndex,
            ]);
        }

        // $tagIds = [];

        // if ($request->filled('tags')) {
        //     foreach ($request->tags as $tagName) {
        //         $cleanTag = trim(strtolower($tagName));

        //         if ($cleanTag === '') continue;

        //         $tag = Tag::firstOrCreate(['name' => $cleanTag]);

        //         $tagIds[] = $tag->id;
        //     }

        //     $product->tags()->sync($tagIds);
        // }

        if ($request->filled('tags')) {
            $tagIds = collect($request->tags)
                ->map(function ($tagName) {
                    return Tag::firstOrCreate(['name' => trim(strtolower($tagName))])->id;
                })
                ->toArray();

            $product->tags()->sync($tagIds);
        }

        toast('created Successfully!', 'success');
        return redirect()->route('admin.products.index');
    }

    private function generateSKU(): string
    {
        $latestId = Product::max('id') ?? 0;
        $nextId = $latestId + 1;

        return 'PRD-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with('images')->findOrFail($id);
        $categories = Category::where('status', 1)->get();
        $brands = Brand::where('status', 1)->get();
        $allTags = Tag::all();
        $productTags = $product->tags->pluck('name')->toArray();
        return view('admin.product.edit', compact('product', 'categories', 'brands', 'allTags', 'productTags'));
    }


    public function update(ProductUpdateRequest $request, string $id)
    {
        $product = Product::with('images')->findOrFail($id);

        $product->update([
            'category_id'       => $request->category,
            'brand_id'          => $request->brand,
            'title'             => $request->title,
            'slug'              => Str::slug($request->title),
            'content'           => $request->content,
            'meta_title'        => $request->meta_title,
            'meta_description'  => $request->meta_description,
            'price'             => $request->price,
            'sale_price'        => $request->sale_price,
            'stock'             => $request->stock,
            'status'            => $request->status,
        ]);


        $oldImagePaths = $product->images->pluck('image_path')->toArray();
        $newFiles = $request->file('images', []);

        $updatedPaths = $this->updateMultipleFiles($oldImagePaths, $newFiles);

        foreach ($updatedPaths as $index => $path) {
            $imageModel = $product->images->get($index);
            if ($imageModel) {
                $imageModel->update([
                    'image_path' => $path,
                    'is_main' => ((int)$request->input('is_main', 0) === $index)
                ]);
            } else {
                Image::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_main' => ((int)$request->input('is_main', 0) === $index)
                ]);
            }
        }

        foreach ($product->images as $index => $image) {
            if (!isset($updatedPaths[$index])) {
                $image->update(['is_main' => ((int)$request->input('is_main', 0) === $index)]);
            }
        }

        if ($request->filled('tags')) {
            $tagIds = collect($request->tags)
                ->map(fn($tagName) => Tag::firstOrCreate(['name' => trim(strtolower($tagName))])->id)
                ->toArray();
            $product->tags()->sync($tagIds);
        } else {
            $product->tags()->detach();
        }

        toast('Updated Successfully!', 'success');
        return redirect()->route('admin.products.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        $imagePaths = $product->images->pluck('image_path')->toArray();
        $this->deleteMultipleFiles($imagePaths);
        $product->images()->delete();
        $product->tags()->detach();
        $product->delete();

        return response(['status' => 'success', 'message' => 'Deleted Successfully!']);
    }

    public function copyProduct(string $id)
    {
        $product = Product::findOrFail($id);
        $copyProduct = $product->replicate();
        $copyProduct->sku = $this->generateSKU();
        $copyProduct->save();

        foreach ($product->images as $image) {
            $copyImage = $image->replicate();
            $copyImage->product_id = $copyProduct->id;

            $originalPath = public_path($image->image_path); // full path to old image
            if (file_exists($originalPath)) {
                $newImageName = uniqid() . '-' . basename($image->image_path);
                $newImagePath = 'uploads/' . $newImageName;
                copy($originalPath, public_path($newImagePath));
                $copyImage->image_path = $newImagePath;
            }


            $copyImage->save();
        }

        $tagIds = $product->tags->pluck('id')->toArray();
        $copyProduct->tags()->sync($tagIds);

        toast('Copied Successfully!', 'success');
        return redirect()->back();
    }
}
