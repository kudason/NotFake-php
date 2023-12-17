<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp cơ sở cho Mã vạch 1D và 2D
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGColor.php');
include_once('BCGLabel.php');
include_once('BCGArgumentException.php');
include_once('BCGDrawException.php');

abstract class BCGBarcode {
    const COLOR_BG = 0;
    const COLOR_FG = 1;

    protected $colorFg, $colorBg;       // Màu nền trước, nền sau
    protected $scale;                   // Tỷ lệ đồ họa, mặc định: 1
    protected $offsetX, $offsetY;       // Vị trí bắt đầu vẽ
    protected $labels = array();        // Mảng nhãn BCG
    protected $pushLabel = array(0, 0); // Đẩy nhãn, bên trái và trên cùng

    /**
     * Hàm khởi tạo.
     */
    protected function __construct() {
        $this->setOffsetX(0);
        $this->setOffsetY(0);
        $this->setForegroundColor(0x000000);
        $this->setBackgroundColor(0xffffff);
        $this->setScale(1);
    }

    /**
     * Phân tích văn bản trước khi hiển thị nó.
     *
     * @param mixed $text
     */
    public function parse($text) {
    }

    /**
     * Gets the foreground color of the barcode.
     *
     * @return BCGColor
     */
    public function getForegroundColor() {
        return $this->colorFg;
    }

    /**
     * Lấy màu nền trước của mã vạch. Nó có thể là màu BCG
     * Giá trị hoặc đơn giản là mã ngôn ngữ (trắng, đen, vàng...) hoặc giá trị hex.
     *
     * @param mixed $code
     */
    public function setForegroundColor($code) {
        if ($code instanceof BCGColor) {
            $this->colorFg = $code;
        } else {
            $this->colorFg = new BCGColor($code);
        }
    }

    /**
     * Lấy màu nền sau của mã vạch.
     *
     * @return BCGColor
     */
    public function getBackgroundColor() {
        return $this->colorBg;
    }

    /**
     * Đặt màu nền sau của mã vạch. Nó có thể là màu BCG
     * Giá trị hoặc đơn giản là mã ngôn ngữ (trắng, đen, vàng...) hoặc giá trị hex.
     *
     * @param mixed $code
     */
    public function setBackgroundColor($code) {
        if ($code instanceof BCGColor) {
            $this->colorBg = $code;
        } else {
            $this->colorBg = new BCGColor($code);
        }

        foreach ($this->labels as $label) {
            $label->setBackgroundColor($this->colorBg);
        }
    }

    /**
     * Đặt màu.
     *
     * @param mixed $fg
     * @param mixed $bg
     */
    public function setColor($fg, $bg) {
        $this->setForegroundColor($fg);
        $this->setBackgroundColor($bg);
    }

    /**
     * Lấy tỷ lệ của mã vạch.
     *
     * @return int
     */
    public function getScale() {
        return $this->scale;
    }

    /**
     * Đặt tỷ lệ của mã vạch theo pixel.
     * Nếu tỷ lệ thấp hơn 1, một ngoại lệ sẽ xuất hiện.
     *
     * @param int $scale
     */
    public function setScale($scale) {
        $scale = intval($scale);
        if ($scale <= 0) {
            throw new BCGArgumentException('The scale must be larger than 0.', 'scale');
        }

        $this->scale = $scale;
    }

    /**
     * Phương pháp trừu tượng vẽ mã vạch trên tài nguyên.
     *
     * @param resource $im
     */
    public abstract function draw($im);

    /**
     * Trả về kích thước tối đa của mã vạch.
     * [0]->chiều rộng
     * [1]->chiều cao
     *
     * @param int $w
     * @param int $h
     * @return int[]
     */
    public function getDimension($w, $h) {
        $labels = $this->getBiggestLabels(false);
        $pixelsAround = array(0, 0, 0, 0); // TRBL
        if (isset($labels[BCGLabel::POSITION_TOP])) {
            $dimension = $labels[BCGLabel::POSITION_TOP]->getDimension();
            $pixelsAround[0] += $dimension[1];
        }

        if (isset($labels[BCGLabel::POSITION_RIGHT])) {
            $dimension = $labels[BCGLabel::POSITION_RIGHT]->getDimension();
            $pixelsAround[1] += $dimension[0];
        }

        if (isset($labels[BCGLabel::POSITION_BOTTOM])) {
            $dimension = $labels[BCGLabel::POSITION_BOTTOM]->getDimension();
            $pixelsAround[2] += $dimension[1];
        }

        if (isset($labels[BCGLabel::POSITION_LEFT])) {
            $dimension = $labels[BCGLabel::POSITION_LEFT]->getDimension();
            $pixelsAround[3] += $dimension[0];
        }

        $finalW = ($w + $this->offsetX) * $this->scale;
        $finalH = ($h + $this->offsetY) * $this->scale;

        // Phần này sẽ kiểm tra xem nhãn trên/dưới có quá lớn so với chiều rộng và trái/phải quá lớn so với chiều cao của nó hay không
        $reversedLabels = $this->getBiggestLabels(true);
        foreach ($reversedLabels as $label) {
            $dimension = $label->getDimension();
            $alignment = $label->getAlignment();
            if ($label->getPosition() === BCGLabel::POSITION_LEFT || $label->getPosition() === BCGLabel::POSITION_RIGHT) {
                if ($alignment === BCGLabel::ALIGN_TOP) {
                    $pixelsAround[2] = max($pixelsAround[2], $dimension[1] - $finalH);
                } elseif ($alignment === BCGLabel::ALIGN_CENTER) {
                    $temp = ceil(($dimension[1] - $finalH) / 2);
                    $pixelsAround[0] = max($pixelsAround[0], $temp);
                    $pixelsAround[2] = max($pixelsAround[2], $temp);
                } elseif ($alignment === BCGLabel::ALIGN_BOTTOM) {
                    $pixelsAround[0] = max($pixelsAround[0], $dimension[1] - $finalH);
                }
            } else {
                if ($alignment === BCGLabel::ALIGN_LEFT) {
                    $pixelsAround[1] = max($pixelsAround[1], $dimension[0] - $finalW);
                } elseif ($alignment === BCGLabel::ALIGN_CENTER) {
                    $temp = ceil(($dimension[0] - $finalW) / 2);
                    $pixelsAround[1] = max($pixelsAround[1], $temp);
                    $pixelsAround[3] = max($pixelsAround[3], $temp);
                } elseif ($alignment === BCGLabel::ALIGN_RIGHT) {
                    $pixelsAround[3] = max($pixelsAround[3], $dimension[0] - $finalW);
                }
            }
        }

        $this->pushLabel[0] = $pixelsAround[3];
        $this->pushLabel[1] = $pixelsAround[0];

        $finalW = ($w + $this->offsetX) * $this->scale + $pixelsAround[1] + $pixelsAround[3];
        $finalH = ($h + $this->offsetY) * $this->scale + $pixelsAround[0] + $pixelsAround[2];

        return array($finalW, $finalH);
    }

    /**
     * Lấy độ lệch X.
     *
     * @return int
     */
    public function getOffsetX() {
        return $this->offsetX;
    }

    /**
     * Đặt độ lệch X.
     *
     * @param int $offsetX
     */
    public function setOffsetX($offsetX) {
        $offsetX = intval($offsetX);
        if ($offsetX < 0) {
            throw new BCGArgumentException('The offset X must be 0 or larger.', 'offsetX');
        }

        $this->offsetX = $offsetX;
    }

    /**
     * Lấy độ lệch Y.
     *
     * @return int
     */
    public function getOffsetY() {
        return $this->offsetY;
    }

    /**
     * Đặt độ lệch Y.
     *
     * @param int $offsetY
     */
    public function setOffsetY($offsetY) {
        $offsetY = intval($offsetY);
        if ($offsetY < 0) {
            throw new BCGArgumentException('The offset Y must be 0 or larger.', 'offsetY');
        }

        $this->offsetY = $offsetY;
    }

    /**
     * Thêm nhãn vào bản vẽ.
     *
     * @param BCGLabel $label
     */
    public function addLabel(BCGLabel $label) {
        $label->setBackgroundColor($this->colorBg);
        $this->labels[] = $label;
    }

    /**
     * Loại bỏ nhãn khỏi bản vẽ.
     *
     * @param BCGLabel $label
     */
    public function removeLabel(BCGLabel $label) {
        $remove = -1;
        $c = count($this->labels);
        for ($i = 0; $i < $c; $i++) {
            if ($this->labels[$i] === $label) {
                $remove = $i;
                break;
            }
        }

        if ($remove > -1) {
            array_splice($this->labels, $remove, 1);
        }
    }

    /**
     * Xóa nhãn.
     */
    public function clearLabels() {
        $this->labels = array();
    }

    /**
     * Vẽ văn bản.
     * Tọa độ được truyền là vị trí của mã vạch.
     * $x1 và $y1 đại diện cho góc trên cùng bên trái.
     * $x2 và $y2 đại diện cho góc dưới cùng bên phải.
     *
     * @param resource $im
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     */
    protected function drawText($im, $x1, $y1, $x2, $y2) {
        foreach ($this->labels as $label) {
            $label->draw($im,
                ($x1 + $this->offsetX) * $this->scale + $this->pushLabel[0],
                ($y1 + $this->offsetY) * $this->scale + $this->pushLabel[1],
                ($x2 + $this->offsetX) * $this->scale + $this->pushLabel[0],
                ($y2 + $this->offsetY) * $this->scale + $this->pushLabel[1]);
        }
    }

    /**
     * Vẽ 1 pixel trên tài nguyên ở một vị trí cụ thể với màu xác định.
     *
     * @param resource $im
     * @param int $x
     * @param int $y
     * @param int $color
     */
    protected function drawPixel($im, $x, $y, $color = self::COLOR_FG) {
        $xR = ($x + $this->offsetX) * $this->scale + $this->pushLabel[0];
        $yR = ($y + $this->offsetY) * $this->scale + $this->pushLabel[1];

        // Luôn vẽ hình chữ nhật
        imagefilledrectangle($im,
            $xR,
            $yR,
            $xR + $this->scale - 1,
            $yR + $this->scale - 1,
            $this->getColor($im, $color));
    }

    /**
     * Vẽ một hình chữ nhật trống trên tài nguyên ở một vị trí cụ thể với màu xác định.
     *
     * @param resource $im
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param int $color
     */
    protected function drawRectangle($im, $x1, $y1, $x2, $y2, $color = self::COLOR_FG) {
        if ($this->scale === 1) {
            imagefilledrectangle($im,
                ($x1 + $this->offsetX) + $this->pushLabel[0],
                ($y1 + $this->offsetY) + $this->pushLabel[1],
                ($x2 + $this->offsetX) + $this->pushLabel[0],
                ($y2 + $this->offsetY) + $this->pushLabel[1],
                $this->getColor($im, $color));
        } else {
            imagefilledrectangle($im, ($x1 + $this->offsetX) * $this->scale + $this->pushLabel[0], ($y1 + $this->offsetY) * $this->scale + $this->pushLabel[1], ($x2 + $this->offsetX) * $this->scale + $this->pushLabel[0] + $this->scale - 1, ($y1 + $this->offsetY) * $this->scale + $this->pushLabel[1] + $this->scale - 1, $this->getColor($im, $color));
            imagefilledrectangle($im, ($x1 + $this->offsetX) * $this->scale + $this->pushLabel[0], ($y1 + $this->offsetY) * $this->scale + $this->pushLabel[1], ($x1 + $this->offsetX) * $this->scale + $this->pushLabel[0] + $this->scale - 1, ($y2 + $this->offsetY) * $this->scale + $this->pushLabel[1] + $this->scale - 1, $this->getColor($im, $color));
            imagefilledrectangle($im, ($x2 + $this->offsetX) * $this->scale + $this->pushLabel[0], ($y1 + $this->offsetY) * $this->scale + $this->pushLabel[1], ($x2 + $this->offsetX) * $this->scale + $this->pushLabel[0] + $this->scale - 1, ($y2 + $this->offsetY) * $this->scale + $this->pushLabel[1] + $this->scale - 1, $this->getColor($im, $color));
            imagefilledrectangle($im, ($x1 + $this->offsetX) * $this->scale + $this->pushLabel[0], ($y2 + $this->offsetY) * $this->scale + $this->pushLabel[1], ($x2 + $this->offsetX) * $this->scale + $this->pushLabel[0] + $this->scale - 1, ($y2 + $this->offsetY) * $this->scale + $this->pushLabel[1] + $this->scale - 1, $this->getColor($im, $color));
        }
    }

    /**
     * Vẽ một hình chữ nhật đầy trên tài nguyên ở một vị trí cụ thể với màu xác định.
     *
     * @param resource $im
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param int $color
     */
    protected function drawFilledRectangle($im, $x1, $y1, $x2, $y2, $color = self::COLOR_FG) {
        if ($x1 > $x2) { // Tráo đổi
            $x1 ^= $x2 ^= $x1 ^= $x2;
        }

        if ($y1 > $y2) { // Tráo đổi
            $y1 ^= $y2 ^= $y1 ^= $y2;
        }

        imagefilledrectangle($im,
            ($x1 + $this->offsetX) * $this->scale + $this->pushLabel[0],
            ($y1 + $this->offsetY) * $this->scale + $this->pushLabel[1],
            ($x2 + $this->offsetX) * $this->scale + $this->pushLabel[0] + $this->scale - 1,
            ($y2 + $this->offsetY) * $this->scale + $this->pushLabel[1] + $this->scale - 1,
            $this->getColor($im, $color));
    }

    /**
     * Phân bổ màu dựa trên số nguyên.
     *
     * @param resource $im
     * @param int $color
     * @return resource
     */
    protected function getColor($im, $color) {
        if ($color === self::COLOR_BG) {
            return $this->colorBg->allocate($im);
        } else {
            return $this->colorFg->allocate($im);
        }
    }

    /**
     * Trả về chiều rộng nhãn lớn nhất cho TRÁI/PHẢI và chiều cao cho TOP/DƯỚI.
     *
     * @param bool $reversed
     * @return BCGLabel[]
     */
    private function getBiggestLabels($reversed = false) {
        $searchLR = $reversed ? 1 : 0;
        $searchTB = $reversed ? 0 : 1;

        $labels = array();
        foreach ($this->labels as $label) {
            $position = $label->getPosition();
            if (isset($labels[$position])) {
                $savedDimension = $labels[$position]->getDimension();
                $dimension = $label->getDimension();
                if ($position === BCGLabel::POSITION_LEFT || $position === BCGLabel::POSITION_RIGHT) {
                    if ($dimension[$searchLR] > $savedDimension[$searchLR]) {
                        $labels[$position] = $label;
                    }
                } else {
                    if ($dimension[$searchTB] > $savedDimension[$searchTB]) {
                        $labels[$position] = $label;
                    }
                }
            } else {
                $labels[$position] = $label;
            }
        }

        return $labels;
    }
}
?>