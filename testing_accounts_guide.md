# 📘 Hướng Dẫn Kiểm Thử Hệ Thống SmartRoom (Renty Portal & Dashboards)

Tài liệu này hướng dẫn chi tiết cách sử dụng các tài khoản demo được tự động sinh ra từ **FullDemoSeeder** sau khi nâng cấp cấu trúc đa chủ trọ và đa cư dân nhằm giúp bạn kiểm thử toàn diện mọi tính năng phân quyền của hệ thống.

---

## 🔑 Danh Sách Tài Khoản Kiểm Thử (Tất cả mật khẩu mặc định là: `password`)

### 1. Hệ Thống Admin & Chủ Trọ Chưa Xác Minh
| STT | Username | Vai Trò | Phạm Vi & Mục Đích Kiểm Thử |
| :--- | :--- | :--- | :--- |
| **1** | `superadmin` | **Admin hệ thống** | Duyệt/từ chối KYC chủ trọ, xem nhật ký kiểm duyệt (Audit Logs), xem báo cáo doanh thu toàn hệ thống. |
| **2** | `unverified-landlord` | **Chủ trọ chưa xác minh** | Trải nghiệm luồng gửi tài liệu KYC, đăng ký phòng trọ mới, chờ duyệt. |

### 2. Nhóm Tài Khoản Quản Trị Của Mỗi Tenant (10 Tenant mẫu)
Với mỗi vùng trong số 10 Tenant (ví dụ Tenant 1: Cầu Giấy, Tenant 2: Quận 10...), chúng tôi đã tạo **4 tài khoản quản trị đồng thời** để kiểm thử phân quyền:
- **Chủ trọ chính:** `demo-landlord-X` (ví dụ: `demo-landlord-1`, `demo-landlord-2`,...)
- **Chủ trọ phụ (Co-owner):** `demo-landlord-X-co` (ví dụ: `demo-landlord-1-co`, `demo-landlord-2-co`,...)
- **Nhân viên quản lý 1 (Staff 1):** `demo-manager-X-1` (ví dụ: `demo-manager-1-1`, `demo-manager-1-2`,...)
- **Nhân viên quản lý 2 (Staff 2):** `demo-manager-X-2` (ví dụ: `demo-manager-1-2`, `demo-manager-2-2`,...)

### 3. Cư Dân Thuê Phòng (Đa cư dân trên mỗi phòng trọ)
Mỗi phòng trọ có khách thuê (trạng thái `occupied` hoặc `overdue`) được gán **2 tài khoản cư dân đăng nhập độc lập**:
- **Cư dân chính (Lessor/Đứng tên hợp đồng):** `demo-resident-X-1` (ví dụ: `demo-resident-101-1`, `demo-resident-102-1`,...)
- **Cư dân ở ghép (Co-resident/Chung phòng):** `demo-resident-X-2` (ví dụ: `demo-resident-101-2`, `demo-resident-102-2`,...)

### 4. Khách Tìm Phòng
- **Khách vãng lai:** `demo-guest` (sử dụng để trải nghiệm Renty Portal, bình luận và đặt phòng).

---

## 📋 Kịch Bản Kiểm Thử Nâng Cao (Multi-User Test Cases)

### 1. Kiểm thử Đồng sở hữu (Co-owner) & Nhân viên quản lý (Staff)
- **Kịch bản**: 
  - Đăng nhập tài khoản chủ trọ chính `demo-landlord-1` để tạo hóa đơn điện nước mới cho phòng 101.
  - Đăng nhập tài khoản nhân viên `demo-manager-1-1` để xác nhận sửa chữa một yêu cầu báo hỏng (ticket) do cư dân gửi lên.
  - Đăng nhập tài khoản chủ trọ đồng sở hữu `demo-landlord-1-co` để kiểm tra hóa đơn và ticket đó có được cập nhật đồng bộ theo thời gian thực (real-time) hay không.
- **Mục tiêu**: Đảm bảo toàn bộ nhóm quản trị trong cùng 1 Tenant đều nhìn thấy cùng một lượng dữ liệu phòng trọ, tòa nhà, và cập nhật trạng thái đồng bộ.

### 2. Kiểm thử Nhiều cư dân cùng một phòng trọ
- **Kịch bản**:
  - Đăng nhập bằng tài khoản cư dân phụ `demo-resident-101-2`. Kiểm tra xem cư dân này có xem được thông tin phòng trọ, danh sách các bạn cùng phòng và các hóa đơn chung hay không.
  - Đăng nhập bằng cư dân chính `demo-resident-101-1` để ký hợp đồng điện tử bằng OTP hoặc gửi khiếu nại báo hỏng thiết bị.
- **Mục tiêu**: Xác thực khả năng phân chia vai trò (cư dân chính và cư dân phụ) trong phòng trọ, cho phép mọi người trong phòng cùng truy cập thông tin nhưng chỉ cư dân chính mới có quyền thực hiện các thao tác pháp lý như ký hợp đồng.

---
> [!NOTE]
> Việc tăng số lượng tài khoản mẫu này giúp bạn mô phỏng môi trường thực tế tối đa khi kiểm thử hoặc thuyết trình dự án.
