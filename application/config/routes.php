<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| ĐỊNH TUYẾN URI
| -------------------------------------------------------------------------
| Tập tin này cho phép bạn ánh xạ lại yêu cầu URI đến các hàm cụ thể của bộ điều khiển.
|
| Thông thường, có mối quan hệ một-đến-một giữa chuỗi URL
| và lớp/phương thức điều khiển tương ứng của nó. Các đoạn trong một
| URL thường tuân theo mẫu sau:
|
|	example.com/class/method/id/
|
| Tuy nhiên, đôi khi, bạn có thể muốn ánh xạ lại mối quan hệ này
| để một lớp/phương thức khác được gọi hơn là lớp/phương thức
| tương ứng với URL.
|
| Vui lòng xem hướng dẫn người dùng để biết thông tin chi tiết:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| CÁC ĐƯỜNG DẪN DỰ TRỮ
| -------------------------------------------------------------------------
|
| Có ba đường dẫn dự trữ:
|
|	$route['default_controller'] = 'welcome';
|
| Đường dẫn này chỉ định lớp điều khiển nào sẽ được tải nếu
| URI không chứa dữ liệu. Trong ví dụ trên, lớp "welcome"
| sẽ được tải.
|
|	$route['404_override'] = 'errors/page_missing';
|
| Đường dẫn này sẽ cho Router biết lớp/phương thức nào được sử dụng nếu những
| thông tin được cung cấp trong URL không thể được phù hợp với một đường dẫn hợp lệ.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| Đây không phải là một đường dẫn chính xác, nhưng cho phép bạn tự động định tuyến
| tên lớp điều khiển và phương thức chứa dấu gạch ngang. Dấu '-' không phải là một
| ký tự hợp lệ cho tên lớp hoặc phương thức, nên nó cần phải được dịch.
| Khi bạn đặt tùy chọn này thành TRUE, nó sẽ thay thế TẤT CẢ dấu gạch ngang trong
| các đoạn URI của lớp điều khiển và phương thức.
|
| Ví dụ:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
