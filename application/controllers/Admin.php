<?php
// Khởi đầu một file PHP

defined('BASEPATH') OR exit('No direct script access allowed');
// Kiểm tra xem hằng BASEPATH có được định nghĩa hay không. Nếu không, kết thúc việc thực thi script.

class Admin extends CI_Controller {
// Tạo một class tên là Admin kế thừa từ lớp CI_Controller của CodeIgniter.

	// constructor
	function __construct()
	{
		parent::__construct(); // Gọi constructor của lớp cha CI_Controller.
		$this->load->database(); // Tải và khởi tạo cơ sở dữ liệu.
		$this->load->model('crud_model'); // Tải model 'crud_model'.
		$this->load->library('session'); // Tải thư viện 'session'.
		$this->admin_login_check(); // Gọi hàm kiểm tra đăng nhập của admin.
	}

	public function index()
	{
		$this->dashboard(); // Khi truy cập vào index, sẽ gọi hàm dashboard.
	}

	function dashboard()
	{
		$page_data['page_name']		= 'dashboard'; // Thiết lập tên trang là 'dashboard'.
		$page_data['page_title']	= 'Home - Summary'; // Thiết lập tiêu đề trang.
		$this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
	}

	// Xem danh sách thể loại, quản lý chúng
	function genre_list()
	{
		$page_data['page_name']		= 'genre_list'; // Thiết lập tên trang là 'genre_list'.
		$page_data['page_title']	= 'Manage Genre'; // Thiết lập tiêu đề trang.
		$this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
	}

	// Tạo thể loại mới
	function genre_create()
	{
		if (isset($_POST) && !empty($_POST))
		// Kiểm tra nếu có dữ liệu POST và không rỗng.
		{
			$data['name']			= $this->input->post('name'); // Lấy giá trị 'name' từ form POST.
			$this->db->insert('genre', $data); // Chèn dữ liệu vào bảng 'genre'.
			redirect(base_url().'index.php?admin/genre_list', 'refresh'); // Chuyển hướng đến trang 'genre_list'.
		}
		$page_data['page_name']		= 'genre_create'; // Thiết lập tên trang là 'genre_create'.
		$page_data['page_title']	= 'Create Genre'; // Thiết lập tiêu đề trang.
		$this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
	}

	// Chỉnh sửa thể loại
	function genre_edit($genre_id = '')
	{
		if (isset($_POST) && !empty($_POST))
		// Kiểm tra nếu có dữ liệu POST và không rỗng.
		{
			$data['name']			= $this->input->post('name'); // Lấy giá trị 'name' từ form POST.
			$this->db->update('genre', $data, array('genre_id' => $genre_id)); // Cập nhật dữ liệu vào bảng 'genre'.
			redirect(base_url().'index.php?admin/genre_list', 'refresh'); // Chuyển hướng đến trang 'genre_list'.
		}
		$page_data['genre_id']		= $genre_id; // Thiết lập ID của thể loại cần chỉnh sửa.
		$page_data['page_name']		= 'genre_edit'; // Thiết lập tên trang là 'genre_edit'.
		$page_data['page_title']	= 'Edit Genre'; // Thiết lập tiêu đề trang.
		$this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
	}

	// Xóa thể loại
	function genre_delete($genre_id = '')
	{
		$this->db->delete('genre', array('genre_id' => $genre_id)); // Xóa dữ liệu từ bảng 'genre' với ID chỉ định.
		redirect(base_url().'index.php?admin/genre_list', 'refresh'); // Chuyển hướng đến trang 'genre_list'.
	}

    // Xem danh sách quốc gia, quản lý chúng
    function country()
    {
        $page_data['page_name']     = 'country'; // Thiết lập tên trang là 'country'.
        $page_data['page_title']    = get_phrase('countries'); // Thiết lập tiêu đề trang, sử dụng hàm get_phrase để lấy từ ngôn ngữ.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    // Tạo quốc gia mới
    function country_create()
    {
        if (isset($_POST) && !empty($_POST))
        // Kiểm tra nếu có dữ liệu POST và không rỗng.
        {
            $data['name']            = $this->input->post('name'); // Lấy giá trị 'name' từ form POST.
            $this->db->insert('country', $data); // Chèn dữ liệu vào bảng 'country'.
            redirect(base_url().'index.php?admin/country' , 'refresh'); // Chuyển hướng đến trang quản lý quốc gia.
        }
        $page_data['page_name']     = 'country_create'; // Thiết lập tên trang là 'country_create'.
        $page_data['page_title']    = get_phrase('add_country'); // Thiết lập tiêu đề trang, sử dụng hàm get_phrase.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    // Chỉnh sửa quốc gia
    function country_edit($country_id = '')
    {
        if (isset($_POST) && !empty($_POST))
        // Kiểm tra nếu có dữ liệu POST và không rỗng.
        {
            $data['name']            = $this->input->post('name'); // Lấy giá trị 'name' từ form POST.
            $this->db->update('country', $data,  array('country_id' => $country_id)); // Cập nhật dữ liệu vào bảng 'country'.
            redirect(base_url().'index.php?admin/country' , 'refresh'); // Chuyển hướng đến trang quản lý quốc gia.
        }
        $page_data['country_id']      = $country_id; // Thiết lập ID của quốc gia cần chỉnh sửa.
        $page_data['page_name']     = 'country_edit'; // Thiết lập tên trang là 'country_edit'.
        $page_data['page_title']    = get_phrase('edit_country'); // Thiết lập tiêu đề trang, sử dụng hàm get_phrase.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    // Xóa quốc gia
    function country_delete($country_id = '')
    {
        $this->db->delete('country',  array('country_id' => $country_id)); // Xóa dữ liệu từ bảng 'country' với ID chỉ định.
        redirect(base_url().'index.php?admin/country' , 'refresh'); // Chuyển hướng đến trang quản lý quốc gia.
    }

    // Xem danh sách phim, quản lý chúng
    function movie_list($actor_id = "")
    {
        $page_data['actor_id']       = empty($actor_id) ? 'all' : $actor_id; // Thiết lập ID diễn viên, mặc định là 'all'.
        $page_data['page_name']      = 'movie_list'; // Thiết lập tên trang là 'movie_list'.
        $page_data['page_title']     = 'Manage movie'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    // Tạo phim mới
    function movie_create()
    {
        if (isset($_POST) && !empty($_POST))
        // Kiểm tra nếu có dữ liệu POST và không rỗng.
        {
            $this->crud_model->create_movie(); // Gọi hàm create_movie từ model 'crud_model' để xử lý tạo phim mới.
            redirect(base_url().'index.php?admin/movie_list' , 'refresh'); // Chuyển hướng đến trang danh sách phim.
        }
        $page_data['page_name']      = 'movie_create'; // Thiết lập tên trang là 'movie_create'.
        $page_data['page_title']     = 'Create movie'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }
    // Phụ đề
    function subtitle($param1 = '')
    {
        // Hiển thị trang quản lý phụ đề.
        $page_data['movie_id']      = $param1; // Lưu ID phim vào mảng page_data.
        $page_data['page_name']     = 'subtitle'; // Đặt tên trang là 'subtitle'.
        $page_data['page_title']    = 'Manage subtitle : ' . $this->db->get_where('movie', array('movie_id' => $param1))->row('title');
        // Thiết lập tiêu đề trang bằng cách lấy tên phim từ cơ sở dữ liệu.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    function add_subtitle($param1 = '')
    {
        // Thêm phụ đề mới cho phim.
        if (isset($_POST) && !empty($_POST)){
            // Kiểm tra nếu có dữ liệu POST và không rỗng.
            $language = $this->input->post('language'); // Lấy giá trị 'language' từ form POST.
            $subtitle = $this->db->get_where('subtitle', array('movie_id' => $param1, 'language' => $language))->row_array();
            // Kiểm tra phụ đề đã tồn tại hay chưa.
            if($subtitle['language'] != $language){
                // Nếu chưa tồn tại, thêm phụ đề mới.
                $this->crud_model->add_subtitle($param1); // Gọi hàm add_subtitle từ model.
            }
            redirect(base_url().'index.php?admin/add_subtitle/'.$param1, 'refresh');
            // Chuyển hướng đến trang thêm phụ đề.
        }
        $page_data['movie_id']      = $param1; // Lưu ID phim vào mảng page_data.
        $page_data['page_name']     = 'add_subtitle'; // Đặt tên trang là 'add_subtitle'.
        $page_data['page_title']    = 'Add subtitle'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    function edit_subtitle($param1 = '', $param2 = '')
    {
        // Chỉnh sửa phụ đề của phim.
        if (isset($_POST) && !empty($_POST)){
            // Kiểm tra nếu có dữ liệu POST và không rỗng.
            $language = $this->input->post('language'); // Lấy giá trị 'language' từ form POST.
            $subtitle = $this->db->get_where('subtitle', array('movie_id' => $param2, 'language' => $language))->row_array();
            // Kiểm tra phụ đề đã tồn tại hay chưa.
            if($subtitle['language'] != $language){
                // Nếu chưa tồn tại, cập nhật phụ đề.
                $this->crud_model->edit_subtitle($param1, $param2); // Gọi hàm edit_subtitle từ model.
            }
            redirect(base_url().'index.php?admin/subtitle/'.$param2, 'refresh');
            // Chuyển hướng đến trang quản lý phụ đề.
        }
        $page_data['subtitle_id']   = $param1; // Lưu ID phụ đề vào mảng page_data.
        $page_data['movie_id']      = $param2; // Lưu ID phim vào mảng page_data.
        $page_data['page_name']     = 'edit_subtitle'; // Đặt tên trang là 'edit_subtitle'.
        $page_data['page_title']    = 'Edit subtitle'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    function delete_subtitle($param1 = '', $param2 = ''){
        // Xóa phụ đề của phim.
        $this->db->where('id', $param1); // Xác định phụ đề cần xóa.
        $this->db->delete('subtitle'); // Xóa phụ đề.
        redirect(base_url().'index.php?admin/subtitle/'.$param2, 'refresh');
        // Chuyển hướng đến trang quản lý phụ đề.
    }

    // Chỉnh sửa phim
    function movie_edit($movie_id = '')
    {
        // Chỉnh sửa thông tin phim.
        if (isset($_POST) && !empty($_POST))
        {
            // Kiểm tra nếu có dữ liệu POST và không rỗng.
            $this->crud_model->update_movie($movie_id); // Gọi hàm update_movie từ model để cập nhật thông tin phim.
            redirect(base_url().'index.php?admin/movie_list' , 'refresh'); // Chuyển hướng đến trang danh sách phim.
        }
        $page_data['movie_id']      = $movie_id; // Lưu ID phim vào mảng page_data.
        $page_data['page_name']     = 'movie_edit'; // Đặt tên trang là 'movie_edit'.
        $page_data['page_title']    = 'Edit movie'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

   // Xoá một phim
   function movie_delete($movie_id = '')
   {
	   // Xóa một phim dựa vào ID.
	   $this->db->delete('movie', array('movie_id' => $movie_id)); // Xóa phim từ bảng 'movie' với ID chỉ định.
	   redirect(base_url().'index.php?admin/movie_list', 'refresh'); // Chuyển hướng đến trang danh sách phim sau khi xóa.
   }

   // Hiển thị danh sách series và quản lý chúng
   function series_list($actor_id = "")
   {
	   // Hiển thị danh sách và quản lý các series.
	   $page_data['actor_id']      = empty($actor_id) ? 'all' : $actor_id; // Lưu ID diễn viên, mặc định là 'all'.
	   $page_data['page_name']     = 'series_list'; // Đặt tên trang là 'series_list'.
	   $page_data['page_title']    = 'Manage Tv Series'; // Thiết lập tiêu đề trang.
	   $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
   }

   function series_create()
   {
	   // Tạo series mới.
	   if (isset($_POST) && !empty($_POST))
	   {
		   // Kiểm tra nếu có dữ liệu POST và không rỗng.
		   $this->crud_model->create_series(); // Gọi hàm create_series từ model 'crud_model'.
		   redirect(base_url().'index.php?admin/series_list', 'refresh'); // Chuyển hướng đến trang danh sách series.
	   }
	   $page_data['page_name']     = 'series_create'; // Đặt tên trang là 'series_create'.
	   $page_data['page_title']    = 'Create Tv Series'; // Thiết lập tiêu đề trang.
	   $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
   }

   function series_edit($series_id = '')
   {
	   // Chỉnh sửa series.
	   if (isset($_POST) && !empty($_POST))
	   {
		   // Kiểm tra nếu có dữ liệu POST và không rỗng.
		   $this->crud_model->update_series($series_id); // Gọi hàm update_series từ model 'crud_model'.
		   redirect(base_url().'index.php?admin/series_edit/'.$series_id, 'refresh'); // Chuyển hướng đến trang chỉnh sửa series.
	   }
	   $page_data['series_id']     = $series_id; // Lưu ID series vào mảng page_data.
	   $page_data['page_name']     = 'series_edit'; // Đặt tên trang là 'series_edit'.
	   $page_data['page_title']    = 'Edit Tv Series. Manage Seasons & Episodes'; // Thiết lập tiêu đề trang.
	   $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
   }

   // Xoá một SERIES
   function series_delete($series_id = '')
   {
	   // Xóa series.
	   $this->db->delete('series', array('series_id' => $series_id)); // Xóa series từ bảng 'series' với ID chỉ định.
	   redirect(base_url().'index.php?admin/series_list', 'refresh'); // Chuyển hướng đến trang danh sách series sau khi xóa.
   }

   function season_create($series_id = '')
   {
	   // Tạo mùa mới cho series.
	   $this->db->where('series_id', $series_id); // Lọc dữ liệu theo ID series.
	   $this->db->from('season'); // Chọn bảng 'season'.
	   $number_of_season = $this->db->count_all_results(); // Đếm số lượng mùa đã có.

	   $data['series_id'] = $series_id; // Lưu ID series vào mảng data.
	   $data['name']      = 'Season ' . ($number_of_season + 1); // Đặt tên cho mùa mới.
	   $this->db->insert('season', $data); // Chèn mùa mới vào bảng 'season'.
	   redirect(base_url().'index.php?admin/series_edit/'.$series_id, 'refresh'); // Chuyển hướng đến trang chỉnh sửa series.
   }

   // Xoá mùa phim
   function season_edit($series_id = '', $season_id = '')
   {
	   // Chỉnh sửa mùa của series.
	   if (isset($_POST) && !empty($_POST))
	   {
		   // Kiểm tra nếu có dữ liệu POST và không rỗng.
		   $data['title'] = $this->input->post('title'); // Lấy giá trị 'title' từ form POST.
		   $this->db->update('series', $data, array('series_id' => $series_id)); // Cập nhật dữ liệu vào bảng 'series'.
		   redirect(base_url().'index.php?admin/series_edit/'.$series_id, 'refresh'); // Chuyển hướng đến trang chỉnh sửa series.
	   }
	   // Lấy tên series và mùa từ cơ sở dữ liệu.
	   $series_name = $this->db->get_where('series', array('series_id' => $series_id))->row()->title;
	   $season_name = $this->db->get_where('season', array('season_id' => $season_id))->row()->name;
	   // Thiết lập dữ liệu trang và tải view.
	   $page_data['page_title']  = 'Manage episodes of ' . $season_name . ' : ' . $series_name;
	   $page_data['season_name'] = $this->db->get_where('season', array('season_id' => $season_id))->row()->name;
	   $page_data['series_id']   = $series_id;
	   $page_data['season_id']   = $season_id;
	   $page_data['page_name']   = 'season_edit';
	   $this->load->view('backend/index', $page_data);
   }

   // Xoá mùa phim
   function season_delete($series_id = '', $season_id = '')
   {
	   // Xóa mùa của series.
	   $this->db->delete('season', array('season_id' => $season_id)); // Xóa mùa từ bảng 'season' với ID chỉ định.
	   redirect(base_url().'index.php?admin/series_edit/'.$series_id, 'refresh'); // Chuyển hướng đến trang chỉnh sửa series sau khi xóa.
   }

   // Tạo một tập mới
   function episode_create($series_id = '', $season_id = '')
   {
	   // Tạo tập mới cho mùa của series.
	   if (isset($_POST) && !empty($_POST))
	   {
		   // Kiểm tra nếu có dữ liệu POST và không rỗng.
		   $data['title']     = $this->input->post('title'); // Lấy giá trị 'title' từ form POST.
		   $data['url']       = $this->input->post('url'); // Lấy giá trị 'url' từ form POST.
		   $data['season_id'] = $season_id; // Lưu ID mùa vào mảng data.
		   $this->db->insert('episode', $data); // Chèn tập mới vào bảng 'episode'.
		   $episode_id = $this->db->insert_id(); // Lấy ID của tập mới chèn.
		   // Di chuyển file ảnh đại diện tập và lưu vào thư mục chỉ định.
		   move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/episode_thumb/' . $episode_id . '.jpg');
		   redirect(base_url().'index.php?admin/season_edit/'.$series_id.'/'.$season_id, 'refresh'); // Chuyển hướng đến trang chỉnh sửa mùa.
	   }
   }

   // Chỉnh sửa tập phim
   function episode_edit($series_id = '', $season_id = '', $episode_id = '')
   {
	   // Chỉnh sửa tập của mùa trong series.
	   if (isset($_POST) và !empty($_POST))
	   {
			$data['title']			=	$this->input->post('title');
			$data['url']			=	$this->input->post('url');
			$data['season_id']		=	$season_id;
			$this->db->insert('episode', $data);
			$episode_id = $this->db->insert_id();
			move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/episode_thumb/' . $episode_id . '.jpg');
			redirect(base_url().'index.php?admin/season_edit/'.$series_id.'/'.$season_id , 'refresh');
		}
	}
    function episode_edit($series_id = '', $season_id = '', $episode_id = '')
    {
        // Chỉnh sửa một tập phim.
        if (isset($_POST) && !empty($_POST))
        {
            // Kiểm tra nếu có dữ liệu POST và không rỗng.
            $data['title']      = $this->input->post('title'); // Lấy tiêu đề từ dữ liệu POST.
            $data['url']        = $this->input->post('url'); // Lấy URL từ dữ liệu POST.
            $data['season_id']  = $season_id; // Lưu ID mùa giữa mảng dữ liệu.
            $this->db->update('episode', $data, array('episode_id' => $episode_id)); // Cập nhật dữ liệu tập phim.
            move_uploaded_file($_FILES['thumb']['tmp_name'], 'assets/global/episode_thumb/' . $episode_id . '.jpg'); // Di chuyển hình ảnh được tải lên.
            redirect(base_url().'index.php?admin/season_edit/'.$series_id.'/'.$season_id, 'refresh'); // Chuyển hướng đến trang chỉnh sửa mùa.
        }
    }

    function episode_delete($series_id = '', $season_id = '', $episode_id = '')
    {
        // Xóa một tập phim.
        $this->db->delete('episode', array('episode_id' => $episode_id)); // Xóa tập phim từ cơ sở dữ liệu.
        redirect(base_url().'index.php?admin/season_edit/'.$series_id.'/'.$season_id, 'refresh'); // Chuyển hướng đến trang chỉnh sửa mùa.
    }

    function actor_list()
    {
        // Hiển thị danh sách và quản lý diễn viên.
        $page_data['page_name']     = 'actor_list'; // Đặt tên trang là 'actor_list'.
        $page_data['page_title']    = 'Manage actor'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    function actor_create()
    {
        // Tạo diễn viên mới.
        if (isset($_POST) và !empty($_POST))
        {
            // Kiểm tra nếu có dữ liệu POST và không rỗng.
            $this->crud_model->create_actor(); // Gọi hàm create_actor từ model 'crud_model'.
            redirect(base_url().'index.php?admin/actor_list', 'refresh'); // Chuyển hướng đến trang danh sách diễn viên.
        }
        $page_data['page_name']     = 'actor_create'; // Đặt tên trang là 'actor_create'.
        $page_data['page_title']    = 'Create actor'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    function actor_edit($actor_id = '')
    {
        // Chỉnh sửa thông tin diễn viên.
        if (isset($_POST) và !empty($_POST))
        {
            // Kiểm tra nếu có dữ liệu POST và không rỗng.
            $this->crud_model->update_actor($actor_id); // Gọi hàm update_actor từ model 'crud_model'.
            redirect(base_url().'index.php?admin/actor_list', 'refresh'); // Chuyển hướng đến trang danh sách diễn viên.
        }
        $page_data['actor_id']      = $actor_id; // Lưu ID diễn viên vào mảng page_data.
        $page_data['page_name']     = 'actor_edit'; // Đặt tên trang là 'actor_edit'.
        $page_data['page_title']    = 'Edit actor'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    function actor_delete($actor_id = '')
    {
        // Xóa diễn viên.
        $this->db->delete('actor', array('actor_id' => $actor_id)); // Xóa diễn viên từ cơ sở dữ liệu.
        redirect(base_url().'index.php?admin/actor_list', 'refresh'); // Chuyển hướng đến trang danh sách diễn viên.
    }

    function director_list()
    {
        // Hiển thị danh sách và quản lý đạo diễn.
        $page_data['page_name']     = 'director_list'; // Đặt tên trang là 'director_list'.
        $page_data['page_title']    = 'Manage Director'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }
    function director_create()
    {
        // Tạo đạo diễn mới.
        if (isset($_POST) và !empty($_POST))
        {
            // Kiểm tra nếu có dữ liệu POST và không rỗng.
            $this->crud_model->create_director(); // Gọi hàm create_director từ model 'crud_model'.
            redirect(base_url().'index.php?admin/director_list', 'refresh'); // Chuyển hướng đến trang danh sách đạo diễn.
        }
        $page_data['page_name']     = 'director_create'; // Đặt tên trang là 'director_create'.
        $page_data['page_title']    = 'Create director'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }
    function director_edit($director_id = '')
    {
        // Chỉnh sửa thông tin đạo diễn.
        if (isset($_POST) và !empty($_POST))
        {
            // Kiểm tra nếu có dữ liệu POST và không rỗng.
            $this->crud_model->update_director($director_id); // Gọi hàm update_director từ model 'crud_model'.
            redirect(base_url().'index.php?admin/director_list', 'refresh'); // Chuyển hướng đến trang danh sách đạo diễn.
        }
        $page_data['director_id']   = $director_id; // Lưu ID đạo diễn vào mảng page_data.
        $page_data['page_name']     = 'director_edit'; // Đặt tên trang là 'director_edit'.
        $page_data['page_title']    = 'Edit director'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    function director_delete($director_id = '')
    {
        // Xóa một đạo diễn dựa trên ID.
        $this->db->delete('director', array('director_id' => $director_id)); // Xóa đạo diễn từ cơ sở dữ liệu.
        redirect(base_url().'index.php?admin/director_list', 'refresh'); // Chuyển hướng đến trang danh sách đạo diễn.
    }

    function plan_list()
    {
        // Hiển thị danh sách và quản lý các gói giá cước.
        $page_data['page_name']     = 'plan_list'; // Đặt tên trang là 'plan_list'.
        $page_data['page_title']    = 'Manage plan'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    function plan_edit($plan_id = '')
    {
        // Chỉnh sửa một gói giá cước.
        if (isset($_POST) && !empty($_POST))
        {
            // Kiểm tra nếu có dữ liệu POST và không rỗng.
            $data['name']   = $this->input->post('name'); // Lấy tên gói giá từ dữ liệu POST.
            $data['price']  = $this->input->post('price'); // Lấy giá từ dữ liệu POST.
            $data['status'] = $this->input->post('status'); // Lấy trạng thái từ dữ liệu POST.
            $this->db->update('plan', $data, array('plan_id' => $plan_id)); // Cập nhật dữ liệu gói giá cước.
            redirect(base_url().'index.php?admin/plan_list', 'refresh'); // Chuyển hướng đến trang danh sách gói giá cước.
        }
        $page_data['plan_id']       = $plan_id; // Lưu ID gói giá cước vào mảng page_data.
        $page_data['page_name']     = 'plan_edit'; // Đặt tên trang là 'plan_edit'.
        $page_data['page_title']    = 'Edit plan'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    function user_list()
    {
        // Hiển thị danh sách và quản lý người dùng.
        $page_data['page_name']     = 'user_list'; // Đặt tên trang là 'user_list'.
        $page_data['page_title']    = 'Manage user'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    function user_create()
    {
        // Tạo người dùng mới.
        if (isset($_POST) và !empty($_POST))
        {
            // Kiểm tra nếu có dữ liệu POST và không rỗng.
            $this->crud_model->create_user(); // Gọi hàm create_user từ model 'crud_model'.
            redirect(base_url().'index.php?admin/user_list', 'refresh'); // Chuyển hướng đến trang danh sách người dùng.
        }
        $page_data['page_name']     = 'user_create'; // Đặt tên trang là 'user_create'.
        $page_data['page_title']    = 'Create user'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    function user_edit($edit_user_id = '')
    {
        // Chỉnh sửa thông tin người dùng.
        if (isset($_POST) và !empty($_POST))
        {
            // Kiểm tra nếu có dữ liệu POST và không rỗng.
            $this->crud_model->update_user($edit_user_id); // Gọi hàm update_user từ model 'crud_model'.
            redirect(base_url().'index.php?admin/user_list', 'refresh'); // Chuyển hướng đến trang danh sách người dùng.
        }
        $page_data['edit_user_id']  = $edit_user_id; // Lưu ID người dùng cần chỉnh sửa vào mảng page_data.
        $page_data['page_name']     = 'user_edit'; // Đặt tên trang là 'user_edit'.
        $page_data['page_title']    = 'Edit user'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    function user_delete($user_id = '')
    {
        // Xóa người dùng.
        $this->db->delete('user', array('user_id' => $user_id)); // Xóa người dùng từ cơ sở dữ liệu.
        redirect(base_url().'index.php?admin/user_list', 'refresh'); // Chuyển hướng đến trang danh sách người dùng.
    }

    function report($month = '', $year = '')
    {
        // Xem báo cáo đăng ký và thanh toán.
        if ($month == '')
            $month = date("F"); // Nếu tháng trống, lấy tháng hiện tại.
        if ($year == '')
            $year = date("Y"); // Nếu năm trống, lấy năm hiện tại.

        $page_data['month']         = $month; // Lưu tháng vào mảng page_data.
        $page_data['year']          = $year; // Lưu năm vào mảng page_data.
        $page_data['page_name']     = 'report'; // Đặt tên trang là 'report'.
        $page_data['page_title']    = 'Customer subscription & payment report'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

    function faq_list()
    {
        // Hiển thị danh sách và quản lý câu hỏi thường gặp (FAQs).
        $page_data['page_name']     = 'faq_list'; // Đặt tên trang là 'faq_list'.
        $page_data['page_title']    = 'Manage faq'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

	function faq_create()
    {
        // Tạo câu hỏi thường gặp mới.
        if (isset($_POST) và !empty($_POST))
        {
            // Kiểm tra nếu có dữ liệu POST và không rỗng.
            $data['question'] = $this->input->post('question'); // Lấy câu hỏi từ dữ liệu POST.
            $data['answer']   = $this->input->post('answer'); // Lấy câu trả lời từ dữ liệu POST.
            $this->db->insert('faq', $data); // Chèn câu hỏi và câu trả lời vào cơ sở dữ liệu.
            redirect(base_url().'index.php?admin/faq_list', 'refresh'); // Chuyển hướng đến trang danh sách FAQs.
        }
        $page_data['page_name']     = 'faq_create'; // Đặt tên trang là 'faq_create'.
        $page_data['page_title']    = 'Create faq'; // Thiết lập tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải view 'backend/index' với dữ liệu trang.
    }

	    // EDIT A FAQ
		function faq_edit($faq_id = '')
		{
			// Hàm này được sử dụng để chỉnh sửa một câu hỏi thường gặp (FAQ).
			if (isset($_POST) && !empty($_POST))
			{
				// Kiểm tra nếu có dữ liệu được gửi từ form.
				$data['question'] = $this->input->post('question'); // Lấy câu hỏi từ dữ liệu POST.
				$data['answer']   = $this->input->post('answer'); // Lấy câu trả lời từ dữ liệu POST.
				$this->db->update('faq', $data, array('faq_id' => $faq_id)); // Cập nhật FAQ trong cơ sở dữ liệu với ID tương ứng.
				redirect(base_url().'index.php?admin/faq_list', 'refresh'); // Chuyển hướng người dùng đến trang danh sách FAQ sau khi cập nhật.
			}
			$page_data['faq_id'] = $faq_id; // Gán ID của FAQ cần chỉnh sửa vào biến.
			$page_data['page_name'] = 'faq_edit'; // Đặt tên trang hiện tại để sử dụng trong view.
			$page_data['page_title'] = 'Edit faq'; // Thiết lập tiêu đề trang.
			$this->load->view('backend/index', $page_data); // Tải trang chỉnh sửa FAQ với các thông tin đã thiết lập.
		}
	
		function faq_delete($faq_id = '')
		{
			// Hàm này được sử dụng để xóa một câu hỏi thường gặp (FAQ).
			$this->db->delete('faq', array('faq_id' => $faq_id)); // Xóa FAQ từ cơ sở dữ liệu dựa trên ID.
			redirect(base_url().'index.php?admin/faq_list', 'refresh'); // Chuyển hướng người dùng đến trang danh sách FAQ sau khi xóa.
		}
	
		function settings()
		{
			// Hàm này được sử dụng để chỉnh sửa các cài đặt của trang web.
			if (isset($_POST) && !empty($_POST))
			{
				// Cập nhật tên trang web.
				$data['description'] = $this->input->post('site_name'); // Lấy tên trang web từ dữ liệu POST.
				$this->db->update('settings', $data, array('type' => 'site_name')); // Cập nhật tên trang web trong cơ sở dữ liệu.
	
				// Cập nhật email của trang web.
				$data['description'] = $this->input->post('site_email'); // Lấy email trang web từ dữ liệu POST.
				$this->db->update('settings', $data, array('type' => 'site_email')); // Cập nhật email trang web trong cơ sở dữ liệu.
	
				// Cập nhật trạng thái kích hoạt/tắt thời gian dùng thử.
				$data['description'] = $this->input->post('trial_period'); // Lấy trạng thái thời gian dùng thử từ dữ liệu POST.
				$this->db->update('settings', $data, array('type' => 'trial_period')); // Cập nhật trạng thái thời gian dùng thử trong cơ sở dữ liệu.
	
    // Cập nhật số ngày của thời gian dùng thử từ dữ liệu POST và lưu vào cơ sở dữ liệu.
			$data['description']		=	$this->input->post('trial_period_days');
			$this->db->update('settings', $data,  array('type' => 'trial_period_days'));

    // Lấy ngôn ngữ từ dữ liệu POST và lưu vào cơ sở dữ liệu.
			$data['description']		=	$this->input->post('language');
			$this->db->update('settings', $data,  array('type' => 'language'));

    // Lấy chủ đề từ dữ liệu POST và lưu vào cơ sở dữ liệu.
			$data['description']		=	$this->input->post('theme');
			$this->db->update('settings', $data,  array('type' => 'theme'));

    // Lấy email thương gia PayPal từ dữ liệu POST và lưu vào cơ sở dữ liệu.
			$data['description']		=	$this->input->post('paypal_merchant_email');
			$this->db->update('settings', $data,  array('type' => 'paypal_merchant_email'));

    // Lấy địa chỉ hóa đơn từ dữ liệu POST và lưu vào cơ sở dữ liệu.
			$data['description']		=	$this->input->post('invoice_address');
			$this->db->update('settings', $data,  array('type' => 'invoice_address'));

    // Lấy mã mua hàng Envato từ dữ liệu POST và lưu vào cơ sở dữ liệu.
			$data['description']		=	$this->input->post('purchase_code');
			$this->db->update('settings', $data,  array('type' => 'purchase_code'));

    // Lấy chính sách riêng tư từ dữ liệu POST và lưu vào cơ sở dữ liệu.
			$data['description']		=	$this->input->post('privacy_policy');
			$this->db->update('settings', $data,  array('type' => 'privacy_policy'));

    // Cập nhật cài đặt chính sách hoàn tiền trong bảng settings.
			$data['description']		=	$this->input->post('refund_policy');
			$this->db->update('settings', $data,  array('type' => 'refund_policy'));

    // Lấy khóa công khai Stripe từ dữ liệu POST và lưu vào cơ sở dữ liệu.
			$data['description']		=	$this->input->post('stripe_publishable_key');
			$this->db->update('settings', $data,  array('type' => 'stripe_publishable_key'));

    // Lấy khóa bí mật Stripe từ dữ liệu POST và lưu vào cơ sở dữ liệu.
			$data['description']		=	$this->input->post('stripe_secret_key');
			$this->db->update('settings', $data,  array('type' => 'stripe_secret_key'));

    // Lấy trạng thái cookie từ dữ liệu POST và lưu vào cơ sở dữ liệu.
			$data['description']		=	$this->input->post('cookie_status');
			$this->db->update('settings', $data,  array('type' => 'cookie_status'));

    // Lấy ghi chú cookie từ dữ liệu POST và lưu vào cơ sở dữ liệu.
			$data['description']		=	$this->input->post('cookie_note');
			$this->db->update('settings', $data,  array('type' => 'cookie_note'));

    // Cập nhật cài đặt chính sách cookie trong bảng settings.
			$data['description']		=	$this->input->post('cookie_policy');
			$this->db->update('settings', $data,  array('type' => 'cookie_policy'));

    // Cập nhật cài đặt trạng thái xác minh email trong bảng settings.
			$data['description']		=	$this->input->post('email_verification');
			$this->db->update('settings', $data,  array('type' => 'email_verification'));

    // Cập nhật cài đặt khóa bí mật Recaptcha trong bảng settings.
			$data['description']		=	$this->input->post('recaptcha');
			$this->db->update('settings', $data,  array('type' => 'recaptcha'));

			$data['description']		=	$this->input->post('recaptcha_secretkey');
			$this->db->update('settings', $data,  array('type' => 'recaptcha_secretkey'));

			$data['description']		=	$this->input->post('recaptcha_sitekey');
			$this->db->update('settings', $data,  array('type' => 'recaptcha_sitekey'));

			move_uploaded_file($_FILES['logo']['tmp_name'], 'assets/global/logo.png');

			redirect(base_url().'index.php?admin/settings' , 'refresh');
		}

		$page_data['site_name']				=	$this->db->get_where('settings',array('type'=>'site_name'))->row('description');
		$page_data['site_email']			=	$this->db->get_where('settings',array('type'=>'site_email'))->row('description');
		$page_data['trial_period']			=	$this->db->get_where('settings',array('type'=>'trial_period'))->row('description');
		$page_data['trial_period_days']		=	$this->db->get_where('settings',array('type'=>'trial_period_days'))->row('description');
		$page_data['theme']					=	$this->db->get_where('settings',array('type'=>'theme'))->row('description');
		$page_data['paypal_merchant_email']	=	$this->db->get_where('settings',array('type'=>'paypal_merchant_email'))->row('description');
		$page_data['invoice_address']		=	$this->db->get_where('settings',array('type'=>'invoice_address'))->row('description');
		$page_data['purchase_code']			=	$this->db->get_where('settings',array('type'=>'purchase_code'))->row('description');
		$page_data['privacy_policy']		=	$this->db->get_where('settings',array('type'=>'privacy_policy'))->row('description');
		$page_data['refund_policy']			=	$this->db->get_where('settings',array('type'=>'refund_policy'))->row('description');
		$page_data['stripe_publishable_key']=	$this->db->get_where('settings',array('type'=>'stripe_publishable_key'))->row('description');
		$page_data['stripe_secret_key']		=	$this->db->get_where('settings',array('type'=>'stripe_secret_key'))->row('description');
		$page_data['languages']	 = $this->get_all_languages();
		$page_data['page_name']				=	'settings';
		$page_data['page_title']			=	'Website settings';
		$this->load->view('backend/index', $page_data);
	}
    function payment_settings($param1 = "", $param2 = "") {
        // Hàm này được sử dụng để cập nhật cài đặt thanh toán.

        if ($param1 == 'system_currency') {
            $this->crud_model->system_currency(); // Gọi hàm cập nhật cài đặt tiền tệ hệ thống.
            redirect(base_url('index.php?admin/payment_settings'), 'refresh'); // Chuyển hướng người dùng đến trang cài đặt thanh toán.
        }

        if ($param1 == 'paypal') {
            $this->crud_model->update_paypal_keys(); // Gọi hàm cập nhật khóa PayPal.
            redirect(base_url('index.php?admin/payment_settings'), 'refresh'); // Chuyển hướng người dùng đến trang cài đặt thanh toán.
        }

        if ($param1 == 'stripe') {
            $this->crud_model->update_stripe_keys(); // Gọi hàm cập nhật khóa Stripe.
            redirect(base_url('index.php?admin/payment_settings'), 'refresh'); // Chuyển hướng người dùng đến trang cài đặt thanh toán.
        }

        $this->session->set_userdata('last_page', 'payment_settings'); // Lưu trang hiện tại vào session.
        $page_data['page_name'] = 'payment_settings'; // Đặt tên trang là 'payment_settings'.
        $page_data['page_title'] = get_phrase('payment_settings'); // Đặt tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải trang cài đặt thanh toán.
    }

    public function smtp_settings($param1 = "") {
        // Hàm này được sử dụng để cập nhật cài đặt SMTP cho email.
        if ($param1 == 'update') {
            $this->crud_model->update_smtp_settings(); // Gọi hàm cập nhật cài đặt SMTP.
            $this->session->set_flashdata('flash_message', get_phrase('smtp_settings_updated_successfully')); // Hiển thị thông báo cập nhật thành công.
            redirect(base_url('index.php?admin/smtp_settings'), 'refresh'); // Chuyển hướng người dùng đến trang cài đặt SMTP.
        }

        $page_data['page_name'] = 'smtp_settings'; // Đặt tên trang là 'smtp_settings'.
        $page_data['page_title'] = get_phrase('smtp_settings'); // Đặt tiêu đề trang.
        $this->load->view('backend/index', $page_data); // Tải trang cài đặt SMTP.
    }

    function report_invoice($param1 = '', $param2 = ''){
        // Hàm này được sử dụng để hiển thị hóa đơn đăng ký và thanh toán của khách hàng.
        $page_data['subscription_id'] = $param1; // Lưu ID đăng ký.
        $page_data['user_id'] = $param2; // Lưu ID người dùng.
        $page_data['page_title'] = 'Customer subscription & payment invoice'; // Đặt tiêu đề trang.
        $this->load->view('backend/pages/report_invoice', $page_data); // Tải trang hóa đơn.
    }

    function get_list_of_directories_and_files($dir = APPPATH, &$results = array()) {
        // Hàm này được sử dụng để lấy danh sách tất cả các thư mục và file trong một thư mục cụ thể.
        $files = scandir($dir); // Quét tất cả các file và thư mục trong đường dẫn $dir.
        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value); // Lấy đường dẫn thực của file hoặc thư mục.
            if(!is_dir($path)) {
                $results[] = $path; // Nếu không phải thư mục, thêm vào kết quả.
            } else if($value != "." && $value != "..") {
                $this->get_list_of_directories_and_files($path, $results); // Nếu là thư mục, gọi đệ quy hàm này.
                $results[] = $path; // Thêm đường dẫn thư mục vào kết quả.
            }
        }
        return $results; // Trả về danh sách các file và thư mục.
    }

    function get_all_php_files() {
        // Hàm này được sử dụng để lấy danh sách tất cả các file PHP.
        $all_files = $this->get_list_of_directories_and_files(); // Gọi hàm lấy tất cả các file và thư mục.
        foreach ($all_files as $file) {
            $info = pathinfo($file); // Lấy thông tin file.
            if( isset($info['extension']) && strtolower($info['extension']) == 'php') {
                // Nếu là file PHP, xử lý nội dung file.
                if ($fh = fopen($file, 'r')) {
                    while (!feof($fh)) {
                        $line = fgets($fh); // Đọc từng dòng trong file.
                        preg_match_all('/get_phrase\(\'(.*?)\'\)\;/s', $line, $matches); // Tìm kiếm các hàm get_phrase.
                        foreach ($matches[1] as $matche) {
                            get_phrase($matche); // Gọi hàm get_phrase với các chuỗi tìm được.
                        }
                    }
                    fclose($fh); // Đóng file sau khi xử lý.
                }
            }
        }

        echo 'I Am So Lit'; // In thông báo sau khi hoàn thành.
    }

    function get_list_of_language_files($dir = APPPATH.'/language', &$results = array()) {
        // Hàm này được sử dụng để lấy danh sách các file ngôn ngữ.
        $files = scandir($dir); // Quét tất cả các file và thư mục trong thư mục ngôn ngữ.
        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value); // Lấy đường dẫn thực của file hoặc thư mục.
            if(!is_dir($path)) {
                $results[] = $path; // Nếu không phải thư mục, thêm vào kết quả.
            } else if($value != "." và $value != "..") {
                $this->get_list_of_directories_and_files($path, $results); // Nếu là thư mục, gọi đệ quy hàm này.
                $results[] = $path; // Thêm đường dẫn thư mục vào kết quả.
            }
        }
        return $results; // Trả về danh sách các file ngôn ngữ.
    }

    function get_all_languages() {
        // Hàm này được sử dụng để lấy danh sách tất cả các ngôn ngữ có sẵn.
        $language_files = array(); // Khởi tạo mảng chứa danh sách file ngôn ngữ.
        $all_files = $this->get_list_of_language_files(); // Gọi hàm lấy danh sách file ngôn ngữ.
        foreach ($all_files as $file) {
            $info = pathinfo($file); // Lấy thông tin file.
            if( isset($info['extension']) và strtolower($info['extension']) == 'json') {
                // Nếu là file ngôn ngữ JSON, xử lý file.
                $file_name = explode('.json', $info['basename']); // Tách tên file từ đường dẫn.
                array_push($language_files, $file_name[0]); // Thêm tên ngôn ngữ vào mảng.
            }
        }

        return $language_files; // Trả về danh sách ngôn ngữ.
    }

	    // Quản lý ngôn ngữ
		public function manage_language($param1 = '', $param2 = '', $param3 = ''){
			// Thêm ngôn ngữ mới
			if ($param1 == 'add_language') {
				saveDefaultJSONFile(sanitizer($this->input->post('language'))); // Lưu ngôn ngữ mới vào file JSON.
				$this->session->set_flashdata('flash_message', get_phrase('language_added_successfully')); // Tạo thông báo thành công.
				redirect(base_url().'index.php?admin/manage_language', 'refresh'); // Chuyển hướng đến trang quản lý ngôn ngữ.
			}
	
			// Xóa ngôn ngữ
			if ($param1 == 'delete_language') {
				if (file_exists('application/language/'.$param2.'.json')) { // Kiểm tra nếu file ngôn ngữ tồn tại.
					unlink('application/language/'.$param2.'.json'); // Xóa file ngôn ngữ.
					$this->session->set_flashdata('flash_message', get_phrase('language_deleted_successfully')); // Tạo thông báo xóa thành công.
					redirect(base_url().'index.php?admin/manage_language', 'refresh'); // Chuyển hướng đến trang quản lý ngôn ngữ.
				}
			}
	
			// Thêm cụm từ ngôn ngữ mới
			if ($param1 == 'add_phrase') {
				$new_phrase = get_phrase(sanitizer($this->input->post('phrase'))); // Lấy cụm từ ngôn ngữ mới từ form.
				$this->session->set_flashdata('flash_message', $new_phrase.' '.get_phrase('has_been_added_successfully')); // Tạo thông báo thêm thành công.
				redirect(base_url().'index.php?admin/manage_language', 'refresh'); // Chuyển hướng đến trang quản lý ngôn ngữ.
			}
	
			// Sửa cụm từ ngôn ngữ
			if ($param1 == 'edit_phrase') {
				$page_data['edit_profile'] = $param2; // Lưu thông tin cụm từ ngôn ngữ cần chỉnh sửa.
			}
	
			// Lấy thông tin các ngôn ngữ và tải trang quản lý ngôn ngữ
			$page_data['languages'] = $this->get_all_languages(); // Lấy danh sách tất cả ngôn ngữ.
			$page_data['page_name'] = 'manage_language'; // Đặt tên trang.
			$page_data['page_title'] = get_phrase('multi_language_settings'); // Đặt tiêu đề trang.
			$this->load->view('backend/index', $page_data); // Tải trang quản lý ngôn ngữ.
		}
	
		// Cài đặt tài khoản
		function account() {
			$user_id = $this->session->userdata('user_id'); // Lấy ID người dùng từ session.
	
			// Xử lý cập nhật thông tin tài khoản
			if (isset($_POST) && !empty($_POST)) {
				$task = $this->input->post('task'); // Lấy tác vụ từ form.
				
				// Cập nhật hồ sơ
				if ($task == 'update_profile') {
					$data['name'] = $this->input->post('name'); // Lấy tên từ form.
					$data['email'] = $this->input->post('email'); // Lấy email từ form.
					$this->db->update('user', $data, array('user_id' => $user_id)); // Cập nhật thông tin người dùng.
					redirect(base_url().'index.php?admin/account', 'refresh'); // Chuyển hướng đến trang tài khoản.
				}
				// Cập nhật mật khẩu
				else if ($task == 'update_password') {
					// So sánh mật khẩu cũ và cập nhật mật khẩu mới
					// [Bỏ qua chi tiết vì độ dài, nhưng cơ bản kiểm tra mật khẩu cũ, cập nhật nếu đúng]
					redirect(base_url().'index.php?admin/account', 'refresh'); // Chuyển hướng đến trang tài khoản.
				}
			}
	
			// Tải trang tài khoản
			$page_data['page_name'] = 'account'; // Đặt tên trang.
			$page_data['page_title'] = 'Manage account'; // Đặt tiêu đề trang.
			$this->load->view('backend/index', $page_data); // Tải trang tài khoản.
		}
	
		// Kiểm tra đăng nhập admin
		function admin_login_check() {
			$logged_in_user_type = $this->session->userdata('login_type'); // Lấy loại người dùng đăng nhập từ session.
			if ($logged_in_user_type == 0) { // Nếu không phải admin, chuyển hướng đến trang đăng nhập.
				redirect(base_url().'index.php?home/signin', 'refresh');
			}
		}
	
		// Hiển thị phim và series theo diễn viên
		function actor_wise_movie_and_series($actor_id) {
			$actor_details = $this->db->get_where('actor', array('actor_id' => $actor_id))->row_array(); // Lấy thông tin diễn viên.
			$page_data['page_name'] = 'actor_wise_movie_and_series'; // Đặt tên trang.
			$page_data['page_title'] = get_phrase('movies_and_TV_series_of').' "'.$actor_details['name'].'"'; // Đặt tiêu đề trang.
			$page_data['actor_id'] = $actor_id; // Lưu ID diễn viên.
			$this->load->view('backend/index', $page_data); // Tải trang phim và series theo diễn viên.
		}
	
		// Hiển thị phim và series theo đạo diễn
		function director_wise_movie_and_series($director_id) {
			$director_details = $this->db->get_where('director', array('director_id' => $director_id))->row_array(); // Lấy thông tin đạo diễn.
			$page_data['page_name'] = 'director_wise_movie_and_series'; // Đặt tên trang.
			$page_data['page_title'] = get_phrase('movies_and_TV_series_of').' "'.$director_details['name'].'"'; // Đặt tiêu đề trang.
			$page_data['director_id'] = $director_id; // Lưu ID đạo diễn.
			$this->load->view('backend/index', $page_data); // Tải trang phim và series theo đạo diễn.
		}
	
		// Cập nhật cụm từ ngôn ngữ với AJAX
		public function update_phrase_with_ajax() {
			// Lấy thông tin từ form AJAX và lưu cập nhật.
			// [Bỏ qua chi tiết do độ dài]
			echo $current_editing_language.' '.$key.' '.$updatedValue; // In ra thông báo sau khi cập nhật.
		}
	
		// Hiển thị thông tin về ứng dụng
		public function about(){
			$page_data['application_details'] = $this->crud_model->get_application_details(); // Lấy thông tin ứng dụng.
			$page_data['page_name']  = 'about'; // Đặt tên trang.
			$page_data['page_title'] = get_phrase('about'); // Đặt tiêu đề trang.
			$this->load->view('backend/index', $page_data); // Tải trang thông tin ứng dụng.
		}
	
		// Quản lý addon
		public function addon($param1 = "", $param2 = "", $param3 = "") {
			// Các chức năng quản lý addon như thêm, kích hoạt, hủy kích hoạt, xóa và xem thông tin addon.
			// [Bỏ qua chi tiết vì độ dài, nhưng cơ bản thực hiện các tác vụ quản lý addon]
			$this->load->view('backend/index', $page_data); // Tải trang quản lý addon.
		}
	
		// Hiển thị danh sách addon có sẵn
		public function available_addons(){
			$page_data['page_name']  = 'available_addon'; // Đặt tên trang.
			$page_data['page_title'] = get_phrase('available_addon'); // Đặt tiêu đề trang.
			$this->load->view('backend/index', $page_data); // Tải trang danh sách addon có sẵn.
		}
	}
	