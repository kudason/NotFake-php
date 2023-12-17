<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Bật/Tắt Di chuyển
|--------------------------------------------------------------------------
|
| Di chuyển được tắt mặc định vì lý do bảo mật.
| Bạn nên bật di chuyển mỗi khi bạn có ý định thực hiện di chuyển schema
| và tắt lại sau khi bạn hoàn thành.
|
*/
$config['migration_enabled'] = FALSE;

/*
|--------------------------------------------------------------------------
| Loại Di chuyển
|--------------------------------------------------------------------------
|
| Tên tệp di chuyển có thể được dựa trên một định danh tuần tự hoặc
| một dấu thời gian. Các tùy chọn là:
|
|   'sequential' = Đặt tên di chuyển theo thứ tự tuần tự (001_add_blog.php)
|   'timestamp'  = Đặt tên di chuyển theo dấu thời gian (20121031104401_add_blog.php)
|                  Sử dụng định dạng dấu thời gian YYYYMMDDHHIISS.
|
| Lưu ý: Nếu giá trị cấu hình này bị thiếu, thư viện Di chuyển
|       mặc định là 'sequential' để tương thích ngược với CI2.
|
*/
$config['migration_type'] = 'timestamp';

/*
|--------------------------------------------------------------------------
| Bảng Di chuyển
|--------------------------------------------------------------------------
|
| Đây là tên bảng sẽ lưu trạng thái di chuyển hiện tại.
| Khi di chuyển chạy, nó sẽ lưu trữ trong bảng cơ sở dữ liệu này mức di chuyển
| hệ thống đang ở. Sau đó, nó so sánh mức di chuyển trong bảng này
| với $config['migration_version'] nếu chúng không giống nhau nó
| sẽ di chuyển lên. Điều này phải được đặt.
|
*/
$config['migration_table'] = 'migrations';

/*
|--------------------------------------------------------------------------
| Tự động Di chuyển Đến Mới Nhất
|--------------------------------------------------------------------------
|
| Nếu đặt thành TRUE khi bạn tải lớp di chuyển và có
| $config['migration_enabled'] được đặt thành TRUE, hệ thống sẽ tự động di chuyển
| đến di chuyển mới nhất của bạn (bất kể $config['migration_version'] là
| đặt thành). Điều này giúp bạn không cần phải gọi di chuyển ở bất kỳ nơi nào khác
| trong mã code của bạn để có di chuyển mới nhất.
|
*/
$config['migration_auto_latest'] = FALSE;

/*
|--------------------------------------------------------------------------
| Phiên bản Di chuyển
|--------------------------------------------------------------------------
|
| Được sử dụng để đặt phiên bản di chuyển mà hệ thống tệp tin nên sử dụng.
| Nếu bạn chạy $this->migration->current() đây là phiên bản mà schema sẽ
| được nâng cấp / hạ cấp đến.
|
*/
$config['migration_version'] = 0;

/*
|--------------------------------------------------------------------------
| Đường Dẫn Di chuyển
|--------------------------------------------------------------------------
|
| Đường dẫn đến thư mục di chuyển của bạn.
| Thông thường, nó sẽ ở trong đường dẫn ứng dụng của bạn.
| Ngoài ra, cần có quyền ghi trong đường dẫn di chuyển.
|
*/
$config['migration_path'] = APPPATH.'migrations/';
