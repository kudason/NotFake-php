<?php
/**
 *--------------------------------------------------------------------
 *
 * Giữ họ phông chữ và kích thước.
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGArgumentException.php');
include_once('BCGFont.php');
include_once('BCGColor.php');

class BCGFontFile implements BCGFont {
    const PHP_BOX_FIX = 0;

    private $path;
    private $size;
    private $text = '';
    private $foregroundColor;
    private $rotationAngle;
    private $box;
    private $boxFix;

    /**
     * Hàm khởi tạo.
     *
     * @param string $fontPath path to the file
     * @param int $size size in point
     */
    public function __construct($fontPath, $size) {
        if (!file_exists($fontPath)) {
            throw new BCGArgumentException('The font path is incorrect.', 'fontPath');
        }

        $this->path = $fontPath;
        $this->size = $size;
        $this->foregroundColor = new BCGColor('black');
        $this->setRotationAngle(0);
        $this->setBoxFix(self::PHP_BOX_FIX);
    }

    /**
     * Lấy văn bản liên quan đến phông chữ.
     *
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Đặt văn bản liên quan đến phông chữ.
     *
     * @param string text
     */
    public function setText($text) {
        $this->text = $text;
        $this->box = null;
    }

    /**
     * Nhận được vòng quay theo mức độ.
     *
     * @return int
     */
    public function getRotationAngle() {
        return (360 - $this->rotationAngle) % 360;
    }

    /**
     * Đặt mức độ xoay.
     *
     * @param int
     */
    public function setRotationAngle($rotationAngle) {
        $this->rotationAngle = (int)$rotationAngle;
        if ($this->rotationAngle !== 90 && $this->rotationAngle !== 180 && $this->rotationAngle !== 270) {
            $this->rotationAngle = 0;
        }

        $this->rotationAngle = (360 - $this->rotationAngle) % 360;

        $this->box = null;
    }

    /**
     * Lấy màu nền.
     *
     * @return BCGColor
     */
    public function getBackgroundColor() {
    }

    /**
     * Đặt màu nền.
     *
     * @param BCGColor $backgroundColor
     */
    public function setBackgroundColor($backgroundColor) {
    }

    /**
     * Lấy màu nền trước.
     *
     * @return BCGColor
     */
    public function getForegroundColor() {
        return $this->foregroundColor;
    }

    /**
     * Đặt màu nền trước.
     *
     * @param BCGColor $foregroundColor
     */
    public function setForegroundColor($foregroundColor) {
        $this->foregroundColor = $foregroundColor;
    }

    /**
     * Nhận thông tin sửa chữa hộp.
     *
     * @return int
     */
    public function getBoxFix() {
        return $this->boxFix;
    }

    /**
     * Đặt thông tin sửa hộp.
     *
     * @param int $value
     */
    public function setBoxFix($value) {
        $this->boxFix = intval($value);
    }

    /**
     * Trả về chiều rộng và chiều cao mà văn bản cần được viết.
     *
     * @return int[]
     */
    public function getDimension() {
        $w = 0.0;
        $h = 0.0;
        $box = $this->getBox();

        if ($box !== null) {
            $minX = min(array($box[0], $box[2], $box[4], $box[6]));
            $maxX = max(array($box[0], $box[2], $box[4], $box[6]));
            $minY = min(array($box[1], $box[3], $box[5], $box[7]));
            $maxY = max(array($box[1], $box[3], $box[5], $box[7]));

            $w = $maxX - $minX;
            $h = $maxY - $minY;
        }

        $rotationAngle = $this->getRotationAngle();
        if ($rotationAngle === 90 || $rotationAngle === 270) {
            return array($h + self::PHP_BOX_FIX, $w);
        } else {
            return array($w + self::PHP_BOX_FIX, $h);
        }
    }

    /**
     * Vẽ văn bản trên hình ảnh ở một vị trí cụ thể.
     * $x và $y đại diện cho góc dưới bên trái.
     *
     * @param resource $im
     * @param int $x
     * @param int $y
     */
    public function draw($im, $x, $y) {
        $drawingPosition = $this->getDrawingPosition($x, $y);
        imagettftext($im, $this->size, $this->rotationAngle, $drawingPosition[0], $drawingPosition[1], $this->foregroundColor->allocate($im), $this->path, $this->text);
    }

    private function getDrawingPosition($x, $y) {
        $dimension = $this->getDimension();
        $box = $this->getBox();
        $rotationAngle = $this->getRotationAngle();
        if ($rotationAngle === 0) {
            $y += abs(min($box[5], $box[7]));
        } elseif ($rotationAngle === 90) {
            $x += abs(min($box[5], $box[7]));
            $y += $dimension[1];
        } elseif ($rotationAngle === 180) {
            $x += $dimension[0];
            $y += abs(max($box[1], $box[3]));
        } elseif ($rotationAngle === 270) {
            $x += abs(max($box[1], $box[3]));
        }

        return array($x, $y);
    }

    private function getBox() {
        if ($this->box === null) {
            $gd = imagecreate(1, 1);
            $this->box = imagettftext($gd, $this->size, 0, 0, 0, 0, $this->path, $this->text);
        }

        return $this->box;
    }
}
?>