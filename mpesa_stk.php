<?php
require_once 'mpesa_config.php';

class MpesaSTK {
    
    // Generate Access Token
    private function getAccessToken() {
        $credentials = base64_encode(MPESA_CONSUMER_KEY . ':' . MPESA_CONSUMER_SECRET);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, MPESA_AUTH_URL);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $credentials,
            'Content-Type: application/json'
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($curl);
        $result = json_decode($response);
        curl_close($curl);
        
        return isset($result->access_token) ? $result->access_token : null;
    }
    
    // Send STK Push
    public function sendSTKPush($phone, $amount, $orderId) {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['ResponseCode' => '1', 'errorMessage' => 'Failed to get access token'];
        }
        
        // Format phone number
        $phone = formatPhoneNumber($phone);
        
        // Generate password
        $passwordData = generateSTKPassword();
        
        $curl_post_data = [
            'BusinessShortCode' => MPESA_SHORTCODE,
            'Password' => $passwordData['password'],
            'Timestamp' => $passwordData['timestamp'],
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => round($amount),
            'PartyA' => $phone,
            'PartyB' => MPESA_SHORTCODE,
            'PhoneNumber' => $phone,
            'CallBackURL' => MPESA_CALLBACK_URL,
            'AccountReference' => 'ByteBliss_Order_' . $orderId,
            'TransactionDesc' => 'Payment for electronics'
        ];
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, MPESA_STK_PUSH_URL);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        return json_decode($response, true);
    }
}
?>