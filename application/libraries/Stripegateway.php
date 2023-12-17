<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include('stripe/init.php');

class Stripegateway {

    public function __construct() {

    }

    public function checkout($data) {
        // Hàm thực hiện thanh toán sử dụng dịch vụ Stripe.

        $stripe_secret_key	=	$data['stripe_secret_key'];

        // Đặt khóa bí mật Stripe cho ứng dụng sử dụng
		\stripe\Stripe::setApiKey($stripe_secret_key);

        try {
            // Tạo một giao dịch thanh toán bằng Stripe sử dụng thông tin được cung cấp.
            $charge = \stripe\Charge::create(array(
                'source'    => $data['stripe_token'],      // Token thanh toán được tạo bởi Stripe.js hoặc Elements.
                'amount'    => $data['amount'],            // Số tiền thanh toán (đơn vị là cent).
                'currency'  => 'usd',                      // Đơn vị tiền tệ (Ví dụ: USD cho đô la Mỹ).
                'description' => $data['description']      // Mô tả giao dịch.
            ));
        } catch (Exception $e) {
            // Xử lý ngoại lệ nếu có lỗi xảy ra trong quá trình giao dịch Stripe.
            // (Chẳng hạn, thẻ tín dụng bị từ chối).
            // Có thể giới thiệu việc xử lý lỗi cụ thể ở đây.
        }
    }
}
