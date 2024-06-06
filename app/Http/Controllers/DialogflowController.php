<?php

namespace App\Http\Controllers;

use App\Helper\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductFlashSale;
use Illuminate\Http\Request;
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
            case 'promotions':
                return $this->handlePromotions($request);
           case 'track_order':
                return $this->handleTrackOrder($request);
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

        if ($productName) {
            $product = Product::where('title', 'like', '%' . $productName . '%')->first();

            if ($product) {
                if ($user) {
                    // User is authenticated
                    $cartItem = CartItem::where(['user_id' => $user->id, 'product_id' => $product->id])->first();
                    if ($cartItem) {
                        $cartItem->increment('quantity', $quantity);
                    } else {
                        CartItem::create([
                            'user_id' => $user->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                        ]);
                    }
                    $response = [
                        'fulfillmentText' => 'Added ' . $quantity . ' units of ' . $product->title . ' to your cart successfully.',
                    ];
                } else {
                    // User is not authenticated, use cookie cart
                    $cartItems = Cart::getCookieCartItems();
                    $isProductExists = false;
                    foreach ($cartItems as &$item) {
                        if ($item['product_id'] === $product->id) {
                            $item['quantity'] += $quantity;
                            $isProductExists = true;
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
                    }
                    Cart::setCookieCartItems($cartItems);

                    $response = [
                        'fulfillmentText' => 'Added ' . $quantity . ' units of ' . $product->title . ' to your cart successfully.',
                    ];
                }
            } else {
                $response = [
                    'fulfillmentText' => 'Sorry, I could not find any product named ' . $productName . '.',
                ];
            }
        } else {
            $response = [
                'fulfillmentText' => 'Please specify a product name and quantity.',
            ];
        }

        return response()->json($response);
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

                $promotionText = $promotion ? 'There is a discount of ' . $promotion->discount . '% on ' . $product->title . '.' : 'No discounts available for ' . $product->title . ' at the moment.';

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





    private function handleTrackOrder(Request $request)
    {
        // Extract the order ID from the request
        $orderId = $request->input('queryResult.parameters.orderId') ?? null;

        if ($orderId) {
            $order = Order::find($orderId);

            if ($order) {
                $response = [
                    'fulfillmentText' => 'Your order status is: ' . $order->status . '. Estimated delivery: ' . $order->estimated_delivery_date . '.',
                ];
            } else {
                $response = [
                    'fulfillmentText' => 'Sorry, I could not find any order with ID ' . $orderId . '.',
                ];
            }
        } else {
            $response = [
                'fulfillmentText' => 'Please provide your order ID.',
            ];
        }

        return response()->json($response);
    }

}
