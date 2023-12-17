<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp con - EAN-13
 *
 * EAN-13 bao gồm
 *    - 2 chữ số hệ thống (1 không được hiển thị nhưng được mã hóa chẵn lẻ)
 *    - 5 chữ số mã nhà sản xuất
 *    - 5 chữ số sản phẩm
 *    - 1 chữ số checksum
 *
 * checksum luôn được hiển thị.
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGParseException.php');
include_once('BCGBarcode.php');
include_once('BCGBarcode1D.php');
include_once('BCGLabel.php');

class BCGean13 extends BCGBarcode1D {
    protected $codeParity = array();
    protected $labelLeft = null;
    protected $labelCenter1 = null;
    protected $labelCenter2 = null;
    protected $alignLabel;

    /**
     * Hàm khởi tạo.
     */
    public function __construct() {
        parent::__construct();

        $this->keys = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

        // Tính chẵn lẻ bên trái bắt đầu bằng khoảng trắng
        // Tính chẵn lẻ bên trái là nghịch đảo (0=0012) bắt đầu bằng khoảng trắng
        // Bêm phải giống với bên trái bắt đầu bằng một bar
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

        // Chẵn lẻ, 0=Lẻ, 1=Chẵn đối với mã nhà sản xuất. Tùy thuộc vào chữ số hệ thống thứ 1
        $this->codeParity = array(
            array(0, 0, 0, 0, 0),   /* 0 */
            array(0, 1, 0, 1, 1),   /* 1 */
            array(0, 1, 1, 0, 1),   /* 2 */
            array(0, 1, 1, 1, 0),   /* 3 */
            array(1, 0, 0, 1, 1),   /* 4 */
            array(1, 1, 0, 0, 1),   /* 5 */
            array(1, 1, 1, 0, 0),   /* 6 */
            array(1, 0, 1, 0, 1),   /* 7 */
            array(1, 0, 1, 1, 0),   /* 8 */
            array(1, 1, 0, 1, 0)    /* 9 */
        );

        $this->alignDefaultLabel(true);
    }

    public function alignDefaultLabel($align) {
        $this->alignLabel = (bool)$align;
    }

    /**
     * Vẽ mã vạch.
     *
     * @param resource $im
     */
    public function draw($im) {
        $this->drawBars($im);
        $this->drawText($im, 0, 0, $this->positionX, $this->thickness);

        if ($this->isDefaultEanLabelEnabled()) {
            $dimension = $this->labelCenter1->getDimension();
            $this->drawExtendedBars($im, $dimension[1] - 2);
        }
    }

    /**
     * Trả về kích thước tối đa của mã vạch.
     *
     * @param int $w
     * @param int $h
     * @return int[]
     */
    public function getDimension($w, $h) {
        $startlength = 3;
        $centerlength = 5;
        $textlength = 12 * 7;
        $endlength = 3;

        $w += $startlength + $centerlength + $textlength + $endlength;
        $h += $this->thickness;
        return parent::getDimension($w, $h);
    }

    /**
     * Thêm nhãn mặc định.
     */
    protected function addDefaultLabel() {
        if ($this->isDefaultEanLabelEnabled()) {
            $this->processChecksum();
            $label = $this->getLabel();
            $font = $this->font;

            $this->labelLeft = new BCGLabel(substr($label, 0, 1), $font, BCGLabel::POSITION_LEFT, BCGLabel::ALIGN_BOTTOM);
            $this->labelLeft->setSpacing(4 * $this->scale);

            $this->labelCenter1 = new BCGLabel(substr($label, 1, 6), $font, BCGLabel::POSITION_BOTTOM, BCGLabel::ALIGN_LEFT);
            $labelCenter1Dimension = $this->labelCenter1->getDimension();
            $this->labelCenter1->setOffset(($this->scale * 44 - $labelCenter1Dimension[0]) / 2 + $this->scale * 2);

            $this->labelCenter2 = new BCGLabel(substr($label, 7, 5) . $this->keys[$this->checksumValue], $font, BCGLabel::POSITION_BOTTOM, BCGLabel::ALIGN_LEFT);
            $this->labelCenter2->setOffset(($this->scale * 44 - $labelCenter1Dimension[0]) / 2 + $this->scale * 48);

            if ($this->alignLabel) {
                $labelDimension = $this->labelCenter1->getDimension();
                $this->labelLeft->setOffset($labelDimension[1]);
            } else {
                $labelDimension = $this->labelLeft->getDimension();
                $this->labelLeft->setOffset($labelDimension[1] / 2);
            }

            $this->addLabel($this->labelLeft);
            $this->addLabel($this->labelCenter1);
            $this->addLabel($this->labelCenter2);
        }
    }

    /**
     * Kiểm tra xem nhãn ean mặc định có được bật hay không.
     *
     * @return bool
     */
    protected function isDefaultEanLabelEnabled() {
        $label = $this->getLabel();
        $font = $this->font;
        return $label !== null && $label !== '' && $font !== null && $this->defaultLabel !== null;
    }

    /**
     * Xác thực đầu vào.
     */
    protected function validate() {
        $c = strlen($this->text);
        if ($c === 0) {
            throw new BCGParseException('ean13', 'No data has been entered.');
        }

        $this->checkCharsAllowed();
        $this->checkCorrectLength();

        parent::validate();
    }

    /**
     * Kiểm tra ký tự được phép.
     */
    protected function checkCharsAllowed() {
        // Kiểm tra xem tất cả các ký tự có được phép không
        $c = strlen($this->text);
        for ($i = 0; $i < $c; $i++) {
            if (array_search($this->text[$i], $this->keys) === false) {
                throw new BCGParseException('ean13', 'The character \'' . $this->text[$i] . '\' is not allowed.');
            }
        }
    }

    /**
     * Kiểm tra độ dài chính xác.
     */
    protected function checkCorrectLength() {
        // Nếu có 13 ký tự, chỉ cần xóa ký tự cuối cùng mà không ném bất cứ thứ gì
        $c = strlen($this->text);
        if ($c === 13) {
            $this->text = substr($this->text, 0, 12);
        } elseif ($c !== 12) {
            throw new BCGParseException('ean13', 'Must contain 12 digits, the 13th digit is automatically added.');
        }
    }

    /**
     * Nạp chồng phương thức tính checksum.
     */
    protected function calculateChecksum() {
        // Tính checksum
        // Coi chữ số ngoài cùng bên phải của tin nhắn ở vị trí "lẻ",
        // và gán số lẻ/chẵn cho từng ký tự di chuyển từ phải sang trái
        // Vị trí lẻ = 3, Vị trí chẵn = 1
        // Nhân nó với số
        // Thêm tất cả những thứ đó và thực hiện 10-(?mod10)
        $odd = true;
        $this->checksumValue = 0;
        $c = strlen($this->text);
        for ($i = $c; $i > 0; $i--) {
            if ($odd === true) {
                $multiplier = 3;
                $odd = false;
            } else {
                $multiplier = 1;
                $odd = true;
            }

            if (!isset($this->keys[$this->text[$i - 1]])) {
                return;
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

    /**
     * Vẽ bars
     *
     * @param resource $im
     */
    protected function drawBars($im) {
        // Checksum
        $this->calculateChecksum();
        $temp_text = $this->text . $this->keys[$this->checksumValue];

        // Bắt đầu Code
        $this->drawChar($im, '000', true);

        // Vẽ mã thứ hai
        $this->drawChar($im, $this->findCode($temp_text[1]), false);

        // Draw Manufacturer Code
        for ($i = 0; $i < 5; $i++) {
            $this->drawChar($im, self::inverse($this->findCode($temp_text[$i + 2]), $this->codeParity[(int)$temp_text[0]][$i]), false);
        }

        // Vẽ Center Guard Bar
        $this->drawChar($im, '00000', false);

        // Vẽ mã sản phẩm
        for ($i = 7; $i < 13; $i++) {
            $this->drawChar($im, $this->findCode($temp_text[$i]), true);
        }

        // Vẽ Right Guard Bar
        $this->drawChar($im, '000', true);
    }

    /**
     * Vẽ các bar mở rộng trên hình ảnh.
     *
     * @param resource $im
     * @param int $plus
     */
    protected function drawExtendedBars($im, $plus) {
        $rememberX = $this->positionX;
        $rememberH = $this->thickness;

        // Tăng bars
        $this->thickness = $this->thickness + intval($plus / $this->scale);
        $this->positionX = 0;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);
        $this->positionX += 2;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);

        // Center Guard Bar
        $this->positionX += 44;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);
        $this->positionX += 2;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);

        // Bars cuối
        $this->positionX += 44;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);
        $this->positionX += 2;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);

        $this->positionX = $rememberX;
        $this->thickness = $rememberH;
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