<?php
/**
 *--------------------------------------------------------------------
 *
 * Cho phép nối 2 đối tượng BCGdraw hoặc 2 hình ảnh để chỉ tạo một hình ảnh.
 * Có một số tùy chọn để căn chỉnh.
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
class JoinDraw {
    const ALIGN_RIGHT       = 0;
    const ALIGN_BOTTOM      = 0;
    const ALIGN_LEFT        = 1;
    const ALIGN_TOP         = 1;
    const ALIGN_CENTER      = 2;

    const POSITION_RIGHT    = 0;
    const POSITION_BOTTOM   = 1;
    const POSITION_LEFT     = 2;
    const POSITION_TOP      = 3;

    private $image1;
    private $image2;
    private $alignement;
    private $position;
    private $space;
    private $im;

    /**
     * Hàm khởi tạo Joindraw.
     * - $image1 và $image2 phải là đối tượng BCGdraw hoặc đối tượng hình ảnh.
     * - $space là khoảng cách giữa hai đồ họa tính bằng pixel.
     * - $position là vị trí của $image2 tùy thuộc vào $image1.
     * - $alignment là căn chỉnh của $image2 nếu cái này nhỏ hơn $image1;
     * nếu $image2 lớn hơn $image1, thì $image1 sẽ được đặt ở phía đối diện được chỉ định.
     *
     * @param mixed $image1
     * @param mixed $image2
     * @param BCGColor $background
     * @param int $space
     * @param int $position
     * @param int $alignment
     */
    public function __construct($image1, $image2, $background, $space = 10, $position = self::POSITION_RIGHT, $alignment = self::ALIGN_TOP) {
        if ($image1 instanceof BCGDrawing) {
            $this->image1 = $image1->get_im();
        } else {
            $this->image1 = $image1;
        }
        if ($image2 instanceof BCGDrawing) {
            $this->image2 = $image2->get_im();
        } else {
            $this->image2 = $image2;
        }

        $this->background = $background;
        $this->space = (int)$space;
        $this->position = (int)$position;
        $this->alignment = (int)$alignment;

        $this->createIm();
    }

    /**
     * Hủy hình ảnh.
     */
    public function __destruct() {
        imagedestroy($this->im);
    }

    /**
     * Tìm vị trí mà mã vạch cần được căn chỉnh.
     *
     * @param int $size1
     * @param int $size2
     * @param int $ailgnment
     * @return int
     */
    private function findPosition($size1, $size2, $alignment) {
        $rsize1 = max($size1, $size2);
        $rsize2 = min($size1, $size2);

        if ($alignment === self::ALIGN_LEFT) { // Or TOP
            return 0;
        } elseif ($alignment === self::ALIGN_CENTER) {
            return $rsize1 / 2 - $rsize2 / 2;
        } else { // RIGHT or TOP
            return $rsize1 - $rsize2;
        }
    }

    /**
     * Thay đổi sự sắp xếp.
     *
     * @param int $alignment
     * @return int
     */
    private function changeAlignment($alignment) {
        if ($alignment === 0) {
            return 1;
        } elseif ($alignment === 1) {
            return 0;
        } else {
            return 2;
        }
    }

    /**
     * Tạo hình ảnh.
     */
    private function createIm() {
        $w1 = imagesx($this->image1);
        $w2 = imagesx($this->image2);
        $h1 = imagesy($this->image1);
        $h2 = imagesy($this->image2);

        if ($this->position === self::POSITION_LEFT || $this->position === self::POSITION_RIGHT) {
            $w = $w1 + $w2 + $this->space;
            $h = max($h1, $h2);
        } else {
            $w = max($w1, $w2);
            $h = $h1 + $h2 + $this->space;
        }

        $this->im = imagecreatetruecolor($w, $h);
        imagefill($this->im, 0, 0, $this->background->allocate($this->im));

        // Chúng ta bắt đầu xác định vị trí của hình ảnh
        if ($this->position === self::POSITION_TOP) {
            if ($w1 > $w2) {
                $posX1 = 0;
                $posX2 = $this->findPosition($w1, $w2, $this->alignment);
            } else {
                $a = $this->changeAlignment($this->alignment);
                $posX1 = $this->findPosition($w1, $w2, $a);
                $posX2 = 0;
            }

            $posY2 = 0;
            $posY1 = $h2 + $this->space;
        } elseif ($this->position === self::POSITION_LEFT) {
            if ($w1 > $w2) {
                $posY1 = 0;
                $posY2 = $this->findPosition($h1, $h2, $this->alignment);
            } else {
                $a = $this->changeAlignment($this->alignment);
                $posY2 = 0;
                $posY1 = $this->findPosition($h1, $h2, $a);
            }

            $posX2 = 0;
            $posX1 = $w2 + $this->space;
        } elseif ($this->position === self::POSITION_BOTTOM) {
            if ($w1 > $w2) {
                $posX2 = $this->findPosition($w1, $w2, $this->alignment);
                $posX1 = 0;
            } else {
                $a = $this->changeAlignment($this->alignment);
                $posX2 = 0;
                $posX1 = $this->findPosition($w1, $w2, $a);
            }

            $posY1 = 0;
            $posY2 = $h1 + $this->space;
        } else { // defaults to RIGHT
            if ($w1 > $w2) {
                $posY2 = $this->findPosition($h1, $h2, $this->alignment);
                $posY1 = 0;
            } else {
                $a = $this->changeAlignment($this->alignment);
                $posY2 = 0;
                $posY1 = $this->findPosition($h1, $h2, $a);
            }

            $posX1 = 0;
            $posX2 = $w1 + $this->space;
        }

        imagecopy($this->im, $this->image1, $posX1, $posY1, 0, 0, $w1, $h1);
        imagecopy($this->im, $this->image2, $posX2, $posY2, 0, 0, $w2, $h2);
    }

    /**
     *Trả về $im mới được tạo.
     *
     * @return resource
     */
    public function get_im() {
        return $this->im;
    }
}
?>