@extends('layouts.app')

@section('content')
    {{-- <h2>Cart Items for User: {{ $user->name }} (ID: {{ $user->id }})</h2> --}}

    <div class="bg-gray-100 h-screen py-8">
        <div class="container mx-auto px-4">
            <h1 class="text-2xl font-semibold mb-4">Shopping Cart</h1>
            <div class="flex flex-col md:flex-row gap-4">
                <div class="md:w-3/4">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-4">
                        @foreach ($users as $user)
                            <h3 class="font-bold mb-2">User: {{ $user->name }} ({{ $user->email }})</h3>

                            @if ($user->cartItems->isEmpty())
                                <p>No cart items for this user.</p>
                            @else
                                <table class="w-full my-2 border-collapse border border-gray-300">
                                    <thead>
                                        <tr>
                                            <th class="border border-gray-300 px-4 py-2">Product Name</th>
                                            <th class="border border-gray-300 px-4 py-2">Price</th>
                                            <th class="border border-gray-300 px-4 py-2">Quantity</th>
                                            <th class="border border-gray-300 px-4 py-2">Total</th>
                                            <th class="border border-gray-300 px-4 py-2">Added At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user->cartItems as $cart)
                                            <tr>
                                                <td class="border border-gray-300 px-4 py-2">
                                                    {{ $cart->product?->name ?? 'N/A' }}</td>
                                                <td class="border border-gray-300 px-4 py-2">₹
                                                    {{ number_format($cart->product->price ?? 0, 2) }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $cart->quantity }}</td>
                                                <td class="border border-gray-300 px-4 py-2">₹
                                                    {{ number_format(($cart->product->price ?? 0) * $cart->quantity, 2) }}
                                                </td>
                                                <td class="border border-gray-300 px-4 py-2">
                                                    {{ $cart->created_at->format('Y-m-d H:i:s') }}
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                <p class="font-semibold mb-5 ">
                                            Total Cart Amount: ₹
                                            {{ number_format(
                                                $user->cartItems->sum(function ($cart) {
                                                    return ($cart->product->price ?? 0) * $cart->quantity;
                                                }),
                                                2,
                                            ) }}
                                        </p>
                            @endif
                            <hr>
                        @endforeach



                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
