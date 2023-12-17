<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| Tập tin này sẽ chứa các cài đặt cần thiết để truy cập cơ sở dữ liệu của bạn.
|
| Để biết hướng dẫn chi tiết, vui lòng tham khảo trang 'Kết nối Cơ sở dữ liệu'
| trong Hướng dẫn Người dùng.
|
| -------------------------------------------------------------------
| GIẢI THÍCH CÁC BIẾN
| -------------------------------------------------------------------
|
|	['dsn']      Chuỗi DSN đầy đủ mô tả một kết nối đến cơ sở dữ liệu.
|	['hostname'] Tên máy chủ cơ sở dữ liệu.
|	['username'] Tên người dùng được sử dụng để kết nối đến cơ sở dữ liệu
|	['password'] Mật khẩu được sử dụng để kết nối đến cơ sở dữ liệu
|	['database'] Tên cơ sở dữ liệu bạn muốn kết nối đến
|	['dbdriver'] Trình điều khiển cơ sở dữ liệu, ví dụ: mysqli.
|			Hiện được hỗ trợ:
|				 cubrid, ibase, mssql, mysql, mysqli, oci8,
|				 odbc, pdo, postgre, sqlite, sqlite3, sqlsrv
|	['dbprefix'] Bạn có thể thêm một tiền tố tùy chọn, sẽ được thêm
|				 vào tên bảng khi sử dụng lớp Query Builder
|	['pconnect'] TRUE/FALSE - Có sử dụng kết nối persistent hay không
|	['db_debug'] TRUE/FALSE - Có hiển thị lỗi cơ sở dữ liệu hay không.
|	['cache_on'] TRUE/FALSE - Bật/tắt bộ nhớ đệm truy vấn
|	['cachedir'] Đường dẫn đến thư mục nơi các tệp bộ nhớ đệm nên được lưu trữ
|	['char_set'] Bảng ký tự được sử dụng trong giao tiếp với cơ sở dữ liệu
|	['dbcollat'] So sánh ký tự được sử dụng trong giao tiếp với cơ sở dữ liệu
|				 LƯU Ý: Đối với cơ sở dữ liệu MySQL và MySQLi, cài đặt này chỉ được sử dụng
| 				 như một tùy chọn dự phòng nếu máy chủ của bạn đang chạy PHP < 5.2.3 hoặc MySQL < 5.0.7
|				 (và trong các truy vấn tạo bảng được tạo ra với DB Forge).
| 				 Có sự không tương thích trong PHP với mysql_real_escape_string() có thể
| 				 làm cho trang web của bạn trở nên dễ tổn thương với tấn công SQL nếu bạn đang sử dụng
| 				 một bảng ký tự nhiều byte và đang chạy các phiên bản thấp hơn này.
| 				 Các trang sử dụng bảng ký tự và so sánh Latin-1 hoặc UTF-8 không bị ảnh hưởng.
|	['swap_pre'] Tiền tố bảng mặc định nên được đổi với dbprefix
|	['encrypt']  Có sử dụng kết nối được mã hóa hay không.
|
|			Trình điều khiển 'mysql' (không được khuyến nghị), 'sqlsrv' và 'pdo/sqlsrv' chấp nhận TRUE/FALSE
|			Trình điều khiển 'mysqli' và 'pdo/mysql' chấp nhận một mảng với các tùy chọn sau:
|
|				'ssl_key'    - Đường dẫn đến tệp khóa riêng
|				'ssl_cert'   - Đường dẫn đến tệp chứng chỉ công khai
|				'ssl_ca'     - Đường dẫn đến tệp cơ quan chứng nhận
|				'ssl_capath' - Đường dẫn đến một thư mục chứa các chứng nhận CA đáng tin cậy trong định dạng PEM
|				'ssl_cipher' - Danh sách các mã hóa *cho phép* được sử dụng cho việc mã hóa, được phân tách bằng dấu hai chấm (':')
|				'ssl_verify' - TRUE/FALSE; Có xác minh chứng chỉ máy chủ hay không ('mysqli' chỉ)
|
|	['compress'] Có sử dụng nén từ khách hàng hay không (chỉ MySQL)
|	['stricton'] TRUE/FALSE - buộc kết nối 'Chế độ Nghiêm túc'
|							- tốt để đảm bảo SQL nghiêm túc trong quá trình phát triển
|	['ssl_options'] Được sử dụng để đặt các tùy chọn SSL khác nhau có thể được sử dụng khi tạo kết nối SSL.
|	['failover'] mảng - Một mảng với 0 hoặc nhiều dữ liệu cho kết nối nếu kết nối chính thất bại.
|	['save_queries'] TRUE/FALSE - Có "lưu" tất cả các truy vấn được thực thi hay không.
| 				LƯU Ý: Tắt tính năng này cũng sẽ tắt cả
| 				$this->db->last_query() và việc ghi nhận truy vấn của DB.
| 				Khi bạn chạy một truy vấn, với cài đặt này đặt thành TRUE (mặc định),
| 				CodeIgniter sẽ lưu trữ câu lệnh SQL để gỡ lỗi.
| 				Tuy nhiên, điều này có thể gây tăng sử dụng bộ nhớ cao, đặc biệt nếu bạn chạy
| 				nhiều truy vấn SQL... vô hiệu hóa nó để tránh vấn đề đó.
|
| Biến $active_group cho phép bạn chọn nhóm kết nối nào để
| kích hoạt. Mặc định chỉ có một nhóm (nhóm 'default').
|
| Biến $query_builder cho phép bạn xác định liệu có nên tải
| lớp query builder hay không.
*/
$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => '',
	'database' => 'neoflex',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
