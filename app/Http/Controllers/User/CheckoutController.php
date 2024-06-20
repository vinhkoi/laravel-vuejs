<?php

namespace App\Http\Controllers\User;
use Omnipay\Omnipay;
use App\Helper\Cart;
use App\Helper\MoMoPayment;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Redirect;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        $carts = $request->carts;
        $products = $request->products;

        $mergedData = [];

        foreach ($carts as $cartItem) {
            foreach ($products as $product) {
                if ($cartItem["product_id"] == $product["id"]) {
                    $mergedData[] = array_merge($cartItem, ["title" => $product["title"], 'price' => $product['price']]);
                }
            }
        }

        $stripe = new \Stripe\StripeClient(env('STRIPE_KEY'));
        $lineItems = [];
        foreach ($mergedData as $item) {
            $lineItems[] =
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $item['title'],
                        ],
                        'unit_amount' => (int)($item['price'] * 100),
                    ],
                    'quantity' => $item['quantity'],
                ];
        }



        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' =>  $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
        ]);


        $newAddress = $request->address;
        if ($newAddress['address1'] != null) {
            $address = UserAddress::where('isMain', 1)->count();
            if ($address > 0) {
                $address = UserAddress::where('isMain', 1)->update(['isMain' => 0]);
            }
            $address = new UserAddress();
            $address->address1 = $newAddress['address1'];
            $address->state = $newAddress['state'];
            $address->zipcode = $newAddress['zipcode'];
            $address->city = $newAddress['city'];
            $address->country_code = $newAddress['country_code'];
            $address->type = $newAddress['type'];
            $address->user_id = Auth::user()->id;
            $address->save();
        }
        $mainAddress = $user->user_address()->where('isMain', 1)->first();
        if ($mainAddress) {
            $order = new Order();
            $order->status = 'unpaid';
            $order->total_price = $request->total;
            $order->session_id = $checkout_session->id;
            $order->created_by = $user->id;
            $order->user_id = $user->id;
            $order->user_address_id = $mainAddress->id;
            $order->save();
            $cartItems = CartItem::where(['user_id' => $user->id])->get();
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->product->price,
                ]);
                $product = Product::find($cartItem->product_id);
                if ($product) {
                    $product->quantity -= $cartItem->quantity;
                    if ($product->quantity == 0) {
                        $product->inStock = 0; // Cập nhật cột inStock thành 0 nếu hết hàng
                    }
                    $product->save();
                }
            }

                // Dữ liệu thanh toán cho Stripe
                $paymentData = [
                    'order_id' => $order->id,
                    'amount' => $request->total,
                    'status' => 'pending',
                    'type' => 'stripe',
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ];
                Payment::create($paymentData);
        }
            return Inertia::location($checkout_session->url);
     }
     public function COD(Request $request)
    {
        $user = $request->user();
        $carts = $request->carts;
        $products = $request->products;

        $mergedData = [];

        foreach ($carts as $cartItem) {
            foreach ($products as $product) {
                if ($cartItem["product_id"] == $product["id"]) {
                    $mergedData[] = array_merge($cartItem, ["title" => $product["title"], 'price' => $product['price']]);
                }
            }
        }
        $newAddress = $request->address;
        if ($newAddress['address1'] != null) {
            $address = UserAddress::where('isMain', 1)->count();
            if ($address > 0) {
                $address = UserAddress::where('isMain', 1)->update(['isMain' => 0]);
            }
            $address = new UserAddress();
            $address->address1 = $newAddress['address1'];
            $address->state = $newAddress['state'];
            $address->zipcode = $newAddress['zipcode'];
            $address->city = $newAddress['city'];
            $address->country_code = $newAddress['country_code'];
            $address->type = $newAddress['type'];
            $address->user_id = Auth::user()->id;
            $address->save();
        }
        $mainAddress = $user->user_address()->where('isMain', 1)->first();
        if ($mainAddress) {
            $order = new Order();
            $order->status = 'unpaid';
            $order->total_price = $request->total;
            $order->created_by = $user->id;
            $order->user_id = $user->id;
            $order->user_address_id = $mainAddress->id;
            $order->save();
            $cartItems = CartItem::where(['user_id' => $user->id])->get();
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->product->price,
                ]);
                $product = Product::find($cartItem->product_id);
                if ($product) {
                    $product->quantity -= $cartItem->quantity;
                    $product->save();
                }
                $cartItem->delete();

                $cartItems = Cart::getCookieCartItems();
                foreach ($cartItems as $item) {
                    unset($item);
                }
                array_splice($cartItems, 0, count($cartItems));
                Cart::setCookieCartItems($cartItems);
            }
            $paymentMethod = $request->paymentMethod;

                // Dữ liệu thanh toán cho thanh toán khi nhận hàng
                $paymentData = [
                    'order_id' => $order->id,
                    'amount' => $request->total,
                    'status' => 'pending',
                    'type' => 'cash_on_delivery',
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ];
                Payment::create($paymentData);


        }
            // Nếu là Cash on Delivery, chuyển hướng đến trang dashboard
            return redirect()->route('dashboard');
     }
    public function success(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_KEY'));
        $sessionId = $request->get('session_id');
        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            if (!$session) {
                throw new NotFoundHttpException;
            }
            $order = Order::where('session_id', $session->id)->first();
            if (!$order) {
                throw new NotFoundHttpException();
            }
            if ($order->status === 'unpaid') {
                $order->status = 'paid';
                $order->save();
            }
            $paymentData = Payment::where('order_id', $order->id)->first();
            if ($paymentData) {
                $paymentData->status = 'success';
                $paymentData->save();
            }
            $user = $request->user();
            $cartItems = CartItem::where(['user_id' => $user->id])->get();
            foreach ($cartItems as $cartItem) {
                $cartItem->delete();

                $cartItems = Cart::getCookieCartItems();
                foreach ($cartItems as $item) {
                    unset($item);
                }
                array_splice($cartItems, 0, count($cartItems));
                Cart::setCookieCartItems($cartItems);
            }

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            throw new NotFoundHttpException();
        }
    }
    public function cancel(Request $request)
    {
        return redirect()->route('cart.view');
    }

//     public function vnpayPayment(Request $request)
// {


//     $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
//     $vnp_Returnurl = "https://localhost/vnpay_php/vnpay_return.php";
//     $vnp_TmnCode = "UTGIB2PJ";//Mã website tại VNPAY
//     $vnp_HashSecret = "POSYXTJLWTZQSLFYGNTGWHVAEKCWHPAX"; //Chuỗi bí mật

//     $vnp_TxnRef = '100000'; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã nàysang VNPAY
//     $vnp_OrderInfo = "thanh toan hoa don";
//     $vnp_OrderType ="abc";
//     $vnp_Amount = 10000 * 100;
//     $vnp_Locale = "vn";
//     $vnp_BankCode = 'NCB';
//     $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

//     $inputData = array(
//         "vnp_Version" => "2.1.0",
//         "vnp_TmnCode" => $vnp_TmnCode,
//         "vnp_Amount" => $vnp_Amount,
//         "vnp_Command" => "pay",
//         "vnp_CreateDate" => date('YmdHis'),
//         "vnp_CurrCode" => "VND",
//         "vnp_IpAddr" => $vnp_IpAddr,
//         "vnp_Locale" => $vnp_Locale,
//         "vnp_OrderInfo" => $vnp_OrderInfo,
//         "vnp_OrderType" => $vnp_OrderType,
//         "vnp_ReturnUrl" => $vnp_Returnurl,
//         "vnp_TxnRef" => $vnp_TxnRef,

//     );

//     if (isset($vnp_BankCode) && $vnp_BankCode != "") {
//         $inputData['vnp_BankCode'] = $vnp_BankCode;
//     }


//     var_dump($inputData);
//     ksort($inputData);
//     $query = "";
//     $i = 0;
//     $hashdata = "";
//     foreach ($inputData as $key => $value) {
//         if ($i == 1) {
//             $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
//         } else {
//             $hashdata .= urlencode($key) . "=" . urlencode($value);
//             $i = 1;
//         }
//         $query .= urlencode($key) . "=" . urlencode($value) . '&';
//     }

//     $vnp_Url = $vnp_Url . "?" . $query;
//     if (isset($vnp_HashSecret)) {
//         $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//
//         $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
//     }
//     $returnData = array('code' => '00'
//         , 'message' => 'success'
//         , 'data' => $vnp_Url);
//         if (isset($_POST['redirect'])) {
//             header('Location: ' . $vnp_Url);
//             die();
//         } else {
//             echo json_encode($returnData);
//         }
//         // vui lòng tham khảo thêm tại code demo

// }
public function vnpayPayment(Request $request)
{
    $user = $request->user();
    $carts = $request->carts;
    $products = $request->products;

    $mergedData = [];
    foreach ($carts as $cartItem) {
        foreach ($products as $product) {
            if ($cartItem["product_id"] == $product["id"]) {
                $mergedData[] = array_merge($cartItem, ["title" => $product["title"], 'price' => $product['price']]);
            }
        }
    }

    $newAddress = $request->address;
    if ($newAddress['address1'] != null) {
        $address = UserAddress::where('isMain', 1)->count();
        if ($address > 0) {
            $address = UserAddress::where('isMain', 1)->update(['isMain' => 0]);
        }
        $address = new UserAddress();
        $address->address1 = $newAddress['address1'];
        $address->state = $newAddress['state'];
        $address->zipcode = $newAddress['zipcode'];
        $address->city = $newAddress['city'];
        $address->country_code = $newAddress['country_code'];
        $address->type = $newAddress['type'];
        $address->user_id = Auth::user()->id;
        $address->save();
    }

    $mainAddress = $user->user_address()->where('isMain', 1)->first();
    if ($mainAddress) {
        $order = new Order();
        $order->status = 'unpaid';
        $order->total_price = $request->total;
        $order->created_by = $user->id;
        $order->user_id = $user->id;
        $order->user_address_id = $mainAddress->id;
        $order->save();

        $cartItems = CartItem::where(['user_id' => $user->id])->get();
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->product->price,
            ]);
            $product = Product::find($cartItem->product_id);
            if ($product) {
                $product->quantity -= $cartItem->quantity;
                if ($product->quantity == 0) {
                    $product->inStock = 0;
                }
                $product->save();
            }
        }

        $paymentData = [
            'order_id' => $order->id,
            'amount' => $request->total,
            'status' => 'pending',
            'type' => 'vnpay',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ];
        Payment::create($paymentData);

        $vnp_TmnCode = env('VNP_TMNCODE');
        $vnp_HashSecret = env('VNP_HASHSECRET');
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('checkout.vnpay.return');
        $vnp_TxnRef = $order->id;
        $vnp_OrderInfo = 'Thanh toán đơn hàng ' . $order->id;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $order->total_price * 100;
        $vnp_Locale = 'vn';
        // $vnp_BankCode = 'NCB';
        $vnp_IpAddr = $request->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return response()->json([
            'code' => '00',
            'message' => 'success',
            'data' => $vnp_Url
        ]);
        // $response = response()->json([
        //     'code' => '00',
        //     'message' => 'success',
        //     'data' => $vnp_Url,
        // ]);

        // // Thêm CSP headers
        // $response->headers->set('Content-Security-Policy', "default-src 'self'; img-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");

        // return $response;
    }
}
public function vnpayReturn(Request $request)
{
    $vnp_HashSecret = env('VNP_HASHSECRET'); // Chuỗi bí mật
    $inputData = $request->all();
    $vnp_SecureHash = $inputData['vnp_SecureHash'];

    unset($inputData['vnp_SecureHash']);
    ksort($inputData);
    $hashData = '';
    foreach ($inputData as $key => $value) {
        if ($hashData != '') {
            $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashData .= urlencode($key) . "=" . urlencode($value);
        }
    }

    $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

    if ($secureHash == $vnp_SecureHash) {
        $orderId = $inputData['vnp_TxnRef'];
        $order = Order::find($orderId);

        if ($order) {
            if ($inputData['vnp_ResponseCode'] == '00') {
                $order->status = 'paid';
                $order->save();

                // Xoá giỏ hàng sau khi thanh toán thành công
                $user = $request->user();
                $cartItems = CartItem::where('user_id', $user->id)->get();
                foreach ($cartItems as $cartItem) {
                    $cartItem->delete();
                }

                // Xóa giỏ hàng từ cookie
                $cookieCartItems = Cart::getCookieCartItems();
                foreach ($cookieCartItems as $item) {
                    unset($item);
                }
                array_splice($cookieCartItems, 0, count($cookieCartItems));
                Cart::setCookieCartItems($cookieCartItems);

                // Cập nhật trạng thái thanh toán
                Payment::where('order_id', $order->id)->update(['status' => 'paid']);

                return redirect()->route('dashboard')->with('message', 'Payment Successful');
            }
        } else {
            $order->status = 'unpaid';
            $order->save();

            // Cập nhật trạng thái thanh toán
            Payment::where('order_id', $order->id)->update(['status' => 'unpaid']);

            return redirect()->route('checkout.cancel')->with('message', 'Payment Failed');
        }

    } else {
        return redirect()->route('checkout.cancel')->with('message', 'Invalid signature');
    }
}





}
