<?php
/**
 *--------------------------------------------------------------------
 *
 * Ngoại lệ đối số
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
class BCGArgumentException extends Exception {
    protected $param;

    /**
     * Hàm khởi tạo với thông báo cụ thể cho một tham số.
     *
     * @param string $message
     * @param string $param
     */
    public function __construct($message, $param) {
        $this->param = $param;
        parent::__construct($message, 20000);
    }
}
?>