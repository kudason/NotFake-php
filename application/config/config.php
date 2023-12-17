<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL đến thư mục gốc của CodeIgniter. Thông thường, đây sẽ là URL cơ bản của bạn,
| CÓ dấu gạch chéo cuối cùng:
|
|	http://example.com/
|
| CẢNH BÁO: BẠN PHẢI đặt giá trị này!
|
| Nếu nó không được đặt, CodeIgniter sẽ cố đoán giao thức và đường dẫn
| cài đặt của bạn, nhưng do vấn đề bảo mật, tên máy chủ sẽ được đặt
| thành $_SERVER['SERVER_ADDR'] nếu có sẵn, hoặc localhost nếu không.
| Cơ chế tự động phát hiện tồn tại chỉ để tiện lợi trong quá trình
| phát triển và KHÔNG ĐƯỢC sử dụng trong sản xuất!
|
| Nếu bạn cần cho phép nhiều tên miền, hãy nhớ rằng tệp này vẫn là một kịch bản PHP
| và bạn có thể dễ dàng thực hiện điều đó một cách tự nhiên.
|
*/
$config['base_url'] = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$config['base_url'] .= "://".$_SERVER['HTTP_HOST'];
$config['base_url'] .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);

/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
|
| Thường là tệp index.php của bạn, trừ khi bạn đã đổi tên nó thành
| cái gì khác. Nếu bạn đang sử dụng mod_rewrite để xóa trang này, hãy đặt
| biến này để nó trống.
|
*/
$config['index_page'] = '';

/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
|
| Mục này xác định nguyên tắc máy chủ toàn cầu nào sẽ được sử dụng để lấy
| chuỗi URI. Thiết lập mặc định của 'REQUEST_URI' hoạt động cho hầu hết các máy chủ.
| Nếu liên kết của bạn dường như không hoạt động, hãy thử một trong những hương vị ngon:
|
| 'REQUEST_URI'    Sử dụng $_SERVER['REQUEST_URI']
| 'QUERY_STRING'   Sử dụng $_SERVER['QUERY_STRING']
| 'PATH_INFO'      Sử dụng $_SERVER['PATH_INFO']
|
| CẢNH BÁO: Nếu bạn đặt nó thành 'PATH_INFO', URI sẽ luôn được giải mã URL!
*/
$config['uri_protocol']	= 'QUERY_STRING';

/*
|--------------------------------------------------------------------------
| URL suffix
|--------------------------------------------------------------------------
|
| Tùy chọn này cho phép bạn thêm hậu tố vào tất cả các URL được tạo ra bởi CodeIgniter.
| Để biết thêm thông tin, vui lòng xem hướng dẫn người dùng:
|
| https://codeigniter.com/user_guide/general/urls.html
*/
$config['url_suffix'] = '';

/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
|
| Điều này xác định bộ tệp ngôn ngữ nào sẽ được sử dụng mặc định. Hãy chắc chắn
| có một bản dịch có sẵn nếu bạn dự định sử dụng cái gì đó khác
| ngoại trừ tiếng Anh.
|
*/
$config['language']	= 'english';

/*
|--------------------------------------------------------------------------
| Default Character Set
|--------------------------------------------------------------------------
|
| Điều này xác định bảng mã ký tự nào được sử dụng mặc định trong các phương thức khác nhau
| yêu cầu bảng mã ký tự cung cấp.
|
| Xem http://php.net/htmlspecialchars để xem danh sách bảng mã được hỗ trợ.
|
*/
$config['charset'] = 'UTF-8';

/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
|
| Nếu bạn muốn sử dụng tính năng 'hooks', bạn phải bật nó bằng cách
| thiết lập biến này thành TRUE (boolean). Xem hướng dẫn để biết chi tiết.
|
*/
$config['enable_hooks'] = FALSE;

/*
|--------------------------------------------------------------------------
| Class Extension Prefix
|--------------------------------------------------------------------------
|
| Mục này cho phép bạn đặt tiền tố tên tệp/tên lớp khi mở rộng
| thư viện native. Để biết thêm thông tin, vui lòng xem hướng dẫn người dùng:
|
| https://codeigniter.com/user_guide/general/core_classes.html
| https://codeigniter.com/user_guide/general/creating_libraries.html
|
*/
$config['subclass_prefix'] = 'MY_';

/*
|--------------------------------------------------------------------------
| Composer auto-loading
|--------------------------------------------------------------------------
|
| Bật cài đặt này sẽ nói với CodeIgniter tìm một kịch bản tự động tải Composer
| package ở application/vendor/autoload.php.
|
|	$config['composer_autoload'] = TRUE;
|
| Hoặc nếu bạn có thư mục vendor/ của mình ở một nơi khác,
| bạn cũng có thể chọn để đặt một đường dẫn cụ thể:
|
|	$config['composer_autoload'] = '/path/to/vendor/autoload.php';
|
| Đối với thông tin thêm về Composer, vui lòng truy cập http://getcomposer.org/
|
| Lưu ý: Điều này SẼ KHÔNG tắt hoặc ghi đè autoload cụ thể cho CodeIgniter
|	tự động (application/config/autoload.php)
*/
$config['composer_autoload'] = FALSE;

/*
|--------------------------------------------------------------------------
| Allowed URL Characters
|--------------------------------------------------------------------------
|
| Cho phép bạn xác định những ký tự được phép trong URL.
| Khi ai đó cố gắng gửi một URL chứa ký tự không được phép, họ sẽ
| nhận một thông báo cảnh báo.
|
| Như biện pháp an ninh, BẠN NÊN hạn chế URL chỉ đến
| ít ký tự nhất có thể. Theo mặc định chỉ có những ký tự này được phép: a-z 0-9~%.:_-
|
| Để trống để cho phép tất cả các ký tự - nhưng chỉ nếu bạn điên.
|
| Giá trị được cấu hình thực sự là một nhóm ký tự biểu thức chính qui
| và nó sẽ được thực thi như sau: ! preg_match('/^[<permitted_uri_chars>]+$/i
|
| KHÔNG THAY ĐỔI ĐIỀU NÀY TRỪ KHI BẠN ĐÃ HIỂU RÕ VỀ CÁC HẬU QUẢ!!
|
*/
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
//$config['permitted_uri_chars'] = '';

/*
|--------------------------------------------------------------------------
| Enable Query Strings
|--------------------------------------------------------------------------
|
| Theo mặc định, CodeIgniter sử dụng URL dựa trên đoạn thân thiện với công cụ tìm kiếm:
| example.com/who/what/where/
|
| Bạn có thể tùy chọn bật URL dựa trên chuỗi truy vấn tiêu chuẩn:
| example.com?who=me&what=something&where=here
|
| Các tùy chọn là: TRUE hoặc FALSE (boolean)
|
| Các mục khác cho phép bạn đặt các 'từ' chuỗi truy vấn
| kích hoạt các bộ điều khiển và chức năng của bạn:
| example.com/index.php?c=controller&m=function
|
| Lưu ý rằng một số trợ giúp viên có thể không hoạt động như mong đợi khi
| tính năng này được bật, vì CodeIgniter được thiết kế chủ yếu để
| sử dụng URL dựa trên đoạn.
|
*/
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';

/*
|--------------------------------------------------------------------------
| Allow $_GET array
|--------------------------------------------------------------------------
|
| Theo mặc định, CodeIgniter kích hoạt quyền truy cập vào mảng $_GET. Nếu vì một số
| lý do bạn muốn tắt nó, đặt 'allow_get_array' thành FALSE.
|
| CẢNH BÁO: TÍNH NĂNG NÀY ĐÃ BỊ LOẠI BỎ và hiện chỉ
|          có sẵn cho mục đích tương thích ngược!
|
*/
$config['allow_get_array'] = TRUE;

/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
|
| Bạn có thể bật ghi lỗi bằng cách đặt một ngưỡng lớn hơn không. Ngưỡng
| quyết định cái gì được ghi vào nhật ký. Các tùy chọn ngưỡng là:
|
|	0 = Tắt ghi lỗi, Ghi lỗi ĐÃ TẮT
|	1 = Các thông báo lỗi (bao gồm lỗi PHP)
|	2 = Các thông báo gỡ lỗi
|	3 = Các thông báo thông tin
|	4 = Tất cả các thông báo
|
| Bạn cũng có thể chuyển một mảng với các cấp độ ngưỡng để hiển thị các loại lỗi cá nhân
|
| 	array(2) = Các thông báo gỡ lỗi, không có Các thông báo lỗi
|
| Đối với một trang web trực tiếp, bạn thường chỉ bật Lỗi (1) để được ghi lại, nếu không
| các tệp nhật ký của bạn sẽ nhanh chóng đầy.
|
*/
$config['log_threshold'] = 0;

/*
|--------------------------------------------------------------------------
| Error Logging Directory Path
|--------------------------------------------------------------------------
|
| Để trống nếu bạn không muốn đặt cái gì đó khác so với mặc định
| thư mục application/logs/. Sử dụng một đường dẫn máy chủ đầy đủ với dấu gạch chéo kết thúc.
|
*/
$config['log_path'] = '';

/*
|--------------------------------------------------------------------------
| Log File Extension
|--------------------------------------------------------------------------
|
| Phần mở rộng tên tệp mặc định cho các tệp nhật ký. Phần mở rộng 'php' cho phép
| bảo vệ các tệp nhật ký thông qua việc viết kịch bản cơ bản, khi chúng được lưu trữ
| dưới một thư mục có thể truy cập công khai.
|
| Lưu ý: Để trống sẽ mặc định là 'php'.
|
*/
$config['log_file_extension'] = '';

/*
|--------------------------------------------------------------------------
| Log File Permissions
|--------------------------------------------------------------------------
|
| Quyền hệ thống tệp để áp dụng cho các tệp nhật ký mới được tạo ra.
|
| QUAN TRỌNG: ĐIỀU NÀY PHẢI là một số nguyên (không có dấu ngoặc) và BẠN PHẢI sử dụng hệ thống bát phân
|            chỉ số octal (ví dụ: 0700, 0644, vv.)
*/
$config['log_file_permissions'] = 0644;

/*
|--------------------------------------------------------------------------
| Date Format for Logs
|--------------------------------------------------------------------------
|
| Mỗi mục được ghi có một ngày liên kết. Bạn có thể sử dụng các mã ngày PHP
| để đặt định dạng ngày của riêng mình
|
*/
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
|--------------------------------------------------------------------------
| Error Views Directory Path
|--------------------------------------------------------------------------
|
| Để trống nếu bạn không muốn đặt cái gì đó khác so với mặc định
| thư mục application/views/errors/. Sử dụng một đường dẫn máy chủ đầy đủ với dấu gạch chéo kết thúc.
|
*/
$config['error_views_path'] = '';
/*
|--------------------------------------------------------------------------
| Cache Directory Path
|--------------------------------------------------------------------------
|
| Đường dẫn thư mục lưu trữ bộ nhớ cache. Để trống nếu bạn muốn sử dụng thư mục mặc định
| application/cache/. Sử dụng một đường dẫn máy chủ đầy đủ với dấu gạch chéo kết thúc.
|
*/
$config['cache_path'] = '';

/*
|--------------------------------------------------------------------------
| Cache Include Query String
|--------------------------------------------------------------------------
|
| Xác định xem có sử dụng chuỗi truy vấn URL khi tạo các tệp cache kết quả hay không.
| Các tùy chọn hợp lệ là:
|
|	FALSE      = Tắt
|	TRUE       = Bật, sử dụng tất cả các tham số truy vấn.
|	             Hãy nhớ rằng điều này có thể dẫn đến việc tạo ra nhiều tệp cache
|	             cho cùng một trang lặp đi lặp lại.
|	array('q') = Bật, nhưng chỉ sử dụng danh sách chỉ định
|	             các tham số truy vấn.
|
*/
$config['cache_query_string'] = FALSE;

/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
|
| Nếu bạn sử dụng lớp Encryption, bạn phải đặt một khóa mã hóa.
| Xem hướng dẫn người dùng để biết thêm thông tin.
|
| https://codeigniter.com/user_guide/libraries/encryption.html
|
*/
$config['encryption_key'] = 'creativeitem';

/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
|
| 'sess_driver'
|
|	Trình điều khiển lưu trữ để sử dụng: files, database, redis, memcached
|
| 'sess_cookie_name'
|
|	Tên cookie phiên, chỉ được chứa các ký tự [0-9a-z_-]
|
| 'sess_expiration'
|
|	Số GIÂY bạn muốn phiên này kéo dài.
|	Đặt thành 0 (không) có nghĩa là hết hạn khi trình duyệt được đóng.
|
| 'sess_save_path'
|
|	Vị trí để lưu trữ phiên, phụ thuộc vào trình điều khiển.
|
|	Đối với trình điều khiển 'files', đó là đường dẫn đến thư mục có thể ghi.
|	CẢNH BÁO: Chỉ hỗ trợ các đường dẫn tuyệt đối!
|
|	Đối với trình điều khiển 'database', đó là tên bảng.
|	Vui lòng đọc hướng dẫn để biết định dạng với các trình điều khiển phiên khác.
|
|	QUAN TRỌNG: Bạn PHẢI đặt một đường dẫn lưu hợp lệ!
|
| 'sess_match_ip'
|
|	Xác định xem có phải khớp địa chỉ IP của người dùng khi đọc dữ liệu phiên hay không.
|
|	CẢNH BÁO: Nếu bạn đang sử dụng trình điều khiển cơ sở dữ liệu, đừng quên cập nhật
|	         PRIMARY KEY của bảng phiên của bạn khi thay đổi cài đặt này.
|
| 'sess_time_to_update'
|
|	Bao nhiêu giây giữa lần CI tái tạo ID phiên.
|
| 'sess_regenerate_destroy'
|
|	Xác định xem có hủy dữ liệu phiên liên quan đến ID phiên cũ
|	khi tự động tái tạo ID phiên hay không. Khi đặt thành FALSE, dữ liệu
|	sẽ sau đó bị xóa bởi bộ thu gom rác.
|
| Các thiết lập cookie phiên khác được chia sẻ với phần còn lại của ứng dụng,
| ngoại trừ 'cookie_prefix' và 'cookie_httponly', không ảnh hưởng ở đây.
|
*/
$config['sess_driver'] = 'database';
$config['sess_cookie_name'] = 'ci_session';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = 'ci_sessions';
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = FALSE;
/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
|
| 'cookie_prefix'   = Tiền tố tên cookie để tránh xung đột
| 'cookie_domain'   = Đặt thành .your-domain.com cho các cookie trên toàn trang web
| 'cookie_path'     = Thường là dấu gạch chéo xuôi
| 'cookie_secure'   = Cookie chỉ được đặt nếu có kết nối HTTPS an toàn.
| 'cookie_httponly' = Cookie chỉ có thể truy cập qua HTTP(S) (không có javascript)
|
| Lưu ý: Các thiết lập này (ngoại trừ 'cookie_prefix' và
|       'cookie_httponly') cũng ảnh hưởng đến các phiên.
|
*/
$config['cookie_prefix']	= '';
$config['cookie_domain']	= '';
$config['cookie_path']		= '/';
$config['cookie_secure']	= FALSE;
$config['cookie_httponly'] 	= FALSE;

/*
|--------------------------------------------------------------------------
| Standardize newlines
|--------------------------------------------------------------------------
|
| Xác định liệu có chuẩn hóa các ký tự xuống dòng trong dữ liệu đầu vào hay không,
| có nghĩa là thay thế \r\n, \r, \n bằng giá trị PHP_EOL.
|
| CẢNH BÁO: Tính năng này ĐÃ BỊ LOẠI BỎ và hiện chỉ có sẵn
|          vì mục đích tương thích ngược!
|
*/
$config['standardize_newlines'] = FALSE;

/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
|
| Xác định liệu bộ lọc XSS có luôn hoạt động khi gặp dữ liệu GET, POST hoặc
| COOKIE hay không.
|
| CẢNH BÁO: Tính năng này ĐÃ BỊ LOẠI BỎ và hiện chỉ có sẵn
|          vì mục đích tương thích ngược!
|
*/
$config['global_xss_filtering'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cross Site Request Forgery
|--------------------------------------------------------------------------
| Bật việc thiết lập một token cookie CSRF. Khi đặt thành TRUE, token sẽ được
| kiểm tra trên một biểu mẫu được gửi. Nếu bạn chấp nhận dữ liệu người dùng, nên
| bật bảo vệ CSRF.
|
| 'csrf_token_name' = Tên token
| 'csrf_cookie_name' = Tên cookie
| 'csrf_expire' = Số giây token nên hết hạn.
| 'csrf_regenerate' = Tái tạo token sau mỗi lần gửi
| 'csrf_exclude_uris' = Mảng các URIs được loại trừ khỏi kiểm tra CSRF
*/
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'creativeitem';
$config['csrf_cookie_name'] = 'creativeitem';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array();

/*
|--------------------------------------------------------------------------
| Output Compression
|--------------------------------------------------------------------------
|
| Kích hoạt nén đầu ra Gzip để tăng tốc độ tải trang. Khi được kích hoạt,
| lớp đầu ra sẽ kiểm tra xem máy chủ của bạn có hỗ trợ Gzip hay không.
| Tuy nhiên, không phải tất cả các trình duyệt đều hỗ trợ nén
| vì vậy chỉ bật nó nếu bạn khá chắc chắn rằng khách truy cập của bạn có thể xử lý nó.
|
| Chỉ sử dụng nếu zlib.output_compression được tắt trong php.ini của bạn.
| Vui lòng không sử dụng nó cùng lúc với việc nén đầu ra cấp độ httpd.
|
| RẤT QUAN TRỌNG:  Nếu bạn nhận được một trang trắng khi nén được bật
| điều đó có nghĩa là bạn đang xuất sớm một cái gì đó ra trình duyệt của bạn.
| Thậm chí có thể là một dòng trắng ở cuối một trong những kịch bản của bạn.
| Để nén hoạt động, không có gì được gửi trước khi bộ đệm đầu ra được gọi
| bởi lớp đầu ra.  Không được 'echo' bất kỳ giá trị nào khi nén được bật.
|
*/
$config['compress_output'] = FALSE;

/*
|--------------------------------------------------------------------------
| Master Time Reference
|--------------------------------------------------------------------------
|
| Các tùy chọn là 'local' hoặc bất kỳ múi giờ được hỗ trợ bởi PHP nào. Lựa chọn này cho biết
| hệ thống có nên sử dụng thời gian cục bộ của máy chủ của bạn như là 'now'
| tham chiếu, hoặc chuyển đổi nó thành múi giờ đã được cấu hình. Xem trang 'date
| helper' trong hướng dẫn sử dụng để biết thông tin về xử lý ngày.
|
*/
$config['time_reference'] = 'local';

/*
|--------------------------------------------------------------------------
| Rewrite PHP Short Tags
|--------------------------------------------------------------------------
|
| Nếu cài đặt PHP của bạn không bật hỗ trợ thẻ ngắn, CI
| có thể tự động viết lại các thẻ ngắn, cho phép bạn sử dụng cú pháp đó
| trong các tệp xem của bạn. Tùy chọn là TRUE hoặc FALSE (boolean)
|
| Lưu ý: Bạn cần bật eval() để tính năng này hoạt động.
|
*/
$config['rewrite_short_tags'] = FALSE;

/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
|
| Nếu máy chủ của bạn đứng sau một proxy nghịch đảo, bạn phải liệt kê danh sách trắng
| địa chỉ IP proxy mà CodeIgniter nên tin tưởng các tiêu đề như
| HTTP_X_FORWARDED_FOR và HTTP_CLIENT_IP để xác định đúng
| địa chỉ IP của khách truy cập.
|
| Bạn có thể sử dụng cả một mảng hoặc một danh sách được phân tách bằng dấu phẩy của địa chỉ proxy,
| cũng như chỉ định toàn bộ mạng con. Dưới đây là một vài ví dụ:
|
| Phân tách bằng dấu phẩy: '10.0.1.200,192.168.5.0/24'
| Mảng:		array('10.0.1.200', '192.168.5.0/24')
*/
$config['proxy_ips'] = '';
