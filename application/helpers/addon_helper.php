<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * Framework phát triển ứng dụng mã nguồn mở dành cho PHP 5.1.6 hoặc mới hơn
 *
 * @package        CodeIgniter
 * @author         ExpressionEngine Dev Team
 * @copyright      Bản quyền (c) 2008 - 2011, EllisLab, Inc.
 * @license        http://codeigniter.com/user_guide/license.html
 * @link           http://codeigniter.com
 * @since          Phiên bản 1.0
 * @filesource
 */

if (!function_exists('addon_status')) {
    /**
     * Hàm addon_status kiểm tra trạng thái của addon dựa trên unique_identifier.
     *
     * @param string $unique_identifier Định danh duy nhất của addon cần kiểm tra trạng thái.
     * @return int Trạng thái của addon. Trả về 0 nếu không tồn tại.
     */
    function addon_status($unique_identifier = '') {
        $CI = &get_instance();
        $CI->load->database();

        // Truy vấn trạng thái của addon dựa trên unique_identifier
        $result = $CI->db->get_where('addons', array('unique_identifier' => $unique_identifier));

        if ($result->num_rows() > 0) {
            $result = $result->row_array();
            return $result['status']; // Trả về trạng thái của addon nếu nó tồn tại trong cơ sở dữ liệu.
        } else {
            return 0; // Trả về 0 nếu addon không tồn tại.
        }
    }
}

// ------------------------------------------------------------------------
/* End of file addon_helper.php */
/* Location: ./system/helpers/common.php */
