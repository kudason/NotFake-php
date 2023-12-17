<?php
/*******************************************************************************
 *                      PHP Paypal IPN Integration Class
 *******************************************************************************
 *      Tác giả:     Micah Carrick
 *      Email:      email@micahcarrick.com
 *      Website:    http://www.micahcarrick.com
 *
 *      Tệp:       paypal.class.php
 *      Phiên bản: 1.3.0
 *      Bản quyền: (c) 2005 - Micah Carrick
 *                  Bạn được tự do sử dụng, phân phối và sửa đổi phần mềm này
 *                  theo các điều khoản của Giấy phép Công cộng GNU. Xem tệp
 *                  license.txt kèm theo.
 *
 *******************************************************************************
 * LỊCH SỬ PHIÊN BẢN:
 *      v1.3.0 [10.10.2005] - Sửa lỗi để xử lý dấu nháy đơn một cách chính xác hơn,
 *                            thay vì chỉ loại bỏ chúng. Điều này cần thiết vì
 *                            người dùng vẫn có thể đặt dấu nháy trong một số trường hợp.
 *  
 *      v1.2.1 [06.05.2005] - Sửa lỗi chính tả từ sửa lỗi trước đó :)
 *
 *      v1.2.0 [05.31.2005] - Thêm tính năng tùy chọn để loại bỏ tất cả dấu nháy
 *                            trong các POST từ paypal. IPN có thể trả về không hợp lệ
 *                            đôi khi khi có dấu nháy trong một số trường.
 *
 *      v1.1.0 [05.15.2005] - Sửa đổi đầu ra biểu mẫu trong phương thức submit_paypal_post
 *                            để cho phép các trình duyệt không hỗ trợ JavaScript
 *                            cung cấp phương tiện cho việc gửi biểu mẫu thủ công.
 *
 *      v1.0.0 [04.16.2005] - Phiên bản ban đầu
 *
 *******************************************************************************
 * MÔ TẢ:
 *
 * CHÚ Ý: Xem www.micahcarrick.com để tải phiên bản mới nhất của lớp này
 * cùng với các tệp mẫu và tài liệu khác có liên quan.
 *
 * Tệp này cung cấp một phương pháp gọn gàng và đơn giản để tương tác với paypal và
 * Giao diện Thông báo thanh toán tức thì (IPN) của paypal. Tệp này KHÔNG được dùng để
 * tích hợp paypal "plug 'n' play". Vẫn cần có người phát triển (nên là bạn) hiểu về quá
 * trình paypal và biết các biến bạn muốn/muốn cần truyền cho paypal để đạt được điều bạn muốn.
 * 
 * Lớp này xử lý việc gửi đơn hàng đến paypal cũng như xử lý Thông báo Thanh toán tức thì (IPN).
 *
 * Mã này dựa trên mã nguồn của php-toolkit từ paypal. Tôi đã lấy
 * các nguyên tắc cơ bản và đặt chúng vào một lớp để dễ sử dụng hơn - ít nhất là với tôi.
 * php-toolkit có thể tải xuống từ
 * http://sourceforge.net/projects/paypal.
 *      
 * Để gửi một đơn hàng đến paypal, có form đơn hàng POST tới một tệp với:
 *
 *      $p = new Paypal;
 *      $p->add_field('business', 'somebody@domain.com');
 *      $p->add_field('first_name', $_POST['first_name']);
 *      ... (thêm tất cả các trường của bạn theo cách tương tự)
 *      $p->submit_paypal_post();
 *
 * Để xử lý một IPN, có tệp xử lý IPN của bạn chứa:
 *
 *      $p = new Paypal;
 *      if ($p->validate_ipn()) {
 *      ... (IPN đã được xác minh. Chi tiết nằm trong mảng ipn_data())
 *      }
 *
 * Trong trường hợp bạn mới sử dụng paypal, dưới đây là một số thông tin giúp bạn:
 *
 * 1. Tải về và đọc Hướng dẫn Người bán và Hướng dẫn tích hợp từ
 *    http://www.paypal.com/en_US/pdf/integration_guide.pdf. Điều này cung cấp
 *    cho bạn tất cả thông tin bạn cần, bao gồm các trường bạn có thể truyền cho
 *    paypal (bằng cách sử dụng add_field() với lớp này) cũng như tất cả các trường
 *    được trả về trong một bài đăng IPN (lưu trong mảng ipn_data() trong lớp này).
 *    Nó cũng vẽ biểu đồ quá trình giao dịch hoàn chỉnh.
 *
 * 2. Tạo tài khoản "sandbox" cho người mua và người bán. Đây chỉ là một
 *    tài khoản kiểm tra (test) cho phép bạn thử nghiệm trang web của mình từ
 *    cả hai góc nhìn người bán và người mua. Hướng dẫn cài đặt tài khoản kiểm tra
 *    này có sẵn tại https://developer.paypal.com/ cùng với một diễn đàn tuyệt vời
 *    để bạn có thể đặt tất cả các câu hỏi tích hợp paypal của mình. Đảm bảo bạn tuân
 *    theo tất cả các hướng dẫn trong việc thiết lập môi trường thử nghiệm kiểm tra,
 *    bao gồm việc thêm tài khoản ngân hàng giả và thẻ tín dụng giả.
 * 
 *******************************************************************************
*/

class Paypal {
    
   var $last_error;                 // lưu lỗi gần nhất gặp phải
   
   var $ipn_log;                    // bool: ghi nhật ký IPN vào tệp văn bản?
   
   var $ipn_log_file;               // tên tệp nhật ký IPN
   var $ipn_response;               // lưu trữ phản hồi IPN từ paypal   
   var $ipn_data = array();         // mảng chứa các giá trị POST cho IPN
   
   var $fields = array();           // mảng chứa các trường để gửi cho paypal

   function __construct(){
   }
   
   function Paypal() {
       
      // constructor khởi tạo. Được gọi khi lớp được tạo.
      
      //$this->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
      $this->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
      
      $this->last_error = '';
      
      $this->ipn_log_file = '.ipn_results.log';
      $this->ipn_log = true; 
      $this->ipn_response = '';
      
      // điền mảng $fields với một số giá trị mặc định. Xem tài liệu của paypal
      // để biết danh sách các trường và kiểu dữ liệu của chúng. Các giá trị mặc định này
      // có thể bị ghi đè bởi tập lệnh gọi lớp.
      
      $this->add_field('rm','2');           // Phương thức trả về = POST
      $this->add_field('cmd','_xclick'); 
      
   }
   
   function add_field($field, $value) {
      
      // thêm một cặp key=>value vào mảng $fields, đó là những gì sẽ được
      // gửi đến paypal dưới dạng các biến POST. Nếu giá trị đã có trong
      // mảng, nó sẽ bị ghi đè.
            
      $this->fields["$field"] = $value;
   }

   function submit_paypal_post() {
 
      // hàm này thực sự tạo ra một trang HTML hoàn chỉnh bao gồm
      // một biểu mẫu với các yếu tố ẩn sẽ được gửi đến paypal thông qua
      // thuộc tính onLoad của phần tử BODY. Chúng tôi làm điều này để bạn có thể xác thực
      // bất kỳ biến POST nào từ biểu mẫu tùy chỉnh của bạn trước khi gửi đến paypal.
      // Vì vậy, thực tế, bạn sẽ có biểu mẫu của riêng mình sẽ được gửi đến tập lệnh của bạn
      // để xác minh dữ liệu, sau đó gọi hàm này để tạo
      // một biểu mẫu ẩn khác và gửi đến paypal.
 
      // Người dùng sẽ thấy ngắn gọn một thông báo trên màn hình có nội dung:
      // "Vui lòng đợi, đơn hàng của bạn đang được xử lý..." và sau đó ngay lập tức
      // được chuyển hướng đến paypal.

      echo "<html>\n";
      //echo "<head><title>Processing Payment...</title></head>\n";
      echo "<body onLoad=\"document.forms['paypal_form'].submit();\">\n";
      //echo "<center><h3>";
      //echo " Redirecting to the paypal.</h3></center>\n";
      echo "<form method=\"post\" name=\"paypal_form\" ";
      echo "action=\"".$this->paypal_url."\">\n";

      foreach ($this->fields as $name => $value) {
         echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
      }
        
      echo "</form>\n";
      echo "</body></html>\n";
    
   }

   
   function validate_ipn() {      
      // BƯỚC 1: Đọc dữ liệu POST
  
      // Đọc dữ liệu POST trực tiếp từ $_POST có thể gây ra vấn đề về serialization
      // với dữ liệu mảng trong POST.
      // Đọc dữ liệu POST raw từ luồng đầu vào thay thế.
      $raw_post_data = file_get_contents('php://input');
      $raw_post_array = explode('&', $raw_post_data);
      $myPost = array();
      foreach ($raw_post_array as $keyval) 
      {
          $keyval = explode ('=', $keyval);
          if (count($keyval) == 2)
              $myPost[$keyval[0]] = urldecode($keyval[1]);
      }
      
      // Đọc dữ liệu POST từ hệ thống PayPal và thêm 'cmd'
      $req = 'cmd=_notify-validate';
      if(function_exists('get_magic_quotes_gpc')) 
      {
          $get_magic_quotes_exists = true;
      } 
      foreach ($myPost as $key => $value) 
      {        
          if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) 
          { 
              $value = urlencode(stripslashes($value)); 
          } 
          else 
          {
              $value = urlencode($value);
          }
          $req .= "&$key=$value";
      }
      
      // BƯỚC 2: Gửi dữ liệu IPN trở lại PayPal để xác minh
  
      $ch = curl_init($this->paypal_url);
      curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
      curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
      
      // Trong môi trường giống với WAMP không có chứng chỉ quản trị ca gốc,
      // hãy tải 'cacert.pem' từ "http://curl.haxx.se/docs/caextract.html" và đặt đường dẫn thư mục 
      // của chứng chỉ như dưới đây.
      // curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
      if( !($res = curl_exec($ch)) ) {
          curl_close($ch);
          $this->write_log('curl error');
          exit;
      }
      curl_close($ch);
      
      // BƯỚC 3: Kiểm tra kết quả xác minh IPN và thực hiện tương ứng
  
      if (strcmp ($res, "VERIFIED") == 0) 
      {
          return true;
          // gán các biến POST vào biến cục bộ và thực hiện kiểm tra
      }
      else if (strcmp ($res, "INVALID") == 0) 
      { 
          return false;
      }
  }
  function log_ipn_results($success) {
   // Ghi log kết quả xác minh IPN vào tệp nếu ghi log được bật.
   
   if (!$this->ipn_log) return;  // có ghi log bật không?
   
   // Thời điểm
   $text = '['.date('m/d/Y g:i A').'] - '; 
   
   // Thành công hay thất bại?
   if ($success) $text .= "SUCCESS!\n";
   else $text .= 'FAIL: '.$this->last_error."\n";
   
   // Ghi log các biến POST
   $text .= "IPN POST Vars from Paypal:\n";
   foreach ($this->ipn_data as $key=>$value) {
       $text .= "$key=$value, ";
   }

   // Ghi log phản hồi từ máy chủ PayPal
   $text .= "\nIPN Response from Paypal Server:\n ".$this->ipn_response;
   
   // Ghi vào log
   $fp=fopen($this->ipn_log_file,'a');
   fwrite($fp, $text . "\n\n"); 

   fclose($fp);  // đóng tệp
}
function dump_fields() {
   // Sử dụng để gỡ lỗi, phương thức này sẽ xuất tất cả các cặp trường/giá trị
   // hiện đang được định nghĩa trong thể hiện của lớp bằng cách sử dụng
   // hàm add_field().
   
   echo "<h3>Paypal->dump_fields() Output:</h3>";
   echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
           <tr>
              <td bgcolor=\"black\"><b><font color=\"white\">Field Name</font></b></td>
              <td bgcolor=\"black\"><b><font color=\"white\">Value</font></b></td>
           </tr>"; 
   
   ksort($this->fields);
   foreach ($this->fields as $key => $value) {
       echo "<tr><td>$key</td><td>".urldecode($value)."&nbsp;</td></tr>";
   }

   echo "</table><br>"; 
}