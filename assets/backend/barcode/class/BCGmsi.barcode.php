<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp con - MSI Plessey
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGParseException.php');
include_once('BCGArgumentException.php');
include_once('BCGBarcode1D.php');

class BCGmsi extends BCGBarcode1D {
    private $checksum;

    /**
     * Hàm khởi tạo.
     */
    public function __construct() {
        parent::__construct();

        $this->keys = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $this->code = array(
            '01010101',     /* 0 */
            '01010110',     /* 1 */
            '01011001',     /* 2 */
            '01011010',     /* 3 */
            '01100101',     /* 4 */
            '01100110',     /* 5 */
            '01101001',     /* 6 */
            '01101010',     /* 7 */
            '10010101',     /* 8 */
            '10010110'      /* 9 */
        );

        $this->setChecksum(0);
    }

    /**
     * Đặt số lượng tổng kiểm tra chúng tôi hiển thị. 0 đến 2.
     *
     * @param int $checksum
     */
    public function setChecksum($checksum) {
        $checksum = intval($checksum);
        if ($checksum < 0 && $checksum > 2) {
            throw new BCGArgumentException('The checksum must be between 0 and 2 included.', 'checksum');
        }

        $this->checksum = $checksum;
    }

    /**
     * Vẽ mã vạch.
     *
     * @param resource $im
     */
    public function draw($im) {
        // Checksum
        $this->calculateChecksum();

        // Bắt đầu Code
        $this->drawChar($im, '10', true);

        // Các ký tự
        $c = strlen($this->text);
        for ($i = 0; $i < $c; $i++) {
            $this->drawChar($im, $this->findCode($this->text[$i]), true);
        }

        $c = count($this->checksumValue);
        for ($i = 0; $i < $c; $i++) {
            $this->drawChar($im, $this->findCode($this->checksumValue[$i]), true);
        }

        // Kết thúc Code
        $this->drawChar($im, '010', true);
        $this->drawText($im, 0, 0, $this->positionX, $this->thickness);
    }

    /**
     * Trả về kích thước tối đa của mã vạch.
     *
     * @param int $w
     * @param int $h
     * @return int[]
     */
    public function getDimension($w, $h) {
        $textlength = 12 * strlen($this->text);
        $startlength = 3;
        $checksumlength = $this->checksum * 12;
        $endlength = 4;

        $w += $startlength + $textlength + $checksumlength + $endlength;
        $h += $this->thickness;
        return parent::getDimension($w, $h);
    }

    /**
     * Xác thực đầu vào.
     */
    protected function validate() {
        $c = strlen($this->text);
        if ($c === 0) {
            throw new BCGParseException('msi', 'No data has been entered.');
        }

        // Kiểm tra xem tất cả các ký tự có được phép không
        for ($i = 0; $i < $c; $i++) {
            if (array_search($this->text[$i], $this->keys) === false) {
                throw new BCGParseException('msi', 'The character \'' . $this->text[$i] . '\' is not allowed.');
            }
        }
    }

    /**
     * Nạp chồng phương thức tính checksum.
     */
    protected function calculateChecksum() {
        // Tạo thành một số mới
        // Nếu số ban đầu là số chẵn thì ta lấy toàn bộ vị trí chẵn
        // Nếu số ban đầu là số lẻ thì ta lấy toàn bộ vị trí lẻ
        // 123456 = 246
        // 12345 = 135
        // Nhân với 2
        // Cộng tất cả các chữ số trong kết quả (270 : 2+7+0)
        // Cộng các chữ số khác chưa được sử dụng.
        // 10 - (? Modulo 10). Nếu kết quả = 10, đổi thành 0
        $last_text = $this->text;
        $this->checksumValue = array();
        for ($i = 0; $i < $this->checksum; $i++) {
            $new_text = '';
            $new_number = 0;
            $c = strlen($last_text);
            if ($c % 2 === 0) { // Chẵn
                $starting = 1;
            } else {
                $starting = 0;
            }

            for ($j = $starting; $j < $c; $j += 2) {
                $new_text .= $last_text[$j];
            }

            $new_text = strval(intval($new_text) * 2);
            $c2 = strlen($new_text);
            for ($j = 0; $j < $c2; $j++) {
                $new_number += intval($new_text[$j]);
            }

            for ($j = ($starting === 0) ? 1 : 0; $j < $c; $j += 2) {
                $new_number += intval($last_text[$j]);
            }

            $new_number = (10 - $new_number % 10) % 10;
            $this->checksumValue[] = $new_number;
            $last_text .= $new_number;
        }
    }

    /**
     * Nạp chồng phương thức hiển thị checksum.
     */
    protected function processChecksum() {
        if ($this->checksumValue === false) { // Tính checksum một lần duy nhất
            $this->calculateChecksum();
        }

        if ($this->checksumValue !== false) {
            $ret = '';
            $c = count($this->checksumValue);
            for ($i = 0; $i < $c; $i++) {
                $ret .= $this->keys[$this->checksumValue[$i]];
            }

            return $ret;
        }

        return false;
    }
}
?>