<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp con - Interleaved 2 of 5
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGParseException.php');
include_once('BCGBarcode1D.php');

class BCGi25 extends BCGBarcode1D {
    private $checksum;
    private $ratio;

    /**
     * Hàm khởi tạo.
     */
    public function __construct() {
        parent::__construct();

        $this->keys = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $this->code = array(
            '00110',    /* 0 */
            '10001',    /* 1 */
            '01001',    /* 2 */
            '11000',    /* 3 */
            '00101',    /* 4 */
            '10100',    /* 5 */
            '01100',    /* 6 */
            '00011',    /* 7 */
            '10010',    /* 8 */
            '01010'     /* 9 */
        );

        $this->setChecksum(false);
        $this->setRatio(2);
    }

    /**
     * Đặt checksum.
     *
     * @param bool $checksum
     */
    public function setChecksum($checksum) {
        $this->checksum = (bool)$checksum;
    }

    /**
     * Đặt tỷ lệ của thanh màu đen so với thanh màu trắng.
     *
     * @param int $ratio
     */
    public function setRatio($ratio) {
        $this->ratio = $ratio;
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
        $this->drawChar($im, '0000', true);

        // Các ký tự
        $c = strlen($temp_text);
        for ($i = 0; $i < $c; $i += 2) {
            $temp_bar = '';
            $c2 = strlen($this->findCode($temp_text[$i]));
            for ($j = 0; $j < $c2; $j++) {
                $temp_bar .= substr($this->findCode($temp_text[$i]), $j, 1);
                $temp_bar .= substr($this->findCode($temp_text[$i + 1]), $j, 1);
            }

            $this->drawChar($im, $this->changeBars($temp_bar), true);
        }

        // Kết thúc Code
        $this->drawChar($im, $this->changeBars('100'), true);
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
        $textlength = (3 + ($this->ratio + 1) * 2) * strlen($this->text);
        $startlength = 4;
        $checksumlength = 0;
        if ($this->checksum === true) {
            $checksumlength = (3 + ($this->ratio + 1) * 2);
        }

        $endlength = 2 + ($this->ratio + 1);

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
            throw new BCGParseException('i25', 'No data has been entered.');
        }

        // Kiểm tra xem tất cả các ký tự có được phép không
        for ($i = 0; $i < $c; $i++) {
            if (array_search($this->text[$i], $this->keys) === false) {
                throw new BCGParseException('i25', 'The character \'' . $this->text[$i] . '\' is not allowed.');
            }
        }

        // Phải bằng nhau
        if ($c % 2 !== 0 && $this->checksum === false) {
            throw new BCGParseException('i25', 'i25 must contain an even amount of digits if checksum is false.');
        } elseif ($c % 2 === 0 && $this->checksum === true) {
            throw new BCGParseException('i25', 'i25 must contain an odd amount of digits if checksum is true.');
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
     * Phương pháp quá tải để hiển thị tổng kiểm tra.
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

    /**
     * Thay đổi kích thước của các thanh dựa trên tỷ lệ
     *
     * @param string $in
     * @return string
     */
    private function changeBars($in) {
        if ($this->ratio > 1) {
            $c = strlen($in);
            for ($i = 0; $i < $c; $i++) {
                $in[$i] = $in[$i] === '1' ? $this->ratio : $in[$i];
            }
        }

        return $in;
    }
}
?>