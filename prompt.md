# 📄 وثيقة التوصيف المعمارية والتنفيذية للمشروع (AI Agent Master Document)

## 1. Project Overview (نظرة عامة على المشروع)
**الاسم:** منصة لتبادل الكتب الجامعية (University Book Exchange Platform).
**الوصف:** منصة ويب (Client-Server) مخصصة لطلاب الجامعة لتبادل، بيع، أو التبرع بالكتب الورقية، ورفع الملخصات الرقمية (PDF). تتميز المنصة بنظام مواعيد للتسليم المباشر، نظام تقييم، ولوحة تحكم متقدمة، مع دمج الذكاء الاصطناعي (Gemini API) لتسهيل إدخال البيانات ومراقبة المحتوى.

## 2. Tech Stack (التقنيات المستخدمة)
*   **Backend Framework:** Laravel 12 (PHP 8.2+).
*   **Database:** MySQL.
*   **Admin Panel:** Filament PHP v3.3 -w.
*   **Frontend:** Laravel Blade, Tailwind CSS, Alpine.js (بدون Livewire للواجهة الأمامية للمستخدم).
*   **AI Integration:** Google Gemini API (عبر خدمة مخصصة ضمن الـ Backend).
*   **Database Features:** Soft Deletes, Eloquent ORM, Polymorphic Relationships.

---

## 3. Database Schema (مخطط قاعدة البيانات النهائي)
*تم تحسين المخطط لدعم Laravel Best Practices (إضافة timestamps, soft deletes، وتحسين جدول المفضلة).*

```dbml
// Enums
Enum offer_type { sale, exchange, donate }
Enum item_status { available, pending, sold }
Enum book_condition { excellent, good, fair, poor }
Enum transaction_status { pending, accepted, completed, cancelled }
Enum moderation_status { pending, approved, rejected }

// Tables
Table roles {
  id int [pk, increment]
  name varchar
  description text [null]
  created_at timestamp
  updated_at timestamp
}

Table users {
  id int [pk, increment]
  role_id int [ref: > roles.id]
  university_id varchar [unique]
  full_name varchar
  email varchar [unique]
  password varchar
  phone_number varchar
  is_banned boolean [default: false]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp // Soft Delete
}

Table categories {
  id int [pk, increment]
  university_name varchar
  faculty_name varchar
  department_name varchar
  study_year varchar
  created_at timestamp
  updated_at timestamp
}

Table books {
  id int [pk, increment]
  user_id int [ref: > users.id]
  category_id int [ref: > categories.id]
  title varchar
  author varchar [null]
  cover_image_url varchar
  condition book_condition
  offer_type offer_type
  price decimal(8,2) [null]
  status item_status [default: 'available']
  moderation_status moderation_status [default: 'pending']
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp // Soft Delete
}

Table digital_notes {
  id int [pk, increment]
  user_id int [ref: > users.id]
  category_id int [ref: > categories.id]
  title varchar
  description text [null]
  pdf_file_url varchar
  moderation_status moderation_status [default: 'pending']
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp // Soft Delete
}

Table transactions {
  id int [pk, increment]
  book_id int [ref: > books.id]
  requester_id int [ref: > users.id]
  owner_id int [ref: > users.id]
  meeting_date date [null]
  meeting_time time [null]
  meeting_location varchar [null]
  status transaction_status [default: 'pending']
  created_at timestamp
  updated_at timestamp
}

Table ratings {
  id int [pk, increment]
  transaction_id int [unique, ref: - transactions.id]
  reviewer_id int [ref: > users.id]
  reviewed_user_id int [ref: > users.id]
  stars int // 1 to 5
  comment text [null]
  created_at timestamp
  updated_at timestamp
}

// Polymorphic Table for Favorites
Table favorites {
  id int [pk, increment]
  user_id int [ref: > users.id]
  favoritable_id int
  favoritable_type varchar // 'App\Models\Book' or 'App\Models\DigitalNote'
  created_at timestamp
  updated_at timestamp
}
```

---

## 4. Filament Admin Panel Architecture (هيكلية لوحة الإدارة)

### A. Resources (الموارد الأساسية)
1.  **UserResource:**
    *   *Table:* عرض (الاسم، الرقم الجامعي، الإيميل، الهاتف، حالة الحظر).
    *   *Filters:* فلترة حسب حالة الحظر (Banned/Active).
    *   *Actions:* زر (Toggle Ban) لحظر/فك حظر المستخدم.
2.  **BookResource & DigitalNoteResource:**
    *   *Table:* عرض (العنوان، اسم الطالب، القسم، حالة المراقبة moderation_status).
    *   *Filters:* فلترة حسب (Pending, Approved, Rejected).
    *   *Actions:* أزرار (Approve / Reject) للموافقة أو الرفض على المحتوى.
3.  **CategoryResource:** لإدارة (الجامعة، الكلية، القسم، السنة).
4.  **TransactionResource (Read-Only للمدير):** لمراقبة عمليات التبادل التي تجري في المنصة.
5.  **RatingResource (Read/Delete):** لمراقبة التقييمات وحذف التقييمات المسيئة أو الوهمية.

### B. Relation Managers (إدارة العلاقات داخل الـ Resources)
*   في `UserResource` يجب إضافة Relation Managers لـ: `BooksRelationManager`, `TransactionsRelationManager` لمعرفة ماذا يمتلك المستخدم وماذا طلب.
*   في `BookResource` يجب إضافة `TransactionsRelationManager`.

### C. Widgets (ودجات لوحة التحكم الرئيسية)
1.  **StatsOverviewWidget:** يعرض (إجمالي الطلاب، إجمالي الكتب، إجمالي الملخصات، عدد الكتب بانتظار الموافقة `pending`).
2.  **LatestTransactionsWidget:** جدول يعرض أحدث 5 عمليات تبادل تمت.
3.  **BooksChartWidget:** مخطط بياني (Line Chart) يوضح معدل إضافة الكتب شهرياً.

---

## 5. AI Integration (Gemini API Service)
يجب إنشاء Service Provider و Class مخصص للذكاء الاصطناعي `app/Services/GeminiAiService.php` يتعامل مع REST API الخاص بـ Google Gemini. ويحتوي على 3 دوال أساسية:
1.  **`extractBookDetails(string $imageUrl)`:** إرسال صورة غلاف الكتاب للـ AI واسترجاع (Title, Author) بتنسيق JSON (Smart Auto-Fill).
2.  **`predictPrice(string $title, string $condition)`:** إرسال اسم الكتاب وحالته، واسترجاع سعر تقديري مقترح للبيع العادل.
3.  **`moderateImage(string $imageUrl)`:** إرسال الصورة للتحقق مما إذا كانت تحتوي على محتوى غير لائق. يرجع `true` (مقبول) أو `false` (مرفوض).

---

## 6. Frontend Architecture (هيكلية الواجهة الأمامية)
*   **Layout:** استخدام Blade Components (`<x-app-layout>`).
*   **Styling:** استخدام Tailwind CSS بالكامل.
*   **Interactivity:** استخدام Alpine.js (`x-data`, `x-show`, `x-on:click`) لعمل:
    *   نوافذ منبثقة (Modals) لطلب كتاب (تحديد موعد ومكان التسليم).
    *   قوائم منسدلة (Dropdowns) للفلترة (حسب القسم، السنة، نوع العرض).
    *   رسائل التنبيه المؤقتة (Toast notifications).

---

## 7. Database Seeding & Factories (توليد البيانات الوهمية)
يجب إنشاء فواكتوري وسيدر متكامل لتسهيل الاختبار:
1.  `RoleSeeder`: إنشاء دور (Admin) ودور (Student).
2.  `AdminUserSeeder`: إنشاء حساب مدير افتراضي (`admin@admin.com`).
3.  `CategorySeeder`: توليد 5 كليات هندسية مع أقسامها.
4.  `UserFactory`: توليد 20 طالب وهمي.
5.  `BookFactory` & `DigitalNoteFactory`: توليد 50 كتاب وملخص مع ربطهم بشكل عشوائي بالطلاب.
6.  `TransactionFactory` & `RatingFactory`: توليد عمليات تبادل وهمية وتقييمات.

---

## 8. Execution Plan (خطة التنفيذ A to Z للـ AI Agent)
*عزيزي الـ AI، يرجى تنفيذ المشروع باتباع هذه الخطوات بالترتيب:*

*   **Phase 1: Project Initialization**
    *   Create a new Laravel 12 project.
    *   Configure `.env` for MySQL DB.
    *   Install Filament PHP v3 (`composer require filament/filament`).
    *   Install Tailwind CSS and Alpine.js via NPM.
*   **Phase 2: Database & Models**
    *   Create all Migrations based on the DBML schema provided above. Ensure exact Enum values and foreign keys.
    *   Create all Eloquent Models. Define relationships (`belongsTo`, `hasMany`, `morphTo`, `morphMany`). Add guarded properties and explicit casts for enums.
*   **Phase 3: Seeding**
    *   Create all Factories and Seeders as specified in Section 7.
    *   Run `php artisan migrate:fresh --seed` to ensure the DB works perfectly.
*   **Phase 4: AI Service Implementation**
    *   Create `GeminiAiService.php`.
    *   Implement HTTP requests using Laravel `Http::facade` to Google Gemini endpoint.
    *   Set up a mock fallback in case the API key is not provided in `.env`.
*   **Phase 5: Admin Panel (Filament)**
    *   Generate Filament Resources for all models.
    *   Configure form fields, table columns, and filters for each resource.
    *   Build the custom Widgets and Relation Managers.
*   **Phase 6: Frontend - Routing & Controllers**
    *   Create web routes for: Home, Browse Books, Browse Notes, Item Details, User Dashboard.
    *   Create Controllers (`BookController`, `TransactionController`, etc.).
*   **Phase 7: Frontend - Views (Blade + Tailwind + Alpine)**
    *   Build responsive UI components (Navbar, Footer, Book Card).
    *   Build the forms (Add Book, Request Exchange, Add Rating).
    *   Integrate `GeminiAiService` into the "Add Book" form via AJAX/Fetch API for Auto-fill.
*   **Phase 8: Final Polish**
    *   Implement the Transaction lifecycle logic (Pending -> Accepted -> Completed).
    *   Secure routes using Middleware (check if user `is_banned`).

**AI Agent Prompt End.**