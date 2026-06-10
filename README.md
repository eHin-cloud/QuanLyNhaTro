# Renty Review

Renty Review là đồ án xây dựng nền tảng tìm kiếm, đánh giá và so sánh phòng trọ dành cho người đi thuê. Mục tiêu của dự án là giúp người dùng hạn chế tình trạng "ảnh mạng một đằng, thực tế một nẻo" bằng cách cung cấp thông tin phòng rõ ràng, đánh giá từ người ở thực tế và giao diện tối ưu cho quá trình ra quyết định trên thiết bị di động.

## 1. Giới Thiệu

Trong quá trình tìm phòng trọ, người thuê thường gặp khó khăn khi thông tin trên mạng không phản ánh đúng chất lượng thực tế của phòng. Hình ảnh có thể được chỉnh sửa, mô tả thiếu chi tiết, chi phí phát sinh không minh bạch và đánh giá từ người từng ở chưa được trình bày đầy đủ.

Renty Review tập trung giải quyết các vấn đề đó thông qua:

- Hiển thị thông tin phòng trọ trực quan, dễ quét nhanh.
- Cung cấp đánh giá từ người ở thực tế.
- Hỗ trợ so sánh nhiều phòng trước khi liên hệ.
- Tối ưu trải nghiệm mobile-first để người dùng dễ thao tác trên điện thoại.

## 2. Công Nghệ Sử Dụng

### Backend

- PHP 8.2+
- Laravel 11
- Laravel Sanctum
- Eloquent ORM
- MySQL hoặc cơ sở dữ liệu tương thích Laravel

### Frontend

- Blade Template Engine
- Vite
- Tailwind CSS
- CSS tùy chỉnh theo dự án tại `resources/css/quan-ly-nha-tro.css`
- JavaScript tùy chỉnh theo dự án tại `resources/js/quan-ly-nha-tro.js`
- FontAwesome Icons
- Chart.js cho biểu đồ/hiển thị dữ liệu trực quan

### Công Cụ Phát Triển

- Composer
- NPM
- PHPUnit
- Laravel Vite Plugin
- Laravel DomPDF cho các chức năng xuất/in tài liệu

## 3. Tính Năng Nổi Bật

### Hệ Thống Đánh Giá Từ Người Ở Thực Tế

Người dùng có thể xem và gửi đánh giá cho từng phòng trọ. Các đánh giá giúp người thuê có thêm góc nhìn thực tế về chất lượng phòng, chủ nhà, an ninh, tiện ích và mức độ phù hợp so với nhu cầu.

### So Sánh Tối Đa 3 Phòng

Người dùng có thể chọn tối đa 3 phòng để so sánh các tiêu chí quan trọng như:

- Giá thuê
- Diện tích
- Điểm đánh giá
- Có gác lửng
- Mức độ an ninh

Bảng so sánh được thiết kế hỗ trợ scroll ngang trên mobile, giúp người dùng vẫn đọc được đầy đủ thông tin mà không vỡ giao diện.

### Giao Diện Mobile-First

Renty Review ưu tiên trải nghiệm trên điện thoại với:

- Thanh liên hệ nổi ở đáy màn hình.
- Icon grid cho thông tin phòng.
- Panel chi phí dự kiến khi vào ở.
- Review summary gọn gàng.
- Layout dark mode hiện đại, dễ đọc và phù hợp với hành vi tìm phòng nhanh trên mobile.

### Quản Lý Phòng Và Dữ Liệu Nội Bộ

Hệ thống có khu vực quản trị phục vụ quản lý phòng, cư dân, thiết bị, hợp đồng, hóa đơn, thanh toán và báo cáo. Đây là phần hỗ trợ chủ trọ vận hành dữ liệu phòng trọ trong cùng một hệ thống.

## 4. Hướng Dẫn Cài Đặt Local

### Yêu Cầu Môi Trường

- PHP >= 8.2
- Composer
- Node.js và NPM
- MySQL hoặc database tương thích

### Bước 1: Clone Dự Án

```bash
git clone <repository-url>
cd QuanLyNhaTro
```

### Bước 2: Cài Dependency Backend

```bash
composer install
```

### Bước 3: Cài Dependency Frontend

```bash
npm install
```

### Bước 4: Cấu Hình Môi Trường

```bash
cp .env.example .env
php artisan key:generate
```

Cập nhật thông tin database trong file `.env`, ví dụ:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quan_ly_nha_tro
DB_USERNAME=root
DB_PASSWORD=
```

### Bước 5: Chạy Migration Và Seeder

```bash
php artisan migrate --seed
```

### Bước 6: Chạy Dự Án

Chạy Laravel server:

```bash
php artisan serve
```

Chạy Vite để build CSS/JS trong môi trường phát triển:

```bash
npm run dev
```

Sau đó truy cập:

```text
http://127.0.0.1:8000
```

Trang người thuê:

```text
http://127.0.0.1:8000/renty
```

## 5. Build Production

Để build frontend cho môi trường production:

```bash
npm run build
```

## 6. Chạy Test

```bash
php artisan test
```

## 7. Cấu Trúc File Liên Quan Giao Diện

- `resources/views/rentry/rentry.blade.php`: giao diện Renty Review cho người thuê.
- `resources/css/quan-ly-nha-tro.css`: CSS tùy chỉnh của dự án.
- `resources/js/quan-ly-nha-tro.js`: JavaScript tùy chỉnh của dự án.
- `resources/css/app.css`: entry CSS của Vite.
- `resources/js/app.js`: entry JavaScript của Vite.

## 8. Mục Tiêu Phát Triển

Renty Review hướng đến việc trở thành một công cụ hỗ trợ thuê trọ minh bạch hơn, giúp người thuê ra quyết định dựa trên dữ liệu thực tế thay vì chỉ dựa vào hình ảnh quảng cáo. Dự án cũng hỗ trợ chủ trọ quản lý thông tin phòng, cư dân và chi phí vận hành hiệu quả hơn.
