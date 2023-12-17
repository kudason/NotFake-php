<?php
/**
 *--------------------------------------------------------------------
 *
 * Giữ tất cả các loại mã vạch cho kiểu 1D
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGArgumentException.php');
include_once('BCGBarcode.php');
include_once('BCGFontPhp.php');
include_once('BCGFontFile.php');
include_once('BCGLabel.php');

abstract class BCGBarcode1D extends BCGBarcode {
    const SIZE_SPACING_FONT = 5;

    const AUTO_LABEL = '##!!AUTO_LABEL!!##';

    protected $thickness;       // int
    protected $keys, $code;     // string[]
    protected $positionX;       // int
    protected $font;            // BCGFont
    protected $text;            // string
    protected $checksumValue;   // int or int[]
    protected $displayChecksum; // bool
    protected $label;           // string
    protected $defaultLabel;    // BCGLabel

    /**
     * Hàm khởi tạo.
     */
    protected function __construct() {
        parent::__construct();

        $this->setThickness(30);

        $this->defaultLabel = new BCGLabel();
        $this->defaultLabel->setPosition(BCGLabel::POSITION_BOTTOM);
        $this->setLabel(self::AUTO_LABEL);
        $this->setFont(new BCGFontPhp(5));

        $this->text = '';
        $this->checksumValue = false;
        $this->positionX = 0;
    }

    /**
     * Lấy độ dày.
     *
     * @return int
     */
    public function getThickness() {
        return $this->thickness;
    }

    /**
     * Đặt độ dày.
     *
     * @param int $thickness
     */
    public function setThickness($thickness) {
        $thickness = intval($thickness);
        if ($thickness <= 0) {
            throw new BCGArgumentException('The thickness must be larger than 0.', 'thickness');
        }

        $this->thickness = $thickness;
    }

    /**
     * Lấy nhãn.
     * Nếu nhãn được đặt thành BCGBarcode1D::AUTO_LABEL, nhãn sẽ hiển thị giá trị từ văn bản được phân tích cú pháp.
     *
     * @return string
     */
    public function getLabel() {
        $label = $this->label;
        if ($this->label === self::AUTO_LABEL) {
            $label = $this->text;
            if ($this->displayChecksum === true && ($checksum = $this->processChecksum()) !== false) {
                $label .= $checksum;
            }
        }

        return $label;
    }

    /**
     * Gán nhãn.
     * Có thể sử dụng BCGBarcode::AUTO_LABEL để nhãn được ghi tự động dựa trên văn bản được phân tích cú pháp.
     *
     * @param string $label
     */
    public function setLabel($label) {
        $this->label = $label;
    }

    /**
     * Lấy font.
     *
     * @return BCGFont
     */
    public function getFont() {
        return $this->font;
    }

    /**
     * Đặt font.
     *
     * @param mixed $font BCGFont or int
     */
    public function setFont($font) {
        if (is_int($font)) {
            if ($font === 0) {
                $font = null;
            } else {
                $font = new BCGFontPhp($font);
            }
        }

        $this->font = $font;
    }

    /**
     * Phân tích văn bản trước khi hiển thị nó.
     *
     * @param mixed $text
     */
    public function parse($text) {
        $this->text = $text;
        $this->checksumValue = false; // Đặt lại checksumValue
        $this->validate();

        parent::parse($text);

        $this->addDefaultLabel();
    }

    /**
     * Lấy checksum của Mã vạch.
     * Nếu ko có checksum hoạt động, trả về FALSE.
     *
     * @return string
     */
    public function getChecksum() {
        return $this->processChecksum();
    }

    /**
     * Đặt xem checksum có được hiển thị cùng với nhãn hay không.
     * checksum phải được kích hoạt trong một số trường hợp để biến này có hiệu lực.
     *
     * @param boolean $displayChecksum
     */
    public function setDisplayChecksum($displayChecksum) {
        $this->displayChecksum = (bool)$displayChecksum;
    }

    /**
     * Thêm nhãn mặc định.
     */
    protected function addDefaultLabel() {
        $label = $this->getLabel();
        $font = $this->font;
        if ($label !== null && $label !== '' && $font !== null && $this->defaultLabel !== null) {
            $this->defaultLabel->setText($label);
            $this->defaultLabel->setFont($font);
            $this->addLabel($this->defaultLabel);
        }
    }

    /**
     * Xác thực đầu vào
     */
    protected function validate() {
        // Không có xác nhận trong lớp trừu tượng.
    }

    /**
     * Trả về chỉ mục trong $keys (cần cho checksum).
     *
     * @param mixed $var
     * @return mixed
     */
    protected function findIndex($var) {
        return array_search($var, $this->keys);
    }

    /**
     * Trả về mã của ký tự (cần cho việc vẽ bars).
     *
     * @param mixed $var
     * @return string
     */
    protected function findCode($var) {
        return $this->code[$this->findIndex($var)];
    }

    /**
     * Vẽ tất cả các ký tự nhờ $code. Nếu $startBar là TRUE, dòng bắt đầu bằng khoảng trắng.
     * Nếu $startBar FALSE, dòng bắt đầu bằng một thanh.
     *
     * @param resource $im
     * @param string $code
     * @param boolean $startBar
     */
    protected function drawChar($im, $code, $startBar = true) {
        $colors = array(BCGBarcode::COLOR_FG, BCGBarcode::COLOR_BG);
        $currentColor = $startBar ? 0 : 1;
        $c = strlen($code);
        for ($i = 0; $i < $c; $i++) {
            for ($j = 0; $j < intval($code[$i]) + 1; $j++) {
                $this->drawSingleBar($im, $colors[$currentColor]);
                $this->nextX();
            }

            $currentColor = ($currentColor + 1) % 2;
        }
    }

    /**
     * Vẽ một bar $color tùy thuộc vào độ phân giải.
     *
     * @param resource $img
     * @param int $color
     */
    protected function drawSingleBar($im, $color) {
        $this->drawFilledRectangle($im, $this->positionX, 0, $this->positionX, $this->thickness - 1, $color);
    }

    /**
     * Di chuyển con trỏ sang phải để viết một bar.
     */
    protected function nextX() {
        $this->positionX++;
    }

    /**
     * Phương thức lưu FALSE vào checksumValue. Điều này có nghĩa là không có checksumValue nhưng phương pháp này nên được ghi đè khi cần thiết.
     */
    protected function calculateChecksum() {
        $this->checksumValue = false;
    }

    /**
     * Trả về FALSE vì không có checksum. Phương thức này phải được ghi đè để trả về chính xác checksum trong chuỗi có checksumValue
     *
     * @return string
     */
    protected function processChecksum() {
        return false;
    }
}
?>