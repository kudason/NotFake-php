<?php
/**
 *--------------------------------------------------------------------
 *
 * Giữ màu ở định dạng RGB.
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
class BCGColor {
    protected $r, $g, $b;    // Giá trị thập lục phân
    protected $transparent;

    /**
     * Lưu giá trị RGB vào các lớp.
     *
     * Có 4 cách để liên kết màu sắc với lớp này:
     * 1. Cho 3 tham số int (R, G, B)
     * 2. Cung cấp 1 giá trị hex chuỗi tham số (#ff0000) (trước #)
     * 3. Cung cấp 1 tham số giá trị int hex (0xff0000)
     * 4. Cung cấp 1 mã màu chuỗi tham số (trắng, đen, cam...)
     *
     * @param mixed ...
     */
    public function __construct() {
        $args = func_get_args();
        $c = count($args);
        if ($c === 3) {
            $this->r = intval($args[0]);
            $this->g = intval($args[1]);
            $this->b = intval($args[2]);
        } elseif ($c === 1) {
            if (is_string($args[0]) && strlen($args[0]) === 7 && $args[0][0] === '#') {        // Giá trị Hex trong chuỗi
                $this->r = intval(substr($args[0], 1, 2), 16);
                $this->g = intval(substr($args[0], 3, 2), 16);
                $this->b = intval(substr($args[0], 5, 2), 16);
            } else {
                if (is_string($args[0])) {
                    $args[0] = self::getColor($args[0]);
                }

                $args[0] = intval($args[0]);
                $this->r = ($args[0] & 0xff0000) >> 16;
                $this->g = ($args[0] & 0x00ff00) >> 8;
                $this->b = ($args[0] & 0x0000ff);
            }
        } else {
            $this->r = $this->g = $this->b = 0;
        }
    }

    /**
     * Đặt màu trong suốt(transparent).
     *
     * @param bool $transparent
     */
    public function setTransparent($transparent) {
        $this->transparent = $transparent;
    }

    /**
     * Trả về màu đỏ.
     *
     * @return int
     */
    public function r() {
        return $this->r;
    }

    /**
     * Trả về màu xanh lục.
     *
     * @return int
     */
    public function g() {
        return $this->g;
    }

    /**
     * Trả về màu xanh dương.
     *
     * @return int
     */
    public function b() {
        return $this->b;
    }

    /**
     * Trả về giá trị int cho màu PHP.
     *
     * @param resource $im
     * @return int
     */
    public function allocate(&$im) {
        $allocated = imagecolorallocate($im, $this->r, $this->g, $this->b);
        if ($this->transparent) {
            return imagecolortransparent($im, $allocated);
        } else {
            return $allocated;
        }
    }

    /**
     * Trả về lớp màu BCG tùy thuộc vào màu chuỗi.
     *
     * Nếu màu không tồn tại, nó sẽ lấy màu mặc định.
     *
     * @param string $code
     * @param string $default
     */
    public static function getColor($code, $default = 'white') {
        switch(strtolower($code)) {
            case '':
            case 'white':
                return 0xffffff;
            case 'black':
                return 0x000000;
            case 'maroon':
                return 0x800000;
            case 'red':
                return 0xff0000;
            case 'orange':
                return 0xffa500;
            case 'yellow':
                return 0xffff00;
            case 'olive':
                return 0x808000;
            case 'purple':
                return 0x800080;
            case 'fuchsia':
                return 0xff00ff;
            case 'lime':
                return 0x00ff00;
            case 'green':
                return 0x008000;
            case 'navy':
                return 0x000080;
            case 'blue':
                return 0x0000ff;
            case 'aqua':
                return 0x00ffff;
            case 'teal':
                return 0x008080;
            case 'silver':
                return 0xc0c0c0;
            case 'gray':
                return 0x808080;
            default:
                return self::getColor($default, 'white');
        }
    }
}
?>