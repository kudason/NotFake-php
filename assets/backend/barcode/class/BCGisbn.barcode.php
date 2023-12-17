<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp con - ISBN-10 và ISBN-13
 *
 * Bạn có thể cung cấp ISBN gồm 10 chữ số có hoặc không có checksum.
 * Bạn có thể cung cấp ISBN có 13 chữ số có hoặc không có checksum.
 * Tính toán ISBN dựa trên mã hóa EAN-13.
 *
 * checksum luôn được hiển thị.
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGParseException.php');
include_once('BCGArgumentException.php');
include_once('BCGean13.barcode.php');

class BCGisbn extends BCGean13 {
    const GS1_AUTO = 0;
    const GS1_PREFIX978 = 1;
    const GS1_PREFIX979 = 2;

    private $gs1;

    /**
     * Hàm khởi tạo.
     *
     * @param int $gs1
     */
    public function __construct($gs1 = self::GS1_AUTO) {
        parent::__construct();
        $this->setGS1($gs1);
    }

    /**
     * Thêm nhãn mặc định.
     */
    protected function addDefaultLabel() {
        if ($this->isDefaultEanLabelEnabled()) {
            $isbn = $this->createISBNText();
            $font = $this->font;

            $topLabel = new BCGLabel($isbn, $font, BCGLabel::POSITION_TOP, BCGLabel::ALIGN_CENTER);

            $this->addLabel($topLabel);
        }

        parent::addDefaultLabel();
    }

    /**
     * Đặt số đầu tiên của mã vạch.
     * - GS1_AUTO: Thêm 978 vào trước mã
     * - GS1_PREFIX978: Thêm 978 vào trước mã
     * - GS1_PREFIX979: Thêm 979 vào trước mã
     *
     * @param int $gs1
     */
    public function setGS1($gs1) {
        $gs1 = (int)$gs1;
        if ($gs1 !== self::GS1_AUTO && $gs1 !== self::GS1_PREFIX978 && $gs1 !== self::GS1_PREFIX979) {
            throw new BCGArgumentException('The GS1 argument must be BCGisbn::GS1_AUTO, BCGisbn::GS1_PREFIX978, or BCGisbn::GS1_PREFIX979', 'gs1');
        }

        $this->gs1 = $gs1;
    }

    /**
     * Kiểm tra ký tự được phép.
     */
    protected function checkCharsAllowed() {
        $c = strlen($this->text);

        // Trường hợp đặc biệt, nếu có 10 chữ số thì chữ số cuối cùng có thể là X
        if ($c === 10) {
            if (array_search($this->text[9], $this->keys) === false && $this->text[9] !== 'X') {
                throw new BCGParseException('isbn', 'The character \'' . $this->text[9] . '\' is not allowed.');
            }

            // Drop the last char
            $this->text = substr($this->text, 0, 9);
        }

        return parent::checkCharsAllowed();
    }

    /**
     * Kiểm tra độ dài chính xác.
     */
    protected function checkCorrectLength() {
        $c = strlen($this->text);

        // Nếu có 13 ký tự, chỉ cần xóa ký tự cuối cùng
        if ($c === 13) {
            $this->text = substr($this->text, 0, 12);
        } elseif ($c === 9 || $c === 10) {
            if ($c === 10) {
                // Trước khi bỏ nó, kiểm tra xem nó có hợp pháp không
                if (array_search($this->text[9], $this->keys) === false && $this->text[9] !== 'X') {
                    throw new BCGParseException('isbn', 'The character \'' . $this->text[9] . '\' is not allowed.');
                }

                $this->text = substr($this->text, 0, 9);
            }

            if ($this->gs1 === self::GS1_AUTO || $this->gs1 === self::GS1_PREFIX978) {
                $this->text = '978' . $this->text;
            } elseif ($this->gs1 === self::GS1_PREFIX979) {
                $this->text = '979' . $this->text;
            }
        } elseif ($c !== 12) {
            throw new BCGParseException('isbn', 'The code parsed must be 9, 10, 12, or 13 digits long.');
        }
    }

    /**
     * Tạo văn bản ISBN.
     *
     * @return string
     */
    private function createISBNText() {
        $isbn = '';
        if (!empty($this->text)) {
            // Cố gắng tạo Văn bản ISBN... dấu gạch nối thực sự phụ thuộc vào cơ quan ISBN.
            // Chỉ đặt một cái trước tổng kiểm tra và một cái sau GS1 nếu có.
            $c = strlen($this->text);
            if ($c === 12 || $c === 13) {
                // Nếu bây giờ có 13 ký tự, chỉ cần chuyển đổi tạm thời để tìm tổng kiểm tra...
                // Hơn nữa trong mã, vẫn xử lý vấn đề đó.
                $lastCharacter = '';
                if ($c === 13) {
                    $lastCharacter = $this->text[12];
                    $this->text = substr($this->text, 0, 12);
                }

                $checksum = $this->processChecksum();
                $isbn = 'ISBN ' . substr($this->text, 0, 3) . '-' . substr($this->text, 3, 9) . '-' . $checksum;

                // Đưa ký tự cuối cùng trở lại
                if ($c === 13) {
                    $this->text .= $lastCharacter;
                }
            } elseif ($c === 9 || $c === 10) {
                $checksum = 0;
                for ($i = 10; $i >= 2; $i--) {
                    $checksum += $this->text[10 - $i] * $i;
                }

                $checksum = 11 - $checksum % 11;
                if ($checksum === 10) {
                    $checksum = 'X'; // Thay đổi kiểu
                }

                $isbn = 'ISBN ' . substr($this->text, 0, 9) . '-' . $checksum;
            }
        }

        return $isbn;
    }
}
?>