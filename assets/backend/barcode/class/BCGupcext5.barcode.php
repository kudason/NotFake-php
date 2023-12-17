<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp con - Mã vạch bổ sung UPC 2 chữ số
 *
 * Làm việc với UPC-A, UPC-E, EAN-13, EAN-8
 * Điều này bao gồm 5 chữ số (thông thường cho giá bán lẻ đề xuất)
 * Phải đặt cạnh Mã UPC hoặc EAN
 * Nếu 90000 -> Không có giá bán lẻ đề xuất
 * Nếu là 99991 -> Đặt phòng miễn phí (thông thường miễn phí)
 * Nếu 90001 đến 98999 -> Mục đích nội bộ của Nhà xuất bản
 * Nếu 99990 -> Được Hiệp hội các cửa hàng đại học quốc gia sử dụng để đánh dấu sách đã qua sử dụng
 * Nếu 0xxxx -> Giá được biểu thị bằng Bảng Anh (xx.xx)
 * Nếu 5xxxx -> Giá được biểu thị bằng đô la Mỹ (US$xx.xx)
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGParseException.php');
include_once('BCGBarcode1D.php');
include_once('BCGLabel.php');

class BCGupcext5 extends BCGBarcode1D {
    protected $codeParity = array();

    /**
     * Hàm khởi tạo.
     */
    public function __construct() {
        parent::__construct();

        $this->keys = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $this->code = array(
            '2100',     /* 0 */
            '1110',     /* 1 */
            '1011',     /* 2 */
            '0300',     /* 3 */
            '0021',     /* 4 */
            '0120',     /* 5 */
            '0003',     /* 6 */
            '0201',     /* 7 */
            '0102',     /* 8 */
            '2001'      /* 9 */
        );

        // Chẵn lẻ, 0=Lẻ, 1=Chẵn. Tổng kiểm tra tùy thuộc
        $this->codeParity = array(
            array(1, 1, 0, 0, 0),   /* 0 */
            array(1, 0, 1, 0, 0),   /* 1 */
            array(1, 0, 0, 1, 0),   /* 2 */
            array(1, 0, 0, 0, 1),   /* 3 */
            array(0, 1, 1, 0, 0),   /* 4 */
            array(0, 0, 1, 1, 0),   /* 5 */
            array(0, 0, 0, 1, 1),   /* 6 */
            array(0, 1, 0, 1, 0),   /* 7 */
            array(0, 1, 0, 0, 1),   /* 8 */
            array(0, 0, 1, 0, 1)    /* 9 */
        );
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
        $this->drawChar($im, '001', true);

        // Code
        for ($i = 0; $i < 5; $i++) {
            $this->drawChar($im, self::inverse($this->findCode($this->text[$i]), $this->codeParity[$this->checksumValue][$i]), false);
            if ($i < 4) {
                $this->drawChar($im, '00', false);    // Inter-char
            }
        }

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
        $startlength = 4;
        $textlength = 5 * 7;
        $intercharlength = 2 * 4;

        $w += $startlength + $textlength + $intercharlength;
        $h += $this->thickness;
        return parent::getDimension($w, $h);
    }

    /**
     * Thêm nhãn mặc định.
     */
    protected function addDefaultLabel() {
        parent::addDefaultLabel();

        if ($this->defaultLabel !== null) {
            $this->defaultLabel->setPosition(BCGLabel::POSITION_TOP);
        }
    }

    /**
     * Xác thực đầu vào.
     */
    protected function validate() {
        $c = strlen($this->text);
        if ($c === 0) {
            throw new BCGParseException('upcext5', 'No data has been entered.');
        }

        // Kiểm tra xem tất cả các ký tự có được phép không
        for ($i = 0; $i < $c; $i++) {
            if (array_search($this->text[$i], $this->keys) === false) {
                throw new BCGParseException('upcext5', 'The character \'' . $this->text[$i] . '\' is not allowed.');
            }
        }

        // Phải chứa 5 chữ số
        if ($c !== 5) {
            throw new BCGParseException('upcext5', 'Must contain 5 digits.');
        }

        parent::validate();
    }

    /**
     * Nạp chồng phương thức tính checksum.
     */
    protected function calculateChecksum() {
        // Tính tổng kiểm tra
        // Coi chữ số ngoài cùng bên phải của tin nhắn ở vị trí "lẻ",
        // và gán số lẻ/chẵn cho từng ký tự di chuyển từ phải sang trái
        // Vị trí lẻ = 3, Vị trí chẵn = 9
        // Nhân nó với số
        // Thêm tất cả những thứ đó và làm ?mod10
        $odd = true;
        $this->checksumValue = 0;
        $c = strlen($this->text);
        for ($i = $c; $i > 0; $i--) {
            if ($odd === true) {
                $multiplier = 3;
                $odd = false;
            } else {
                $multiplier = 9;
                $odd = true;
            }

            if (!isset($this->keys[$this->text[$i - 1]])) {
                return;
            }

            $this->checksumValue += $this->keys[$this->text[$i - 1]] * $multiplier;
        }

        $this->checksumValue = $this->checksumValue % 10;
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

    /**
     * Đảo ngược chuỗi khi tham số $inverse bằng 1.
     *
     * @param string $text
     * @param int $inverse
     * @return string
     */
    private static function inverse($text, $inverse = 1) {
        if ($inverse === 1) {
            $text = strrev($text);
        }

        return $text;
    }
}
?>