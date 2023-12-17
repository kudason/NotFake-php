<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp con - Code 11
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGParseException.php');
include_once('BCGBarcode1D.php');

class BCGcode11 extends BCGBarcode1D {
    /**
     * Hàm khởi tạo.
     */
    public function __construct() {
        parent::__construct();

        $this->keys = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-');
        $this->code = array(    // 0 được thêm vào để thêm một không gian bổ sung
            '000010',   /* 0 */
            '100010',   /* 1 */
            '010010',   /* 2 */
            '110000',   /* 3 */
            '001010',   /* 4 */
            '101000',   /* 5 */
            '011000',   /* 6 */
            '000110',   /* 7 */
            '100100',   /* 8 */
            '100000',   /* 9 */
            '001000'    /* - */
        );
    }

    /**
     * Vẽ mã vạch.
     *
     * @param resource $im
     */
    public function draw($im) {
        // Bắt đầu Code
        $this->drawChar($im, '001100', true);

        // Ký tự
        $c = strlen($this->text);
        for ($i = 0; $i < $c; $i++) {
            $this->drawChar($im, $this->findCode($this->text[$i]), true);
        }

        // Checksum
        $this->calculateChecksum();
        $c = count($this->checksumValue);
        for ($i = 0; $i < $c; $i++) {
            $this->drawChar($im, $this->code[$this->checksumValue[$i]], true);
        }

        // Kết thúc Code
        $this->drawChar($im, '00110', true);
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
        $startlength = 8;

        $textlength = 0;
        $c = strlen($this->text);
        for ($i = 0; $i < $c; $i++) {
            $textlength += $this->getIndexLength($this->findIndex($this->text[$i]));
        }

        $checksumlength = 0;
        $this->calculateChecksum();
        $c = count($this->checksumValue);
        for ($i = 0; $i < $c; $i++) {
            $checksumlength += $this->getIndexLength($this->checksumValue[$i]);
        }

        $endlength = 7;

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
            throw new BCGParseException('code11', 'No data has been entered.');
        }

        // Kiểm tra xem tất cả các ký tự có được phép không
        for ($i = 0; $i < $c; $i++) {
            if (array_search($this->text[$i], $this->keys) === false) {
                throw new BCGParseException('code11', 'The character \'' . $this->text[$i] . '\' is not allowed.');
            }
        }

        parent::validate();
    }

    /**
     * Nạp chồng phương thức để tính checksum.
     */
    protected function calculateChecksum() {
        // Checksum
        // Đầu tiên CheckSUM "C"
        // Ký tự checksum "C" là phần còn lại theo modulo 11 của tổng giá trị trọng số của các ký tự dữ liệu. 
        // Giá trị trọng số bắt đầu từ "1" cho ký tự dữ liệu ngoài cùng bên phải, 2 cho ký tự thứ hai đến cuối cùng, 3 cho ký tự thứ ba đến cuối cùng, v.v. cho đến 20.
        // Sau 10, chuỗi sẽ quay về 1.

        // Thứ hai CheckSUM "K"
        // Tương tự như CheckSUM "C" nhưng chúng tôi tính CheckSum "C" ở cuối
        // Sau 9, chuỗi sẽ quay về 1.
        $sequence_multiplier = array(10, 9);
        $temp_text = $this->text;
        $this->checksumValue = array();
        for ($z = 0; $z < 2; $z++) {
            $c = strlen($temp_text);

            // Chúng tôi không hiển thị CheckSum K nếu văn bản gốc có độ dài nhỏ hơn 10
            if ($c <= 10 && $z === 1) {
                break;
            }

            $checksum = 0;
            for ($i = $c, $j = 0; $i > 0; $i--, $j++) {
                $multiplier = $i % $sequence_multiplier[$z];
                if ($multiplier === 0) {
                    $multiplier = $sequence_multiplier[$z];
                }

                $checksum += $this->findIndex($temp_text[$j]) * $multiplier;
            }

            $this->checksumValue[$z] = $checksum % 11;
            $temp_text .= $this->keys[$this->checksumValue[$z]];
        }
    }

    /**
     * Nạp chồng phương thức để hiển thị checksum.
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

    private function getIndexLength($index) {
        $length = 0;
        if ($index !== false) {
            $length += 6;
            $length += substr_count($this->code[$index], '1');
        }

        return $length;
    }
}
?>