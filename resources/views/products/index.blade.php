@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-6">
        <h2 class="text-2xl font-bold mb-6">All Products</h2>

        <div class="mb-4 text-right">
            <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Add Product
            </a>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($products as $product)
                <div class="bg-white shadow rounded-lg p-4" id="product-card-{{ $product->id }}">
                    <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                    <p class="text-gray-700 mb-2">₹ {{ number_format($product->price, 2) }}</p>

                    <div class="flex flex-wrap gap-2 mb-3">
                        @forelse($product->images as $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product Image"
                                class="w-24 h-24 object-cover rounded border zoomable-image cursor-pointer">
                        @empty
                            <p class="text-sm text-gray-500">No images</p>
                        @endforelse
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('products.edit', $product->id) }}"
                            class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                            Edit
                        </a>


                        <button class="delete-btn bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
                            data-id="{{ $product->id }}">
                            Delete
                        </button>

                    </div>
                </div>
            @empty
                <p>No products found.</p>
            @endforelse

            <!-- Zoom Modal -->
            <div id="zoomModal" class="fixed inset-0 z-50 bg-black bg-opacity-80 hidden items-center justify-center">
                <div class="relative max-w-4xl w-full p-4">
                    <button onclick="closeZoomModal()"
                        class="absolute top-2 right-2 text-white bg-red-600 px-2 py-1 rounded hover:bg-red-700">
                        ✕
                    </button>
                    <img id="zoomImage" src=""
                        class="max-h-[80vh] w-auto mx-auto rounded border-4 border-white shadow-lg">
                </div>
            </div>


        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.delete-btn').on('click', function() {
                let id = $(this).data('id');

                if (!confirm('Are you sure to delete this product?')) return;

                $.ajax({
                    url: `/products/${id}`,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $(`#product-card-${id}`).remove();
                        } else {
                            alert('Failed to delete the product.');
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });

        function closeZoomModal() {
            document.getElementById('zoomModal').classList.add('hidden');
            document.getElementById('zoomImage').src = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.zoomable-image').forEach(image => {
                image.addEventListener('click', function() {
                    const zoomModal = document.getElementById('zoomModal');
                    const zoomImage = document.getElementById('zoomImage');
                    zoomImage.src = this.src;
                    zoomModal.classList.remove('hidden');
                });
            });
        });
    </script>


@endsection
