<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * Một framework phát triển ứng dụng mã nguồn mở dành cho PHP 5.1.6 trở lên.
 *
 * @package		CodeIgniter
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// Kiểm tra xem hàm 'video_type' đã tồn tại chưa
if ( ! function_exists('video_type'))
{
    // Kiểm tra loại video (YouTube, Vimeo hoặc khác)
    function video_type($url) {
        if (strpos($url, 'youtube') > 0 || strpos($url, 'youtu') > 0) {
            return 'youtube';
        } elseif (strpos($url, 'vimeo') > 0) {
            return 'vimeo';
        } elseif(strpos($url, 'drive') > 0){
            return 'drive';
        }else{
            return 'unknown';
        }
    }
}

// Kiểm tra xem hàm 'get_vimeo_video_id' đã tồn tại chưa
if ( ! function_exists('get_vimeo_video_id'))
{
    // Lấy ID video Vimeo từ URL
    function get_vimeo_video_id($url) {
        return (int) substr(parse_url($url, PHP_URL_PATH), 1);
    }
}

// Kiểm tra xem hàm 'get_video_extension' đã tồn tại chưa
if ( ! function_exists('get_video_extension'))
{
    // Lấy phần mở rộng của video từ URL
    function get_video_extension($url) {
        if (strpos($url, '.mp4') > 0) {
            return 'mp4';
        } elseif (strpos($url, '.webm') > 0) {
            return 'webm';
        } else {
            return 'unknown';
        }
    }
}

// Kiểm tra xem hàm 'get_settings' đã tồn tại chưa
if (! function_exists('get_settings')) {
  function get_settings($type = '') {
    $CI =&  get_instance();
    $CI->load->database();

    $CI->db->where('type', $type);
    $result = $CI->db->get('settings')->row('description');
    return $result;
  }
}

// Kiểm tra xem hàm 'currency' đã tồn tại chưa
if (! function_exists('currency')) {
  function currency($price = "") {
    $CI =&  get_instance();
    $CI->load->database();
        if ($price != "") {
            $CI->db->where('type', 'system_currency');
            $currency_code = $CI->db->get('settings')->row('description');

            $CI->db->where('code', $currency_code);
            $symbol = $CI->db->get('currency')->row('symbol');

            $CI->db->where('type', 'currency_position');
            $position = $CI->db->get('settings')->row('description');

            if ($position == 'right') {
                return $price.$symbol;
            }elseif ($position == 'right-space') {
                return $price.' '.$symbol;
            }elseif ($position == 'left') {
                return $symbol.$price;
            }elseif ($position == 'left-space') {
                return $symbol.' '.$price;
            }
        }
  }
}

// Kiểm tra xem hàm 'currency_code_and_symbol' đã tồn tại chưa
if (! function_exists('currency_code_and_symbol')) {
  function currency_code_and_symbol($type = "") {
    $CI =&  get_instance();
    $CI->load->database();
        $CI->db->where('type', 'system_currency');
        $currency_code = $CI->db->get('settings')->row('description');

        $CI->db->where('code', $currency_code);
        $symbol = $CI->db->get('currency')->row('symbol');
        if ($type == "") {
            return $symbol;
        }else {
            return $currency_code;
        }

  }
}

// Hàm 'sanitizer' dùng để làm sạch chuỗi đầu vào
if (! function_exists('sanitizer')) {
  function sanitizer($string = "") {
    //$sanitized_string = preg_replace("/[^@ -.a-zA-Z0-9]+/", "", html_escape($string));
    $sanitized_string = html_escape($string);
    return $sanitized_string;
  }
}

// Kiểm tra xem hàm 'slugify' đã tồn tại chưa
if ( ! function_exists('slugify'))
{
  function slugify($text) {
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = strtolower($text);
        //$text = preg_replace('~[^-\w]+~', '', $text);
        if (empty($text))
            return 'n-a';
        return $text;
    }
}

// ------------------------------------------------------------------------
/* End of file common_helper.php */
/* Location: ./system/helpers/common_helper.php */
