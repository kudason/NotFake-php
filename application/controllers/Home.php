<?php
defined('BASEPATH') OR exit('No direct script access allowed');

    // Định nghĩa lớp Home kế thừa từ CI_Controller
    class Home extends CI_Controller {
        function __construct()
		 {
            parent::__construct(); // Gọi constructor của lớp cha
            $this->load->database(); // Kết nối cơ sở dữ liệu
            $this->load->model('crud_model'); // Load model 'crud_model'
            $this->load->model('email_model'); // Load model 'email_model'
            $this->load->library('session'); // Load thư viện session
            $this->load->helper('directory'); // Load helper 'directory'
        }

        // Trang chủ
        public function index() {
            $this->login_check(); // Kiểm tra đăng nhập
            $page_data['page_name'] = 'landing'; // Thiết lập tên trang là 'landing'
            $page_data['page_title'] = 'Welcome'; // Thiết lập tiêu đề trang là 'Welcome'
            $this->load->view('frontend/index', $page_data); // Tải trang chủ
        }

        // Đăng ký tài khoản
        function signup() {
            $this->login_check(); // Kiểm tra đăng nhập
            if (isset($_POST) && !empty($_POST)) { // Kiểm tra nếu có dữ liệu POST
                if (!$this->crud_model->check_recaptcha() && get_settings('recaptcha') == 1) {
                    $this->session->set_flashdata('error_message', get_phrase('recaptcha_verification_failed')); // Tạo thông báo lỗi nếu xác minh Recaptcha thất bại
                    redirect(base_url().'index.php?home/signup', 'refresh'); // Chuyển hướng đến trang đăng ký
                }

                $signup_result = $this->crud_model->signup_user(); // Thực hiện đăng ký
                // Xử lý kết quả đăng ký
                if ($signup_result == true) {
                    sleep(2); // Chờ 2 giây
                    $trial_period = $this->crud_model->get_settings('trial_period'); // Lấy cài đặt thời gian dùng thử

                    // Chuyển hướng dựa trên cài đặt thời gian dùng thử
                    if ($trial_period == 'on')
                        redirect(base_url().'index.php?browse/switchprofile', 'refresh');
                    else if ($trial_period == 'off')
                        redirect(base_url().'index.php?browse/youraccount', 'refresh');
                } else if ($signup_result == false) {
                    redirect(base_url().'index.php?home/signup', 'refresh'); // Chuyển hướng trở lại trang đăng ký
                }
            }
            $page_data['page_name'] = 'signup'; // Thiết lập tên trang là 'signup'
            $page_data['page_title'] = get_phrase('sign_up'); // Thiết lập tiêu đề trang là 'Sign Up'
            $this->load->view('frontend/index', $page_data); // Tải trang đăng ký
        }

        // Xác minh mã xác nhận
        public function verification_code() {
            // Kiểm tra nếu không tồn tại email đăng ký trong session
            if ($this->session->userdata('register_email') == null) {
                redirect(base_url().'index.php?home/signin', 'refresh'); // Chuyển hướng đến trang đăng nhập
            }
            $page_data['page_name'] = 'verify_email_address'; // Thiết lập tên trang là 'verify_email_address'
            $page_data['page_title'] = get_phrase('verify_email_address'); // Thiết lập tiêu đề trang là 'Verify Email Address'
            $this->load->view('frontend/index', $page_data); // Tải trang xác minh email
        }

        // Gửi lại mã xác nhận
        public function resend_verification_code() {
            // [Code gửi lại mã xác nhận qua email]
            return true;
        }

        // Xác nhận địa chỉ email
        public function verify_email_address() {
            // [Code xác nhận địa chỉ email]
            // [Bỏ qua chi tiết do độ dài]
        }

        // Chuyển hướng sau khi đăng ký
        public function redirect_after_signup() {
            $this->login_check(); // Kiểm tra đăng nhập
            $trial_period = $this->crud_model->get_settings('trial_period'); // Lấy cài đặt thời gian dùng thử
            // Chuyển hướng dựa trên cài đặt thời gian dùng thử
            if ($trial_period == 'on')
                redirect(base_url().'index.php?browse/switchprofile', 'refresh');
            else if ($trial_period == 'off')
                redirect(base_url().'index.php?browse/youraccount', 'refresh');
        }
    }

    // Hàm đăng nhập
    function signin($param1 = "") 
	{
        $this->login_check(); // Kiểm tra xem người dùng đã đăng nhập chưa

        // Xử lý khi có dữ liệu được gửi từ form đăng nhập
        if (isset($_POST) && !empty($_POST)) {
            // Kiểm tra xác thực Recaptcha nếu cần
            if (!$this->crud_model->check_recaptcha() && get_settings('recaptcha') == 1) {
                $this->session->set_flashdata('error_message', get_phrase('recaptcha_verification_failed')); // Tạo thông báo lỗi nếu xác minh Recaptcha thất bại
                // Chuyển hướng đến trang đăng nhập phù hợp dựa trên tham số
                if ($param1 == 'admin') {
                    redirect(base_url().'index.php?home/signin/admin', 'refresh');
                } else {
                    redirect(base_url().'index.php?home/signin', 'refresh');
                }
            }

            // Lấy email và mật khẩu từ form
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $signin_result = $this->crud_model->signin($email, $password); // Gọi hàm đăng nhập

            // Xử lý kết quả đăng nhập
            if ($signin_result == true) {
                // Xác định hướng chuyển đến dựa trên loại tài khoản
                if ($this->session->userdata('login_type') == 1) {
                    $this->session->set_userdata('active_user', 'admin');
                    redirect(base_url().'index.php?admin/dashboard', 'refresh');
                } else if ($this->session->userdata('login_type') == 0) {
                    redirect(base_url().'index.php?browse/switchprofile', 'refresh');
                }
            } else if ($signin_result == false) {
                // Xử lý đăng nhập thất bại
                if ($param1 == 'admin') {
                    $this->session->set_flashdata('error_message', get_phrase('Login_failed'));
                    redirect(base_url().'index.php?home/signin/admin', 'refresh');
                } else {
                    redirect(base_url().'index.php?home/signin', 'refresh');
                }
            }
        }

        // Hiển thị trang đăng nhập tương ứng
        if ($param1 == 'admin') {
            $this->load->view('backend/login.php');
        } else {
            $page_data['page_name'] = 'signin';
            $page_data['page_title'] = 'Sign in';
            $this->load->view('frontend/index', $page_data);
        }
    }

    // Hàm quên mật khẩu
    function forget($param1 = "") {
        $this->login_check(); // Kiểm tra xem người dùng đã đăng nhập chưa

        // Xử lý quên mật khẩu
        if ($param1 == 'admin') {
            if (isset($_POST) && !empty($_POST)) {
                $signup_result = $this->email_model->reset_password(); // Gọi hàm reset mật khẩu
                redirect(base_url().'index.php?home/signin/admin', 'refresh'); // Chuyển hướng đến trang đăng nhập admin
            }
        } else {
            if (isset($_POST) && !empty($_POST)) {
                $signup_result = $this->email_model->reset_password(); // Gọi hàm reset mật khẩu
                redirect(base_url().'index.php?home/forget', 'refresh'); // Chuyển hướng đến trang quên mật khẩu
            }
        }

        // Hiển thị trang quên mật khẩu
        $page_data['page_name'] = 'forget';
        $page_data['page_title'] = 'Forget Password';
        $this->load->view('frontend/index', $page_data);
    }

    // Hàm đăng xuất
    function signout() {
        // Xóa thông tin người dùng khỏi session và hủy session
        $this->session->set_userdata('user_login_status', '');
        $this->session->set_userdata('user_id', '');
        $this->session->set_userdata('login_type', '');
        $this->session->sess_destroy();
        $this->session->set_flashdata('logout_notification', 'logged_out');
        redirect(base_url().'index.php?home/signin', 'refresh'); // Chuyển hướng đến trang đăng nhập
    }

    // Kiểm tra trạng thái đăng nhập
    function login_check() {
        // Nếu người dùng đã đăng nhập, chuyển hướng đến trang chủ
        if ($this->session->userdata('user_login_status') == 1)
            redirect(base_url().'index.php?browse/home', 'refresh');
    }
}
