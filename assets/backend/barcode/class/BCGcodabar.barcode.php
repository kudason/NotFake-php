<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp con - Codabar
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGParseException.php');
include_once('BCGBarcode1D.php');

class BCGcodabar extends BCGBarcode1D {
    /**
     * Hàm khởi tạo.
     */
    public function __construct() {
        parent::__construct();

        $this->keys = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '$', ':', '/', '.', '+', 'A', 'B', 'C', 'D');
        $this->code = array(    // 0 được thêm vào để thêm một không gian bổ sung
            '00000110',     /* 0 */
            '00001100',     /* 1 */
            '00010010',     /* 2 */
            '11000000',     /* 3 */
            '00100100',     /* 4 */
            '10000100',     /* 5 */
            '01000010',     /* 6 */
            '01001000',     /* 7 */
            '01100000',     /* 8 */
            '10010000',     /* 9 */
            '00011000',     /* - */
            '00110000',     /* $ */
            '10001010',     /* : */
            '10100010',     /* / */
            '10101000',     /* . */
            '00111110',     /* + */
            '00110100',     /* A */
            '01010010',     /* B */
            '00010110',     /* C */
            '00011100'      /* D */
        );
    }

    /**
     * Phân tích văn bản trước khi hiển thị nó.
     *
     * @param mixed $text
     */
    public function parse($text) {
        parent::parse(strtoupper($text));    // Chỉ cho phép chữ in hoa
    }

    /**
     * Vẽ mã vạch.
     *
     * @param resource $im
     */
    public function draw($im) {
        $c = strlen($this->text);
        for ($i = 0; $i < $c; $i++) {
            $this->drawChar($im, $this->findCode($this->text[$i]), true);
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
        $textLength = 0;
        $c = strlen($this->text);
        for ($i = 0; $i < $c; $i++) {
            $index = $this->findIndex($this->text[$i]);
            if ($index !== false) {
                $textLength += 8;
                $textLength += substr_count($this->code[$index], '1');
            }
        }

        $w += $textLength;
        $h += $this->thickness;
        return parent::getDimension($w, $h);
    }

    /**
     * Xác thực đầu vào.
     */
    protected function validate() {
        $c = strlen($this->text);
        if ($c === 0) {
            throw new BCGParseException('codabar', 'No data has been entered.');
        }

        // Kiểm tra xem tất cả các ký tự có được phép không
        for ($i = 0; $i < $c; $i++) {
            if (array_search($this->text[$i], $this->keys) === false) {
                throw new BCGParseException('codabar', 'The character \'' . $this->text[$i] . '\' is not allowed.');
            }
        }

        // Phải bắt đầu bằng A, B, C hoặc D
        if ($c == 0 || ($this->text[0] !== 'A' && $this->text[0] !== 'B' && $this->text[0] !== 'C' && $this->text[0] !== 'D')) {
            throw new BCGParseException('codabar', 'The text must start by the character A, B, C, or D.');
        }

        // Phải kết thúc bằng A, B, C hoặc D
        $c2 = $c - 1;
        if ($c2 === 0 || ($this->text[$c2] !== 'A' && $this->text[$c2] !== 'B' && $this->text[$c2] !== 'C' && $this->text[$c2] !== 'D')) {
            throw new BCGParseException('codabar', 'The text must end by the character A, B, C, or D.');
        }

        parent::validate();
    }
}
?>