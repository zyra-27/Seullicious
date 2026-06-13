<?php
// ── Xendit Configuration ──────────────────────────────────────────────────────
define('XENDIT_SECRET_KEY', 'xnd_development_41oJ2DjtaSlDIyQFZBFnYSkCj9LeCmpRADgo1lBOZvtQzFCQ6Nt0Xg01gfLMlE');
define('XENDIT_PUBLIC_KEY', 'xnd_public_development__n3iQMlZFR1Hhdf6LnyS5nYaYCh9ywGfnCuTEXA6rhJB3LHyx9G3Uff5ouUrZoy');
define('XENDIT_BASE_URL',   'https://api.xendit.co');

/**
 * Buat Payment Invoice via Xendit
 */
function xendit_create_invoice($external_id, $amount, $customer_name = 'Customer', $description = 'Seoullicious Order') {
    $success_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') 
                 . '://' . $_SERVER['HTTP_HOST'] 
                 . '/seoullicious_fixed/user/receipt.php?paid=1&ext=' . urlencode($external_id);
    $failure_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') 
                 . '://' . $_SERVER['HTTP_HOST'] 
                 . '/seoullicious_fixed/user/cart.php';

    $payload = json_encode([
        'external_id'         => $external_id,
        'amount'              => (int)$amount,
        'description'         => $description,
        'invoice_duration'    => 1800, // 30 menit
        'customer'            => ['given_names' => $customer_name],
        'success_redirect_url'=> $success_url,
        'failure_redirect_url'=> $failure_url,
        'currency'            => 'IDR',
        'items'               => [['name' => $description, 'quantity' => 1, 'price' => (int)$amount]],
        'payment_methods'     => ['QRIS','OVO','DANA','LINKAJA','SHOPEEPAY','BCA','BNI','BRI','MANDIRI'],
    ]);

    $ch = curl_init(XENDIT_BASE_URL . '/v2/invoices');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode(XENDIT_SECRET_KEY . ':'),
        ],
    ]);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);

    if($httpcode === 200 || $httpcode === 201){
        return ['ok' => true, 'data' => $data];
    }
    return ['ok' => false, 'error' => $data['message'] ?? $response, 'code' => $httpcode];
}

/**
 * Cek status invoice
 */
function xendit_check_invoice($invoice_id) {
    $ch = curl_init(XENDIT_BASE_URL . '/v2/invoices/' . urlencode($invoice_id));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Basic ' . base64_encode(XENDIT_SECRET_KEY . ':'),
        ],
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}