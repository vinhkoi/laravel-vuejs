<?php

namespace App\Http\Controllers;

use App\Helper\Cart;
use App\Http\Controllers\User\CartController;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductFlashSale;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DialogflowController extends Controller
{
    //
    public function handleWebhook(Request $request)
    {
        $action = $request->input('queryResult.action');

        switch ($action) {
            case 'product.search':
                return $this->handleProductSearch($request);
            case 'check_stock':
                return $this->handleCheckStock($request);
            case 'recommend_products':
                return $this->handleRecommendProducts($request);
            case 'place_order':
                return $this->handlePlaceOrder($request);
            case 'product.sale':
                return $this->handlePromotions($request);

            case 'product.wishlist':
                return $this->handleAddToWishlist($request);
            default:
                return response()->json(['fulfillmentText' => 'How can I assist you today?']);
        }
    }

    private function handleProductSearch(Request $request)
    {
        // Extract the product name from the request
        $productName = $request->input('queryResult.parameters.product')[0] ?? null;

        if ($productName) {
            $product = Product::where('title', 'like', '%' . $productName . '%')->first();

            if ($product) {
                $response = [
                    'fulfillmentText' => 'The price of ' . $product->title . ' is ' . $product->price . '.',
                ];
            } else {
                $response = [
                    'fulfillmentText' => 'Sorry, I could not find any product named ' . $productName . '.',
                ];
            }
        } else {
            $response = [
                'fulfillmentText' => 'Please specify a product name.',
            ];
        }

        return response()->json($response);
    }

    private function handleCheckStock(Request $request)
    {
        // Extract the product name from the request
        $productName = $request->input('queryResult.parameters.product')[0] ?? null;

        if ($productName) {
            $product = Product::where('title', 'like', '%' . $productName . '%')->first();

            if ($product) {
                if ($product->inStock > 0) {
                    $response = [
                        'fulfillmentText' => $product->title . ' is in stock. We have ' . $product->quantity . ' units available.',
                    ];
                } else {
                    $response = [
                        'fulfillmentText' => 'Sorry, ' . $product->title . ' is out of stock.',
                    ];
                }
            } else {
                $response = [
                    'fulfillmentText' => 'Sorry, I could not find any product named ' . $productName . '.',
                ];
            }
        } else {
            $response = [
                'fulfillmentText' => 'Please specify a product name.',
            ];
        }

        return response()->json($response);
    }

    private function handleRecommendProducts(Request $request)
    {
        // Extract the product name from the request
        $productName = $request->input('queryResult.parameters.product')[0] ?? null;

        if ($productName) {
            $product = Product::where('title', 'like', '%' . $productName . '%')->first();

            if ($product) {
                $relatedProducts = Product::where('title', 'like', '%' . $productName . '%')
                    ->orWhere('category_id', $product->category_id)
                    ->where('id', '!=', $product->id)
                    ->take(3)
                    ->get();

                $relatedText = $relatedProducts->isEmpty() ? 'No related products found.' : 'You may also like: ' . $relatedProducts->implode('title', ', ') . '.';

                $response = [
                    'fulfillmentText' => 'The price of ' . $product->title . ' is ' . $product->price . '. ' . $relatedText,
                ];
            } else {
                $response = [
                    'fulfillmentText' => 'Sorry, I could not find any product named ' . $productName . '.',
                ];
            }
        } else {
            $response = [
                'fulfillmentText' => 'Please specify a product name.',
            ];
        }

        return response()->json($response);
    }

    private function handlePlaceOrder(Request $request)
    {
        $productName = $request->input('queryResult.parameters.product')[0] ?? null;
        $quantity = $request->input('queryResult.parameters.quantity') ?? 1;
        $user = $request->user();
        Log::info('Dialogflow Request data:', $request->all()); // Log toàn bộ request

        Log::info('handlePlaceOrder', ['productName' => $productName, 'quantity' => $quantity, 'user' => $user ? $user->id : 'Guest']);

        if ($productName) {
            $product = Product::where('title', 'like', '%' . $productName . '%')->first();
            Log::info('Product found', ['product' => $product]);

            if ($product) {
                try {
                    if ($user) {
                        Log::info('Authenticated user', ['user_id' => $user->id]);
                        $cartItem = CartItem::where(['user_id' => $user->id, 'product_id' => $product->id])->first();
                        if ($cartItem) {
                            $cartItem->increment('quantity', $quantity);
                            Log::info('Cart item updated', ['cartItem' => $cartItem]);
                        } else {
                            CartItem::create([
                                'user_id' => $user->id,
                                'product_id' => $product->id,
                                'quantity' => $quantity,
                            ]);
                            Log::info('Cart item created', ['product_id' => $product->id, 'quantity' => $quantity]);
                        }
                    } else {
                        Log::info('Unauthenticated user, using cookie cart');
                        $cartItems = Cart::getCookieCartItem();
                        Log::info('Current cookie cart items', ['cartItems' => $cartItems]);

                        $isProductExists = false;
                        foreach ($cartItems as &$item) {
                            if ($item['product_id'] === $product->id) {
                                $item['quantity'] += $quantity;
                                $isProductExists = true;
                                Log::info('Updated cart item in cookie', ['item' => $item]);
                                break;
                            }
                        }

                        if (!$isProductExists) {
                            $cartItems[] = [
                                'user_id' => null,
                                'product_id' => $product->id,
                                'quantity' => $quantity,
                                'price' => $product->price,
                            ];
                            Log::info('Added new cart item to cookie', ['product_id' => $product->id, 'quantity' => $quantity]);
                        }
                        Cart::setCookieCartItem($cartItems);

                        // Verify that cookie was set correctly
                        $updatedCartItems = Cart::getCookieCartItem();
                        Log::info('Updated cookie cart items', ['cartItems' => $updatedCartItems]);
                    }

                    Log::info('Product added to cart', ['productName' => $productName, 'quantity' => $quantity]);

                    return response()->json([
                        'fulfillmentText' => 'Added ' . $quantity . ' units of ' . $product->title . ' to your cart successfully.',
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error adding product to cart', ['message' => $e->getMessage()]);

                    return response()->json([
                        'fulfillmentText' => 'An error occurred while adding the product to the cart.',
                    ]);
                }
            } else {
                return response()->json([
                    'fulfillmentText' => 'Sorry, I could not find any product named ' . $productName . '.',
                ]);
            }
        } else {
            return response()->json([
                'fulfillmentText' => 'Please specify a product name and quantity.',
            ]);
        }
    }



    private function handlePromotions(Request $request)
    {
        // Extract the product name from the request
        $productName = $request->input('queryResult.parameters.product')[0] ?? null;

        if ($productName) {
            $product = Product::where('title', 'like', '%' . $productName . '%')->first();

            if ($product) {
                // Check for promotions
                $promotion = ProductFlashSale::where('product_id', $product->id)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();

                $promotionText = $promotion ? 'There is a discount of ' . $promotion->discount_rate . '% on ' . $product->title .'. The price after discount is $' . $promotion->discounted_price . '.' : 'No discounts available for ' . $product->title . ' at the moment.';

                $response = [
                    'fulfillmentText' => $promotionText,
                ];
            } else {
                $response = [
                    'fulfillmentText' => 'Sorry, I could not find any product named ' . $productName . '.',
                ];
            }
        } else {
            $response = [
                'fulfillmentText' => 'Please specify a product name.',
            ];
        }

        return response()->json($response);
    }

    public function handleAddToWishlist(Request $request)
    {
        $productName = $request->input('queryResult.parameters.product')[0] ?? null;
        $user = Auth::user(); // Sử dụng Auth facade để xác nhận người dùng hiện tại

        Log::info('handleAddToWishlist', ['productName' => $productName, 'user' => $user ? $user->name : 'Guest']);

        // if (!$user) {
        //     return response()->json([
        //         'fulfillmentText' => 'You need to be logged in to add products to your wishlist.',
        //     ]);
        // }

        if ($productName) {
            $product = Product::where('title', 'like', '%' . $productName . '%')->first();

            if ($product) {
                try {
                    // Thêm user vào request trước khi gọi WishlistController
                    $wishlistRequest = new Request($request->all());
                    $wishlistRequest->setUserResolver(function () use ($user) {
                        return $user;
                    });

                    $wishlistController = new WishlistController();
                    $response = $wishlistController->store($wishlistRequest, $product);

                    Log::info('Wishlist Store Response', ['status' => $response->getStatusCode(), 'content' => $response->getContent()]);

                    if ($response->getStatusCode() == 302) { // Redirect response status
                        return response()->json([
                            'fulfillmentText' => $product->title . ' has been added to your wishlist successfully.',
                        ]);
                    } else {
                        return response()->json([
                            'fulfillmentText' => 'Failed to add ' . $product->title . ' to your wishlist.',
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Wishlist Store Error', ['message' => $e->getMessage()]);
                    return response()->json([
                        'fulfillmentText' => 'An error occurred while adding the product to the wishlist.',
                    ]);
                }
            } else {
                return response()->json([
                    'fulfillmentText' => 'Sorry, I could not find any product named ' . $productName . '.',
                ]);
            }
        } else {
            return response()->json([
                'fulfillmentText' => 'Please specify a product name.',
            ]);
        }
    }
}
