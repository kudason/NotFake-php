<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp con - Standard 2 of 5
 *
 * TODO I25 và S25 -> 1/3 hoặc 1/2 cho thanh lớn
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGParseException.php');
include_once('BCGBarcode1D.php');

class BCGs25 extends BCGBarcode1D {
    private $checksum;

    /**
     * Hàm khởi tạo.
     */
    public function __construct() {
        parent::__construct();

        $this->keys = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $this->code = array(
            '0000202000',   /* 0 */
            '2000000020',   /* 1 */
            '0020000020',   /* 2 */
            '2020000000',   /* 3 */
            '0000200020',   /* 4 */
            '2000200000',   /* 5 */
            '0020200000',   /* 6 */
            '0000002020',   /* 7 */
            '2000002000',   /* 8 */
            '0020002000'    /* 9 */
        );

        $this->setChecksum(false);
    }

    /**
     * Đặt nếu chúng tôi hiển thị tổng kiểm tra.
     *
     * @param bool $checksum
     */
    public function setChecksum($checksum) {
        $this->checksum = (bool)$checksum;
    }

    /**
     * Vẽ mã vạch.
     *
     * @param resource $im
     */
    public function draw($im) {
        $temp_text = $this->text;

        // Checksum
        if ($this->checksum === true) {
            $this->calculateChecksum();
            $temp_text .= $this->keys[$this->checksumValue];
        }

        // Bắt đầu Code
        $this->drawChar($im, '101000', true);

        // Các ký tự
        $c = strlen($temp_text);
        for ($i = 0; $i < $c; $i++) {
            $this->drawChar($im, $this->findCode($temp_text[$i]), true);
        }

        // Kết thúc Code
        $this->drawChar($im, '10001', true);
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
        $c = strlen($this->text);
        $startlength = 8;
        $textlength = $c * 14;
        $checksumlength = 0;
        if ($c % 2 !== 0) {
            $checksumlength = 14;
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
            throw new BCGParseException('s25', 'No data has been entered.');
        }

        // Kiểm tra xem tất cả các ký tự có được phép không
        for ($i = 0; $i < $c; $i++) {
            if (array_search($this->text[$i], $this->keys) === false) {
                throw new BCGParseException('s25', 'The character \'' . $this->text[$i] . '\' is not allowed.');
            }
        }

        // Phải bằng nhau
        if ($c % 2 !== 0 && $this->checksum === false) {
            throw new BCGParseException('s25', 's25 must contain an even amount of digits if checksum is false.');
        } elseif ($c % 2 === 0 && $this->checksum === true) {
            throw new BCGParseException('s25', 's25 must contain an odd amount of digits if checksum is true.');
        }

        parent::validate();
    }

    /**
     * Nạp chồng phương thức tính checksum.
     */
    protected function calculateChecksum() {
        // Tính checksum
        // Coi chữ số ngoài cùng bên phải của tin nhắn ở vị trí "chẵn",
        // và gán số lẻ/chẵn cho từng ký tự di chuyển từ phải sang trái
        // Vị trí chẵn = 3, Vị trí lẻ = 1
        // Nhân nó với số
        // Thêm tất cả những thứ đó và thực hiện 10-(?mod10)
        $even = true;
        $this->checksumValue = 0;
        $c = strlen($this->text);
        for ($i = $c; $i > 0; $i--) {
            if ($even === true) {
                $multiplier = 3;
                $even = false;
            } else {
                $multiplier = 1;
                $even = true;
            }

            $this->checksumValue += $this->keys[$this->text[$i - 1]] * $multiplier;
        }
        $this->checksumValue = (10 - $this->checksumValue % 10) % 10;
    }

    /**
     * Nạp chồng phương thức hiển thị checksum.
     */
    protected function processChecksum() {
        if ($this->checksumValue === false) { // Tính checksum một lần duy nhất
            $this->calculateChecksum();
        }

        if ($this->checksumValue !== false) {
            return $this->keys[$this->checksumValue];
        }

        return false;
    }
}
?>