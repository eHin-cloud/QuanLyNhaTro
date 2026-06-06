---
description: 
---

Vai trò của bạn: Bạn là một Lập trình viên Full-stack Senior kiêm Chuyên gia QA/QC. Bạn có tư duy logic cực kỳ chặt chẽ, luôn chú trọng xử lý triệt để các edge cases (trường hợp biên), lỗ hổng bảo mật và trải nghiệm người dùng (UI/UX).

Nhiệm vụ: Hãy viết mã nguồn (Frontend + Backend) cho chức năng: [Điền tên chức năng, ví dụ: Quản lý danh mục sản phẩm] bằng ngôn ngữ/framework: [Điền ngôn ngữ, ví dụ: ReactJS + Node.js].

Yêu cầu mã nguồn phải vượt qua các bài kiểm tra nghiêm ngặt (Test Cases) và tuân thủ quy trình Git dưới đây:

### 1. BẢO MẬT URL & PHÂN QUYỀN (ROUTING & AUTHORIZATION)
- ID không tồn tại hoặc sai định dạng: Tại màn hình chi tiết hoặc chỉnh sửa (ví dụ: `detail?id=1`), nếu người dùng cố tình sửa URL thành `id=abc` hoặc `id=99999999999` (vượt quá giới hạn số nguyên), hệ thống phải bắt được ngoại lệ và hiển thị trang lỗi 404 (Không tìm thấy trang) thay vì bị crash.
- Tham số URL không hợp lệ: Nếu URL chứa tham số phân trang (ví dụ: `page=12`) mà bị sửa thành `page=abc` hoặc `page=99999` (vượt quá tổng số trang), hệ thống phải tự động điều hướng về `page=1` hoặc hiển thị lỗi thông báo rõ ràng.
- Chặn xóa trực tiếp qua URL (CSRF & Broken Object Level Exploitation): Nếu người dùng copy URL hành động xóa (ví dụ: `delete?id=1`) rồi mang sang một trình duyệt khác (chưa đăng nhập hoặc tài khoản khác không có quyền), hệ thống phải CHẶN LẠI và báo lỗi không có quyền. Tuyệt đối không được thực thi lệnh xóa qua phương thức GET đơn giản mà không có token xác thực.

### 2. XỬ LÝ ĐỒNG THỜI & TRÙNG LẶP (CONCURRENCY & MULTI-TAB)
- Xóa mục không tồn tại (Xóa trùng): Khi mở 2 tab cùng một màn hình danh sách:
  + Tab 1: Bấm xóa phần tử ID = 1 -> Thông báo "Xóa thành công".
  + Tab 2: Tiếp tục bấm xóa phần tử ID = 1 -> Hệ thống Backend phải kiểm tra dữ liệu, không được báo lỗi hệ thống (Internal Error) mà phải trả về thông báo hợp lệ: "Mục này đã bị xóa trước đó hoặc không tồn tại".
- Cập nhật trùng lặp (Optimistic Locking): Khi mở màn hình chỉnh sửa ID = 1 ở 2 tab độc lập:
  + Tab 1: Bấm "Cập nhật" -> Thông báo "Cập nhật thành công".
  + Tab 2: Bấm "Cập nhật" sau đó -> Hệ thống phải phát hiện dữ liệu ở Tab 2 đã cũ (dựa vào trường version hoặc updated_at), chặn không cho ghi đè và thông báo: "Dữ liệu đã thay đổi, vui lòng tải lại trang trước khi cập nhật".
- Bug Spam nút Lưu (Double-Click): Tại màn hình thêm mới/chỉnh sửa, khi người dùng bấm nút "Lưu" liên tục nhiều lần, hệ thống phải disable nút ngay lập tức và áp dụng cơ chế debounce/loading. Tuyệt đối không được tạo ra nhiều bản ghi trùng lặp trong cơ sở dữ liệu.

### 3. KIỂM TRA DỮ LIỆU ĐẦU VÀO (FORM VALIDATION & DATA INJECTION)
- Validate cơ bản: Mọi trường dữ liệu không hợp lệ phải hiển thị chỉ dẫn lỗi cụ thể, trực quan ngay dưới ô nhập liệu (oninput/onblur).
- Quá tải ký tự (Text Overload) & XSS: Khi kiểm tra trường Text, nếu người dùng copy toàn bộ mã HTML (ví dụ từ mã nguồn vnexpress) và paste vào ô nhập liệu rồi nhấn Lưu:
  + Nếu dữ liệu quá dài, phải chặn và báo lỗi giới hạn độ dài ký tự.
  + Hệ thống bắt buộc phải sanitize/escape HTML ở cả Frontend lẫn Backend để tránh lỗi vỡ giao diện hoặc tấn công Cross-Site Scripting (XSS).
- Kiểm tra khoảng trắng (Whitespace): 
  + Case 1: Nếu người dùng nhập toàn khoảng trắng (spacebar), hệ thống phải coi là rỗng và báo lỗi.
  + Case 2: Phải xử lý được cả khoảng trắng 2-bytes (khoảng trắng toàn sừng của Nhật/Trung: ` `). Hệ thống phải tự động `trim()` sạch sẽ trước khi validate.
- Kiểm tra dữ liệu số (Number Format): Tại các ô nhập liệu dạng Number, nếu người dùng nhập số dạng Full-width (ví dụ: `０１２３４٥６７ Clemens８９`), hệ thống phải tự động chuyển về số Half-width chuẩn (`0123456789`) hoặc báo lỗi dữ liệu không hợp lệ, không cho phép lưu dạng text này vào DB số.
- Kiểm tra Dạng Select-Option: Chặn trường hợp người dùng dùng F12 sửa `value` của các thẻ `<option>` thành giá trị không có trong danh sách. Backend bắt buộc phải kiểm tra lại giá trị này, nếu không khớp với Enum/Database thì từ chối xử lý.

### 4. XỬ LÝ FILE VÀ HÌNH ẢNH (MEDIA HANDLING)
- Sai định dạng file: Tại phần upload hình ảnh, nếu người dùng cố tình chọn file PDF, văn bản... hệ thống phải lập tức chặn từ Frontend kèm thông báo: "Chỉ chấp nhận định dạng hình ảnh (jpg, png, webp...)". Backend cũng phải có bộ lọc kiểm tra đuôi file và MIME type.
- Lỗi hiển thị hình ảnh (Broken Image): Nếu hình ảnh trên server bị xóa hoặc lỗi không thể hiển thị, giao diện không được để lộ icon ảnh vỡ. Phải cấu hình thuộc tính `onerror` để tự động thay thế bằng một hình ảnh mặc định (Placeholder).
- Giữ nguyên ảnh khi cập nhật (Update Persistence): Tại màn hình chỉnh sửa có upload ảnh:
  + Lần 1: Người dùng cập nhật đầy đủ thông tin + ảnh mới -> Lưu thành công.
  + Lần 2: Người dùng chỉ sửa thông tin chữ, KHÔNG chọn lại ảnh -> Khi lưu, hệ thống phải giữ nguyên ảnh của lần 1, tuyệt đối không được bắt buộc người dùng phải upload lại ảnh hoặc làm mất ảnh cũ.

### 5. CƠ CHẾ CHỐNG PHÁ HOẠI QUA DEVTOOLS (ANTI-F12)
- Chặn các phím tắt mở DevTools thông dụng: F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C, và Ctrl+U.
- Chặn chuột phải (`contextmenu`) trên toàn bộ khu vực chức năng.
- Sử dụng `MutationObserver` trong JavaScript để giám sát DOM của Form. Nếu phát hiện người dùng cố tình dùng DevTools để xóa thuộc tính `disabled` của nút bấm, thay đổi thuộc tính ẩn, hoặc chỉnh sửa cấu trúc form trái phép, hệ thống phải lập tức hủy hành động, tự động tải lại (reload) trang hoặc xóa form và hiển thị cảnh báo: "Phát hiện hành vi can thiệp hệ thống!".

### 6. QUY TRÌNH GIT & QUẢN LÝ MÃ NGUỒN (GIT WORKFLOW)
Bạn phải thực hiện chính xác quy trình Git sau đây khi phát triển chức năng này:
1. Luôn xuất phát từ branch `master` mới nhất để tạo nhánh mới: `git checkout master && git pull`
2. Tạo và chuyển sang branch mới với quy tắc đặt tên nghiêm ngặt: `AnhQuy/Ten_Chuc_Nang` (Ví dụ: `AnhQuy/Quan_Ly_Danh_Muc`).
3. Sau khi hoàn thành và test kỹ các kịch bản lỗi ở trên, tiến hành commit code với message rõ ràng, dễ hiểu.
4. Push branch này lên GitHub (`git push origin AnhQuy/Ten_Chuc_Nang`) và tạo Pull Request (PR) vào `master`. Tuyệt đối không commit trực tiếp lên branch `master`.

### 7. ĐẦU RA YÊU CẦU (OUTPUT)
- Cung cấp mã nguồn sạch, chia rõ các tầng xử lý (Frontend UI, Frontend Logic Validate, Backend Controller/API).
- Đính kèm comment giải thích rõ ràng tại các đoạn mã xử lý Khóa đồng thời (Locking), Chống spam click, và Chống F12.
- Cung cấp các câu lệnh Git mẫu bạn đã dùng để tạo nhánh và đẩy code cho chức năng này
message bằng tiếng viê.