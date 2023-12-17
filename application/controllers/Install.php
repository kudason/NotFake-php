<?php
    // Kiểm tra nếu không được định nghĩa BASEPATH thì thoát để không cho truy cập trực tiếp vào file này.
    if (!defined('BASEPATH'))
        exit('No direct script access allowed');

    // Cài đặt thời gian tối đa thực thi và giới hạn bộ nhớ cho script
    ini_set('max_execution_time', 0); // Không giới hạn thời gian thực thi
    ini_set('memory_limit','2048M'); // Giới hạn bộ nhớ là 2048MB

    // Định nghĩa lớp Install kế thừa từ CI_Controller
    class Install extends CI_Controller {

        // Hàm khởi tạo mặc định
        public function index() {
            if ($this->router->default_controller == 'install') {
                redirect(site_url('index.php?install/step0'), 'refresh'); // Chuyển hướng đến bước đầu tiên của quá trình cài đặt
            }
            redirect(site_url('index.php?home'), 'refresh'); // Nếu không phải trang cài đặt, chuyển hướng đến trang chủ
        }

        // Hiển thị bước 0 của quá trình cài đặt
        function step0() {
            if ($this->router->default_controller != 'install') {
                redirect(site_url('index.php?home'), 'refresh'); // Nếu không phải trang cài đặt, chuyển hướng đến trang chủ
            }
            $page_data['page_name'] = 'step0'; // Thiết lập tên trang
            $this->load->view('install/index', $page_data); // Hiển thị trang cài đặt bước 0
        }

        // Hiển thị bước 1 của quá trình cài đặt
        function step1() {
            if ($this->router->default_controller != 'install') {
                redirect(site_url('index.php?home'), 'refresh'); // Nếu không phải trang cài đặt, chuyển hướng đến trang chủ
            }
            $page_data['page_name'] = 'step1'; // Thiết lập tên trang
            $this->load->view('install/index', $page_data); // Hiển thị trang cài đặt bước 1
        }

        // Hiển thị bước 2 của quá trình cài đặt, xác thực mã mua hàng
        function step2($param1 = '', $param2 = '') {
            if ($this->router->default_controller != 'install') {
                redirect(site_url('index.php?home'), 'refresh'); // Nếu không phải trang cài đặt, chuyển hướng đến trang chủ
            }
            if ($param1 == 'error') {
                $page_data['error'] = 'Purchase Code Verification Failed'; // Hiển thị thông báo lỗi nếu xác thực mã mua hàng thất bại
            }
            $page_data['page_name'] = 'step2'; // Thiết lập tên trang
            $this->load->view('install/index', $page_data); // Hiển thị trang cài đặt bước 2
        }

        // Xác thực mã mua hàng
        function validate_purchase_code() {
            $purchase_code = $this->input->post('purchase_code'); // Lấy mã mua hàng từ form

            // Gửi yêu cầu xác thực đến máy chủ
            $validation_response = $this->crud_model->curl_request($purchase_code);
            if ($validation_response == true) {
                // Lưu mã mua hàng vào session nếu xác thực thành công
                session_start();
                $_SESSION['purchase_code'] = $purchase_code;
                $_SESSION['purchase_code_verified'] = 1;
                redirect(site_url('index.php?install/step3'), 'refresh'); // Chuyển hướng đến bước tiếp theo
            } else {
                // Giữ nguyên tại bước hiện tại và hiển thị lỗi nếu xác thực thất bại
                session_start();
                $_SESSION['purchase_code_verified'] = 0;
                redirect(site_url('index.php?install/step2/error'), 'refresh');
            }
        }

        // Hiển thị bước 3 của quá trình cài đặt, cấu hình cơ sở dữ liệu
        function step3($param1 = '', $param2 = '') {
            if ($this->router->default_controller != 'install') {
                redirect(site_url('index.php?home'), 'refresh'); // Nếu không phải trang cài đặt, chuyển hướng đến trang chủ
            }

            $this->check_purchase_code_verification(); // Kiểm tra xác thực mã mua hàng

            // Xử lý thông báo lỗi
            if ($param1 == 'error_con_fail') {
                $page_data['error_con_fail'] = 'Error establishing a database connection using your provided information. Please recheck hostname, username, password and try again with correct information';
            }
            if ($param1 == 'error_nodb') {
                $page_data['error_con_fail'] = 'The database you are trying to use for the application does not exist. Please create the database first';
            }

            // Cấu hình cơ sở dữ liệu
            if ($param1 == 'configure_database') {
                // Lấy thông tin cơ sở dữ liệu từ form
                $hostname = $this->input->post('hostname');
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $dbname   = $this->input->post('dbname');

                // Kiểm tra kết nối cơ sở dữ liệu
                $db_connection = $this->check_database_connection($hostname, $username, $password, $dbname);
                if ($db_connection == 'failed') {
                    redirect(site_url('index.php?install/step3/error_con_fail'), 'refresh');
                } else if ($db_connection == 'db_not_exist') {
                    redirect(site_url('index.php?install/step3/error_nodb'), 'refresh');
                } else {
                    // Tiến hành bước tiếp theo nếu kết nối thành công
                    session_start();
                    $_SESSION['hostname'] = $hostname;
                    $_SESSION['username'] = $username;
                    $_SESSION['password'] = $password;
                    $_SESSION['dbname']   = $dbname;
                    redirect(site_url('index.php?install/step4'), 'refresh');
                }
            }
            $page_data['page_name'] = 'step3'; // Thiết lập tên trang
            $this->load->view('install/index', $page_data); // Hiển thị trang cài đặt bước 3
        }

      // Kiểm tra xác thực mã mua hàng
      function check_purchase_code_verification() {
        if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
            //return 'running_locally'; // Bỏ qua xác thực khi chạy trên localhost
        } else {
            session_start(); // Bắt đầu session
            if (!isset($_SESSION['purchase_code_verified']))
                redirect(site_url('index.php?install/step2'), 'refresh'); // Chuyển hướng nếu mã mua hàng chưa được xác thực
            else if ($_SESSION['purchase_code_verified'] == 0)
                redirect(site_url('index.php?install/step2'), 'refresh'); // Chuyển hướng nếu mã mua hàng không hợp lệ
        }
    }

    // Kiểm tra kết nối cơ sở dữ liệu
    function check_database_connection($hostname, $username, $password, $dbname) {
        $link = mysqli_connect($hostname, $username, $password, $dbname); // Kết nối đến CSDL
        if (!$link) {
            mysqli_close($link); // Đóng kết nối
            return 'failed'; // Trả về thất bại nếu kết nối không thành công
        }
        $db_selected = mysqli_select_db($link, $dbname); // Chọn CSDL
        if (!$db_selected) {
            mysqli_close($link); // Đóng kết nối
            return "db_not_exist"; // Trả về không tồn tại CSDL nếu không thể chọn CSDL
        }
        mysqli_close($link); // Đóng kết nối
        return 'success'; // Trả về thành công nếu kết nối CSDL thành công
    }

    // Bước 4 của quá trình cài đặt
    function step4($param1 = '') {
        if ($this->router->default_controller != 'install') {
            redirect(site_url('index.php?home'), 'refresh'); // Chuyển hướng nếu không phải trang cài đặt
        }
        if ($param1 == 'confirm_install') {
            $this->configure_database(); // Cấu hình CSDL
            $this->run_blank_sql(); // Chạy SQL để tạo cấu trúc CSDL
            redirect(site_url('index.php?install/finalizing_setup'), 'refresh'); // Chuyển hướng đến bước cuối cùng
        }

        $page_data['page_name'] = 'step4'; // Thiết lập tên trang
        $this->load->view('install/index', $page_data); // Hiển thị trang cài đặt bước 4
    }

    // Cấu hình CSDL
    function configure_database() {
        $data_db = file_get_contents('./application/config/database.php'); // Đọc nội dung file cấu hình CSDL
        session_start(); // Bắt đầu session
        // Thay thế thông tin CSDL trong file cấu hình
        $data_db = str_replace('db_name', $_SESSION['dbname'], $data_db);
        $data_db = str_replace('db_user', $_SESSION['username'], $data_db);
        $data_db = str_replace('db_pass', $_SESSION['password'], $data_db);
        $data_db = str_replace('db_host', $_SESSION['hostname'], $data_db);
        file_put_contents('./application/config/database.php', $data_db); // Ghi nội dung đã thay đổi vào file cấu hình
    }

    // Chạy SQL rỗng để tạo cấu trúc CSDL
    function run_blank_sql() {
        $this->load->database(); // Tải CSDL
        $templine = ''; // Tạo biến lưu trữ dòng lệnh tạm thời
        $lines = file('./assets/install.sql'); // Đọc file SQL
        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '--' || $line == '')
                continue; // Bỏ qua nếu là comment hoặc dòng trống
            $templine .= $line; // Thêm dòng vào biến tạm
            if (substr(trim($line), -1, 1) == ';') {
                $this->db->query($templine); // Thực thi câu lệnh SQL
                $templine = ''; // Đặt lại biến tạm
            }
        }
    }

    // Hoàn tất cài đặt
    function finalizing_setup($param1 = '', $param2 = '') {
        if ($this->router->default_controller != 'install') {
            redirect(site_url('index.php?home'), 'refresh'); // Chuyển hướng nếu không phải trang cài đặt
        }
        if ($param1 == 'setup_admin') {
            // Cài đặt tài khoản quản trị
            // [Bỏ qua chi tiết do độ dài]
            redirect(site_url('index.php?install/success'), 'refresh'); // Chuyển hướng đến trang thành công
        }

        $page_data['page_name'] = 'finalizing_setup'; // Thiết lập tên trang
        $this->load->view('install/index', $page_data); // Hiển thị trang hoàn tất cài đặt
    }

    // Trang thành công sau cài đặt
    function success($param1 = '') {
        if ($this->router->default_controller != 'install') {
            redirect(site_url('index.php?home'), 'refresh'); // Chuyển hướng nếu không phải trang cài đặt
        }
        if ($param1 == 'login') {
            $this->configure_routes(); // Cấu hình routes
            redirect(site_url('index.php?home/signin/admin'), 'refresh'); // Chuyển hướng đến trang đăng nhập admin
        }

        $this->load->database(); // Tải CSDL
        $admin_email = $this->db->get_where('user', array('user_id' => 1))->row()->email; // Lấy email của admin

        session_start(); // Bắt đầu session
        if (isset($_SESSION['purchase_code'])) {
            $data['description'] = $_SESSION['purchase_code']; // Lưu mã mua hàng vào CSDL
            $this->db->where('type', 'purchase_code');
            $this->db->update('settings', $data);
        }
        session_destroy(); // Hủy session

        $page_data['admin_email'] = $admin_email; // Thiết lập dữ liệu email admin
        $page_data['page_name'] = 'success'; // Thiết lập tên trang
        $this->load->view('install/index', $page_data); // Hiển thị trang thành công
    }

    // Cấu hình routes sau cài đặt
    function configure_routes() {
        $data_routes = file_get_contents('./application/config/routes.php'); // Đọc file cấu hình routes
        $data_routes = str_replace('install', 'home', $data_routes); // Thay thế controller mặc định từ 'install' sang 'home'
        file_put_contents('./application/config/routes.php', $data_routes); // Ghi lại file cấu hình đã thay đổi
    }
