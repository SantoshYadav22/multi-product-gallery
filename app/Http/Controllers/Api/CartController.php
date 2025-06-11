<?php

// app/Http/Controllers/Api/CartController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserWithCartResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    public function index()
    {
        try {
            $userId = 1;

            $user = User::with(['cartItems.product'])->findOrFail($userId);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'User cart retrieved successfully.',
                    'data' => [
                        'user' => new UserWithCartResource($user),
                    ],
                ],
                200,
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'User not found.',
                ],
                404,
            );
        } catch (\Exception $e) {
            Log::error('Cart retrieval error: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'An unexpected error occurred.',
                ],
                500,
            );
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|min:1',
                'product_id' => [
                    'required',
                     Rule::exists('products', 'id')->whereNull('deleted_at'),
                ],
                'quantity' => 'nullable|integer|min:1',
            ], [
                'product_id.exists' => 'Product does not exist or has been removed.',
            ]);

            $cart = Cart::create([
                'user_id' => $validated['user_id'],
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'] ?? 1,
            ]);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Product added to cart successfully.',
                    'data' => $cart,
                ],
                Response::HTTP_CREATED,
            );
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        } catch (\Exception $e) {
            Log::error('Error adding to cart: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'An unexpected error occurred. Please try again later.',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function updateByCart(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:carts,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $userId = 1;

            $cart = Cart::where('user_id', $userId)->where('id', $request->id)->firstOrFail();

            $cart->update([
                'quantity' => $request->quantity,
            ]);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Cart item updated successfully',
                    'data' => $cart,
                ],
                Response::HTTP_OK,
            );
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Cart item not found.',
                ],
                Response::HTTP_NOT_FOUND,
            );
        } catch (\Exception $e) {
            Log::error('Cart update error: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Something went wrong. Please try again later.',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function deleteByCart(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:products,id',
            ]);

            $userId = 1;

            $cart = Cart::where('user_id', $userId)->where('id', $request->id)->first();

            if (!$cart) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Cart item not found for this product and user.',
                    ],
                    Response::HTTP_NOT_FOUND,
                );
            }

            $cart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart item deleted successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        } catch (\Exception $e) {
            Log::error('Cart deletion error: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'An unexpected error occurred.',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}