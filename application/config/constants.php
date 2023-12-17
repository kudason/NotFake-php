<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| Nếu được đặt là TRUE, một dấu vết sẽ được hiển thị cùng với lỗi php. Nếu
| error_reporting được vô hiệu hóa, dấu vết sẽ không hiển thị, bất kể
| cài đặt này
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| Những giá trị này được sử dụng khi kiểm tra và đặt chế độ khi làm việc
| với hệ thống tệp tin. Giá trị mặc định là phù hợp trên máy chủ với bảo mật đúng,
| nhưng bạn có thể muốn (hoặc thậm chí cần) thay đổi giá trị trong
| môi trường cụ thể (Apache chạy một quy trình riêng cho mỗi
| người dùng, PHP dưới CGI với Apache suEXEC, v.v.). Giá trị octal nên
| luôn được sử dụng để đặt chế độ chính xác.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| Những chế độ này được sử dụng khi làm việc với fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Được sử dụng để chỉ định điều kiện dưới đó kịch bản đang exit()ing.
| Mặc dù không có tiêu chuẩn chung cho mã lỗi, có một số
| giao thức rộng. Dưới đây là ba giao thức chính được sử dụng để xác định
| mã lỗi thoát:
|
|    Thư viện Tiêu chuẩn C/C++ (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (Liên kết này cũng chứa các giao thức đặc biệt của GNU khác)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // không có lỗi
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // lỗi chung
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // lỗi cấu hình
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // tệp không tìm thấy
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // lớp không tìm thấy
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // thành viên lớp không tìm thấy
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // đầu vào người dùng không hợp lệ
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // lỗi cơ sở dữ liệu
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // mã lỗi được gán tự động thấp nhất
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // mã lỗi được gán tự động cao nhất
