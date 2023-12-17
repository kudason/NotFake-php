<?php  
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * Framework phát triển ứng dụng mã nguồn mở dành cho PHP 5.1.6 hoặc mới hơn
 *
 * @package        CodeIgniter
 * @copyright      Bản quyền (c) 2008 - 2011, EllisLab, Inc.
 * @license        http://codeigniter.com/user_guide/license.html
 * @link           http://codeigniter.com
 * @since          Phiên bản 1.0
 * @filesource
 */

// Hàm này giúp lấy cụm từ đã được dịch từ tệp ngôn ngữ. Nếu cụm từ chưa tồn tại, hàm này sẽ lưu cụm từ và giá trị mặc định của nó sẽ giống với đầu vào ban đầu.
 if ( ! function_exists('get_phrase'))
{
	function get_phrase($phrase = '') {
		$CI	=&	get_instance();
		$CI->load->database();
		$language_code = $CI->db->get_where('settings' , array('type' => 'language'))->row()->description;
		$key = strtolower(preg_replace('/\s+/', '_', $phrase));

		$langArray = openJSONFile($language_code);
		if (array_key_exists($key, $langArray)) {
		} else {
			$langArray[$key] = ucfirst(str_replace('_', ' ', $key));
			$jsonData = json_encode($langArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
			file_put_contents(APPPATH.'language/'.$language_code.'.json', stripslashes($jsonData));
		}

		return ucwords($langArray[$key]);
	}
}

//Hàm này giúp giải mã tệp JSON ngôn ngữ và trả về một mảng từ tệp đó.
if ( ! function_exists('openJSONFile'))
{
	function openJSONFile($code)
	{
		$jsonString = [];
		if (file_exists(APPPATH.'language/'.$code.'.json')) {
			$jsonString = file_get_contents(APPPATH.'language/'.$code.'.json');
			$jsonString = json_decode($jsonString, true);
		}
		return $jsonString;
	}
}

// Hàm này giúp chúng ta tạo một tệp JSON mới cho ngôn ngữ mới.
if ( ! function_exists('saveDefaultJSONFile'))
{
	function saveDefaultJSONFile($language_code){
		$language_code = strtolower($language_code);
		if(file_exists(APPPATH.'language/'.$language_code.'.json')){
			$newLangFile 	= APPPATH.'language/'.$language_code.'.json';
			$enLangFile   = APPPATH.'language/english.json';
			copy($enLangFile, $newLangFile);
		}else {
			$fp = fopen(APPPATH.'language/'.$language_code.'.json', 'w');
			$newLangFile = APPPATH.'language/'.$language_code.'.json';
			$enLangFile   = APPPATH.'language/english.json';
			copy($enLangFile, $newLangFile);
			fclose($fp);
		}
	}
}

// Hàm này giúp chúng ta cập nhật một cụm từ bên trong tệp ngôn ngữ.
if ( ! function_exists('saveJSONFile'))
{
	function saveJSONFile($language_code, $updating_key, $updating_value){
		$jsonString = [];
		if(file_exists(APPPATH.'language/'.$language_code.'.json')){
			$jsonString = file_get_contents(APPPATH.'language/'.$language_code.'.json');
			$jsonString = json_decode($jsonString, true);
			$jsonString[$updating_key] = $updating_value;
		}else {
			$jsonString[$updating_key] = $updating_value;
		}
		$jsonData = json_encode($jsonString, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
		file_put_contents(APPPATH.'language/'.$language_code.'.json', stripslashes($jsonData));
	}
}
