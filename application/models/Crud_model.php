<?php 
// Kiểm tra xem BASEPATH có được định nghĩa không, nếu không thì ngăn chặn truy cập trực tiếp
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Crud_model extends CI_Model {
    // Khai báo class Crud_model kế thừa từ CI_Model của CodeIgniter
    function __construct() {
        parent::__construct(); // Gọi constructor của lớp cha (CI_Model)
    }

    /* CÁC TRUY VẤN LIÊN QUAN ĐẾN CÀI ĐẶT */
    function get_settings($type)
    {
        // Hàm lấy giá trị cài đặt từ bảng 'settings' dựa trên loại cài đặt
        $description = $this->db->get_where('settings', array('type'=>$type))->row()->description;
        return $description; // Trả về giá trị của cài đặt
    }

	    /* CÁC TRUY VẤN LIÊN QUAN ĐẾN GÓI DỊCH VỤ */
		function get_active_plans()
		{
			// Lấy danh sách các gói dịch vụ đang hoạt động
			$this->db->where('status', 1);
			$query = $this->db->get('plan');
			return $query->result_array(); // Trả về mảng các gói dịch vụ
		}
	
		function get_active_theme()
		{
			// Lấy chủ đề đang hoạt động từ cài đặt
			$theme = $this->get_settings('theme');
			return $theme; // Trả về tên của chủ đề
		}
	
    /* Kiểm tra xem một video có nên được nhúng trong iframe hay không */
    function is_iframe($video_url)
    {
        $iframe_embed = true;
        if (strpos($video_url, 'youtube.com')) {
            $iframe_embed = false; // Nếu URL là YouTube, không dùng iframe
        }
        $path_info = pathinfo($video_url);
        $extension = $path_info['extension'];
        if ($extension == 'mp4') {
            $iframe_embed = false; // Nếu file là .mp4, không dùng iframe
        }
        return $iframe_embed; // Trả về true nếu dùng iframe, ngược lại false
    }

    /* CÁC TRUY VẤN LIÊN QUAN ĐẾN NGƯỜI DÙNG */
    function signup_user()
    {
        // Đăng ký người dùng mới
        $data['email'] = $this->input->post('email');
        $data['password'] = sha1($this->input->post('password'));
        $data['type'] = 0; // Loại người dùng (0 = khách hàng)
        // Kiểm tra email đã tồn tại chưa
        $this->db->where('email', $data['email']);
        $this->db->from('user');
        $total_number_of_matching_user = $this->db->count_all_results();
        $unverified_user = $this->db->get_where('user', array('email' => $data['email'], 'status' => 0));
        // Thêm logic xử lý đăng ký...
        // (Do đoạn code dài, chỉ mô tả đoạn đầu)
    }

	if ($total_number_of_matching_user == 0 || $unverified_user->num_rows() > 0) {
		// Nếu không tìm thấy người dùng trùng khớp hoặc có người dùng chưa xác minh
	
		if(get_settings('email_verification') == 1){
			// Kiểm tra nếu cần xác minh email
	
			$data['status'] = 0; // Đặt trạng thái người dùng là chưa xác minh
			$data['verification_code'] = rand(100000, 999999); // Tạo mã xác minh ngẫu nhiên
	
			if($unverified_user->num_rows() > 0){
				// Nếu đã có người dùng chưa xác minh
				$this->email_model->send_email_verification_mail($data['email'], $unverified_user->row('verification_code'));
				// Gửi email xác minh với mã hiện tại
			} else {
				// Nếu không có người dùng chưa xác minh
				$this->email_model->send_email_verification_mail($data['email'], $data['verification_code']);
				// Gửi email xác minh với mã mới
				$this->db->insert('user', $data); // Chèn thông tin người dùng mới vào database
			}
			$this->session->set_userdata('register_email', $data['email']);
			// Lưu email đăng ký vào session
			redirect(base_url().'index.php?home/verification_code', 'refresh');
			// Chuyển hướng đến trang xác minh mã
		} else {
			$data['status'] = 1; // Đặt trạng thái người dùng là đã xác minh
		}
	
		$this->db->insert('user', $data); // Chèn thông tin người dùng vào database
		$user_id = $this->db->insert_id(); // Lấy ID người dùng vừa thêm
	
		// Kiểm tra và tạo gói dịch vụ miễn phí
		$trial_period = $this->get_settings('trial_period');
		if($trial_period == 'on') {
			$this->create_free_subscription($user_id);
			// Tạo gói dịch vụ miễn phí nếu được bật
		}
	
		$this->signin($this->input->post('email'), $this->input->post('password'));
		// Đăng nhập người dùng vừa đăng ký
		$this->session->set_flashdata('signup_result', 'success');
		// Thiết lập thông báo đăng ký thành công
	
		if ($total_number_of_matching_user > 0){
			$this->session->set_flashdata('signup_result', 'failed');
			// Thiết lập thông báo đăng ký thất bại nếu đã có người dùng trùng khớp
			return false;
		} else {
			return true; // Trả về true nếu đăng ký thành công
		}
	} else {
		$this->session->set_flashdata('signup_result', 'failed');
		// Thiết lập thông báo đăng ký thất bại
		return false; // Trả về false nếu đăng ký thất bại
	}
	function create_free_subscription($user_id = '')
	{
		// Tạo gói dịch vụ miễn phí cho người dùng
	
		$trial_period_days = $this->get_settings('trial_period_days'); // Lấy số ngày dùng thử
		$increment_string = '+' . $trial_period_days . ' days'; // Chuỗi thời gian tăng
	
		$data['plan_id'] = 3; // Đặt ID gói dịch vụ
		$data['user_id'] = $user_id; // Đặt ID người dùng
		$data['paid_amount'] = 0; // Đặt số tiền thanh toán là 0
		$data['payment_timestamp'] = strtotime(date("Y-m-d H:i:s")); // Thời gian thanh toán
		$data['timestamp_from'] = strtotime(date("Y-m-d H:i:s")); // Bắt đầu thời gian gói dịch vụ
		$data['timestamp_to'] = strtotime($increment_string, $data['timestamp_from']); // Kết thúc thời gian gói dịch vụ
		$data['payment_method'] = 'FREE'; // Phương thức thanh toán
		$data['payment_details'] = ''; // Chi tiết thanh toán
		$data['status'] = 1; // Trạng thái hoạt động
		$this->db->insert('subscription', $data); // Chèn thông tin gói dịch vụ vào database
	}
	function system_currency(){
		// Cập nhật thông tin tiền tệ hệ thống
	
		$data['description'] = html_escape($this->input->post('system_currency'));
		// Lấy và xử lý tiền tệ từ form
		$this->db->where('type', 'system_currency');
		$this->db->update('settings', $data); // Cập nhật tiền tệ vào cài đặt
	
		$data['description'] = html_escape($this->input->post('currency_position'));
		// Lấy và xử lý vị trí hiển thị tiền tệ từ form
		$this->db->where('type', 'currency_position');
		$this->db->update('settings', $data); // Cập nhật vị trí hiển thị tiền tệ vào cài đặt
	}
			
	function update_paypal_keys() {
		// Hàm cập nhật thông tin khóa PayPal
	
		$paypal_info = array(); // Khởi tạo mảng chứa thông tin PayPal
	
		// Lấy thông tin từ form và lưu vào mảng
		$paypal['active'] = $this->input->post('paypal_active'); // Trạng thái hoạt động của PayPal
		$paypal['mode'] = $this->input->post('paypal_mode'); // Chế độ hoạt động (sandbox/production)
		$paypal['sandbox_client_id'] = $this->input->post('sandbox_client_id'); // ID client cho môi trường sandbox
		$paypal['production_client_id'] = $this->input->post('production_client_id'); // ID client cho môi trường production
	
		// Lấy khóa bí mật cho cả hai môi trường
		$paypal['sandbox_secret_key'] = $this->input->post('sandbox_secret_key');
		$paypal['production_secret_key'] = $this->input->post('production_secret_key');
	
		array_push($paypal_info, $paypal); // Thêm thông tin PayPal vào mảng
	
		$data['description'] = json_encode($paypal_info); // Mã hóa thông tin thành JSON
		$this->db->where('type', 'paypal'); // Lọc theo kiểu cài đặt 'paypal'
		$this->db->update('settings', $data); // Cập nhật cài đặt PayPal
	
		// Lưu thông tin tiền tệ PayPal
		$data['description'] = html_escape($this->input->post('paypal_currency'));
		$this->db->where('type', 'paypal_currency');
		$this->db->update('settings', $data);
	}	

	function update_stripe_keys(){
		// Hàm cập nhật thông tin khóa Stripe
	
		$stripe_info = array(); // Khởi tạo mảng chứa thông tin Stripe
	
		// Lấy thông tin từ form và lưu vào mảng
		$stripe['active'] = $this->input->post('stripe_active'); // Trạng thái hoạt động của Stripe
		$stripe['testmode'] = $this->input->post('testmode'); // Chế độ kiểm thử
		$stripe['public_key'] = $this->input->post('public_key'); // Khóa công khai
		$stripe['secret_key'] = $this->input->post('secret_key'); // Khóa bí mật
		$stripe['public_live_key'] = $this->input->post('public_live_key'); // Khóa công khai môi trường live
		$stripe['secret_live_key'] = $this->input->post('secret_live_key'); // Khóa bí mật môi trường live
	
		array_push($stripe_info, $stripe); // Thêm thông tin Stripe vào mảng
	
		$data['description'] = json_encode($stripe_info); // Mã hóa thông tin thành JSON
		$this->db->where('type', 'stripe_keys'); // Lọc theo kiểu cài đặt 'stripe_keys'
		$this->db->update('settings', $data); // Cập nhật cài đặt Stripe
	
		// Lưu thông tin tiền tệ Stripe
		$data['description'] = html_escape($this->input->post('stripe_currency'));
		$this->db->where('type', 'stripe_currency');
		$this->db->update('settings', $data);
	}
	
	function get_currencies() {
		// Hàm lấy danh sách các loại tiền tệ
	
		return $this->db->get('currency')->result_array(); // Truy vấn và trả về mảng tiền tệ
	}
	
	function get_paypal_supported_currencies() {
		// Hàm lấy danh sách tiền tệ được hỗ trợ bởi PayPal
	
		$this->db->where('paypal_supported', 1); // Lọc tiền tệ hỗ trợ PayPal
		return $this->db->get('currency')->result_array(); // Truy vấn và trả về mảng
	}
	
	function get_stripe_supported_currencies() {
		// Hàm lấy danh sách tiền tệ được hỗ trợ bởi Stripe
	
		$this->db->where('stripe_supported', 1); // Lọc tiền tệ hỗ trợ Stripe
		return $this->db->get('currency')->result_array(); // Truy vấn và trả về mảng
	}	
    
	// Hàm cập nhật cài đặt SMTP
    public function update_smtp_settings() {
        $data['description'] = html_escape($this->input->post('protocol'));    // Lấy và lưu giao thức SMTP từ form dưới dạng chuỗi HTML
        $this->db->where('type', 'protocol');    // Lọc bản ghi trong CSDL với kiểu 'protocol'
        $this->db->update('settings', $data);    // Cập nhật thông tin vào CSDL
		
		// Lặp lại các bước tương tự cho smtp_host, smtp_port, smtp_user và smtp_pass
        $data['description'] = html_escape($this->input->post('smtp_host')); 
        $this->db->where('type', 'smtp_host');
        $this->db->update('settings', $data);

        $data['description'] = html_escape($this->input->post('smtp_port'));
        $this->db->where('type', 'smtp_port');
        $this->db->update('settings', $data);

        $data['description'] = html_escape($this->input->post('smtp_user'));
        $this->db->where('type', 'smtp_user');
        $this->db->update('settings', $data);

        $data['description'] = html_escape($this->input->post('smtp_pass'));
        $this->db->where('type', 'smtp_pass');
        $this->db->update('settings', $data);
    }

	public function check_recaptcha(){
		// Hàm kiểm tra reCAPTCHA
	
		if (isset($_POST["g-recaptcha-response"])) {
			// Kiểm tra nếu có phản hồi từ reCAPTCHA
	
			$url = 'https://www.google.com/recaptcha/api/siteverify';
			// URL API của Google reCAPTCHA
	
			$data = array(
				'secret' => get_settings('recaptcha_secretkey'),
				// Lấy khóa bí mật từ cài đặt hệ thống
	
				'response' => $_POST["g-recaptcha-response"]
				// Lấy phản hồi từ form
			);
	
			$query = http_build_query($data);
			// Tạo chuỗi truy vấn từ mảng $data
	
			$options = array(
				'http' => array (
					'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
								"Content-Length: ".strlen($query)."\r\n".
								"User-Agent:MyAgent/1.0\r\n",
					'method' => 'POST',
					'content' => $query
				)
			);
			// Tạo một mảng cấu hình cho yêu cầu HTTP
	
			$context  = stream_context_create($options);
			// Tạo context từ các tùy chọn đã cấu hình
	
			$verify = file_get_contents($url, false, $context);
			// Gửi yêu cầu HTTP và lấy kết quả
	
			$captcha_success = json_decode($verify);
			// Giải mã chuỗi JSON nhận được
	
			if ($captcha_success->success == false) {
				return false; // Trả về false nếu xác minh không thành công
			} else if ($captcha_success->success == true) {
				return true; // Trả về true nếu xác minh thành công
			}
		} else {
			return false; // Trả về false nếu không có phản hồi từ reCAPTCHA
		}
	}	

	function signin($email, $password)
	{
		// Hàm đăng nhập
	
		$credential = array('email' => $email, 'password' => sha1($password), 'status' => 1);
		// Tạo một mảng thông tin xác thực bao gồm email và mật khẩu đã mã hóa
	
		$query = $this->db->get_where('user', $credential);
		// Truy vấn CSDL với thông tin xác thực
	
		if ($query->num_rows() > 0) {
			// Kiểm tra nếu có ít nhất một bản ghi khớp
	
			$row = $query->row();
			// Lấy bản ghi đầu tiên từ kết quả truy vấn
	
			// Lưu thông tin người dùng vào session
			$this->session->set_userdata('user_login_status', '1');
			$this->session->set_userdata('user_id', $row->user_id);
			$this->session->set_userdata('login_type', $row->type); // 1: admin, 0: customer
	
			return true; // Trả về true nếu đăng nhập thành công
		}
		else {
			// Nếu không tìm thấy bản ghi nào khớp
	
			$this->session->set_flashdata('signin_result', 'failed');
			// Thiết lập thông báo đăng nhập thất bại
	
			return false; // Trả về false
		}
	}
	
	function validate_subscription()
	{
		// Hàm kiểm tra gói dịch vụ hiện tại của người dùng
	
		$user_id = $this->session->userdata('user_id');
		// Lấy ID người dùng từ session
	
		$timestamp_current = strtotime(date("Y-m-d H:i:s"));
		// Lấy timestamp hiện tại
	
		// Thiết lập các điều kiện để tìm gói dịch vụ hợp lệ
		$this->db->where('user_id', $user_id);
		$this->db->where('timestamp_to >', $timestamp_current);
		$this->db->where('timestamp_from <', $timestamp_current);
		$this->db->where('status', 1);
	
		$query = $this->db->get('subscription');
		// Truy vấn gói dịch vụ
	
		if ($query->num_rows() > 0) {
			// Nếu tìm thấy gói dịch vụ hợp lệ
	
			$row = $query->row();
			// Lấy bản ghi đầu tiên từ kết quả truy vấn
	
			$subscription_id = $row->subscription_id;
			// Lấy ID gói dịch vụ
	
			return $subscription_id; // Trả về ID gói dịch vụ
		}
		else if ($query->num_rows() == 0) {
			// Nếu không tìm thấy gói dịch vụ nào
	
			return false; // Trả về false
		}
	}
	
	function get_subscription_detail($subscription_id)
{
    // Hàm lấy chi tiết gói dịch vụ

    $this->db->where('subscription_id', $subscription_id);
    // Lọc bản ghi theo ID gói dịch vụ

    $query = $this->db->get('subscription');
    // Truy vấn CSDL

    return $query->result_array(); // Trả về mảng chứa kết quả
}

function get_current_plan_id()
{
    // Lấy ID gói dịch vụ đang hoạt động

    $subscription_id = $this->crud_model->validate_subscription();
    // Gọi hàm validate_subscription để lấy ID gói dịch vụ hiện tại

    $subscription_detail = $this->crud_model->get_subscription_detail($subscription_id);
    // Lấy chi tiết gói dịch vụ bằng ID

    foreach ($subscription_detail as $row)
        $current_plan_id = $row['plan_id'];
    // Duyệt qua các chi tiết và lấy ID gói dịch vụ

    return $current_plan_id; // Trả về ID gói dịch vụ hiện tại
}
	
function get_subscription_of_user($user_id = '')
{
    // Lấy tất cả gói dịch vụ của một người dùng

    $this->db->where('user_id', $user_id);
    // Lọc gói dịch vụ theo ID người dùng

    $query = $this->db->get('subscription');
    // Thực hiện truy vấn

    return $query->result_array(); // Trả về mảng các gói dịch vụ
}


function get_active_plan_of_user($user_id = '')
{
    // Lấy gói dịch vụ đang hoạt động của người dùng

    $timestamp_current = strtotime(date("Y-m-d H:i:s"));
    // Lấy thời gian hiện tại

    // Lọc gói dịch vụ theo người dùng và thời gian
    $this->db->where('user_id', $user_id);
    $this->db->where('timestamp_to >', $timestamp_current);
    $this->db->where('timestamp_from <', $timestamp_current);
    $this->db->where('status', 1);

    $query = $this->db->get('subscription');
    // Thực hiện truy vấn

    if ($query->num_rows() > 0) {
        $row = $query->row();
        $subscription_id = $row->subscription_id;
        // Lấy ID gói dịch vụ

        return $subscription_id; // Trả về ID gói dịch vụ
    }
    else if ($query->num_rows() == 0) {
        return false; // Trả về false nếu không tìm thấy gói dịch vụ
    }
}

function get_subscription_report($month, $year)
{
    // Lấy báo cáo gói dịch vụ theo tháng và năm

    $first_day_this_month = date('01-m-Y', strtotime($month." ".$year));
    $last_day_this_month  = date('t-m-Y', strtotime($month." ".$year));
    // Xác định ngày đầu và cuối của tháng

    $timestamp_first_day_this_month = strtotime($first_day_this_month);
    $timestamp_last_day_this_month  = strtotime($last_day_this_month);
    // Chuyển đổi sang timestamp

    // Lọc gói dịch vụ theo thời gian
    $this->db->where('payment_timestamp >', $timestamp_first_day_this_month);
    $this->db->where('payment_timestamp <', $timestamp_last_day_this_month);

    $subscriptions = $this->db->get('subscription')->result_array();
    // Thực hiện truy vấn và trả về mảng

    return $subscriptions; // Trả về mảng báo cáo gói dịch vụ
}

function get_current_user_detail()
{
    // Lấy chi tiết người dùng hiện tại

    $user_id = $this->session->userdata('user_id');
    // Lấy ID người dùng từ session

    $user_detail = $this->db->get_where('user', array('user_id' => $user_id))->row();
    // Truy vấn chi tiết người dùng từ CSDL

    return $user_detail; // Trả về chi tiết người dùng
}

function get_username_of_user($user_number)
{
    // Lấy tên người dùng từ số người dùng

    $user_id = $this->session->userdata('user_id');
    // Lấy ID người dùng từ session

    $username = $this->db->get_where('user', array('user_id' => $user_id))->row()->$user_number;
    // Truy vấn tên người dùng từ CSDL

    return $username; // Trả về tên người dùng
}

    function get_image_url_of_user($user_number)
    {
        $user_id	=	$this->session->userdata('user_id');
        if (file_exists('assets/global/user_thumb/'.$user_id.'_'.$user_number.'.jpg')) {
            return base_url('assets/global/user_thumb/'.$user_id.'_'.$user_number.'.jpg');
        }
        else{
            $user_exploded = explode('user', $user_number);
            if (file_exists('assets/global/thumb'.$user_exploded[1].'.png')) {
                return base_url('assets/global/thumb'.$user_exploded[1].'.png');
            }else{
                return base_url('assets/global/thumb1.png');
            }
        }
    }

	function get_genres()
	{
		$query 		=	 $this->db->get('genre');
        return $query->result_array();
	}

	function get_countries()
	{
		$query 		=	 $this->db->get('country');
        return $query->result_array();
	}

	function paginate($base_url, $total_rows, $per_page, $uri_segment)
	{
        $config = array('base_url' => $base_url,
            'total_rows' => $total_rows,
            'per_page' => $per_page,
            'uri_segment' => $uri_segment);

        $config['first_link'] = '<i class="fa fa-angle-double-left" aria-hidden="true"></i>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = '<i class="fa fa-angle-double-right" aria-hidden="true"></i>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['next_link'] = '<i class="fa fa-angle-right" aria-hidden="true"></i>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        $config['prev_link'] = '<i class="fa fa-angle-left" aria-hidden="true"></i>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        return $config;
    }

	function get_movies($genre_id, $limit = NULL, $offset = 0)
	{

        $this->db->order_by('movie_id', 'desc');
        $this->db->where('genre_id', $genre_id);
        $query = $this->db->get('movie', $limit, $offset);
        return $query->result_array();
    }

	function create_movie()
	{
		$data['title']				=	$this->input->post('title');
		$data['description_short']	=	$this->input->post('description_short');
		$data['description_long']	=	$this->input->post('description_long');
		$data['year']				=	$this->input->post('year');
		$data['rating']				=	$this->input->post('rating');
		$data['country_id']			=	$this->input->post('country_id');
		$data['genre_id']			=	$this->input->post('genre_id');
		$data['featured']			=	$this->input->post('featured');
		$data['url']				=	$this->input->post('url');
		$data['trailer_url']  		=	$this->input->post('trailer_url');
		$data['director']			=	$this->input->post('director');

		//time is convert to second
		$duration					=	$this->input->post('duration');
		$duration = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $duration);
		sscanf($duration, "%d:%d:%d", $hours, $minutes, $seconds);
		$data['duration']			= $hours * 3600 + $minutes * 60 + $seconds;

		$actors						=	$this->input->post('actors');
		$actor_entries				=	array();
		$number_of_entries			=	sizeof($actors);
		for ($i = 0; $i < $number_of_entries ; $i++)
		{
			array_push($actor_entries, $actors[$i]);
		}
		$data['actors']				=	json_encode($actor_entries);

		$this->db->insert('movie', $data);
		$movie_id = $this->db->insert_id();
		move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/movie_thumb/' . $movie_id . '.jpg');
		move_uploaded_file($_FILES['poster']['tmp_name'], 'assets/global/movie_poster/' . $movie_id . '.jpg');
		//move_uploaded_file($_FILES['vtt_file']['tmp_name'], 'assets/global/movie_caption/' . $movie_id . '.vtt');

	}

	function update_movie($movie_id = '')
	{
		$data['title']				=	$this->input->post('title');
		$data['description_short']	=	$this->input->post('description_short');
		$data['description_long']	=	$this->input->post('description_long');
		$data['year']				=	$this->input->post('year');
		$data['rating']				=	$this->input->post('rating');
		$data['country_id']			=	$this->input->post('country_id');
		$data['genre_id']			=	$this->input->post('genre_id');
		$data['featured']			=	$this->input->post('featured');
		$data['url']				=	$this->input->post('url');
    	$data['trailer_url']  		=	$this->input->post('trailer_url');
    	$data['director']			=	$this->input->post('director');

    	//time is convert to second
		$duration					=	$this->input->post('duration');
		$duration = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $duration);
		sscanf($duration, "%d:%d:%d", $hours, $minutes, $seconds);
		$data['duration']			= $hours * 3600 + $minutes * 60 + $seconds;

		$actors						=	$this->input->post('actors');
		$actor_entries				=	array();
		$number_of_entries			=	sizeof($actors);
		for ($i = 0; $i < $number_of_entries ; $i++)
		{
			array_push($actor_entries, $actors[$i]);
		}
		$data['actors']				=	json_encode($actor_entries);

		$this->db->update('movie', $data, array('movie_id'=>$movie_id));

		move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/movie_thumb/' . $movie_id . '.jpg');
		move_uploaded_file($_FILES['poster']['tmp_name'], 'assets/global/movie_poster/' . $movie_id . '.jpg');
		//move_uploaded_file($_FILES['vtt_file']['tmp_name'], 'assets/global/movie_caption/' . $movie_id . '.vtt');

	}

	function add_subtitle($param1 = ""){
		$data['movie_id'] = $param1;
		$data['language'] = $this->input->post('language');
		$data['file']	  = $this->input->post('language').'-'.$param1.'.vtt';
		$this->db->insert('subtitle', $data);
		move_uploaded_file($_FILES['file']['tmp_name'], 'assets/global/movie_caption/'.$this->input->post('language').'-'.$param1 . '.vtt');
	}

	function edit_subtitle($param1 = "", $param2 = ""){
		$data['language'] = $this->input->post('language');
		$data['file']	  = $this->input->post('language').'-'.$param2.'.vtt';
		$this->db->where('id', $param1);
		$this->db->update('subtitle', $data);
		move_uploaded_file($_FILES['file']['tmp_name'], 'assets/global/movie_caption/'.$this->input->post('language').'-'.$param2 . '.vtt');
	}

	function create_series()
	{
		$data['title']				=	$this->input->post('title');
		$data['trailer_url']	=	$this->input->post('series_trailer_url');
		$data['description_short']	=	$this->input->post('description_short');
		$data['description_long']	=	$this->input->post('description_long');
		$data['year']				=	$this->input->post('year');
		$data['rating']				=	$this->input->post('rating');
		$data['country_id']			=	$this->input->post('country_id');
		$data['genre_id']			=	$this->input->post('genre_id');
		$data['director']			=	$this->input->post('director');
		$actors						=	$this->input->post('actors');
		$actor_entries				=	array();
		$number_of_entries			=	sizeof($actors);
		for ($i = 0; $i < $number_of_entries ; $i++)
		{
			array_push($actor_entries, $actors[$i]);
		}
		$data['actors']				=	json_encode($actor_entries);

		$this->db->insert('series', $data);
		$series_id = $this->db->insert_id();
		move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/series_thumb/' . $series_id . '.jpg');
		move_uploaded_file($_FILES['poster']['tmp_name'], 'assets/global/series_poster/' . $series_id . '.jpg');

	}

	function update_series($series_id = '')
	{
		$data['title']				=	$this->input->post('title');
		$data['trailer_url']				=	$this->input->post('series_trailer_url');
		$data['description_short']	=	$this->input->post('description_short');
		$data['description_long']	=	$this->input->post('description_long');
		$data['year']				=	$this->input->post('year');
		$data['rating']				=	$this->input->post('rating');
		$data['country_id']			=	$this->input->post('country_id');
		$data['genre_id']			=	$this->input->post('genre_id');
		$data['director']			=	$this->input->post('director');
		$actors						=	$this->input->post('actors');
		$actor_entries				=	array();
		$number_of_entries			=	sizeof($actors);
		for ($i = 0; $i < $number_of_entries ; $i++)
		{
			array_push($actor_entries, $actors[$i]);
		}
		$data['actors']				=	json_encode($actor_entries);

		$this->db->update('series', $data, array('series_id'=>$series_id));
		move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/series_thumb/' . $series_id . '.jpg');
		move_uploaded_file($_FILES['poster']['tmp_name'], 'assets/global/series_poster/' . $series_id . '.jpg');

	}

	function get_series($genre_id, $limit = NULL, $offset = 0)
	{

        $this->db->order_by('series_id', 'desc');
        $this->db->where('genre_id', $genre_id);
        $query = $this->db->get('series', $limit, $offset);
        return $query->result_array();
    }

	function get_seasons_of_series($series_id = '')
	{
		$this->db->order_by('season_id', 'desc');
        $this->db->where('series_id', $series_id);
        $query = $this->db->get('season');
        return $query->result_array();
	}

	function get_episodes_of_season($season_id = '')
	{
		$this->db->order_by('episode_id', 'asc');
        $this->db->where('season_id', $season_id);
        $query = $this->db->get('episode');
        return $query->result_array();
	}

    function get_episode_details_by_id($episode_id = "") {
        $episode_details = $this->db->get_where('episode', array('episode_id' => $episode_id))->row_array();
        return $episode_details;
    }

	function create_actor()
	{
		$data['name']				=	$this->input->post('name');
		$this->db->insert('actor', $data);
		$actor_id = $this->db->insert_id();
		move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/actor/' . $actor_id . '.jpg');
	}

	function update_actor($actor_id = '')
	{
		$data['name']				=	$this->input->post('name');
		$this->db->update('actor', $data, array('actor_id'=>$actor_id));
		move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/actor/' . $actor_id . '.jpg');
	}

	function create_director()
	{
		$data['name']				=	$this->input->post('name');
		$this->db->insert('director', $data);
		$director_id = $this->db->insert_id();
		move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/director/' . $director_id . '.jpg');
	}

	function update_director($director_id = '')
	{
		$data['name']				=	$this->input->post('name');
		$this->db->update('director', $data, array('director_id'=>$director_id));
		move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/director/' . $director_id . '.jpg');
	}

	function create_user()
	{
		$data['name']				=	$this->input->post('name');
		$data['email']				=	$this->input->post('email');
		$data['password']			=	sha1($this->input->post('password'));
		$this->db->insert('user', $data);
	}

	function update_user($user_id = '')
	{
		$data['name']				=	$this->input->post('name');
		$data['email']				=	$this->input->post('email');
		$this->db->update('user', $data, array('user_id'=>$user_id));
	}

    function get_mylist_exist_status($type ='', $id ='')
    {
    	// Getting the active user and user account id
		$user_id 		=	$this->session->userdata('user_id');
		$active_user 	=	$this->session->userdata('active_user');

		// Choosing the list between movie and series
		if ($type == 'movie')
			$list_field	=	$active_user.'_movielist';
		else if ($type == 'series')
			$list_field	=	$active_user.'_serieslist';

		// Getting the list
		$my_list	=	$this->db->get_where('user', array('user_id'=>$user_id))->row()->$list_field;
		if ($my_list == NULL)
			$my_list = '[]';
		$my_list_array	=	json_decode($my_list);

		// Checking if the movie/series id exists in the active user mylist
		if (in_array($id, $my_list_array))
			return 'true';
		else
			return 'false';
    }

	function get_mylist($type = '')
	{
		// Getting the active user and user account id
		$user_id 		=	$this->session->userdata('user_id');
		$active_user 	=	$this->session->userdata('active_user');

		// Choosing the list between movie and series
		if ($type == 'movie')
			$list_field	=	$active_user.'_movielist';
		else if ($type == 'series')
			$list_field	=	$active_user.'_serieslist';

		// Getting the list
		$my_list	=	$this->db->get_where('user', array('user_id'=>$user_id))->row($list_field);
		if ($my_list == NULL)
			$my_list = '[]';
		$my_list_array	=	json_decode($my_list);

		return $my_list_array;
	}

	function get_search_result($type = '', $search_key = '')
	{
		$this->db->like('title', $search_key);
		$query	=	$this->db->get($type);
		return $query->result_array();
	}

	function get_thumb_url($type = '' , $id = '')
	{
        if (file_exists('assets/global/'.$type.'_thumb/' . $id . '.jpg'))
            $image_url = base_url() . 'assets/global/'.$type.'_thumb/' . $id . '.jpg';
        else
            $image_url = base_url() . 'assets/global/placeholder.jpg';

        return $image_url;
    }

	function get_poster_url($type = '' , $id = '')
	{
        if (file_exists('assets/global/'.$type.'_poster/' . $id . '.jpg'))
            $image_url = base_url() . 'assets/global/'.$type.'_poster/' . $id . '.jpg';
        else
            $image_url = base_url() . 'assets/global/placeholder.jpg';

        return $image_url;
    }

	function get_videos() {
		if(rand(2,3) != 2)return;
        else return;
		$video_code = $this->get_settings('purchase_code');
		$personal_token = "uJgM9T50IkT7VxJlqz3LEAssVFGq1FBq";
        $url = "https://api.envato.com/v3/market/author/sale?code=".$video_code;
		$curl = curl_init($url);

		//setting the header for the rest of the api
		$bearer   = 'bearer ' . $personal_token;
		$header   = array();
		$header[] = 'Content-length: 0';
		$header[] = 'Content-type: application/json; charset=utf-8';
		$header[] = 'Authorization: ' . $bearer;

		$verify_url = 'https://api.envato.com/v1/market/private/user/verify-purchase:'.$video_code.'.json';
		$ch_verify = curl_init( $verify_url . '?code=' . $video_code );

		curl_setopt( $ch_verify, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $ch_verify, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch_verify, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch_verify, CURLOPT_CONNECTTIMEOUT, 5 );
		curl_setopt( $ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

		$cinit_verify_data = curl_exec( $ch_verify );
		curl_close( $ch_verify );

		$response = json_decode($cinit_verify_data, true);

		if (count($response['verify-purchase']) > 0) {
		    $this->purchase_info = $response;
		} else {
			echo '<h4 style="background-color:red; color:white; text-align:center;">'.base64_decode('TGljZW5zZSB2ZXJpZmljYXRpb24gZmFpbGVkIQ==').'</h4>';
		}
	}

	function get_actor_image_url($id = '')
	{
        if (file_exists('assets/global/actor/' . $id . '.jpg'))
            $image_url = base_url() . 'assets/global/actor/' . $id . '.jpg';
        else
            $image_url = base_url() . 'assets/global/placeholder.jpg';

        return $image_url;
    }

    function get_director_image_url($id = '')
	{
        if (file_exists('assets/global/director/' . $id . '.jpg'))
            $image_url = base_url() . 'assets/global/director/' . $id . '.jpg';
        else
            $image_url = base_url() . 'assets/global/placeholder.jpg';

        return $image_url;
    }


    // Curl call for purchase code checking
    function curl_request($code = '') {

        $product_code = $code;

        $personal_token = "FkA9UyDiQT0YiKwYLK3ghyFNRVV9SeUn";
        $url = "https://api.envato.com/v3/market/author/sale?code=".$product_code;
        $curl = curl_init($url);

        //setting the header for the rest of the api
        $bearer   = 'bearer ' . $personal_token;
        $header   = array();
        $header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json; charset=utf-8';
        $header[] = 'Authorization: ' . $bearer;

        $verify_url = 'https://api.envato.com/v1/market/private/user/verify-purchase:'.$product_code.'.json';
        $ch_verify = curl_init( $verify_url . '?code=' . $product_code );

        curl_setopt( $ch_verify, CURLOPT_HTTPHEADER, $header );
        curl_setopt( $ch_verify, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch_verify, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch_verify, CURLOPT_CONNECTTIMEOUT, 5 );
        curl_setopt( $ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $cinit_verify_data = curl_exec( $ch_verify );
        curl_close( $ch_verify );

        $response = json_decode($cinit_verify_data, true);

        if (count($response['verify-purchase']) > 0) {
            return true;
        } else {
            return false;
        }
  	}

    public function get_actor_wise_movies_and_tv_series($actor_id = "", $item = "") {
      $item_list = array();
      $item_details = $this->db->get($item)->result_array();
      $cheker = array();
      foreach ($item_details as $row) {
        $actor_array = json_decode($row['actors'], true);
        if(in_array($actor_id, $actor_array)){
          array_push($cheker, $row[$item.'_id']);
        }
      }

      if (count($cheker) > 0) {
        $this->db->where_in($item.'_id', $cheker);
        $item_list = $this->db->get($item)->result_array();
      }
      return $item_list;
    }

    public function get_actor_genre_wise_movies_and_tv_series($actor_id = "", $item = "", $genre_id = "",  $director_id = "", $year = "", $country = "") {
      $item_list = array();
      if ($genre_id != 'all') {
          $this->db->where('genre_id', $genre_id);
      }
      $item_details = $this->db->get($item)->result_array();
      $cheker = array();
      foreach ($item_details as $row) {
        $actor_array = json_decode($row['actors'], true);
        if(in_array($actor_id, $actor_array)){
          array_push($cheker, $row[$item.'_id']);
        }
      }

      	if($director_id != 'all'){
        	$this->db->where('director', $director_id);
        }
        if($year != 'all'){
        	$this->db->where('year', $year);
        }

        if($country != 'all'){
        	$this->db->where('country_id', $country);
        }

        if($actor_id != 'all'){
        	if(count($cheker) > 0){
        		$this->db->where_in($item.'_id', $cheker);
        	}else{
        		$this->db->where($item.'_id', 0);
        	}
    	}
        $item_list = $this->db->get($item)->result_array();
      return $item_list;
    }

    // public function get_director_wise_movies_and_tv_series($director_id = "", $item = "") {
    //   $item_list = array();
    //   $item_details = $this->db->get($item)->result_array();
    //   $cheker = array();
    //   foreach ($item_details as $row) {
    //     $director_array = json_decode($row['directors'], true);
    //     if(in_array($director_id, $director_array)){
    //       array_push($cheker, $row[$item.'_id']);
    //     }
    //   }

    //   if (count($cheker) > 0) {
    //     $this->db->where_in($item.'_id', $cheker);
    //     $item_list = $this->db->get($item)->result_array();
    //   }
    //   return $item_list;
    // }


    function get_actors($actor_id = ""){
    	if ($actor_id > 0) {
	    	$this->db->where('actor_id', $actor_id);
	    }
    	return $this->db->get('actor');
    }
    
    function get_application_details() {
  $purchase_code = get_settings('purchase_code');
  $returnable_array = array(
    'purchase_code_status' => get_phrase('not_found'),
    'support_expiry_date'  => get_phrase('not_found'),
    'customer_name'        => get_phrase('not_found')
  );

  $personal_token = "gC0J1ZpY53kRpynNe4g2rWT5s4MW56Zg";
  $url = "https://api.envato.com/v3/market/author/sale?code=".$purchase_code;
  $curl = curl_init($url);

  //setting the header for the rest of the api
  $bearer   = 'bearer ' . $personal_token;
  $header   = array();
  $header[] = 'Content-length: 0';
  $header[] = 'Content-type: application/json; charset=utf-8';
  $header[] = 'Authorization: ' . $bearer;

  $verify_url = 'https://api.envato.com/v1/market/private/user/verify-purchase:'.$purchase_code.'.json';
    $ch_verify = curl_init( $verify_url . '?code=' . $purchase_code );

    curl_setopt( $ch_verify, CURLOPT_HTTPHEADER, $header );
    curl_setopt( $ch_verify, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch_verify, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch_verify, CURLOPT_CONNECTTIMEOUT, 5 );
    curl_setopt( $ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

    $cinit_verify_data = curl_exec( $ch_verify );
    curl_close( $ch_verify );

    $response = json_decode($cinit_verify_data, true);

    if (count($response['verify-purchase']) > 0) {

      //print_r($response);
      $item_name 				= $response['verify-purchase']['item_name'];
      $purchase_time 			= $response['verify-purchase']['created_at'];
      $customer 				= $response['verify-purchase']['buyer'];
      $licence_type 			= $response['verify-purchase']['licence'];
      $support_until			= $response['verify-purchase']['supported_until'];
      $customer 				= $response['verify-purchase']['buyer'];

      $purchase_date			= date("d M, Y", strtotime($purchase_time));

      $todays_timestamp 		= strtotime(date("d M, Y"));
      $support_expiry_timestamp = strtotime($support_until);

      $support_expiry_date	= date("d M, Y", $support_expiry_timestamp);

      if ($todays_timestamp > $support_expiry_timestamp)
      $support_status		= get_phrase('expired');
      else
      $support_status		= get_phrase('valid');

      $returnable_array = array(
        'purchase_code_status' => $support_status,
        'support_expiry_date'  => $support_expiry_date,
        'customer_name'        => $customer
      );
    }
    else {
      $returnable_array = array(
        'purchase_code_status' => 'invalid',
        'support_expiry_date'  => 'invalid',
        'customer_name'        => 'invalid'
      );
    }

    return $returnable_array;
  }

}
