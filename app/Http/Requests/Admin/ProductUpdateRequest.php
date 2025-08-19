<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category'         => 'required|string|max:255',
            'brand'            => 'nullable|string|max:255',
            'images'           => 'nullable|array|min:1|max:4',
            'images.*'         => 'nullable|image|max:2048',
            'is_main'          => 'nullable|integer',
            'title'            => 'required|string|max:255',
            'content'          => 'required|string',
            'price'            => 'required|numeric|min:0',
            'sale_price'       => 'nullable|numeric|lte:price',
            'stock'            => 'required|integer|min:0',
            'tags'             => 'nullable|array',
            'tags.*'           => 'string|max:100',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status'           => 'required|in:0,1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $images = $this->file('images', []);
            $isMain = $this->input('is_main');

            if ($isMain === null || $isMain === '') {
                return;
            }

            if (!is_numeric($isMain)) {
                $validator->errors()->add('is_main', 'The main image selection must be a number.');
                return;
            }

            $isMain = (int) $isMain;
            $validIndexes = [];

            foreach ($images as $index => $file) {
                if ($file && $file->isValid()) {
                    $validIndexes[] = (int)$index;
                }
            }

            $productId = $this->route('product') ?? $this->route('id');
            if ($productId) {
                $product = Product::with('images')->find($productId);
                if ($product) {
                    $existing = $product->images->values();
                    foreach ($existing as $idx => $img) {
                        $validIndexes[] = (int)$idx;
                    }
                }
            }

            $validIndexes = array_unique($validIndexes);

            if (!in_array($isMain, $validIndexes)) {
                $validator->errors()->add('is_main', 'The selected main image is invalid or was not uploaded.');
            }
        });
    }
}
