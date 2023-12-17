<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp con - othercode
 *
 * Ohtercode
 * Bắt đầu bằng một thanh và thay thế bằng dấu cách, thanh, ...
 * 0 là nhỏ nhất
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGParseException.php');
include_once('BCGBarcode1D.php');

class BCGothercode extends BCGBarcode1D {
    /**
     * Hàm khởi tạo.
     */
    public function __construct() {
        parent::__construct();

        $this->keys = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    }

    /**
     * Vẽ mã vạch.
     *
     * @param resource $im
     */
    public function draw($im) {
        $this->drawChar($im, $this->text, true);
        $this->drawText($im, 0, 0, $this->positionX, $this->thickness);
    }

    /**
     * Nhận được nhãn.
     * Nếu nhãn được đặt thành BCGBarcode1D::AUTO_LABEL, nhãn sẽ hiển thị giá trị từ văn bản được phân tích cú pháp.
     *
     * @return string
     */
    public function getLabel() {
        $label = $this->label;
        if ($this->label === BCGBarcode1D::AUTO_LABEL) {
            $label = '';
        }

        return $label;
    }

    /**
     * Trả về kích thước tối đa của mã vạch.
     *
     * @param int $w
     * @param int $h
     * @return int[]
     */
    public function getDimension($w, $h) {
        $array = str_split($this->text, 1);
        $textlength = array_sum($array) + count($array);

        $w += $textlength;
        $h += $this->thickness;
        return parent::getDimension($w, $h);
    }

    /**
     * Xác thực đầu vào.
     */
    protected function validate() {
        $c = strlen($this->text);
        if ($c === 0) {
            throw new BCGParseException('othercode', 'No data has been entered.');
        }

        // Kiểm tra xem tất cả các ký tự có được phép không
        for ($i = 0; $i < $c; $i++) {
            if (array_search($this->text[$i], $this->keys) === false) {
                throw new BCGParseException('othercode', 'The character \'' . $this->text[$i] . '\' is not allowed.');
            }
        }

        parent::validate();
    }
}
?>