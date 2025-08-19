@extends('admin.layouts.master')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Products</h1>
        </div>
        <div class="card card-primary">
            <div class="card-header">
                <h4>All Products</h4>
                <div class="card-header-action">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        Create new <i class="fas fa-plus"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table-1">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    #
                                </th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <img src="{{ asset($product->main_image->image_path) }}"
                                            style="width: 70px; height: 70px; object-fit: cover;" alt="Main Image"
                                            class="rounded img-thumbnail">
                                    </td>

                                    <td>
                                        <span data-bs-toggle="tooltip"
                                            title="{{ $product->sku }}">{{ $product->title }}</span>

                                        <div class="badge-wrapper mt-1">
                                            @if ($product->badge)
                                                <span class="badge {{ $product->badge['class'] }}">
                                                    {{ $product->badge['text'] }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td>{{ $product->category->name }}</td>

                                    <td>
                                        @if ($product->has_discount)
                                            <span class="text-muted mr-2">
                                                <del>${{ number_format($product->price, 2) }}</del>
                                            </span>
                                            <span class="font-weight-bold text-danger">
                                                ${{ number_format($product->sale_price, 2) }}
                                            </span>
                                            <small class="text-success ml-2">
                                                (discount {{ $product->discount_percent }}%)
                                            </small>
                                        @else
                                            <span class="font-weight-bold">
                                                ${{ number_format($product->price, 2) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($product->stock == 0)
                                            <span class="text-danger">
                                                Sold Out
                                            </span>
                                        @else
                                            <span class="">
                                                {{ $product->stock }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($product->status == 1)
                                            <span class="badge badge-light text-success">Active</span>
                                        @else
                                            <span class="badge badge-light text-danger">Inactive</span>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.products.edit', $product->id) }}"
                                            class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                        <a href="{{ route('admin.products.destroy', $product->id) }}"
                                            class="btn btn-danger delete-item"><i class="fas fa-trash-alt"></i></a>
                                        <a href="{{ route('admin.product-copy', $product->id) }}"
                                            class="btn btn-success"><i class="fas fa-copy"></i></a>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $("#table-1").dataTable({
            "columnDefs": [{
                "sortable": false,
                "targets": [2, 3]
            }]
        });

        // تفعيل الـ Tooltip
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(t => new bootstrap.Tooltip(t));
    </script>
@endpush
