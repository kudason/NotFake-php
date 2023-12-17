<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| AUTO-LOADER
| -------------------------------------------------------------------
| Tệp này chỉ định hệ thống nào nên được tải mặc định.
|
| Để giữ framework nhẹ nhất có thể, chỉ tải tài nguyên tối thiểu tuyệt đối.
| Ví dụ, cơ sở dữ liệu không được kết nối tự động vì không có giả định
| rằng bạn có ý định sử dụng nó. Tệp này cho phép bạn đặt toàn cầu
| xác định hệ thống nào bạn muốn tải với mỗi yêu cầu.
|
| -------------------------------------------------------------------
| Hướng dẫn
| -------------------------------------------------------------------
|
| Đây là những điều bạn có thể tải tự động:
|
| 1. Gói
| 2. Thư viện
| 3. Trình điều khiển
| 4. Tệp trợ giúp
| 5. Tệp cấu hình tùy chỉnh
| 6. Tệp ngôn ngữ
| 7. Mô hình
|
*/

/*
| -------------------------------------------------------------------
|  Auto-load Packages
| -------------------------------------------------------------------
| Mẫu:
|
|  $autoload['packages'] = array(APPPATH.'third_party', '/usr/local/shared');
|
*/
$autoload['packages'] = array();

/*
| -------------------------------------------------------------------
|  Auto-load Libraries
| -------------------------------------------------------------------
| Đây là các lớp nằm trong thư mục system/libraries/ hoặc thư mục của bạn
| application/libraries/, với thêm vào đó của thư viện 'database',
| một trường hợp đặc biệt một chút.
|
| Mẫu:
|
|	$autoload['libraries'] = array('database', 'email', 'session');
|
| Bạn cũng có thể cung cấp tên thư viện thay thế để được gán
| trong điều khiển:
|
|	$autoload['libraries'] = array('user_agent' => 'ua');
*/
$autoload['libraries'] = array('pagination' , 'xmlrpc' , 'form_validation', 'email', 'paypal');

/*
| -------------------------------------------------------------------
|  Auto-load Drivers
| -------------------------------------------------------------------
| Những lớp này nằm trong thư mục system/libraries/ hoặc trong thư mục của bạn
| application/libraries/, nhưng cũng được đặt bên trong
| thư mục con của chúng và chúng mở rộng từ lớp CI_Driver_Library. Chúng
| cung cấp nhiều tùy chọn trình điều khiển có thể hoán đổi.
|
| Mẫu:
|
|	$autoload['drivers'] = array('cache');
|
| Bạn cũng có thể cung cấp tên thuộc tính thay thế để được gán trong
| điều khiển:
|
|	$autoload['drivers'] = array('cache' => 'cch');
|
*/
$autoload['drivers'] = array();

/*
| -------------------------------------------------------------------
|  Auto-load Helper Files
| -------------------------------------------------------------------
| Mẫu:
|
|	$autoload['helper'] = array('url', 'file');
*/
$autoload['helper'] = array('url','file','form','security','string','inflector','directory','download','multi_language', 'common', 'addon');

/*
| -------------------------------------------------------------------
|  Auto-load Config files
| -------------------------------------------------------------------
| Mẫu:
|
|	$autoload['config'] = array('config1', 'config2');
|
| LƯU Ý: Mục này được dành CHỈ nếu bạn đã tạo tệp cấu hình tùy chỉnh
| Nếu không, hãy để nó trống.
|
*/
$autoload['config'] = array();

/*
| -------------------------------------------------------------------
|  Auto-load Language files
| -------------------------------------------------------------------
| Mẫu:
|
|	$autoload['language'] = array('lang1', 'lang2');
|
| LƯU Ý: Đừng bao gồm phần "_lang" của tệp. Ví dụ
| "codeigniter_lang.php" sẽ được tham chiếu như là array('codeigniter');
|
*/
$autoload['language'] = array();

/*
| -------------------------------------------------------------------
|  Auto-load Models
| -------------------------------------------------------------------
| Mẫu:
|
|	$autoload['model'] = array('first_model', 'second_model');
|
| Bạn cũng có thể cung cấp tên mô hình thay thế để được gán
| trong điều khiển:
|
|	$autoload['model'] = array('first_model' => 'first');
*/
$autoload['model'] = array('crud_model', 'addon_model');
