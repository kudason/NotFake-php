<?php
/**
 *--------------------------------------------------------------------
 *
 * Ngoại lệ khi vẽ
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
class BCGDrawException extends Exception {
    /**
     * Hàm khởi tạo với thông điệp cụ thể.
     *
     * @param string $message
     */
    public function __construct($message) {
        parent::__construct($message, 30000);
    }
}
?>