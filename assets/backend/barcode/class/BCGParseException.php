<?php
/**
 *--------------------------------------------------------------------
 *
 * Ngoại lệ phân tích
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
class BCGParseException extends Exception {
    protected $barcode;

    /**
     * Hàm khởi tạo với thông báo cụ thể cho một tham số.
     *
     * @param string $barcode
     * @param string $message
     */
    public function __construct($barcode, $message) {
        $this->barcode = $barcode;
        parent::__construct($message, 10000);
    }
}
?>