<?php
// Kiểm tra nếu không được định nghĩa BASEPATH thì thoát để không cho truy cập trực tiếp vào file này.
    if (!defined('BASEPATH'))
        exit('No direct script access allowed');

    // Định nghĩa lớp Updater kế thừa từ CI_Controller
    class Updater extends CI_Controller {

        // Hàm khởi tạo
        function __construct() {
            parent::__construct(); // Gọi hàm khởi tạo của lớp cha
            $this->load->database(); // Kết nối cơ sở dữ liệu
            $this->load->library('session'); // Load thư viện session

            // Kiểm soát bộ nhớ đệm
            $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            $this->output->set_header('Pragma: no-cache');
        }

        // Hàm mặc định, chuyển hướng đến trang đăng nhập nếu không có quản trị viên nào đăng nhập
        public function index() {
            if ($this->session->userdata('login_type') != 1)
                redirect(base_url().'index.php?home/signin', 'refresh'); // Nếu không phải là quản trị viên, chuyển hướng đến trang đăng nhập
            if ($this->session->userdata('login_type') == 1)
                redirect(base_url().'index.php?admin/dashboard', 'refresh'); // Nếu là quản trị viên, chuyển hướng đến trang quản trị
        }

        // Hàm cập nhật sản phẩm
        function update($task = '', $purchase_code = '') {
            if ($this->session->userdata('login_type') != 1)
                redirect(base_url(), 'refresh'); // Chuyển hướng nếu không phải là quản trị viên

            // Tạo thư mục cập nhật nếu chưa tồn tại
            $dir = 'update';
            if (!is_dir($dir))
                mkdir($dir, 0777, true);

            $zipped_file_name = $_FILES["file_name"]["name"]; // Lấy tên file nén
            $path = 'update/' . $zipped_file_name; // Đường dẫn lưu file nén

            move_uploaded_file($_FILES["file_name"]["tmp_name"], $path); // Di chuyển file nén đã tải lên vào thư mục cập nhật

            // Giải nén file cập nhật và xóa file zip
            $zip = new ZipArchive;
            $res = $zip->open($path);
            if ($res === TRUE) {
                $zip->extractTo('update');
                $zip->close();
                unlink($path);
            }

            $unzipped_file_name = substr($zipped_file_name, 0, -4); // Tên file sau khi giải nén
            $str = file_get_contents('./update/' . $unzipped_file_name . '/update_config.json'); // Đọc nội dung file cấu hình cập nhật
            $json = json_decode($str, true); // Chuyển đổi dữ liệu JSON thành mảng

            // Chạy các chỉnh sửa PHP
            require './update/' . $unzipped_file_name . '/update_script.php';

            // Tạo thư mục mới theo cấu hình
            if (!empty($json['directory'])) {
                foreach ($json['directory'] as $directory) {
                    if (!is_dir($directory['name']))
                        mkdir($directory['name'], 0777, true);
                }
            }

            // Tạo/Thay thế các file mới
            if (!empty($json['files'])) {
                foreach ($json['files'] as $file)
                    copy($file['root_directory'], $file['update_directory']);
            }

            $this->session->set_flashdata('flash_message', get_phrase('product_updated_successfully'));
            redirect(site_url('index.php?admin/settings')); // Chuyển hướng đến trang cài đặt sau khi cập nhật
        }
    }
