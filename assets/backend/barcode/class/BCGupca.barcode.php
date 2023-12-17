<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp con - UPC-A
 *
 * UPC-A bao gồm
 *    - 2 chữ số hệ thống (1 không được cung cấp, số 0 được tự động thêm vào)
 *    - 5 chữ số mã nhà sản xuất
 *    - 5 chữ số sản phẩm
 *    - 1 chữ số tổng kiểm tra
 *
 * checksum luôn hiển thị.
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGParseException.php');
include_once('BCGBarcode.php');
include_once('BCGean13.barcode.php');
include_once('BCGLabel.php');

class BCGupca extends BCGean13 {
    protected $labelRight = null;

    /**
     * Hàm khởi tạo.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Vẽ mã vạch.
     *
     * @param resource $im
     */
    public function draw($im) {
        // Đoạn mã sau hoàn toàn giống với EAN13. Chỉ cần thêm số 0 vào trước mã!
        $this->text = '0' . $this->text; // Xóa ở dưới

        parent::draw($im);

        // Xóa
        $this->text = substr($this->text, 1);
    }

    /**
     * Vẽ các thanh mở rộng trên hình ảnh.
     *
     * @param resource $im
     * @param int $plus
     */
    protected function drawExtendedBars($im, $plus) {
        $temp_text = $this->text . $this->keys[$this->checksumValue];
        $rememberX = $this->positionX;
        $rememberH = $this->thickness;

        // Tăng bars
        // 2 bars đầu tiên
        $this->thickness = $this->thickness + intval($plus / $this->scale);
        $this->positionX = 0;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);
        $this->positionX += 2;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);

        // Đang cố gắng tăng 2 bars sau
        $this->positionX += 1;
        $temp_value = $this->findCode($temp_text[1]);
        $this->drawChar($im, $temp_value, false);

        // Center Guard Bar
        $this->positionX += 36;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);
        $this->positionX += 2;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);

        // Đang cố gắng tăng 2 bars cuối cùng
        $this->positionX += 37;
        $temp_value = $this->findCode($temp_text[12]);
        $this->drawChar($im, $temp_value, true);

        // Bars cuối cùng 
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);
        $this->positionX += 2;
        $this->drawSingleBar($im, BCGBarcode::COLOR_FG);

        $this->positionX = $rememberX;
        $this->thickness = $rememberH;
    }

    /**
     *Thêm nhãn mặc định.
     */
    protected function addDefaultLabel() {
        if ($this->isDefaultEanLabelEnabled()) {
            $this->processChecksum();
            $label = $this->getLabel();
            $font = $this->font;

            $this->labelLeft = new BCGLabel(substr($label, 0, 1), $font, BCGLabel::POSITION_LEFT, BCGLabel::ALIGN_BOTTOM);
            $this->labelLeft->setSpacing(4 * $this->scale);

            $this->labelCenter1 = new BCGLabel(substr($label, 1, 5), $font, BCGLabel::POSITION_BOTTOM, BCGLabel::ALIGN_LEFT);
            $labelCenter1Dimension = $this->labelCenter1->getDimension();
            $this->labelCenter1->setOffset(($this->scale * 44 - $labelCenter1Dimension[0]) / 2 + $this->scale * 6);

            $this->labelCenter2 = new BCGLabel(substr($label, 6, 5), $font, BCGLabel::POSITION_BOTTOM, BCGLabel::ALIGN_LEFT);
            $this->labelCenter2->setOffset(($this->scale * 44 - $labelCenter1Dimension[0]) / 2 + $this->scale * 45);

            $this->labelRight = new BCGLabel($this->keys[$this->checksumValue], $font, BCGLabel::POSITION_RIGHT, BCGLabel::ALIGN_BOTTOM);
            $this->labelRight->setSpacing(4 * $this->scale);

            if ($this->alignLabel) {
                $labelDimension = $this->labelCenter1->getDimension();
                $this->labelLeft->setOffset($labelDimension[1]);
                $this->labelRight->setOffset($labelDimension[1]);
            } else {
                $labelDimension = $this->labelLeft->getDimension();
                $this->labelLeft->setOffset($labelDimension[1] / 2);
                $labelDimension = $this->labelLeft->getDimension();
                $this->labelRight->setOffset($labelDimension[1] / 2);
            }

            $this->addLabel($this->labelLeft);
            $this->addLabel($this->labelCenter1);
            $this->addLabel($this->labelCenter2);
            $this->addLabel($this->labelRight);
        }
    }

    /**
     * Kiểm tra độ dài chính xác.
     */
    protected function checkCorrectLength() {
        // Nếu có 12 ký tự, chỉ cần xóa ký tự cuối cùng mà không ném bất cứ thứ gì
        $c = strlen($this->text);
        if ($c === 12) {
            $this->text = substr($this->text, 0, 11);
        } elseif ($c !== 11) {
            throw new BCGParseException('upca', 'Must contain 11 digits, the 12th digit is automatically added.');
        }
    }
}
?>