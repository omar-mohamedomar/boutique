@extends('admin.layouts.master')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Product</h1>
        </div>
        <div class="card card-primary">
            <div class="card-header">
                <h4>Create Product</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">Category</label>
                        <select name="category" id="category"
                            class="form-control select2 @error('category') is-invalid @enderror">
                            <option value="">---Select---</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"{{ old('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">Brand</label>
                        <select name="brand" id="brand"
                            class="form-control select2 @error('brand') is-invalid @enderror">
                            <option value="">---Select</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('brand')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">Title</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                            placeholder="Title" value="{{ old('title') }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        @for ($i = 1; $i <= 4; $i++)
                            @php
                                $index = $i - 1;
                            @endphp
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="image-upload-{{ $i }}">Image {{ $i }}</label>

                                    <div class="image-preview" id="image-preview-{{ $i }}">
                                        <label for="image-upload-{{ $i }}"
                                            id="image-label-{{ $i }}">Choose File</label>
                                        <input type="file" name="images[]" id="image-upload-{{ $i }}"
                                            class="form-control @error('images.' . $index) is-invalid @enderror">
                                    </div>
                                    @error('images.' . $index)
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror

                                    <div class="form-check mt-2">
                                        <input class="form-check-input @error('is_main') is-invalid @enderror"
                                            type="radio" name="is_main" value="{{ $index }}"
                                            {{ old('is_main', 0) == $index ? 'checked' : '' }}>
                                        <label class="form-check-label">Set as main image</label>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                    @error('images')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror

                    @error('is_main')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror


                    <div class="form-group">
                        <label for="">Content</label>
                        <textarea name="content" class="summernote-simple @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pricing</label>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="price" class="form-label">Original Price <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror"
                                    name="price" id="price" step="0.01" value="{{ old('price') }}">
                            </div>

                            <div class="col-md-6">
                                <label for="sale_price" class="form-label">Sale Price (Optional)</label>
                                <input type="number" class="form-control @error('sale_price') is-invalid @enderror"
                                    name="sale_price" id="sale_price" step="0.01" min="0" value="{{ old('sale_price') }}">
                            </div>
                        </div>

                        @if ($errors->has('price') || $errors->has('sale_price'))
                            <div class="invalid-feedback">
                                {{ $errors->first('price') ?: $errors->first('sale_price') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" name="stock" id="stock"
                            class="form-control @error('stock') is-invalid @enderror" step="1" min="0"
                            value="{{ old('stock') }}">
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">Tags</label>
                        <select name="tags[]" id="tags" multiple
                            class="form-control @error('tags') is-invalid @enderror">
                            @foreach ($allTags as $tag)
                                <option value="{{ $tag->name }}"
                                    {{ collect(old('tags'))->contains($tag->name) ? 'selected' : '' }}>{{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('tags')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">Meta Title</label>
                        <input name="meta_title" type="text"
                            class="form-control @error('meta_title') is-invalid @enderror" id="meta_title"
                            value="{{ old('meta_title') }}">
                        @error('meta_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="">Meta Description</label>
                        <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="">Status</label>
                        <select name="status" class="form-control select2 @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            for (let i = 1; i <= 4; i++) {
                const inputSelector = "#image-upload-" + i;
                const radioSelector = 'input[type="radio"][value="' + (i - 1) + '"]';

                $.uploadPreview({
                    input_field: inputSelector,
                    preview_box: "#image-preview-" + i,
                    label_field: "#image-label-" + i,
                    label_default: "Choose File",
                    label_selected: "Change File",
                    no_label: false,
                    success_callback: null
                });

                // radio
                $(inputSelector).on('change', function() {
                    const fileSelected = this.files.length > 0;

                    if (fileSelected) {
                        $(radioSelector).prop('disabled', false);
                    } else {
                        $(radioSelector).prop('disabled', true).prop('checked', false);
                    }
                });

            }
        });


        $(document).ready(function() {
            $('#tags').select2({
                tags: true,
                tokenSeparators: [',']
            });
        });
    </script>
@endpush
