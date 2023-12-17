<?php
// Khai báo namespace (không gian tên) và kiểm tra quyền truy cập trực tiếp vào file này

if (!defined('BASEPATH'))
    exit('No direct script access allowed'); // Nếu BASEPATH không được định nghĩa, kết thúc script

class Email_model extends CI_Model {
    // Định nghĩa class Email_model, kế thừa từ CI_Model của CodeIgniter

    function __construct()
    {
        parent::__construct(); // Gọi hàm khởi tạo của lớp cha CI_Model
        $this->load->library('email'); // Tải thư viện email của CodeIgniter
    }
    
    function reset_password() {
        // Định nghĩa hàm reset_password để reset mật khẩu của người dùng

        // Kiểm tra và lấy dữ liệu email từ POST request
        $email      =   $this->input->post('email');
        // Truy vấn CSDL để kiểm tra email có tồn tại trong bảng 'user' không
        $query      =   $this->db->get_where('user', array('email' => $email));
        if($query->num_rows() > 0) {
            // Nếu email tồn tại trong CSDL

            $new_password = rand(100000, 999999); // Tạo mật khẩu mới ngẫu nhiên
            // Cập nhật mật khẩu mới vào CSDL
            $this->db->where('user_id', $query->row('user_id'));
            $this->db->update('user', array('password' => sha1($new_password)));

            // Chuẩn bị dữ liệu email để gửi
            $email_data['subject'] = "Password reset request"; // Tiêu đề email
            $email_data['from'] = get_settings('site_email'); // Email người gửi (lấy từ cài đặt hệ thống)
            $email_data['to'] = $email; // Email người nhận
            $email_data['to_name'] = $query->row('name'); // Tên người nhận
            // Nội dung email
            $email_data['message'] = 'Your password has been changed. Your new password is : <b style="cursor: pointer;"><u>'.$new_password.'</u></b><br />';
            // Tải template email và gửi email thông qua hàm send_smtp_mail
            $email_template = $this->load->view('email/common_template', $email_data, TRUE);
            $this->send_smtp_mail($email_template, $email_data['subject'], $email_data['to'], $email_data['from']);
            return true; // Trả về true nếu gửi email thành công
        } else {
            // Nếu email không tồn tại, đặt thông báo lỗi
            $this->session->set_flashdata('password_reset', 'failed');
        }

    }

    public function send_email_verification_mail($to = "", $verification_code = "") {
        // Hàm gửi email xác minh

        // Lấy thông tin người dùng từ CSDL dựa vào email
        $to_name = $this->db->get_where('user', array('email' => $to))->row_array();

        // Chuẩn bị dữ liệu email
        $email_data['subject'] = "Verify email address"; // Tiêu đề email
        $email_data['from'] = get_settings('site_email'); // Email người gửi
        $email_data['to'] = $to; // Email người nhận
        $email_data['to_name'] = $to_name['name']; // Tên người nhận
        $email_data['verification_code'] = $verification_code; // Mã xác minh
        // Tải template email xác minh và gửi email
        $email_template = $this->load->view('email/email_verification', $email_data, TRUE);
        $this->send_smtp_mail($email_template, $email_data['subject'], $email_data['to'], $email_data['from']);
    }


    public function send_smtp_mail($msg=NULL, $sub=NULL, $to=NULL, $from=NULL) {
        // Hàm cấu hình và gửi email thông qua SMTP

        // Tải thư viện email
        $this->load->library('email');

        // Đặt email người gửi mặc định nếu không được cung cấp
        if($from == NULL)
            $from       =   $this->db->get_where('settings' , array('type' => 'site_email'))->row('value');

        // Cấu hình SMTP và cấu hình email
        $config = array(
            'protocol'  => get_settings('protocol'), // Protocol
            'smtp_host' => get_settings('smtp_host'), // Host SMTP
            'smtp_port' => get_settings('smtp_port'), // Port SMTP
            'smtp_user' => get_settings('smtp_user'), // Tài khoản SMTP
            'smtp_pass' => get_settings('smtp_pass'), // Mật khẩu SMTP
            'mailtype'  => 'html', // Kiểu email (HTML)
            'charset'   => 'utf-8' // Bảng mã ký tự
        );
        // Đặt các tiêu đề (header) cho email
        $this->email->set_header('MIME-Version', 1.0);
        $this->email->set_header('Content-type', 'text/html');
        $this->email->set_header('charset', 'UTF-8');
        
        // Khởi tạo cấu hình email
        $this->email->initialize($config);
        // Đặt loại email là HTML
        $this->email->set_mailtype("html");
        // Đặt ký tự xuống dòng mới
        $this->email->set_newline("\r\n");

        // Cấu hình thông tin người nhận và người gửi
        $this->email->to($to); // Đến ai
        $this->email->from($from, get_settings('site_name')); // Từ ai, với tên hiển thị là tên trang web
        $this->email->subject($sub); // Tiêu đề email
        $this->email->message($msg); // Nội dung email

        // Gửi email
        $this->email->send();
    }
}
