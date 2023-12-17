<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp cho nhãn(Label)
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGArgumentException.php');
include_once('BCGFontPhp.php');
include_once('BCGFontFile.php');

class BCGLabel {
    const POSITION_TOP = 0;
    const POSITION_RIGHT = 1;
    const POSITION_BOTTOM = 2;
    const POSITION_LEFT = 3;

    const ALIGN_LEFT = 0;
    const ALIGN_TOP = 0;
    const ALIGN_CENTER = 1;
    const ALIGN_RIGHT = 2;
    const ALIGN_BOTTOM = 2;

    private $font;
    private $text;
    private $position;
    private $alignment;
    private $offset;
    private $spacing;
    private $rotationAngle;
    private $backgroundColor;
    private $foregroundColor;

    /**
     * Hàm khởi tạo.
     *
     * @param string $text
     * @param BCGFont $font
     * @param int $position
     * @param int $alignment
     */
    public function __construct($text = '', $font = null, $position = self::POSITION_BOTTOM, $alignment = self::ALIGN_CENTER) {
        $font = $font === null ? new BCGFontPhp(5) : $font;
        $this->setFont($font);
        $this->setText($text);
        $this->setPosition($position);
        $this->setAlignment($alignment);
        $this->setSpacing(4);
        $this->setOffset(0);
        $this->setRotationAngle(0);
        $this->setBackgroundColor(new BCGColor('white'));
        $this->setForegroundColor(new BCGColor('black'));
    }

    /**
     * Nhận được văn bản.
     *
     * @return string
     */
    public function getText() {
        return $this->font->getText();
    }

    /**
     * Đặt văn bản.
     *
     * @param string $text
     */
    public function setText($text) {
        $this->text = $text;
        $this->font->setText($this->text);
    }

    /**
     * Nhận được phông chữ.
     *
     * @return BCGFont
     */
    public function getFont() {
        return $this->font;
    }

    /**
     * Đặt phông chữ.
     *
     * @param BCGFont $font
     */
    public function setFont($font) {
        if ($font === null) {
            throw new BCGArgumentException('Font cannot be null.', 'font');
        }

        $this->font = clone $font;
        $this->font->setText($this->text);
        $this->font->setRotationAngle($this->rotationAngle);
        $this->font->setBackgroundColor($this->backgroundColor);
        $this->font->setForegroundColor($this->foregroundColor);
    }

    /**
     * Lấy vị trí văn bản để vẽ.
     *
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * Đặt vị trí văn bản để vẽ.
     *
     * @param int $position
     */
    public function setPosition($position) {
        $position = intval($position);
        if ($position !== self::POSITION_TOP && $position !== self::POSITION_RIGHT && $position !== self::POSITION_BOTTOM && $position !== self::POSITION_LEFT) {
            throw new BCGArgumentException('The text position must be one of a valid constant.', 'position');
        }

        $this->position = $position;
    }

    /**
     * Lấy căn chỉnh văn bản để vẽ.
     *
     * @return int
     */
    public function getAlignment() {
        return $this->alignment;
    }

    /**
     * Đặt căn chỉnh văn bản cho bản vẽ.
     *
     * @param int $alignment
     */
    public function setAlignment($alignment) {
        $alignment = intval($alignment);
        if ($alignment !== self::ALIGN_LEFT && $alignment !== self::ALIGN_TOP && $alignment !== self::ALIGN_CENTER && $alignment !== self::ALIGN_RIGHT && $alignment !== self::ALIGN_BOTTOM) {
            throw new BCGArgumentException('The text alignment must be one of a valid constant.', 'alignment');
        }

        $this->alignment = $alignment;
    }

    /**
     * Nhận phần bù.
     *
     * @return int
     */
    public function getOffset() {
        return $this->offset;
    }

    /**
     * Đặt phần bù.
     *
     * @param int $offset
     */
    public function setOffset($offset) {
        $this->offset = intval($offset);
    }

    /**
     * Nhận khoảng cách.
     *
     * @return int
     */
    public function getSpacing() {
        return $this->spacing;
    }

    /**
     * Đặt khoảng cách.
     *
     * @param int $spacing
     */
    public function setSpacing($spacing) {
        $this->spacing = max(0, intval($spacing));
    }

    /**
     * Lấy góc quay theo độ.
     *
     * @return int
     */
    public function getRotationAngle() {
        return $this->font->getRotationAngle();
    }

    /**
     * Đặt góc quay theo độ.
     *
     * @param int $rotationAngle
     */
    public function setRotationAngle($rotationAngle) {
        $this->rotationAngle = intval($rotationAngle);
        $this->font->setRotationAngle($this->rotationAngle);
    }

    /**
     * Lấy màu nền trong trường hợp xoay.
     *
     * @return BCGColor
     */
    public function getBackgroundColor() {
        return $this->backgroundColor;
    }

    /**
     * Đặt màu nền trong trường hợp xoay.
     *
     * @param BCGColor $backgroundColor
     */
    public /*internal*/ function setBackgroundColor($backgroundColor) {
        $this->backgroundColor = $backgroundColor;
        $this->font->setBackgroundColor($this->backgroundColor);
    }

    /**
     * Lấy màu nền trước.
     *
     * @return BCGColor
     */
    public function getForegroundColor() {
        return $this->font->getForegroundColor();
    }

    /**
     * Đặt màu nền trước.
     *
     * @param BCGColor $foregroundColor
     */
    public function setForegroundColor($foregroundColor) {
        $this->foregroundColor = $foregroundColor;
        $this->font->setForegroundColor($this->foregroundColor);
    }

    /**
     * Lấy kích thước được lấy bởi nhãn, bao gồm khoảng cách và khoảng cách.
     * [0]: chiều rộng
     * [1]: chiều cao
     *
     * @return int[]
     */
    public function getDimension() {
        $w = 0;
        $h = 0;

        $dimension = $this->font->getDimension();
        $w = $dimension[0];
        $h = $dimension[1];

        if ($this->position === self::POSITION_TOP || $this->position === self::POSITION_BOTTOM) {
            $h += $this->spacing;
            $w += max(0, $this->offset);
        } else {
            $w += $this->spacing;
            $h += max(0, $this->offset);
        }

        return array($w, $h);
    }

    /**
     * Vẽ văn bản.
     * Tọa độ được truyền là vị trí của mã vạch.
     * $x1 và $y1 đại diện cho góc trên cùng bên trái.
     * $x2 và $y2 đại diện cho góc dưới bên phải.
     *
     * @param resource $im
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     */
    public /*internal*/ function draw($im, $x1, $y1, $x2, $y2) {
        $x = 0;
        $y = 0;

        $fontDimension = $this->font->getDimension();

        if ($this->position === self::POSITION_TOP || $this->position === self::POSITION_BOTTOM) {
            if ($this->position === self::POSITION_TOP) {
                $y = $y1 - $this->spacing - $fontDimension[1];
            } elseif ($this->position === self::POSITION_BOTTOM) {
                $y = $y2 + $this->spacing;
            }

            if ($this->alignment === self::ALIGN_CENTER) {
                $x = ($x2 - $x1) / 2 + $x1 - $fontDimension[0] / 2 + $this->offset;
            } elseif ($this->alignment === self::ALIGN_LEFT)  {
                $x = $x1 + $this->offset;
            } else {
                $x = $x2 + $this->offset - $fontDimension[0];
            }
        } else {
            if ($this->position === self::POSITION_LEFT) {
                $x = $x1 - $this->spacing - $fontDimension[0];
            } elseif ($this->position === self::POSITION_RIGHT) {
                $x = $x2 + $this->spacing;
            }

            if ($this->alignment === self::ALIGN_CENTER) {
                $y = ($y2 - $y1) / 2 + $y1 - $fontDimension[1] / 2 + $this->offset;
            } elseif ($this->alignment === self::ALIGN_TOP)  {
                $y = $y1 + $this->offset;
            } else {
                $y = $y2 + $this->offset - $fontDimension[1];
            }
        }

        $this->font->setText($this->text);
        $this->font->draw($im, $x, $y);
    }
}
?>