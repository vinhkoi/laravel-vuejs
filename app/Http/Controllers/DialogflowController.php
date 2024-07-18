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
            case 'delivery.options':
                return $this->handleDelivery($request);
            // default:
            //     return response()->json(['fulfillmentText' => 'Hôm nay tôi có thể giúp gì cho bạn?']);
        }
    }

    private function handleProductSearch(Request $request)
    {
        // Extract the product name from the request
        $productName = $request->input('queryResult.parameters.product')[0] ?? null;

        if ($productName) {
            $product = Product::where('title', 'like', '%' . $productName . '%')->first();

            if ($product) {
                $responseText = "Sản phẩm: " . $product->title . "\n";
                $responseText .= "Giá: " . $product->price . "\n";
                $responseText .= "Mô tả: " . $product->description . "\n";
                $responseText .= "Tình trạng kho: " . ($product->inStock == 1 ? 'Còn hàng' : 'Hết hàng') . "\n";
                // Nếu có thông tin khuyến mãi
                $promotion = ProductFlashSale::where('product_id', $product->id)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();

                if ($promotion) {
                    $responseText .= "Khuyến mãi: Đang có giảm giá " . $promotion->discount_rate . "% cho sản phẩm " . $product->title . ". Giá sau khi giảm là " . $promotion->discounted_price . ".\n";
                } else {
                    $responseText .= "Khuyến mãi: Hiện không có giảm giá cho " . $product->title . " ở thời điểm hiện tại.\n";
                }

                $response = [
                    'fulfillmentMessages' => [
                        [
                            'text' => [
                                'text' => [
                                    $responseText
                                ]
                            ]
                        ],
                        [
                            'payload' => [
                                'richContent' => [
                                    [
                                        [
                                            'type' => 'info',
                                            'title' => 'Sản phẩm: ' . $product->title,
                                            'subtitle' => 'Giá: ' . $product->price,
                                            'image' => [
                                                'src' => [
                                                    'rawUrl' => 'https://poetic-new-rhino.ngrok-free.app/product_images/1715091018-0zfwYKbtkh.webp',
                                                ]
                                            ],
                                            'actionLink' => 'https://poetic-new-rhino.ngrok-free.app/detail/view/' . $product->id,
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
            } else {
                $response = [
                    'fulfillmentText' => 'Xin lỗi tôi không tìm thấy tên sản phẩm ' . $productName . '.',
                ];
            }
        } else {
            $response = [
                'fulfillmentText' => 'Vui lòng chỉ định tên sản phẩm.',
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
                        'fulfillmentText' => $product->title . ' còn hàng,chúng tôi có ' . $product->quantity . '  sản phẩm còn lại.',
                    ];
                } else {
                    $response = [
                        'fulfillmentText' => 'Xin lỗi mặt hàng ' . $product->title . ' đã hết hàng.',
                    ];
                }
            } else {
                $response = [
                    'fulfillmentText' => 'Xin lỗi tôi không tìm thấy tên sản phẩm ' . $productName . '.',
                ];
            }
        } else {
            $response = [
                'fulfillmentText' => 'Vui lòng chỉ định tên sản phẩm',
            ];
        }

        return response()->json($response);
    }

    private function handleRecommendProducts(Request $request)
    {
        // Extract the product name from the request
        $productName = $request->input('queryResult.parameters.product1')[0] ?? null;

        if ($productName) {
            $product = Product::where('title', 'like', '%' . $productName . '%')->first();

            if ($product) {
                $relatedProducts = Product::where('category_id', $product->category_id)
                    ->where('id', '!=', $product->id)
                    ->take(3)
                    ->get();

                $relatedText = $relatedProducts->isEmpty()
                    ? 'Không tìm thấy sản phẩm tương tự.'
                    : 'Bạn cũng có thể thích: ' . $relatedProducts->pluck('title')->implode(', ') . '.';

                $response = [
                    'fulfillmentText' => 'Giá của ' . $product->title . ' là ' . $product->price . '. ' . $relatedText,
                ];
            } else {
                $response = [
                    'fulfillmentText' => 'Xin lỗi, tôi không tìm thấy sản phẩm nào tên là ' . $productName . '.',
                ];
            }
        } else {
            $response = [
                'fulfillmentText' => 'Vui lòng chỉ định tên sản phẩm.',
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
        $productName = $request->input('queryResult.parameters.product2')[0] ?? null;

        if ($productName) {
            $product = Product::where('title', 'like', '%' . $productName . '%')->first();

            if ($product) {
                // Check for promotions of the specified product
                $promotion = ProductFlashSale::where('product_id', $product->id)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();

                $promotionText = $promotion ? 'Đang có giảm giá ' . $promotion->discount_rate . '% cho sản phẩm ' . $product->title . '. Giá sau khi giảm là ' . $promotion->discounted_price . '.' : 'Hiện không có giảm giá cho ' . $product->title . ' ở thời điểm hiện tại.';

                $response = [
                    'fulfillmentText' => $promotionText,
                ];
            } else {
                $response = [
                    'fulfillmentText' => 'Sorry, I could not find any product named ' . $productName . '.',
                ];
            }
        } else {
            // Fetch all promotions currently active
            $promotions = ProductFlashSale::where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->get();

            if ($promotions->isEmpty()) {
                $response = [
                    'fulfillmentText' => 'Hiện không có chương trình khuyến mãi nào.',
                ];
            } else {
                $promotionText = 'Các chương trình khuyến mãi hiện có:' . "\n";
                foreach ($promotions as $promotion) {
                    $product = Product::find($promotion->product_id);
                    $promotionText .= 'Giảm giá ' . $promotion->discount_rate . '% cho sản phẩm ' . $product->title . "\n";
                }

                $response = [
                    'fulfillmentText' => $promotionText,
                ];
            }
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
    private function handleDelivery(Request $request)
    {
        // Extract necessary parameters from the request
        $orderId = $request->input('queryResult.parameters.order_id')[0] ?? null;
        $queryType = $request->input('queryResult.parameters.query_type');

        if (!$orderId || !$queryType) {
            $response = [
                'fulfillmentText' => 'Vui lòng cung cấp đầy đủ thông tin về mã đơn hàng và loại thông tin cần tra cứu.',
            ];
            return response()->json($response);
        }

        // Retrieve order information from the database
        $order = Order::find($orderId);

        if (!$order) {
            $response = [
                'fulfillmentText' => 'Xin lỗi, không tìm thấy thông tin cho đơn hàng có mã ' . $orderId . '.',
            ];
            return response()->json($response);
        }

        // Process based on query type (delivery time or shipping cost)
        $responseText = '';

        switch ($queryType) {
            case 'delivery_time':
                $deliveryTime = $order->estimated_delivery_time;
                $responseText = 'Thời gian dự kiến đơn hàng số ' . $orderId . ' đến nơi là ' . $deliveryTime . '.';
                break;
            case 'shipping_cost':
                $shippingCost = $order->shipping_cost;
                $responseText = 'Chi phí vận chuyển của đơn hàng số ' . $orderId . ' là ' . $shippingCost . '.';
                break;
            default:
                $responseText = 'Xin lỗi, tôi không thể xử lý yêu cầu này.';
                break;
        }

        $response = [
            'fulfillmentText' => $responseText,
        ];

        return response()->json($response);
    }
}
