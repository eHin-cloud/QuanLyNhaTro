# 🏠 Renty - Hệ Thống Quản Lý Nhà Trọ Toàn Diện & Tích Hợp AI

**Renty** là một giải pháp công nghệ toàn diện phục vụ việc quản lý vận hành nhà trọ, kết hợp với nền tảng tìm kiếm, đánh giá phòng trọ thực tế dành cho người thuê. Dự án được phát triển với mục tiêu nâng cao tính minh bạch thông tin, tối ưu hóa quy trình vận hành của chủ trọ và mang lại trải nghiệm tiện lợi nhất cho cư dân thông qua các công nghệ hiện đại.

---

## 🚀 Các Tính Năng Nổi Bật

### 1. 🤖 Renty AI Chatbot (Tích hợp Gemini API)
*   **Trợ lý ảo thông minh**: Sử dụng mô hình AI của Google Gemini được tinh chỉnh theo dữ liệu phòng trọ thực tế.
*   **Tìm kiếm & Lọc thông minh**: Trả lời tự động các câu hỏi của người dùng về phòng trống, giá cả, khu vực và tiện ích theo thời gian thực.
*   **Tư vấn khu vực**: Đưa ra gợi ý thông minh, chuyên nghiệp và các cảnh báo khu vực khi không tìm thấy phòng phù hợp.

### 2. 👥 Cổng Thông Tin Cư Dân (Resident Portal)
*   **Giao diện cá nhân hóa**: Dành riêng cho người thuê trọ theo dõi thông tin hợp đồng, hóa đơn, và lịch sử thanh toán.
*   **Gửi yêu cầu & Báo sự cố**: Cư dân có thể tạo yêu cầu sửa chữa trang thiết bị hoặc phản ánh dịch vụ trực tiếp đến chủ trọ.
*   **Nhận thông báo tức thời**: Cập nhật nhanh các thông tin quan trọng từ ban quản lý.

### 3. 📝 Quản Lý Hợp Đồng Điện Tử (E-Contract)
*   **Ký số trực tuyến**: Cho phép chủ trọ và người thuê thực hiện ký hợp đồng điện tử nhanh chóng, bảo mật.
*   **Xuất bản PDF chuyên nghiệp**: Tích hợp công cụ xuất hợp đồng ra định dạng PDF thông qua thư viện `DomPDF`.
*   **Lưu trữ tập trung**: Quản lý lịch sử hợp đồng, hạn hợp đồng và tự động cảnh báo khi sắp hết hạn.

### 4. 💰 Quản Lý Hóa Đơn & Thanh Toán
*   **Tự động tính hóa đơn**: Tính toán chi phí dịch vụ (điện, nước, internet, phí vệ sinh...) theo chỉ số công tơ điện nước đầu vào.
*   **Lịch sử thanh toán**: Theo dõi chi tiết các hóa đơn đã thanh toán hoặc còn nợ, cập nhật trạng thái tự động.

### 5. 🚨 Cảnh Báo Thông Minh (Smart Alerts)
*   **Gửi cảnh báo diện rộng**: Hệ thống hỗ trợ gửi cảnh báo khẩn cấp (sự cố mất điện nước, phòng cháy chữa cháy, nhắc nhở đóng tiền trọ) đến toàn bộ cư dân hoặc nhóm phòng cụ thể.
*   **Phân loại độ ưu tiên**: Phân biệt cảnh báo khẩn cấp, cảnh báo định kỳ và thông báo thông thường.

### 6. 📊 Báo Cáo & Thống Kê Chi Tiết
*   **Trực quan hóa dữ liệu**: Sử dụng biểu đồ `Chart.js` hiển thị doanh thu, chi phí, tỷ lệ lấp đầy phòng trọ theo tháng/năm.
*   **Quản lý trang thiết bị**: Thống kê danh mục tài sản, trang thiết bị trong từng phòng và trạng thái hao mòn của chúng.

### 7. 🔐 Phân Quyền Chi Tiết (RBAC)
*   Hệ thống phân quyền chặt chẽ giữa các vai trò người dùng:
    *   **Admin**: Quản lý toàn bộ hệ thống, cấu hình chung.
    *   **Chủ trọ (Landlord) / Quản lý (Manager)**: Vận hành cơ sở trọ, quản lý phòng, cư dân, hóa đơn và hợp đồng.
    *   **Cư dân (Resident)**: Sử dụng các dịch vụ tiện ích, thanh toán và gửi phản hồi.
    *   **Khách vãng lai (Guest)**: Tìm kiếm phòng trọ, xem đánh giá thực tế từ người dùng trước.

---

## 🛠️ Công Nghệ Sử Dụng

### Backend
*   **PHP >= 8.2**
*   **Laravel 11** (Framework cốt lõi)
*   **Laravel Sanctum** (Xác thực API)
*   **Eloquent ORM** (Quản lý CSDL)
*   **MySQL** (Hệ quản trị cơ sở dữ liệu)
*   **Google Gemini API** (Bộ não cho Chatbot Renty AI)

### Frontend
*   **Blade Template Engine** (Giao diện phía server)
*   **Vite** (Build tool hiệu năng cao)
*   **Tailwind CSS** (Framework CSS)
*   **Chart.js** (Thư viện vẽ biểu đồ)
*   **JavaScript (ES6+)** & **CSS Custom** (Tối ưu hóa UI/UX mobile-first)

---

## 💻 Hướng Dẫn Cài Đặt Local

### Yêu Cầu Hệ Thống
*   PHP 8.2 trở lên
*   Composer
*   Node.js & NPM
*   MySQL Server

### Các Bước Cài Đặt

#### Bước 1: Clone dự án và truy cập thư mục
```bash
git clone <repository-url>
cd QuanLyNhaTro
```

#### Bước 2: Cài đặt các gói phụ thuộc Backend
```bash
composer install
```

#### Bước 3: Cài đặt các gói phụ thuộc Frontend
```bash
npm install
```

#### Bước 4: Thiết lập file cấu hình môi trường `.env`
Sao chép cấu hình mẫu:
```bash
cp .env.example .env
```
Tạo khóa ứng dụng:
```bash
php artisan key:generate
```

Cấu hình thông tin kết nối Cơ sở dữ liệu và API trong file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quan_ly_nha_tro
DB_USERNAME=root
DB_PASSWORD=

# Cấu hình Google Gemini API (cho Chatbot Renty AI)
GEMINI_API_KEY=your_gemini_api_key_here
```

#### Bước 5: Chạy Migration và Seed Dữ liệu mẫu
Khởi tạo cấu trúc bảng và nạp dữ liệu demo phục vụ kiểm thử:
```bash
php artisan migrate --seed
```

#### Bước 6: Chạy Server Phát Triển
Khởi động Laravel Server:
```bash
php artisan serve
```

Khởi chạy Vite phát triển giao diện (CSS/JS compiling):
```bash
npm run dev
```

Sau khi hoàn tất, mở trình duyệt và truy cập:
*   Trang chủ người thuê: `http://127.0.0.1:8000/renty`
*   Hệ thống quản trị (Admin/Landlord): `http://127.0.0.1:8000`

---

## 🧪 Chạy Kiểm Thử (Testing)

Dự án tích hợp các ca kiểm thử tự động để đảm bảo tính ổn định của các chức năng cốt lõi:
```bash
php artisan test
```

## 📦 Đóng Gói Production

Để build tối ưu hóa toàn bộ tài nguyên frontend cho môi trường production:
```bash
npm run build
```

---

## 📁 Cấu Trúc File Giao Diện Quan Trọng

*   `resources/views/rentry/rentry.blade.php`: Giao diện tìm kiếm và đánh giá dành cho người thuê trọ.
*   `resources/views/admin/`: Các view phục vụ giao diện quản trị (hóa đơn, thiết bị, hợp đồng, cư dân).
*   `resources/css/quan-ly-nha-tro.css`: Stylesheet tùy chỉnh phục vụ giao diện đặc thù của dự án.
*   `resources/js/quan-ly-nha-tro.js`: Logic JavaScript tùy chỉnh cho giao diện.

---
☘️ *Chúc bạn có trải nghiệm tuyệt vời khi phát triển và sử dụng hệ thống Renty!*
