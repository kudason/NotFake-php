<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp con - EAN-8
 *
 * EAN-8 bao gồm
 * - 4 chữ số
 * - 3 chữ số
 * - 1 checksum
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

class BCGean8 extends BCGBarcode1D {
    protected $labelLeft = null;
    protected $labelRight = null;

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->keys = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

        // Tính chẵn lẻ bên trái bắt đầu bằng khoảng trắng
        // Bên phải giống với Bên trái bắt đầu bằng một ô nhịp
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
    }

    /**
     * Vẽ mã vạch.
     *
     * @param resource $im
     */
    public function draw($im) {
        // Checksum
        $this->calculateChecksum();
        $temp_text = $this->text . $this->keys[$this->checksumValue];

        // Bắt đầu Code
        $this->drawChar($im, '000', true);

        // Vẽ 4 ký tự đầu tiên (bên trái)
        for ($i = 0; $i < 4; $i++) {
            $this->drawChar($im, $this->findCode($temp_text[$i]), false);
        }

        // Vẽ Center Guard Bar
        $this->drawChar($im, '00000', false);

        // Vẽ 4 ký tự cuối cùng (bên phải)
        for ($i = 4; $i < 8; $i++) {
            $this->drawChar($im, $this->findCode($temp_text[$i]), true);
        }

        // Vẽ Right Guard Bar
        $this->drawChar($im, '000', true);
        $this->drawText($im, 0, 0, $this->positionX, $this->thickness);

        if ($this->isDefaultEanLabelEnabled()) {
            $dimension = $this->labelRight->getDimension();
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
        $textlength = 8 * 7;
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

            $this->labelLeft = new BCGLabel(substr($label, 0, 4), $font, BCGLabel::POSITION_BOTTOM, BCGLabel::ALIGN_LEFT);
            $labelLeftDimension = $this->labelLeft->getDimension();
            $this->labelLeft->setOffset(($this->scale * 30 - $labelLeftDimension[0]) / 2 + $this->scale * 2);

            $this->labelRight = new BCGLabel(substr($label, 4, 3) . $this->keys[$this->checksumValue], $font, BCGLabel::POSITION_BOTTOM, BCGLabel::ALIGN_LEFT);
            $labelRightDimension = $this->labelRight->getDimension();
            $this->labelRight->setOffset(($this->scale * 30 - $labelRightDimension[0]) / 2 + $this->scale * 34);

            $this->addLabel($this->labelLeft);
            $this->addLabel($this->labelRight);
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
            throw new BCGParseException('ean8', 'No data has been entered.');
        }

        // Kiểm tra xem tất cả các ký tự có được phép không
        for ($i = 0; $i < $c; $i++) {
            if (array_search($this->text[$i], $this->keys) === false) {
                throw new BCGParseException('ean8', 'The character \'' . $this->text[$i] . '\' is not allowed.');
            }
        }

        // Nếu có 8 ký tự, chỉ cần xóa ký tự cuối cùng
        if ($c === 8) {
            $this->text = substr($this->text, 0, 7);
        } elseif ($c !== 7) {
            throw new BCGParseException('ean8', 'Must contain 7 digits, the 8th digit is automatically added.');
        }

        parent::validate();
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
        if ($this->checksumValue === false) { // Tính check sum một lần duy nhất
            $this->calculateChecksum();
        }

        if ($this->checksumValue !== false) {
            return $this->keys[$this->checksumValue];
        }

        return false;
    }

    /**
     * Vẽ các thanh mở rộng trên hình ảnh.
     *
     * @param resource $im
     * @param int $plus
     */
    private function drawExtendedBars($im, $plus) {
        $rememberX = $this->positionX;
        $rememberH = $this->thickness;

        // Tăng bar
        $this->thickness = $this->thickness + intval($plus / $this->scale);
        $this->positionX = 0;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);
        $this->positionX += 2;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);

        // Center Guard Bar
        $this->positionX += 30;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);
        $this->positionX += 2;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);

        // Bars cuối
        $this->positionX += 30;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);
        $this->positionX += 2;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);

        $this->positionX = $rememberX;
        $this->thickness = $rememberH;
    }
}
?>