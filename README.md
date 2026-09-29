# CurtainLux — Nền tảng bán rèm cửa & nội thất

CurtainLux là ứng dụng thương mại điện tử chuyên về rèm cửa đặt may theo kích thước, được xây dựng bằng Laravel 12. Hệ thống kết hợp cửa hàng trực tuyến, tính giá theo từng ô cửa, đặt lịch khảo sát tại nhà và trang quản trị phục vụ tư vấn, báo giá, đơn hàng và chăm sóc khách hàng.

Giao diện sử dụng phong cách **Japandi / Warm Neutral**, với nền kem, màu nâu linen và nút hành động màu đất nung. Nội dung hướng đến người dùng Việt Nam, kích thước nhập bằng **cm**, giá tiền hiển thị bằng **VNĐ**.

> Tài liệu mô tả mã nguồn hiện có. VNPAY đang là giao diện mô phỏng nội bộ; các luồng sandbox thanh toán và đăng nhập cần được xử lý trước khi vận hành công khai.

## Mục lục

- [Chức năng](#chức-năng)
- [Công nghệ và yêu cầu](#công-nghệ-và-yêu-cầu)
- [Cài đặt và chạy trên máy cá nhân](#cài-đặt-và-chạy-trên-máy-cá-nhân)
- [Dữ liệu mẫu và tài khoản quản trị](#dữ-liệu-mẫu-và-tài-khoản-quản-trị)
- [Cấu hình tích hợp](#cấu-hình-tích-hợp)
- [Quy tắc tính giá](#quy-tắc-tính-giá)
- [Quy trình nghiệp vụ](#quy-trình-nghiệp-vụ)
- [Cấu trúc mã nguồn và dữ liệu](#cấu-trúc-mã-nguồn-và-dữ-liệu)
- [Các đường dẫn chính](#các-đường-dẫn-chính)
- [Phát triển và kiểm tra](#phát-triển-và-kiểm-tra)
- [Triển khai](#triển-khai)
- [Xử lý lỗi thường gặp](#xử-lý-lỗi-thường-gặp)
- [Quy ước đóng góp](#quy-ước-đóng-góp)

## Chức năng

### Cửa hàng trực tuyến

- Danh mục rèm vải, rèm cầu vồng, rèm cuốn, rèm gỗ, động cơ và phụ kiện.
- Chi tiết sản phẩm với ảnh, thông số, đơn vị tính giá, giá bán và giá khuyến mãi.
- Nhập chiều rộng, chiều cao, số lượng và tên phòng cho từng bộ rèm.
- Tùy chọn kiểu may, thanh treo, động cơ và lớp voan; tính phụ phí theo cấu hình.
- Giỏ hàng lưu kích thước và tùy chọn của từng ô cửa; kiểm tra tồn kho khi thêm, cập nhật và đặt hàng.
- Đặt hàng với COD, chuyển khoản ngân hàng/VietQR hoặc VNPAY mô phỏng.
- Tra cứu đơn hàng theo mã đơn hoặc số điện thoại.
- Đặt lịch khảo sát, tư vấn và đo đạc tại nhà.
- Danh sách yêu thích, đánh giá sản phẩm, tin tức và câu hỏi thường gặp.
- Chat tư vấn với CurtainBot và nhân viên; cập nhật tin nhắn bằng polling.

### Tài khoản khách hàng

- Đăng ký, đăng nhập, đăng xuất và khôi phục mật khẩu.
- Luồng đăng nhập Google, Facebook, Zalo; có cơ chế sandbox khi chưa cấu hình tích hợp.
- Cập nhật hồ sơ và đổi mật khẩu.
- Xem đơn hàng, chi tiết đơn và hủy đơn theo điều kiện của hệ thống.
- Theo dõi lịch khảo sát, xem báo giá, duyệt hoặc yêu cầu điều chỉnh báo giá.
- Khách chưa đăng nhập có thể truy cập báo giá qua liên kết có chữ ký.

### Trang quản trị

| Phân hệ | Nội dung |
| --- | --- |
| Tổng quan và thống kê | Dashboard, báo cáo và chức năng xuất báo cáo |
| Danh mục và sản phẩm | Quản lý thông số rèm, giá, hình ảnh, tồn kho và trạng thái hiển thị |
| Tùy chọn rèm | Nhóm tùy chọn, giá trị và phụ phí |
| Đơn hàng | Theo dõi chi tiết đặt may, trạng thái đơn và thanh toán |
| Khảo sát | Tiếp nhận lịch hẹn, phân công nhân viên, ghi nhận thông tin từng cửa |
| Báo giá | Lập, chỉnh sửa, công bố cho khách và chuyển thành đơn hàng |
| Khách hàng và đánh giá | Quản lý khách hàng, kiểm duyệt nhận xét |
| Nội dung | Banner, tin tức, FAQ và mã giảm giá |
| Tư vấn | Hội thoại chat và quy tắc trả lời của bot |
| Nhân sự | Nhân viên, vai trò và quyền truy cập |
| Hệ thống | Cài đặt cửa hàng và nhật ký hoạt động |

Ảnh giao diện tham khảo được lưu trong [public/screenshots](public/screenshots).

## Công nghệ và yêu cầu

| Thành phần | Công nghệ / yêu cầu |
| --- | --- |
| PHP | 8.2 trở lên, đáp ứng ràng buộc trong `composer.lock` |
| Backend | Laravel 12, Eloquent ORM |
| Dependency PHP | Composer 2 |
| Database | SQLite cho phát triển; MySQL 8.x theo định hướng triển khai |
| Giao diện | Blade, CSS và JavaScript thuần trong `public/` |
| Công cụ frontend | Vite 7; package có Tailwind CSS 4, Axios và Concurrently |
| Node.js | Theo Vite trong dự án: `^20.19.0` hoặc `>=22.12.0` |
| Session / cache / queue | Mặc định dùng database trong `.env.example` |
| Kiểm thử / định dạng | PHPUnit 11, Laravel Pint |
| Thiết kế | Plus Jakarta Sans, FontAwesome, bảng màu Japandi |

Bật các extension PHP mà Composer yêu cầu và driver database tương ứng: `pdo_sqlite` / `sqlite3` cho SQLite, hoặc `pdo_mysql` cho MySQL. PHP chạy trên terminal và PHP của web server cần dùng cấu hình tương thích.

## Cài đặt và chạy trên máy cá nhân

Các ví dụ PowerShell dưới đây dùng thư mục Laragon `C:\laragon\www\lab`. Với máy khác, thay đường dẫn bằng nơi chứa mã nguồn.

### 1. Cài dependency và tạo cấu hình

```powershell
cd C:\laragon\www\lab
composer install
npm ci

if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
}

php artisan key:generate
```

Chỉ tạo `APP_KEY` ở lần cài mới; giữ nguyên khóa của môi trường đã có dữ liệu. Không đưa `.env` vào Git.

### 2. Cấu hình SQLite

Chỉnh các giá trị tương ứng trong `.env`:

```dotenv
APP_NAME=CurtainLux
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_LOCALE=vi
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=vi_VN

DB_CONNECTION=sqlite

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
MAIL_MAILER=log
```

Khi không đặt `DB_DATABASE`, Laravel sử dụng file SQLite mặc định của dự án. Tạo file nếu chưa có rồi chạy migration:

```powershell
if (-not (Test-Path database/database.sqlite)) {
    New-Item -ItemType File -Path database/database.sqlite | Out-Null
}

php artisan config:clear
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build
```

Các lệnh seed ở đây dành cho database phát triển mới. Xem phần dữ liệu mẫu trước khi chạy lại trên database đã chỉnh sửa.

### 3. Chạy ứng dụng

```powershell
php artisan serve
```

Truy cập:

- Cửa hàng: `http://127.0.0.1:8000`
- Đăng nhập quản trị: `http://127.0.0.1:8000/admin/login`

Khi phát triển asset qua Vite, mở thêm terminal và chạy:

```powershell
npm run dev
```

Nếu sử dụng tác vụ đưa vào queue, mở thêm terminal:

```powershell
php artisan queue:listen --tries=1 --timeout=0
```

Dự án cũng có `composer dev` để chạy đồng thời server, queue listener, Laravel Pail và Vite. Nếu Pail không chạy trên môi trường Windows hiện tại, sử dụng các terminal riêng như trên và đọc log trong `storage/logs`.

### 4. Chạy bằng virtual host Laragon

Cấu hình Document Root của virtual host trỏ đến **`C:\laragon\www\lab\public`**. Nếu tên miền đã cấu hình là `lab.test`, đặt `APP_URL=http://lab.test` và chạy `php artisan config:clear`.

Không trỏ Document Root vào thư mục gốc dự án. Khi dùng Apache/Nginx của Laragon, không cần chạy thêm `php artisan serve`.

### 5. Dùng MySQL thay SQLite

Tạo database rỗng, ví dụ `curtainlux`, rồi chỉnh `.env` theo tài khoản MySQL trên máy:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=curtainlux
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

Sau đó chạy `php artisan config:clear`, `php artisan migrate` và seed nếu đây là môi trường demo mới. Thay cấu hình kết nối không tự chuyển dữ liệu từ SQLite sang MySQL.

## Dữ liệu mẫu và tài khoản quản trị

`DatabaseSeeder` tạo tài khoản quản trị nếu email chưa tồn tại, sau đó gọi `CurtainSeeder` để nạp dữ liệu cửa hàng mẫu.

| Thông tin | Giá trị demo |
| --- | --- |
| Trang đăng nhập | `/admin/login` |
| Email | `admin@curtainlux.vn` |
| Mật khẩu | `password123` |
| Vai trò | `admin` |

Đây là thông tin demo công khai trong seeder. Đổi mật khẩu trước khi dùng ngoài môi trường cá nhân. Nếu tài khoản đã tồn tại, `firstOrCreate` không đặt lại mật khẩu.

Một số seeder bổ sung không được `DatabaseSeeder` tự động gọi:

| Seeder | Mục đích |
| --- | --- |
| `RoleSeeder` | Khởi tạo nhóm vai trò và quyền |
| `ChatBotRuleSeeder` | Dữ liệu quy tắc trả lời bot |
| `CurtainNewsSeeder` | Bài viết mẫu |
| `ProductReviewSeeder` | Đánh giá mẫu |
| `FaqAndAuditSeeder` | Dữ liệu FAQ và nhật ký mẫu |
| `CurtainProductMultiImageSeeder` | Dữ liệu nhiều ảnh sản phẩm |
| `CurtainProductCrawlerSeeder` | Nạp sản phẩm từ nguồn bên ngoài |
| `TestProduct2kSeeder` | Dữ liệu phục vụ thử nghiệm sản phẩm |

Ví dụ chạy riêng bộ vai trò hoặc quy tắc bot:

```powershell
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=ChatBotRuleSeeder
```

Đọc từng seeder trước khi chạy trên database có dữ liệu. `CurtainSeeder` có thao tác ghi lại cài đặt cửa hàng/ngân hàng; không xem việc chạy lại seed là thao tác chỉ bổ sung dữ liệu. Các seeder lấy ảnh hoặc dữ liệu bên ngoài có thể cần kết nối mạng.

## Cấu hình tích hợp

### Cửa hàng và tài khoản ngân hàng

Trang `/admin/settings` quản lý cấu hình lưu trong bảng `system_settings`. Luồng tạo VietQR đọc các khóa `bank_id`, `bank_account_number`, `bank_account_name`, đồng thời có giá trị dự phòng từ `BANK_ID`, `BANK_ACCOUNT_NO`, `BANK_ACCOUNT_NAME`.

Cập nhật đúng mã ngân hàng, số tài khoản và chủ tài khoản trước khi thử chuyển khoản. Giá trị trong database được ưu tiên ở luồng này; thay `.env` có thể chưa thay đổi thông tin đang hiển thị nếu `system_settings` đã có giá trị.

### Thanh toán

| Phương thức | Hiện trạng trong mã nguồn |
| --- | --- |
| COD | Có trong luồng checkout |
| Chuyển khoản / VietQR | Có trang xác nhận và API polling trạng thái |
| SePay | Có webhook nhận thông báo và endpoint giả lập thanh toán |
| VNPAY | Giao diện sandbox nội bộ; callback cập nhật trạng thái đơn |
| MoMo | Chưa có luồng tích hợp trong các route thanh toán hiện tại |

Webhook SePay nhận `POST /api/sepay/webhook`, có alias `/sepay/webhook`. Biến môi trường liên quan:

```dotenv
SEPAY_WEBHOOK_APIKEY=replace_with_your_webhook_key
```

Trong triển khai hiện tại, webhook chỉ kiểm tra API key khi biến này có giá trị. Callback VNPAY hiện chưa xác thực chữ ký cổng thanh toán; endpoint `POST /api/payment/sandbox-simulate/{orderCode}` có thể đánh dấu đơn đã thanh toán. Vì vậy, cấu hình thông tin ngân hàng hoặc mã VNPAY chưa đủ để biến các luồng này thành tích hợp thanh toán production.

### Đăng nhập mạng xã hội

Các khóa được khai báo trong `config/services.php`:

```dotenv
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
FACEBOOK_APP_ID=
FACEBOOK_APP_SECRET=
ZALO_APP_ID=
ZALO_SECRET_KEY=
```

Callback tương ứng có dạng `{APP_URL}/auth/social/{provider}/callback`, với `provider` là `google`, `facebook` hoặc `zalo`. Đăng ký đúng callback trên ứng dụng của nhà cung cấp và đặt `APP_URL` theo địa chỉ đang dùng.

Controller có luồng sandbox và route `/auth/social/{provider}/sandbox`. Cần rà soát và giới hạn các luồng này trước khi mở đăng nhập cho người dùng thật.

### Email

Mặc định `MAIL_MAILER=log`: email được ghi vào log, không gửi đến hộp thư thật. Để dùng khôi phục mật khẩu và email đơn hàng thực tế, cấu hình mail transport cùng các giá trị `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` và `MAIL_FROM_NAME` phù hợp.

## Quy tắc tính giá

Chiều rộng `W` và chiều cao `H` được nhập bằng cm. Giá cơ sở sử dụng `effective_price`: lấy `sale_price` khi giá này lớn hơn 0 và nhỏ hơn `price`; các trường hợp khác dùng `price`.

### Đơn vị tính

| `price_unit` | Cách tính số đơn vị | Nhóm sản phẩm điển hình |
| --- | --- | --- |
| `sqm` | `max((W / 100) × (H / 100), min_area)` | Rèm cuốn, cầu vồng, rèm gỗ |
| `meter` | `max(W / 100, 1)` | Rèm vải tính theo mét ngang hoàn thiện |
| `piece` | `1` cho mỗi bộ/chiếc | Động cơ và phụ kiện |

Đơn vị mét ngang hoàn thiện theo quy ước nghiệp vụ đã bao gồm độ nhún vải; không tự nhân thêm hệ số nhún vào công thức giỏ hàng.

### Phụ phí và thành tiền

| `price_impact_type` | Cách tính trong `CartController` |
| --- | --- |
| `fixed` | `extra_price` |
| `per_meter` | `extra_price × (W / 100)` |
| `per_sqm` | `extra_price × calculated_units` |

```text
Thành tiền = (Số đơn vị × Giá cơ sở + Tổng phụ phí) × Số lượng
```

Lưu ý: mã hiện tại dùng `calculated_units` cho phụ phí `per_sqm`. Với sản phẩm tính theo mét ngang hoặc chiếc, giá trị này không phải diện tích vật lý của cửa. Cần tính đến hành vi này khi cấu hình tùy chọn.

**Ví dụ:** rèm tính theo m², cửa rộng 200 cm, cao 250 cm, đơn giá 300.000 ₫/m², phụ phí cố định 100.000 ₫, số lượng 2:

```text
Diện tích = 2 × 2,5 = 5 m²
Thành tiền = (5 × 300.000 + 100.000) × 2 = 3.200.000 ₫
```

Giỏ hàng lưu kích thước, tên phòng, đơn giá, số đơn vị và snapshot tùy chọn. Khi checkout, thông tin được chép sang chi tiết đơn hàng và hệ thống trừ tồn kho. `Product::calculateStockNeeded()` làm tròn lên số tồn kho cần dùng theo đơn vị sản phẩm; số tồn kho này cần phân biệt với số lượng bộ rèm.

## Quy trình nghiệp vụ

### Mua trực tiếp

1. Khách chọn sản phẩm, kích thước, tùy chọn và số lượng.
2. Hệ thống tính giá, kiểm tra tồn kho và lưu giỏ hàng.
3. Khách nhập thông tin nhận hàng, chọn phương thức thanh toán.
4. Hệ thống tạo đơn, lưu chi tiết từng cửa và trừ tồn kho.
5. Quản trị theo dõi tiến độ; khách xem đơn trong tài khoản hoặc trang tra cứu.

Các trạng thái đơn có trong model: `pending`, `confirmed`, `manufacturing`, `shipping`, `installed`, `completed`, `cancelled`. Trạng thái thanh toán được lưu riêng: `pending`, `partially_paid`, `paid`, `failed`.

### Khảo sát và báo giá

1. Khách gửi yêu cầu khảo sát tại nhà.
2. Quản trị tiếp nhận và phân công nhân viên.
3. Nhân viên ghi nhận thông tin đo đạc cho từng cửa.
4. Lập và công bố báo giá cho khách.
5. Khách duyệt hoặc yêu cầu điều chỉnh qua tài khoản/liên kết có chữ ký.
6. Báo giá phù hợp được chuyển thành đơn hàng.

Pipeline khảo sát: `pending → assigned → surveying → quoted → completed`.

Các trạng thái báo giá trong model: `draft`, `sent`, `revision_requested`, `accepted`, `rejected`, `converted`. Đây là danh sách trạng thái, không có nghĩa mọi cặp trạng thái đều cho phép chuyển trực tiếp.

## Cấu trúc mã nguồn và dữ liệu

```text
app/
├── Http/
│   ├── Controllers/       # Storefront, tài khoản, giỏ hàng, thanh toán
│   │   └── Admin/        # Chức năng quản trị
│   └── Middleware/       # Kiểm tra admin và quyền
├── Mail/                 # Email đơn hàng
├── Models/               # Eloquent models
└── Services/             # ChatBotEngine
bootstrap/app.php         # Routing, middleware và ngoại lệ CSRF
config/                   # Database, mail, dịch vụ và cấu hình Laravel
database/
├── factories/
├── migrations/           # Lịch sử cấu trúc database
└── seeders/              # Dữ liệu mẫu
public/
├── css/                  # CSS giao diện
├── js/                   # JavaScript giao diện
└── screenshots/          # Ảnh tham khảo
resources/views/          # Blade: admin, shop, auth, email
routes/web.php            # Route giao diện và endpoint của ứng dụng
routes/console.php        # Route/lệnh console
storage/                  # Log, cache và file do ứng dụng tạo
tests/                    # PHPUnit Unit và Feature
AGENTS.md                 # Quy ước kiến trúc và thiết kế
CHANGELOG.md              # Ghi chép thay đổi
```

Những nhóm dữ liệu chính:

| Nhóm | Bảng / model liên quan |
| --- | --- |
| Sản phẩm | `categories`, `products`, `product_images` |
| Tùy chọn | `curtain_option_groups`, `curtain_option_values` |
| Đặt hàng | `cart_items`, `orders`, `order_items` |
| Khảo sát / báo giá | `consultations`, `consultation_windows`, `quotations`, `quotation_items` |
| Người dùng / quyền | `users`, `roles` |
| Nội dung / chăm sóc | `product_reviews`, `wishlists`, `news`, `banners`, `faqs`, `discount_codes` |
| Chat | `chat_conversations`, `chat_messages`, `chat_bot_rules` |
| Hệ thống | `system_settings`, `audit_logs` và bảng hạ tầng Laravel |

Thông số sản phẩm nằm trong `Product`, gồm `price_unit`, giới hạn kích thước, `min_area`, `blackout_rate`, `material`, `origin` và `stock`. Quan hệ và cột cụ thể được định nghĩa trong model và migration; lấy mã nguồn này làm căn cứ khi mở rộng tính năng.

## Các đường dẫn chính

| Phương thức | Đường dẫn | Chức năng |
| --- | --- | --- |
| GET | `/` | Cửa hàng |
| GET | `/san-pham/{slug}` | Chi tiết sản phẩm |
| GET | `/gio-hang` | Giỏ hàng |
| POST | `/gio-hang/them` | Thêm bộ rèm |
| POST | `/gio-hang/dat-hang` | Đặt hàng |
| POST | `/dat-lich-khao-sat` | Gửi yêu cầu khảo sát |
| GET | `/tra-cuu-don-hang` | Tra cứu đơn |
| GET | `/tin-tuc`, `/faq`, `/wishlist` | Nội dung và sản phẩm yêu thích |
| GET | `/login`, `/register` | Đăng nhập / đăng ký |
| GET | `/tai-khoan` | Hồ sơ khách hàng, yêu cầu đăng nhập |
| GET | `/tai-khoan/don-hang` | Danh sách đơn của khách |
| GET | `/tai-khoan/lich-khao-sat` | Lịch khảo sát của khách |
| GET | `/bao-gia/{code}` | Báo giá khách vãng lai, yêu cầu chữ ký URL |
| GET | `/admin/login` | Đăng nhập quản trị |
| GET | `/admin` | Dashboard, qua middleware admin |
| GET | `/up` | Health endpoint Laravel |

Các endpoint mang tiền tố `/api/` hiện vẫn được khai báo trong `routes/web.php`; không mặc định xem chúng là API độc lập dùng token. Cấu hình middleware và ngoại lệ CSRF nằm trong `bootstrap/app.php`.

Xem danh sách đầy đủ:

```powershell
php artisan route:list
php artisan route:list --path=admin
```

## Phát triển và kiểm tra

| Lệnh | Mục đích |
| --- | --- |
| `composer setup` | Cài dependency, tạo `.env` nếu thiếu, tạo khóa, migrate và build asset; không seed |
| `composer dev` | Chạy server, queue, Pail và Vite đồng thời |
| `npm run dev` | Vite development server |
| `npm run build` | Build asset |
| `php artisan migrate:status` | Kiểm tra migration |
| `php artisan optimize:clear` | Xóa cache tối ưu hóa Laravel |
| `composer test` | Xóa cache cấu hình và chạy test |
| `vendor/bin/pint --test` | Kiểm tra định dạng PHP |

Không dùng `composer setup` như lệnh bảo trì môi trường đang vận hành vì script có bước tạo lại khóa ứng dụng.

Bộ test hiện chỉ có các `ExampleTest`, chưa bao phủ đầy đủ tính giá, tồn kho, báo giá, phân quyền và thanh toán. `phpunit.xml` dùng SQLite `:memory:`; Feature ExampleTest truy cập trang chủ nhưng chưa có `RefreshDatabase`, nên có thể lỗi thiếu bảng khi trang chủ truy vấn dữ liệu. Không xem test mẫu là bằng chứng toàn bộ nghiệp vụ đã được kiểm chứng.

Khi thay đổi nghiệp vụ, ưu tiên kiểm tra kích thước tối thiểu, giá khuyến mãi, phụ phí, nhiều bộ cùng sản phẩm, tồn kho, quyền truy cập báo giá và trạng thái thanh toán. Với thay đổi giao diện, kiểm tra cả desktop/mobile và dấu tiếng Việt.

## Triển khai

Trước khi đưa lên môi trường thật, cần hoàn thiện việc giới hạn/tắt sandbox thanh toán và đăng nhập, xác thực callback thanh toán và cấu hình khóa webhook. Những điểm này xuất phát từ các controller hiện tại, không được tự khắc phục chỉ bằng `APP_ENV=production`.

Các bước triển khai cơ bản sau khi hoàn tất phần tích hợp:

1. Cấu hình web server trỏ vào `public/`, bật HTTPS.
2. Cấu hình `.env` với `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` đúng tên miền và thông tin database/mail thật.
3. Giữ ổn định `APP_KEY`; sao lưu database và file tải lên trước cập nhật.
4. Cài dependency và build asset:

   ```powershell
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   php artisan migrate --force
   php artisan storage:link
   php artisan view:cache
   ```

5. Cấp quyền ghi cho tiến trình web vào `storage/` và `bootstrap/cache/`.
6. Cấu hình tiến trình queue worker nếu sử dụng job chạy nền; kiểm tra log, email và các luồng đặt hàng sau triển khai.

Một số controller hiện gọi `env()` trực tiếp, như kiểm tra API key SePay và giá trị dự phòng ngân hàng. Trước khi bật `config:cache`, nên đưa các giá trị này vào file cấu hình và đọc bằng `config()` để tránh mất giá trị sau khi cache. Không seed dữ liệu demo lên production.

## Xử lý lỗi thường gặp

| Hiện tượng | Cách kiểm tra |
| --- | --- |
| `No application encryption key has been specified` | Kiểm tra `.env`; chạy `php artisan key:generate` nếu là cài đặt mới |
| SQLite không tồn tại | Tạo `database/database.sqlite`, kiểm tra `DB_CONNECTION` và `DB_DATABASE` |
| `could not find driver` | Bật driver PDO đúng database trong `php.ini` của PHP đang chạy |
| Thiếu bảng `sessions`, `cache` hoặc bảng nghiệp vụ | Kiểm tra kết nối và chạy `php artisan migrate` |
| 404 với virtual host | Kiểm tra Document Root `public/` và cấu hình rewrite |
| 419 / hết phiên | Kiểm tra cookie, domain, session và CSRF token; dùng nhất quán một địa chỉ truy cập |
| Không hiện ảnh tải lên | Chạy `php artisan storage:link`, kiểm tra đường dẫn ảnh và quyền thư mục |
| Lỗi Vite manifest hoặc asset | Chạy `npm ci`, `npm run build`; kiểm tra chế độ chạy Vite |
| Không nhận email | `MAIL_MAILER=log` chỉ ghi log; kiểm tra mail transport thực tế |
| Admin trả về 403 | Kiểm tra trạng thái tài khoản, vai trò và quyền của chức năng |
| Đổi `.env` nhưng chưa có tác dụng | Chạy `php artisan config:clear`; kiểm tra giá trị ưu tiên trong bảng `system_settings` |
| Tiếng Việt lỗi ký tự | Lưu file UTF-8 không BOM; không bỏ dấu để che lỗi encoding |

Log ứng dụng nằm trong `storage/logs`; tên file cụ thể phụ thuộc `LOG_CHANNEL` và cấu hình logging.

## Quy ước đóng góp

Tham khảo [AGENTS.md](AGENTS.md) trước khi sửa mã nguồn và [CHANGELOG.md](CHANGELOG.md) để xem ghi chép thay đổi.

- Giữ kiến trúc Laravel: controller xử lý request và điều phối; model/service chứa logic phù hợp.
- Validate dữ liệu đầu vào; khai báo `$fillable`, không mass-assign `$request->all()`.
- Eager load quan hệ khi cần để tránh truy vấn N+1.
- Tạo migration mới cho thay đổi schema, không sửa migration đã chạy.
- Dùng `DECIMAL(12, 0)` theo quy ước lưu tiền VNĐ; tránh bổ sung phép tính tiền bằng float.
- Lưu source bằng **UTF-8 không BOM**, giữ nguyên dấu tiếng Việt.
- Giữ bảng màu: nền `#FAF7F2`, primary `#8C7A6B`, CTA `#B86B53`, chữ `#2C2724`; icon đơn sắc.
- Không commit `.env`, khóa dịch vụ, database chứa dữ liệu thật hoặc log nhạy cảm.

`composer.json` hiện khai báo giấy phép MIT. Khi phân phối dự án, kiểm tra thêm quyền sử dụng ảnh, font và dữ liệu lấy từ nguồn bên ngoài.
