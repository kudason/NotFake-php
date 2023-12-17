<?php
/**
 *--------------------------------------------------------------------
 *
 * Lớp con - Code 128, A, B, C
 *
 * # Code C Working properly only on PHP4 or PHP5.0.3+ due to bug :
 * http://bugs.php.net/bug.php?id=28862
 *
 * !! Cảnh báo !!
 * Nếu bạn hiển thị checksum trên nhãn, bạn có thể nhận được
 * một số rác vì một số ký tự không thể hiển thị được.
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
include_once('BCGParseException.php');
include_once('BCGBarcode1D.php');

define('CODE128_A',    1);            // Bảng A
define('CODE128_B',    2);            // Bảng B
define('CODE128_C',    3);            // Bảng C
class BCGcode128 extends BCGBarcode1D {
    const KEYA_FNC3 = 96;
    const KEYA_FNC2 = 97;
    const KEYA_SHIFT = 98;
    const KEYA_CODEC = 99;
    const KEYA_CODEB = 100;
    const KEYA_FNC4 = 101;
    const KEYA_FNC1 = 102;

    const KEYB_FNC3 = 96;
    const KEYB_FNC2 = 97;
    const KEYB_SHIFT = 98;
    const KEYB_CODEC = 99;
    const KEYB_FNC4 = 100;
    const KEYB_CODEA = 101;
    const KEYB_FNC1 = 102;

    const KEYC_CODEB = 100;
    const KEYC_CODEA = 101;
    const KEYC_FNC1 = 102;

    const KEY_STARTA = 103;
    const KEY_STARTB = 104;
    const KEY_STARTC = 105;

    const KEY_STOP = 106;

    protected $keysA, $keysB, $keysC;
    private $starting_text;
    private $indcheck, $data, $lastTable;
    private $tilde;

    private $shift;
    private $latch;
    private $fnc;

    private $METHOD            = null; // Mảng phương thức có sẵn để tạo Code128 (CODE128_A, CODE128_B, CODE128_C)

    /**
     * Hàm khởi tọa.
     *
     * @param char $start
     */
    public function __construct($start = null) {
        parent::__construct();

        /* CODE 128 A */
        $this->keysA = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_';
        for ($i = 0; $i < 32; $i++) {
            $this->keysA .= chr($i);
        }

        /* CODE 128 B */
        $this->keysB = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~' . chr(127);

        /* CODE 128 C */
        $this->keysC = '0123456789';

        $this->code = array(
            '101111',   /* 00 */
            '111011',   /* 01 */
            '111110',   /* 02 */
            '010112',   /* 03 */
            '010211',   /* 04 */
            '020111',   /* 05 */
            '011102',   /* 06 */
            '011201',   /* 07 */
            '021101',   /* 08 */
            '110102',   /* 09 */
            '110201',   /* 10 */
            '120101',   /* 11 */
            '001121',   /* 12 */
            '011021',   /* 13 */
            '011120',   /* 14 */
            '002111',   /* 15 */
            '012011',   /* 16 */
            '012110',   /* 17 */
            '112100',   /* 18 */
            '110021',   /* 19 */
            '110120',   /* 20 */
            '102101',   /* 21 */
            '112001',   /* 22 */
            '201020',   /* 23 */
            '200111',   /* 24 */
            '210011',   /* 25 */
            '210110',   /* 26 */
            '201101',   /* 27 */
            '211001',   /* 28 */
            '211100',   /* 29 */
            '101012',   /* 30 */
            '101210',   /* 31 */
            '121010',   /* 32 */
            '000212',   /* 33 */
            '020012',   /* 34 */
            '020210',   /* 35 */
            '001202',   /* 36 */
            '021002',   /* 37 */
            '021200',   /* 38 */
            '100202',   /* 39 */
            '120002',   /* 40 */
            '120200',   /* 41 */
            '001022',   /* 42 */
            '001220',   /* 43 */
            '021020',   /* 44 */
            '002012',   /* 45 */
            '002210',   /* 46 */
            '022010',   /* 47 */
            '202010',   /* 48 */
            '100220',   /* 49 */
            '120020',   /* 50 */
            '102002',   /* 51 */
            '102200',   /* 52 */
            '102020',   /* 53 */
            '200012',   /* 54 */
            '200210',   /* 55 */
            '220010',   /* 56 */
            '201002',   /* 57 */
            '201200',   /* 58 */
            '221000',   /* 59 */
            '203000',   /* 60 */
            '110300',   /* 61 */
            '320000',   /* 62 */
            '000113',   /* 63 */
            '000311',   /* 64 */
            '010013',   /* 65 */
            '010310',   /* 66 */
            '030011',   /* 67 */
            '030110',   /* 68 */
            '001103',   /* 69 */
            '001301',   /* 70 */
            '011003',   /* 71 */
            '011300',   /* 72 */
            '031001',   /* 73 */
            '031100',   /* 74 */
            '130100',   /* 75 */
            '110003',   /* 76 */
            '302000',   /* 77 */
            '130001',   /* 78 */
            '023000',   /* 79 */
            '000131',   /* 80 */
            '010031',   /* 81 */
            '010130',   /* 82 */
            '003101',   /* 83 */
            '013001',   /* 84 */
            '013100',   /* 85 */
            '300101',   /* 86 */
            '310001',   /* 87 */
            '310100',   /* 88 */
            '101030',   /* 89 */
            '103010',   /* 90 */
            '301010',   /* 91 */
            '000032',   /* 92 */
            '000230',   /* 93 */
            '020030',   /* 94 */
            '003002',   /* 95 */
            '003200',   /* 96 */
            '300002',   /* 97 */
            '300200',   /* 98 */
            '002030',   /* 99 */
            '003020',   /* 100*/
            '200030',   /* 101*/
            '300020',   /* 102*/
            '100301',   /* 103*/
            '100103',   /* 104*/
            '100121',   /* 105*/
            '122000'    /*STOP*/
        );
        $this->setStart($start);
        $this->setTilde(true);

        // Chốt và Chuyển đổi
        $this->latch = array(
            array(null,             self::KEYA_CODEB,   self::KEYA_CODEC),
            array(self::KEYB_CODEA, null,               self::KEYB_CODEC),
            array(self::KEYC_CODEA, self::KEYC_CODEB,   null)
        );
        $this->shift = array(
            array(null,             self::KEYA_SHIFT),
            array(self::KEYB_SHIFT, null)
        );
        $this->fnc = array(
            array(self::KEYA_FNC1,  self::KEYA_FNC2,    self::KEYA_FNC3,    self::KEYA_FNC4),
            array(self::KEYB_FNC1,  self::KEYB_FNC2,    self::KEYB_FNC3,    self::KEYB_FNC4),
            array(self::KEYC_FNC1,  null,               null,               null)
        );

        // Phương thức có sẵn
        $this->METHOD        = array(CODE128_A => 'A', CODE128_B => 'B', CODE128_C => 'C');
    }

    /**
     * Chỉ định mã bắt đầu. Có thể là 'A', 'B', 'C' hoặc null
     * - Bảng A: Chữ hoa + ASCII 0-31 + dấu chấm câu
     * - Bảng B: Chữ hoa + Chữ thường + dấu chấm câu
     * - Bảng C: Các con số
     *
     * Nếu null được chỉ định, việc chọn bảng sẽ tự động được thực hiện.
     * Mặc định là null.
     *
     * @param string $table
     */
    public function setStart($table) {
        if ($table !== 'A' && $table !== 'B' && $table !== 'C' && $table !== null) {
            throw new BCGArgumentException('The starting table must be A, B, C or null.', 'table');
        }

        $this->starting_text = $table;
    }

    /**
     * Lấy dấu ngã.
     *
     * @return bool
     */
    public function getTilde() {
        return $this->tilde;
    }

    /**
     * Chấp nhận dấu ngã được xử lý như một ký tự đặc biệt.
     * Nếu đúng thì có thể làm như sau:
     * - ~~ : để tạo MỘT dấu ngã
     * - ~Fx : để chèn FCNx. x bằng từ 1 đến 4.
     *
     * @param boolean $accept
     */
    public function setTilde($accept) {
        $this->tilde = (bool)$accept;
    }

    /**
     * Phân tích văn bản trước khi hiển thị nó.
     *
     * @param mixed $text
     */
    public function parse($text) {
        $this->setStartFromText($text);

        $this->text = '';
        $seq = '';

        $currentMode = $this->starting_text;

        // Định dạng chính xác những gì người dùng cung cấp.
        if (!is_array($text)) {
            $seq = $this->getSequence($text, $currentMode);
            $this->text = $text;
        } else {
            // Vòng lặp này kiểm tra UnknownText AND có tạo ngoại lệ không nếu ký tự không được chấp nhận trong bảng
            reset($text);
            while (list($key1, $val1) = each($text)) {     // Lấy từng giá trị
                if (!is_array($val1)) {                    // Đây không phải một bảng
                    if (is_string($val1)) {                // Nếu đó là một chuỗi(string), hãy phân tích dưới dạng không xác định
                        $seq .= $this->getSequence($val1, $currentMode);
                        $this->text .= $val1;
                    } else {
                        // Trường hợp của "array(ENCODING, 'text')"
                        // Chúng ta đã nhận được ENCODING trong $val1, gọi lại 'each' sẽ nhận được 'text' trong $val2
                        list($key2, $val2) = each($text);
                        $seq .= $this->{'setParse' . $this->METHOD[$val1]}($val2, $currentMode);
                        $this->text .= $val2;
                    }
                } else {                        // Phương pháp được chỉ định
                    // $val1[0] = ENCODING
                    // $val1[1] = 'text'
                    $value = isset($val1[1]) ? $val1[1] : '';    // Nếu dữ liệu có sẵn
                    $seq .= $this->{'setParse' . $this->METHOD[$val1[0]]}($value, $currentMode);
                    $this->text .= $value;
                }
            }
        }

        if ($seq !== '') {
            $bitstream = $this->createBinaryStream($this->text, $seq);
            $this->setData($bitstream);
        }

        $this->addDefaultLabel();
    }

    /**
     * Vẽ mã vạch.
     *
     * @param resource $im
     */
    public function draw($im) {
        $c = count($this->data);
        for ($i = 0; $i < $c; $i++) {
            $this->drawChar($im, $this->data[$i], true);
        }

        $this->drawChar($im, '1', true);
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
        // Bao gồm bắt đâu + văn bản + checksum + kết thúc
        $textlength = count($this->data) * 11;
        $endlength = 2; // + bar cuối

        $w += $textlength + $endlength;
        $h += $this->thickness;
        return parent::getDimension($w, $h);
    }

    /**
     * Xác thực đầu vào.
     */
    protected function validate() {
        $c = count($this->data);
        if ($c === 0) {
            throw new BCGParseException('code128', 'No data has been entered.');
        }

        parent::validate();
    }

    /**
     * Nạp chồng phương thức tính checksum.
     */
    protected function calculateChecksum() {
        // Checksum
        // Ký tự đầu tiên (START)
        // + Bắt đầu với ký tự dữ liệu đầu tiên theo sau ký tự bắt đầu,
        // lấy giá trị của ký tự (từ 0 đến 102) nhân lên
        // theo vị trí ký tự của nó (1) và thêm vào checksum đang chạy.
        // Modulated 103
        $this->checksumValue = $this->indcheck[0];
        $c = count($this->indcheck);
        for ($i = 1; $i < $c; $i++) {
            $this->checksumValue += $this->indcheck[$i] * $i;
        }

        $this->checksumValue = $this->checksumValue % 103;
    }

    /**
     * Nạp chồng phương thức hiển thị checksum.
     */
    protected function processChecksum() {
        if ($this->checksumValue === false) { // Tính checksum một lần duy nhất
            $this->calculateChecksum();
        }

        if ($this->checksumValue !== false) {
            if ($this->lastTable === 'C') {
                return (string)$this->checksumValue;
            }

            return $this->{'keys' . $this->lastTable}[$this->checksumValue];
        }

        return false;
    }

    /**
     * Chỉ định bảng starting_text nếu chưa có bảng nào được chỉ định trước đó.
     *
     * @param string $text
     */
    private function setStartFromText($text) {
        if ($this->starting_text === null) {
            // Nếu chúng ta có một bảng bắt buộc ngay từ đầu, chúng ta sẽ có được bảng đó...
            if (is_array($text)) {
                if (is_array($text[0])) {
                    // Mã như mảng(array(ENCODING, ''))
                    $this->starting_text = $this->METHOD[$text[0][0]];
                    return;
                } else {
                    if (is_string($text[0])) {
                        // Mã như mảng('test') (Automatic text)
                        $text = $text[0];
                    } else {
                        // Mã như mảng(ENCODING, '')
                        $this->starting_text = $this->METHOD[$text[0]];
                        return;
                    }
                }
            }

            // Tại thời điểm này, đã có lựa chọn bảng "tự động"...
            // Nếu có thể nhận được ít nhất 4 số, hãy chuyển sang C; nếu không thì vào B.
            $tmp = preg_quote($this->keysC, '/');
            $length = strlen($text);
            if ($length >= 4 && preg_match('/[' . $tmp . ']/', substr($text, 0, 4))) {
                $this->starting_text = 'C';
            } else {
                if ($length > 0 && strpos($this->keysB, $text[0]) !== false) {
                    $this->starting_text = 'B';
                } else {
                    $this->starting_text = 'A';
                }
            }
        }
    }

    /**
     * Trích xuất giá trị ~ từ $text tại vị trí $pos.
     * Nếu dấu ngã không phải là ~~, ~F1, ~F2, ~F3, ~F4; một lỗi được nêu ra.
     *
     * @param string $text
     * @param int $pos
     * @return string
     */
    private static function extractTilde($text, $pos) {
        if ($text[$pos] === '~') {
            if (isset($text[$pos + 1])) {
                // Chúng ta có dấu ngã không?
                if ($text[$pos + 1] === '~') {
                    return '~~';
                } elseif ($text[$pos + 1] === 'F') {
                    // Chúng ta có số sau không?
                    if (isset($text[$pos + 2])) {
                        $v = intval($text[$pos + 2]);
                        if ($v >= 1 && $v <= 4) {
                            return '~F' . $v;
                        } else {
                            throw new BCGParseException('code128', 'Bad ~F. You must provide a number from 1 to 4.');
                        }
                    } else {
                        throw new BCGParseException('code128', 'Bad ~F. You must provide a number from 1 to 4.');
                    }
                } else {
                    throw new BCGParseException('code128', 'Wrong code after the ~.');
                }
            } else {
                throw new BCGParseException('code128', 'Wrong code after the ~.');
            }
        } else {
            throw new BCGParseException('code128', 'There is no ~ at this location.');
        }
    }

    /**
     * Lấy chuỗi "chấm" cho $text dựa trên $currentMode.
     * Ngoài ra còn có một kiểm tra xem chúng tôi có sử dụng dấu ngã đặc biệt hay không ~
     *
     * @param string $text
     * @param string $currentMode
     * @return string
     */
    private function getSequenceParsed($text, $currentMode) {
        if ($this->tilde) {
            $sequence = '';
            $previousPos = 0;
            while (($pos = strpos($text, '~', $previousPos)) !== false) {
                $tildeData = self::extractTilde($text, $pos);

                $simpleTilde = ($tildeData === '~~');
                if ($simpleTilde && $currentMode !== 'B') {
                    throw new BCGParseException('code128', 'The Table ' . $currentMode . ' doesn\'t contain the character ~.');
                }

                // Tại thời điểm này, chúng tôi biết mình có ~Fx
                if ($tildeData !== '~F1' && $currentMode === 'C') {
                    // Chế độ C không hỗ trợ ~F2, ~F3, ~F4
                    throw new BCGParseException('code128', 'The Table C doesn\'t contain the function ' . $tildeData . '.');
                }

                $length = $pos - $previousPos;
                if ($currentMode === 'C') {
                    if ($length % 2 === 1) {
                        throw new BCGParseException('code128', 'The text "' . $text . '" must have an even number of character to be encoded in Table C.');
                    }
                }

                $sequence .= str_repeat('.', $length);
                $sequence .= '.';
                $sequence .= (!$simpleTilde) ? 'F' : '';
                $previousPos = $pos + strlen($tildeData);
            }

            // Xả ra
            $length = strlen($text) - $previousPos;
            if ($currentMode === 'C') {
                if ($length % 2 === 1) {
                    throw new BCGParseException('code128', 'The text "' . $text . '" must have an even number of character to be encoded in Table C.');
                }
            }

            $sequence .= str_repeat('.', $length);

            return $sequence;
        } else {
            return str_repeat('.', strlen($text));
        }
    }

    /**
     * Phân tích văn bản và trả về trình tự thích hợp cho Bảng A.
     *
     * @param string $text
     * @param string $currentMode
     * @return string
     */
    private function setParseA($text, &$currentMode) {
        $tmp = preg_quote($this->keysA, '/');

        // Nếu chấp nhận ~ cho ký tự đặc biệt, phải cho phép nó.
        if ($this->tilde) {
            $tmp .= '~';
        }

        $match = array();
        if (preg_match('/[^' . $tmp . ']/', $text, $match) === 1) {
            // Tìm thấy thứ gì đó không được phép
            throw new BCGParseException('code128', 'The text "' . $text . '" can\'t be parsed with the Table A. The character "' . $match[0] . '" is not allowed.');
        } else {
            $latch = ($currentMode === 'A') ? '' : '0';
            $currentMode = 'A';

            return $latch . $this->getSequenceParsed($text, $currentMode);
        }
    }

    /**
     * Phân tích văn bản và trả về trình tự thích hợp cho Bảng B.
     *
     * @param string $text
     * @param string $currentMode
     * @return string
     */
    private function setParseB($text, &$currentMode) {
        $tmp = preg_quote($this->keysB, '/');

        $match = array();
        if (preg_match('/[^' . $tmp . ']/', $text, $match) === 1) {
            // Tìm thấy thứ gì đó không được phép
            throw new BCGParseException('code128', 'The text "' . $text . '" can\'t be parsed with the Table B. The character "' . $match[0] . '" is not allowed.');
        } else {
            $latch = ($currentMode === 'B') ? '' : '1';
            $currentMode = 'B';

            return $latch . $this->getSequenceParsed($text, $currentMode);
        }
    }

    /**
     * Phân tích văn bản và trả về trình tự thích hợp cho Bảng C.
     *
     * @param string $text
     * @param string $currentMode
     * @return string
     */
    private function setParseC($text, &$currentMode) {
        $tmp = preg_quote($this->keysC, '/');

        // Nếu chấp nhận ~ cho ký tự đặc biệt, phải cho phép nó.
        if ($this->tilde) {
            $tmp .= '~F';
        }

        $match = array();
        if (preg_match('/[^' . $tmp . ']/', $text, $match) === 1) {
            // Tìm thấy thứ gì đó không được phép
            throw new BCGParseException('code128', 'The text "' . $text . '" can\'t be parsed with the Table C. The character "' . $match[0] . '" is not allowed.');
        } else {
            $latch = ($currentMode === 'C') ? '' : '2';
            $currentMode = 'C';

            return $latch . $this->getSequenceParsed($text, $currentMode);
        }
    }

    /**
     * Tùy thuộc vào $text, nó sẽ trả về đúng
     * trình tự để mã hóa văn bản.
     *
     * @param string $text
     * @param string $starting_text
     * @return string
     */
    private function getSequence($text, &$starting_text) {
        $e = 10000;
        $latLen = array(
            array(0, 1, 1),
            array(1, 0, 1),
            array(1, 1, 0)
        );
        $shftLen = array(
            array($e, 1, $e),
            array(1, $e, $e),
            array($e, $e, $e)
        );
        $charSiz = array(2, 2, 1);

        $startA = $e;
        $startB = $e;
        $startC = $e;
        if ($starting_text === 'A') { $startA = 0; }
        if ($starting_text === 'B') { $startB = 0; }
        if ($starting_text === 'C') { $startC = 0; }

        $curLen = array($startA, $startB, $startC);
        $curSeq = array(null, null, null);

        $nextNumber = false;

        $x = 0;
        $xLen = strlen($text);
        for ($x = 0; $x < $xLen; $x++) {
            $input = $text[$x];

            // 1.
            for ($i = 0; $i < 3; $i++) {
                for ($j = 0; $j < 3; $j++) {
                    if (($curLen[$i] + $latLen[$i][$j]) < $curLen[$j]) {
                        $curLen[$j] = $curLen[$i] + $latLen[$i][$j];
                        $curSeq[$j] = $curSeq[$i] . $j;
                    }
                }
            }

            // 2.
            $nxtLen = array($e, $e, $e);
            $nxtSeq = array();

            // 3.
            $flag = false;
            $posArray = array();

            // Trường hợp đặc biệt, có một đấu ngã và xử lý
            if ($this->tilde && $input === '~') {
                $tildeData = self::extractTilde($text, $x);

                if ($tildeData === '~~') {
                    // Chỉ cần bỏ qua dấu ngã
                    $posArray[] = 1;
                    $x++;
                } elseif (substr($tildeData, 0, 2) === '~F') {
                    $v = intval($tildeData[2]);
                    $posArray[] = 0;
                    $posArray[] = 1;
                    if ($v === 1) {
                        $posArray[] = 2;
                    }

                    $x += 2;
                    $flag = true;
                }
            } else {
                $pos = strpos($this->keysA, $input);
                if ($pos !== false) {
                    $posArray[] = 0;
                }

                $pos = strpos($this->keysB, $input);
                if ($pos !== false) {
                    $posArray[] = 1;
                }

                // Có số ký tự tiếp theo không?? Hoặc a ~F1
                $pos = strpos($this->keysC, $input);
                if ($nextNumber || ($pos !== false && isset($text[$x + 1]) && strpos($this->keysC, $text[$x + 1]) !== false)) {
                    $nextNumber = !$nextNumber;
                    $posArray[] = 2;
                }
            }

            $c = count($posArray);
            for ($i = 0; $i < $c; $i++) {
                if (($curLen[$posArray[$i]] + $charSiz[$posArray[$i]]) < $nxtLen[$posArray[$i]]) {
                    $nxtLen[$posArray[$i]] = $curLen[$posArray[$i]] + $charSiz[$posArray[$i]];
                    $nxtSeq[$posArray[$i]] = $curSeq[$posArray[$i]] . '.';
                }

                for ($j = 0; $j < 2; $j++) {
                    if ($j === $posArray[$i]) { continue; }
                    if (($curLen[$j] + $shftLen[$j][$posArray[$i]] + $charSiz[$posArray[$i]]) < $nxtLen[$j]) {
                        $nxtLen[$j] = $curLen[$j] + $shftLen[$j][$posArray[$i]] + $charSiz[$posArray[$i]];
                        $nxtSeq[$j] = $curSeq[$j] . chr($posArray[$i] + 65) . '.';
                    }
                }
            }

            if ($c === 0) {
                // Tìm thấy một ký tự không được hỗ trợ
                throw new BCGParseException('code128', 'Character ' .  $input . ' not supported.');
            }

            if ($flag) {
                for ($i = 0; $i < 5; $i++) {
                    if (isset($nxtSeq[$i])) {
                        $nxtSeq[$i] .= 'F';
                    }
                }
            }

            // 4.
            for ($i = 0; $i < 3; $i++) {
                $curLen[$i] = $nxtLen[$i];
                if (isset($nxtSeq[$i])) {
                    $curSeq[$i] = $nxtSeq[$i];
                }
            }
        }

        // Mọi curLen dưới $e đều có thể thực hiện được nhưng lấy giá trị nhỏ nhất
        $m = $e;
        $k = -1;
        for ($i = 0; $i < 3; $i++) {
            if ($curLen[$i] < $m) {
                $k = $i;
                $m = $curLen[$i];
            }
        }

        if ($k === -1) {
            return '';
        }

        return $curSeq[$k];
    }

    /**
     * Tùy thuộc vào chuỗi $seq đã cho (trả về từ getSequence()),
     * phương thức này sẽ trả về dòng mã trong một mảng. Mỗi ký tự sẽ là một
     * chuỗi bit dựa trên Mã 128.
     *
     * Mỗi chữ cái trong chuỗi đại diện cho các bit.
     *
     * 0 đến 2 là chốt
     * A đến B là Shift + Letter
     * . là một char trong bảng mã hiện tại
     *
     * @param string $text
     * @param string $seq
     * @return string[][]
     */
    private function createBinaryStream($text, $seq) {
        $c = strlen($seq);

        $data = array(); // dòng mã
        $indcheck = array(); // chỉ mục cho checksum

        $currentEncoding = 0;
        if ($this->starting_text === 'A') {
            $currentEncoding = 0;
            $indcheck[] = self::KEY_STARTA;
            $this->lastTable = 'A';
        } elseif ($this->starting_text === 'B') {
            $currentEncoding = 1;
            $indcheck[] = self::KEY_STARTB;
            $this->lastTable = 'B';
        } elseif ($this->starting_text === 'C') {
            $currentEncoding = 2;
            $indcheck[] = self::KEY_STARTC;
            $this->lastTable = 'C';
        }

        $data[] = $this->code[103 + $currentEncoding];

        $temporaryEncoding = -1;
        for ($i = 0, $counter = 0; $i < $c; $i++) {
            $input = $seq[$i];
            $inputI = intval($input);
            if ($input === '.') {
                $this->encodeChar($data, $currentEncoding, $seq, $text, $i, $counter, $indcheck);
                if ($temporaryEncoding !== -1) {
                    $currentEncoding = $temporaryEncoding;
                    $temporaryEncoding = -1;
                }
            } elseif ($input >= 'A' && $input <= 'B') {
                // Thay đổi
                $encoding = ord($input) - 65;
                $shift = $this->shift[$currentEncoding][$encoding];
                $indcheck[] = $shift;
                $data[] = $this->code[$shift];
                if ($temporaryEncoding === -1) {
                    $temporaryEncoding = $currentEncoding;
                }

                $currentEncoding = $encoding;
            } elseif ($inputI >= 0 && $inputI < 3) {
                $temporaryEncoding = -1;

                // Chốt
                $latch = $this->latch[$currentEncoding][$inputI];
                if ($latch !== null) {
                    $indcheck[] = $latch;
                    $this->lastTable = chr(65 + $inputI);
                    $data[] = $this->code[$latch];
                    $currentEncoding = $inputI;
                }
            }
        }

        return array($indcheck, $data);
    }

    /**
     * Mã hóa các ký tự, dựa trên mã hóa và trình tự của nó
     *
     * @param int[] $data
     * @param int $encoding
     * @param string $seq
     * @param string $text
     * @param int $i
     * @param int $counter
     * @param int[] $indcheck
     */
    private function encodeChar(&$data, $encoding, $seq, $text, &$i, &$counter, &$indcheck) {
        if (isset($seq[$i + 1]) && $seq[$i + 1] === 'F') {
            // Có một flag
            if ($text[$counter + 1] === 'F') {
                $number = $text[$counter + 2];
                $fnc = $this->fnc[$encoding][$number - 1];
                $indcheck[] = $fnc;
                $data[] = $this->code[$fnc];

                // Bỏ qua F + number
                $counter += 2;
            } else {
                // Not supposed
            }

            $i++;
        } else {
            if ($encoding === 2) {
                // Lấy 2 số cùng một lúc
                $code = (int)substr($text, $counter, 2);
                $indcheck[] = $code;
                $data[] = $this->code[$code];
                $counter++;
                $i++;
            } else {
                $keys = ($encoding === 0) ? $this->keysA : $this->keysB;
                $pos = strpos($keys, $text[$counter]);
                $indcheck[] = $pos;
                $data[] = $this->code[$pos];
            }
        }

        $counter++;
    }

    /**
     * Lưu dữ liệu vào các lớp.
     *
     * Phương pháp này sẽ lưu dữ liệu, tính số cột thực
     * (nếu -1 được chọn), mức lỗi thực tế (nếu -1 được chọn)
     * đã chọn)... Nó sẽ thêm Padding vào cuối và tạo
     * mã lỗi.
     *
     * @param array $data
     */
    private function setData($data) {
        $this->indcheck = $data[0];
        $this->data = $data[1];
        $this->calculateChecksum();
        $this->data[] = $this->code[$this->checksumValue];
        $this->data[] = $this->code[self::KEY_STOP];
    }
}
?>