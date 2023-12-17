<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp cơ sở để vẽ hình ảnh
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
abstract class BCGDraw {
    protected $im;
    protected $filename;

    /**
     * Hàm khởi tạo.
     *
     * @param resource $im
     */
    protected function __construct($im) {
        $this->im = $im;
    }

    /**
     * Đặt tên tệp tin.
     *
     * @param string $filename
     */
    public function setFilename($filename) {
        $this->filename = $filename;
    }

    /**
     * Phương pháp cần thiết để vẽ hình ảnh dựa trên thông số kỹ thuật của nó (JPG, GIF, etc.).
     */
    abstract public function draw();
}
?>