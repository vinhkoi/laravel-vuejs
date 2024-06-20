<?php
namespace App\Helper;

use Illuminate\Support\Facades\Http;

class MoMoPayment
{
    public static function createPayment($orderId, $amount)
    {
        $endpoint = "https://test-payment.momo.vn";
        $partnerCode = env('DEV_PARTNER_CODE');
        $accessKey = env('DEV_ACCESS_KEY');
        $secretKey = env('DEV_SECRET_KEY');
        $orderInfo = "Payment for order #" . $orderId;
        $redirectUrl = route('checkout.momo.success');
        // $ipnUrl = route('payment.momo.ipn');
        $extraData = "";

        $requestId = time() . "";
        $requestType = "captureMoMoWallet";
        $extraData = ($extraData ? $extraData : "");
        $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&amount=" . $amount . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&returnUrl=" . $redirectUrl .  "&extraData=" . $extraData;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = array(
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'returnUrl' => $redirectUrl,
            // 'notifyUrl' => $ipnUrl,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );

        $result = Http::post($endpoint, $data);
        return $result['payUrl'];
    }
}
