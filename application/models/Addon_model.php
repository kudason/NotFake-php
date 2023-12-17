<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Addon_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}

	public function install_addon() {
		// Hàm này được sử dụng để cài đặt một addon vào hệ thống.
		
		// Kiểm tra nếu thư mục 'addons' bên trong 'controllers' không tồn tại, thì tạo mới.
		if (!is_dir('application/controllers/addons')){
			mkdir("application/controllers/addons", 0777, true);
		}

		// Kiểm tra nếu thư mục 'addons' bên trong 'models' không tồn tại, thì tạo mới.
		if (!is_dir('application/models/addons')){
			mkdir("application/models/addons", 0777, true);
		}

		// Lấy tên file nén addon từ dữ liệu POST.
		$zipped_file_name = $_FILES['addon_zip']['name'];

		if (!empty($zipped_file_name)) {
			// Tạo thư mục 'uploads/addons' nếu chưa tồn tại.
			$dir = 'uploads/addons';
			if (!is_dir($dir))
			mkdir($dir, 0777, true);

			$path = "uploads/addons/".$zipped_file_name;
			move_uploaded_file($_FILES['addon_zip']['tmp_name'], $path);

			// Giải nén tệp tin addon và xóa tệp nén.
			$zip = new ZipArchive;
			$res = $zip->open($path);
			if ($res === TRUE) {
				$zip->extractTo('uploads/addons');
				$zip->close();
				unlink($path);
			}else{
				// Xử lý lỗi nếu không thể giải nén tệp tin.
				$this->session->set_flashdata('error_message', get_phrase('make_sure').' ZipArchive '.get_phrase('is_enabled_on_your_server'));
				redirect(base_url('index.php?admin/addon'), 'refresh');
			}

			$unzipped_file_name = substr($zipped_file_name, 0, -4);
			$config_str = file_get_contents('uploads/addons/' . $unzipped_file_name . '/config.json');
			$config = json_decode($config_str, true);

			// Tạo các thư mục nếu được định nghĩa trong tệp cấu hình.
			if (!empty($config['directories'])) {
				foreach ($config['directories'] as $directory) {
					if (!is_dir($directory['name'])){
						mkdir($directory['name'], 0777, true);
					}
				}
			}

			// Tạo hoặc thay thế các tệp tin mới.
			if (!empty($config['files'])) {
				foreach ($config['files'] as $file){
					copy($file['root_directory'], $file['update_directory']);
				}
			}

			// Tạo hoặc thay thế các thư viện mới.
			if (!empty($config['libraries'])) {
				foreach ($config['libraries'] as $libraries){
					copy($libraries['root_directory'], $libraries['update_directory']);

					// Giải nén tệp tin thư viện và xóa tệp tin nén.
					$library_path = $libraries['update_directory'];

					// Đường dẫn để giải nén thư viện.
					$library_path_array = explode('/', $library_path);
					array_pop($library_path_array);
					$extract_to = implode('/', $library_path_array);
					$library_zip = new ZipArchive;
					$library_result = $library_zip->open($library_path);
					if ($library_result === TRUE) {
						$library_zip->extractTo($extract_to);
						$library_zip->close();
					}else{
						// Xử lý lỗi nếu không thể giải nén thư viện.
						$this->session->set_flashdata('error_message', get_phrase('make_sure').' ZipArchive '.get_phrase('is_enabled_on_your_server'));
						redirect(base_url('index.php?admin/addon'), 'refresh');
					}
					unlink($library_path);
				}
			}

			// Thực hiện tệp SQL để cài đặt cơ sở dữ liệu.
			if (!empty($config['sql_file'])) {
				require './uploads/addons/'.$unzipped_file_name.'/sql/'.$config['sql_file'];
			}

			// Thêm hoặc cập nhật một bản ghi addon vào cơ sở dữ liệu.
			$data['name'] = $config['name'];
			$data['unique_identifier'] = $config['unique_identifier'];
			$data['version'] = $config['version'];
			$data['about'] = $config['about'];
			$data['status'] = 1;

			// Kiểm tra nếu addon đã được cài đặt trước đó.
			$addon_details = $this->db->get_where('addons', array('unique_identifier' => $data['unique_identifier'], 'version' => $data['version']));

			if ($addon_details->num_rows() > 0) {
				$data['updated_at'] = strtotime(date('d-m-y'));
				$this->db->where('unique_identifier', $data['unique_identifier']);
				$this->db->update('addons', $data);

			}else{
				$data['created_at'] = strtotime(date('d-m-y'));
				$this->db->insert('addons', $data);
			}

			$this->remove_from_uploads($unzipped_file_name);

			$this->session->set_flashdata('flash_message', get_phrase('addon_installed_successfully'));
			redirect(base_url('index.php?admin/addon'), 'refresh');
		}else{
			$this->session->set_flashdata('error_message', get_phrase('no_addon_found'));
			redirect(base_url('index.php?admin/addon'), 'refresh');
		}

		return json_encode($response);
	}

	public function remove_from_uploads($folder_name) {
		// Hàm này dùng để xóa thư mục addon đã giải nén và sạch sau khi cài đặt.
		$dir = 'uploads/addons/'.$folder_name;
		$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
		foreach($files as $file) {
			if ($file->isDir()){
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}
		rmdir($dir);
	}

	public function addon_activate($addon_id = ""){
		// Hàm này dùng để kích hoạt addon dựa trên ID của addon.
		$check_addon_status = $this->db->get_where('addons', array('id' => $addon_id))->row('status');
		if($check_addon_status == 0):
			$data['status'] = 1;
			$this->db->where('id', $addon_id);
			$this->db->update('addons', $data);
			return "addon_is_activated_successfully";
		else:
			return "addon_is_already_activated";
		endif;
	}

	public function addon_deactivate($addon_id = ""){
		// Hàm này dùng để vô hiệu hóa addon dựa trên ID của addon.
		$check_addon_status = $this->db->get_where('addons', array('id' => $addon_id))->row('status');
		if($check_addon_status == 1):
			$data['status'] = 0;
			$this->db->where('id', $addon_id);
			$this->db->update('addons', $data);
			return "addon_is_deactivated_successfully";
		else:
			return "addon_is_already_deactivated";
		endif;
	}

	public function addon_delete($addon_id = ""){
		// Hàm này dùng để xóa addon dựa trên ID của addon.
		$this->db->where('id', $addon_id);
		$this->db->delete('addons');
	}

	public function addon_list($unique_identifier = ""){
		// Hàm này dùng để lấy danh sách addon dựa trên một số tiêu chí, như một unique identifier cụ thể.
		if($unique_identifier != ""){
			$this->db->where('unique_identifier', $unique_identifier);
		}
		return $this->db->get('addons');
	}
}
