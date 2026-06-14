# تقرير شامل عن مشروع Book Exchange App

---

## 1. نظرة عامة على المشروع

| البيان | التفاصيل |
|--------|----------|
| **اسم المشروع** | Book Exchange App (منصة تبادل الكتب الجامعية) |
| **الإطار المستخدم** | Laravel 12 + Filament 3.3 (لوحة إدارة) |
| **لغة البرمجة** | PHP 8.2+ |
| **قاعدة البيانات** | SQLite (افتراضياً) |
| **الواجهة الأمامية** | Blade Templates + Tailwind CSS + Vite |
| **الذكاء الاصطناعي** | Google Gemini عبر LiteLLM Proxy (OpenAI-compatible) |
| **نظام المصادقة** | Laravel Breeze |

### الهدف من المشروع
منصة جامعية لتبادل وبيع وهبة الكتب الجامعية والملخصات الدراسية بين الطلاب، مع لوحة إدارة كاملة للمشرفين، وخدمات ذكاء اصطناعي لاستخراج بيانات الكتب وتسعيرها ومراقبة المحتوى.

---

## 2. البنية العامة للمشروع

```
book-exchange-app/
├── app/                          ← الكود الأساسي للتطبيق
│   ├── Filament/                 ← لوحة الإدارة (Admin Panel)
│   ├── Http/                     ← المتحكمات والـ Middleware
│   ├── Models/                   ← نماذج قاعدة البيانات (Eloquent Models)
│   ├── Providers/                ← مزودي الخدمات (Service Providers)
│   ├── Services/                 ← خدمات الأعمال (Business Services)
│   └── View/                     ← مكونات Blade View
├── bootstrap/                    ← ملفات إقلاع Laravel
├── config/                       ← ملفات الإعدادات
├── database/                     ← التهجرات والـ Seeders
├── lang/                         ← ملفات الترجمة (عربي/إنجليزي)
├── public/                       ← الملفات العامة (CSS, JS, images)
├── resources/                    ← القوالب والأصول الأمامية
├── routes/                       ← ملفات التوجيه (Routes)
├── storage/                      ← التخزين المحلي
├── tests/                        ← الاختبارات
└── vendor/                       ← حزم Composer
```

---

## 3. تفصيل مجلد `app/` - النماذج (Models)

### 3.1 `app/Models/User.php`

نموذج المستخدم - يمثل المستخدمين في النظام (طلاب ومديرون).

**الحقول القابلة للتعبئة (fillable):**
- `role_id` - معرف الدور
- `university_id` - الرقم الجامعي
- `full_name` - الاسم الكامل
- `email` - البريد الإلكتروني
- `password` - كلمة المرور (مشفرة تلقائياً عبر cast `hashed`)
- `phone_number` - رقم الهاتف
- `is_banned` - حالة الحظر (boolean)

**الحقول المخفية (hidden):**
- `password`
- `remember_token`

**الأنواع (casts):**
- `email_verified_at` → datetime
- `password` → hashed
- `is_banned` → boolean

**العلاقات (Relationships):**
| العلاقة | النوع | الوصف |
|---------|-------|-------|
| `role()` | belongsTo | كل مستخدم له دور واحد |
| `books()` | hasMany | الكتب التي يملكها المستخدم |
| `digitalNotes()` | hasMany | الملخصات التي رفعها المستخدم |
| `favorites()` | hasMany | العناصر المفضلة للمستخدم |
| `ownedTransactions()` | hasMany | عمليات التبادل كمالك كتاب |
| `requestedTransactions()` | hasMany | عمليات التبادل كطالب كتاب |
| `ratingsGiven()` | hasMany | التقييمات التي كتبها المستخدم |
| `ratingsReceived()` | hasMany | التقييمات التي حصل عليها المستخدم |

**الملحقات (Accessors):**
- `getAverageRatingAttribute()` - حساب متوسط التقييمات المحصلة (يعيد 0 إذا لم توجد)
- `getIsAdminAttribute()` - يعيد true إذا كان دور المستخدم Admin

**واجهات Filament:**
- `canAccessPanel(Panel $panel)` - يسمح فقط للمستخدمين بدور Admin بالدخول للوحة الإدارة
- `getFilamentName()` - يعرض الاسم الكامل في لوحة الإدارة

**ملاحظة:** يستخدم النموذج `SoftDeletes` مما يعني أن حذف المستخدم يكون حذفاً ناعماً (يبقى في قاعدة البيانات مع timestamp).

---

### 3.2 `app/Models/Book.php`

نموذج الكتاب - يمثل الكتب المعروضة في المنصة للتبادل أو البيع أو الهبة.

**الحقول القابلة للتعبئة:**
- `user_id` - معرف مالك الكتاب
- `category_id` - معرف التصنيف الأكاديمي
- `title` - عنوان الكتاب
- `author` - اسم المؤلف
- `cover_image_url` - رابط صورة الغلاف
- `pages_count` - عدد الصفحات
- `condition` - حالة الكتاب المادي
- `offer_type` - نوع العرض
- `exchange_for` - ماذا يريد بالمقابل (في حال التبادل)
- `price` - سعر الكتاب (في حال البيع)
- `status` - حالة الكتاب
- `moderation_status` - حالة المراجعة
- `content_description` - وصف محتوى الكتاب
- `payment_method` - طريقة الدفع

**أنواع العرض (offer_type):**
| القيمة | المعنى |
|--------|--------|
| `sale` | بيع |
| `exchange` | تبادل |
| `donate` | هبة |

**حالات الكتاب (status):**
| القيمة | المعنى |
|--------|--------|
| `available` | متاح |
| `pending` | قيد الانتظار |
| `sold` | تم البيع |

**حالة المراجعة (moderation_status):**
| القيمة | المعنى |
|--------|--------|
| `pending` | بانتظار المراجعة |
| `approved` | معتمد |
| `rejected` | مرفوض |

**حالات الكتاب المادي (condition):**
| القيمة | المعنى |
|--------|--------|
| `excellent` | ممتاز |
| `good` | جيد |
| `fair` | مقبول |
| `poor` | ضعيف |

**طرق الدفع (payment_method):**
- `cash_on_delivery` - الدفع عند الاستلام
- `syriatel_cash` - سيرياتيل كاش
- `mtn_cash` - MTN كاش
- `bank_transfer` - تحويل بنكي
- `cham_cash` - شام كاش

**الأنواع (casts):**
- `price` → decimal:2
- `pages_count` → integer

**العلاقات:**
| العلاقة | النوع | الوصف |
|---------|-------|-------|
| `user()` | belongsTo | المستخدم المالك للكتاب |
| `category()` | belongsTo | التصنيف الأكاديمي |
| `transactions()` | hasMany | عمليات التبادل المرتبطة |
| `favorites()` | morphMany | علاقة Polymorphic للمفضلة |

**Scopes المحددة مسبقاً:**
- `scopeAvailable($query)` - الكتب المتاحة والمعتمدة (`status = available` AND `moderation_status = approved`)
- `scopePending($query)` - الكتب بانتظار المراجعة (`moderation_status = pending`)
- `scopeApproved($query)` - الكتب المعتمدة (`moderation_status = approved`)

---

### 3.3 `app/Models/Category.php`

نموذج التصنيف - يمثل التصنيفات الأكاديمية التي تنظّم الكتب والملخصات حسب الجامعة والكلية والقسم.

**الحقول القابلة للتعبئة:**
- `university_name` - اسم الجامعة (مثال: الجامعة الوطنية الخاصة)
- `faculty_name` - اسم الكلية (مثال: كلية الهندسة)
- `department_name` - اسم القسم/الاختصاص (مثال: هندسة الحواسيب)
- `study_year` - السنة الدراسية (مثال: السنة الأولى)

**العلاقات:**
| العلاقة | النوع | الوصف |
|---------|-------|-------|
| `books()` | hasMany | الكتب المنتمية لهذا التصنيف |
| `digitalNotes()` | hasMany | الملخصات المنتمية لهذا التصنيف |

**ملاحظة:** التصنيفات تُنشأ تلقائياً عبر الذكاء الاصطناعي عند رفع كتب جديدة إذا لم يتم العثور على تطابق كافٍ.

---

### 3.4 `app/Models/DigitalNote.php`

نموذج الملخصات الرقمية - ملفات PDF للملخصات والمحاضرات الدراسية.

**الحقول القابلة للتعبئة:**
- `user_id` - معرف الطالب الذي رفع الملخص
- `category_id` - معرف التصنيف الأكاديمي
- `title` - عنوان الملخص
- `description` - وصف الملخص
- `pdf_file_url` - رابط ملف PDF
- `moderation_status` - حالة المراجعة

**العلاقات:**
| العلاقة | النوع | الوصف |
|---------|-------|-------|
| `user()` | belongsTo | المستخدم الذي رفع الملخص |
| `category()` | belongsTo | التصنيف الأكاديمي |
| `favorites()` | morphMany | علاقة Polymorphic للمفضلة |

**Scopes:**
- `scopeApproved($query)` - الملخصات المعتمدة
- `scopePending($query)` - الملخصات بانتظار المراجعة

---

### 3.5 `app/Models/Transaction.php`

نموذج عملية التبادل/البيع - يمثل طلبات الحصول على الكتب بين الطالب والمالك.

**الحقول القابلة للتعبئة:**
- `book_id` - معرف الكتاب المطلوب
- `offered_book_id` - معرف الكتاب المعروض للمقايضة (في حالة التبادل)
- `requester_id` - معرف الطالب (طالب الكتاب)
- `owner_id` - معرف المالك (مالك الكتاب)
- `meeting_date` - تاريخ اللقاء
- `meeting_time` - وقت اللقاء
- `meeting_location` - مكان اللقاء
- `status` - حالة العملية

**حالات العملية (status):**
| القيمة | المعنى | اللون في Filament |
|--------|--------|-------------------|
| `pending` | قيد الانتظار | warning (أصفر) |
| `accepted` | مقبول | info (أزرق) |
| `rejected` | مرفوض | danger (أحمر) |
| `completed` | مكتمل | success (أخضر) |
| `cancelled` | ملغى | gray (رمادي) |

**الأنواع (casts):**
- `meeting_date` → date
- `meeting_time` → datetime:H:i

**العلاقات:**
| العلاقة | النوع | الوصف |
|---------|-------|-------|
| `book()` | belongsTo | الكتاب المطلوب |
| `offeredBook()` | belongsTo | الكتاب المعروض للتبادل |
| `requester()` | belongsTo | الطالب الذي طلب الكتاب |
| `owner()` | belongsTo | المالك الذي يملك الكتاب |
| `rating()` | hasOne | التقييم المرتبط بالعملية |

**Scopes:**
- `scopePending($query)` - العمليات قيد الانتظار
- `scopeCompleted($query)` - العمليات المكتملة

---

### 3.6 `app/Models/Rating.php`

نموذج التقييم - يتيح للمستخدمين تقييم بعضهم البعض بعد إتمام عملية التبادل.

**الحقول القابلة للتعبئة:**
- `transaction_id` - معرف العملية المرتبطة
- `reviewer_id` - معرف المُقيّم (من كتب التقييم)
- `reviewed_user_id` - معرف المُقيَّم (من حصل على التقييم)
- `stars` - عدد النجوم (1-5)
- `comment` - تعليق نصي

**الأنواع (casts):**
- `stars` → integer

**العلاقات:**
| العلاقة | النوع | الوصف |
|---------|-------|-------|
| `transaction()` | belongsTo | العملية المرتبطة بالتقييم |
| `reviewer()` | belongsTo | المستخدم الذي كتب التقييم |
| `reviewedUser()` | belongsTo | المستخدم الذي حصل على التقييم |

**القاعدة:** كل مستخدم في العملية يقيّم الطرف الآخر مرة واحدة فقط لكل عملية.

---

### 3.7 `app/Models/Favorite.php`

نموذج المفضلة - نظام مفضلة Polymorphic يدعم الكتب والملخصات معاً.

**الحقول القابلة للتعبئة:**
- `user_id` - معرف المستخدم
- `favoritable_id` - معرف العنصر المفضل
- `favoritable_type` - نوع العنصر (App\Models\Book أو App\Models\DigitalNote)

**العلاقات:**
| العلاقة | النوع | الوصف |
|---------|-------|-------|
| `user()` | belongsTo | المستخدم الذي أضاف المفضلة |
| `favoritable()` | morphTo | العنصر المفضل (كتاب أو ملخص) |

**ملاحظة:** تصميم Polymorphic يسمح بإضافة أي نوع من المحتوى للمفضلة مستقبلاً بدون تعديل هيكل الجدول.

---

### 3.8 `app/Models/Role.php`

نموذج الدور - تحديد صلاحيات المستخدمين.

**الحقول القابلة للتعبئة:**
- `name` - اسم الدور
- `description` - وصف الدور

**العلاقات:**
| العلاقة | النوع | الوصف |
|---------|-------|-------|
| `users()` | hasMany | المستخدمون المنتمون لهذا الدور |

**الأدوار المتوقعة:**
| الدور | الوصف |
|-------|-------|
| `Admin` | مدير المنصة - يتحكم في كل شيء عبر لوحة Filament |
| `Student` | طالب - يستخدم واجهة الموقع العامة |

---

## 4. تفصيل مجلد `app/Filament/` - لوحة الإدارة

لوحة إدارة مبنية بـ **Filament 3.3** على المسار `/admin`، يمكن فقط للمستخدمين بدور Admin الوصول إليها.

### 4.1 الموارد (Resources)

#### `BookResource.php` - إدارة الكتب

**الأيقونة:** `heroicon-o-book-open`
**مجموعة التنقل:** إدارة المحتوى
**ترتيب التنقل:** 2

**نموذج النموذج (Form):**
يحتوي على قسم واحد يحتوي على الحقول التالية مرتبة في عمودين:
- `title` - عنوان الكتاب (نص، مطلوب، حد 255 حرف)
- `author` - المؤلف (نص، مطلوب، حد 255 حرف)
- `user_id` - المستخدم (قائمة منسدلة قابلة للبحث)
- `category_id` - التصنيف (قائمة منسدلة قابلة للبحث)
- `condition` - حالة الكتاب (قائمة: excellent/good/fair/poor)
- `offer_type` - نوع العرض (قائمة: sale/exchange/donate)
- `price` - السعر (رقمي، يظهر فقط عند offer_type = sale، مع بادئة "ل.س")
- `status` - حالة الكتاب (قائمة: available/pending/sold)
- `moderation_status` - حالة المراجعة (قائمة: pending/approved/rejected)
- `cover_image_url` - رابط صورة الغلاف (URL، حد 500 حرف)

**جدول البيانات (Table):**
| العمود | الوصف | الميزات |
|--------|-------|---------|
| `title` | العنوان | قابل للبحث والترتيب، حد 30 حرف |
| `author` | المؤلف | قابل للبحث، حد 20 حرف |
| `user.full_name` | المستخدم | قابل للبحث |
| `condition` | الحالة | شارة ملونة حسب القيمة |
| `offer_type` | نوع العرض | شارة ملونة |
| `price` | السعر | بالليرة السورية، قابل للترتيب |
| `status` | حالة الكتاب | شارة ملونة |
| `moderation_status` | حالة المراجعة | شارة ملونة |
| `created_at` | تاريخ الإضافة | قابل للترتيب، مخفي افتراضياً |

**الفلترة:**
- حسب حالة المراجعة
- حسب حالة الكتاب
- حسب نوع العرض

**إجراءات مخصصة (Actions):**
| الإجراء | الأيقونة | اللون | الشرط | الوظيفة |
|---------|----------|-------|-------|---------|
| `approveBook` | check-circle | أخضر | moderation_status = pending | قبول الكتاب مع إشعار نجاح |
| `rejectBook` | x-circle | أحمر | moderation_status = pending | رفض الكتاب مع إشعار خطر |

كلا الإجراءين يتطلبان تأكيداً عبر نافذة منبثقة (modal).

**مدير العلاقات (RelationManager):**
- `TransactionsRelationManager` - عرض عمليات التبادل المرتبطة بالكتاب

**الصفحات:** قائمة (index)، إنشاء (create)، تعديل (edit)

---

#### `UserResource.php` - إدارة المستخدمين

**الأيقونة:** `heroicon-o-users`
**مجموعة التنقل:** إدارة المستخدمين
**ترتيب التنقل:** 1

**نموذج النموذج (Form):**
- `full_name` - الاسم الكامل (مطلوب، حد 255)
- `email` - البريد (مطلوب، فريد، حد 255)
- `password` - كلمة المرور (مطلوب عند الإنشاء فقط، مشفر)
- `university_id` - الرقم الجامعي (مطلوب، فريد، حد 50)
- `phone_number` - رقم الهاتف (tel، حد 20)
- `role_id` - الدور (قائمة منسدلة قابلة للبحث)
- `is_banned` - حالة الحظر (toggle، افتراضي false)

**جدول البيانات (Table):**
| العمود | الوصف | الميزات |
|--------|-------|---------|
| `id` | المعرف | قابل للترتيب |
| `full_name` | الاسم | قابل للبحث والترتيب |
| `email` | البريد | قابل للبحث والترتيب |
| `university_id` | الرقم الجامعي | قابل للبحث |
| `role.name` | الدور | شارة ملونة (Admin=أحمر، Student=أخضر) |
| `is_banned` | حالة الحظر | أيقونة (محظور=no-symbol أحمر، نشط=check-circle أخضر) |
| `created_at` | تاريخ التسجيل | قابل للترتيب، مخفي افتراضياً |

**الفلترة:**
- `TernaryFilter` لحالة الحظر (الكل / المحظورون فقط / النشطون فقط)

**إجراء مخصص:**
| الإجراء | الوظيفة |
|---------|---------|
| `toggleBan` | تبديل حالة الحظر (حظر/إلغاء حظر) مع رسالة تأكيد |

**مديرو العلاقات:**
- `BooksRelationManager` - كتب المستخدم
- `TransactionsRelationManager` - عمليات المستخدم

---

#### `CategoryResource.php` - إدارة التصنيفات

**الأيقونة:** `heroicon-o-academic-cap`
**مجموعة التنقل:** إدارة المحتوى
**ترتيب التنقل:** 4

**نموذج النموذج (Form):**
- `university_name` - اسم الجامعة (مطلوب، حد 255)
- `faculty_name` - اسم الكلية (مطلوب، حد 255)
- `department_name` - اسم القسم (مطلوب، حد 255)
- `study_year` - السنة الدراسية (مطلوب، حد 50)

**جدول البيانات (Table):**
| العمود | الميزات |
|--------|---------|
| `id` | قابل للترتيب |
| `university_name` | قابل للبحث والترتيب |
| `faculty_name` | قابل للبحث |
| `department_name` | قابل للبحث |
| `study_year` | قابل للبحث |
| `created_at` | قابل للترتيب، مخفي افتراضياً |

---

#### `DigitalNoteResource.php` - إدارة الملخصات

**الأيقونة:** `heroicon-o-document-text`
**مجموعة التنقل:** إدارة المحتوى
**ترتيب التنقل:** 3

**نموذج النموذج (Form):**
- `title` - العنوان (مطلوب، حد 255)
- `description` - الوصف (textarea، حد 1000، عرض كامل)
- `user_id` - المستخدم (قائمة منسدلة قابلة للبحث)
- `category_id` - التصنيف (قائمة منسدلة قابلة للبحث)
- `pdf_file_url` - ملف PDF (رفع ملف، أنواع مقبولة: PDF فقط، مجلد: digital_notes)
- `moderation_status` - حالة المراجعة (قائمة: pending/approved/rejected)

**إجراءات مخصصة:**
| الإجراء | الشرط |
|---------|-------|
| `approveNote` | moderation_status = pending |
| `rejectNote` | moderation_status = pending |

---

#### `TransactionResource.php` - عرض العمليات

**الأيقونة:** `heroicon-o-arrows-right-left`
**مجموعة التنقل:** المتابعة
**ترتيب التنقل:** 1

**ملاحظة هامة:** لا يمكن إنشاء عمليات من لوحة الإدارة (`canCreate() = false`)، والعرض فقط.

**نموذج النموذج (Form):** جميع الحقول **معطلة (disabled)**

**Infolist مخصص:**
يعرض تفاصيل العملية بتنسيق بطاقات مع ألوان حسب الحالة:
- pending → أصفر
- accepted → أزرق
- rejected → أحمر
- completed → أخضر
- cancelled → رمادي

**جدول البيانات (Table):**
| العمود | الوصف |
|--------|-------|
| `book.title` | عنوان الكتاب (حد 25 حرف) |
| `requester.full_name` | اسم الطالب |
| `owner.full_name` | اسم المالك |
| `status` | الحالة (شارة ملونة) |
| `meeting_date` | تاريخ اللقاء |
| `created_at` | تاريخ الإنشاء |

**الفلترة:** حسب حالة العملية

---

#### `RatingResource.php` - عرض التقييمات

**الأيقونة:** `heroicon-o-star`
**مجموعة التنقل:** المتابعة
**ترتيب التنقل:** 2

**ملاحظة:** لا يمكن إنشاء تقييمات من لوحة الإدارة (`canCreate() = false`).

**نموذج النموذج (Form):** جميع الحقول معطلة (disabled)

**Infolist:** عرض تفصيلي للتقييم مع عرض النجوم كرموز ⭐

**جدول البيانات (Table):**
| العمود | الوصف |
|--------|-------|
| `transaction_id` | معرف العملية |
| `reviewer.full_name` | المُقيّم |
| `reviewedUser.full_name` | المُقيَّم |
| `stars` | النجوم (معروضة كرموز ⭐) |
| `comment` | التعليق (حد 40 حرف) |
| `created_at` | التاريخ |

**الإجراءات:** عرض فقط (View) وحذف (Delete)

---

### 4.2 عناصر لوحة المعلومات (Widgets)

#### `StatsOverviewWidget.php`
إحصائيات عامة مع رسوم بيانية مصغرة (sparklines):
| الإحصائية | اللون | الأيقونة | الرسم البياني |
|-----------|-------|----------|---------------|
| إجمالي الطلاب المسجلين | أخضر | user-group | آخر 30 يوم |
| إجمالي الكتب | أزرق | book-open | آخر 30 يوم |
| إجمالي الملخصات | برتقالي | document-text | آخر 30 يوم |
| كتب بانتظار المراجعة | أحمر | clock | بدون رسم |

#### `BooksChartWidget.php`
رسم بياني خطي (Line Chart) يعرض عدد الكتب المضافة شهرياً خلال آخر 12 شهر.
- اللون: برتقالي (rgb(245, 158, 11))
- ملء أسفل الخط مع شفافية
- التوتر (tension): 0.3

#### `LatestTransactionsWidget.php`
جدول يعرض آخر 5 عمليات تبادل:
| العمود | الوصف |
|--------|-------|
| `book.title` | عنوان الكتاب (حد 25) |
| `requester.full_name` | الطالب |
| `owner.full_name` | المالك |
| `status` | الحالة (شارة ملونة) |
| `created_at` | التاريخ والوقت |

---

### 4.3 صفحة تسجيل الدخول المخصصة

#### `RedirectToMainLogin.php`
تحويل صفحة تسجيل دخول Filament إلى صفحة تسجيل الدخول الرئيسية للموقع:
- إذا كان المستخدم مسجل الدخول بالفعل → توجيه إلى `/admin`
- إذا لم يكن مسجلاً → توجيه إلى `/login`

---

## 5. تفصيل مجلد `app/Http/` - المتحكمات (Controllers)

### 5.1 المتحكمات العامة

#### `HomeController.php`

| الدالة | المسار | الوصف |
|--------|--------|-------|
| `index()` | `GET /` | الصفحة الرئيسية: جلب آخر 8 كتب معتمدة + آخر 4 ملخصات + إحصائيات |

**البيانات المرسلة للعرض:**
- `latestBooks` - آخر 8 كتب معتمدة ومتاحة (مع المستخدم والتصنيف)
- `latestNotes` - آخر 4 ملخصات معتمدة (مع المستخدم والتصنيف)
- `stats` - إحصائيات: عدد الكتب المعتمدة، عدد الملخصات المعتمدة، عدد المستخدمين

---

#### `BrowseController.php`

| الدالة | المسار | الوصف |
|--------|--------|-------|
| `books()` | `GET /books` | تصفح الكتب مع فلاتر |
| `showBook()` | `GET /books/{book}` | تفاصيل كتاب محدد |
| `notes()` | `GET /notes` | تصفح الملخصات مع فلاتر |
| `showNote()` | `GET /notes/{note}` | تفاصيل ملخص محدد |
| `userRatings()` | `GET /users/{user}/ratings` | تقييمات مستخدم محدد |

**تفصيل `books()`:**
- فلاتر: بحث نصي (عنوان أو مؤلف)، نوع العرض، حالة الكتاب، التصنيف
- ترتيب: الأحدث أولاً
- ترقيم الصفحات: 12 كتاب بالصفحة

**تفصيل `showBook()`:**
- التحقق من أن الكتاب معتمد ومتاح (وإلا 404)
- جلب كتب الطالب المتاحة للتبادل (إذا كان مسجلاً)
- جلب 4 كتب مشابهة من نفس التصنيف

---

#### `ProfileController.php`

| الدالة | المسار | الوصف |
|--------|--------|-------|
| `edit()` | `GET /profile` | عرض نموذج تعديل الملف الشخصي |
| `update()` | `PUT /profile` | تحديث البيانات (إذا تغير البريد → إلغاء التحقق) |
| `destroy()` | `DELETE /profile` | حذف الحساب مع التحقق من كلمة المرور |

---

### 5.2 متحكمات الطالب `Student/`

#### `BookController.php`

التحكم الكامل في دورة حياة الكتب للطالب المسجل.

| الدالة | المسار | الوصف |
|--------|--------|-------|
| `index()` | `GET /student/books` | قائمة كتب الطالب مع فلاتر وترتيب |
| `create()` | `GET /student/books/create` | نموذج إضافة كتاب جديد |
| `store()` | `POST /student/books` | حفظ الكتاب مع فحص AI |
| `edit()` | `GET /student/books/{book}/edit` | نموذج تعديل كتاب |
| `update()` | `PUT /student/books/{book}` | تحديث بيانات الكتاب |
| `destroy()` | `DELETE /student/books/{book}` | حذف الكتاب |

**تفصيل `index()`:**
- عرض كتب الطالب الحالي فقط (`Auth::user()->books()`)
- فلاتر: بحث نصي، نوع العرض، حالة الكتاب، حالة المراجعة، التصنيف
- ترتيب: الأحدث، الأقدم، حسب العنوان (تصاعدي/تنازلي)، حسب السعر

**تفصيل `store()`:**
1. التحقق من البيانات المدخلة
2. رفع صورة الغلاف إلى `books/covers` في التخزين العام
3. استخراج بيانات الكتاب بالذكاء الاصطناعي (`extractBookDetails`)
4. إذا كانت الصورة مرفوضة → حذف الملف وإرجاع رسالة خطأ
5. الموافقة التلقائية على الكتاب (`moderation_status = approved`)
6. إنشاء الكتاب وربطه بالطالب

**حماية:** `edit()` و `update()` و `destroy()` تتحقق من أن الكتاب يخص الطالب الحالي فقط (`$book->user_id !== Auth::id()` → 403).

---

#### `DigitalNoteController.php`

| الدالة | المسار | الوصف |
|--------|--------|-------|
| `index()` | `GET /student/notes` | قائمة ملخصات الطالب |
| `create()` | `GET /student/notes/create` | نموذج رفع ملخص |
| `store()` | `POST /student/notes` | حفظ الملخص مع فحص AI |
| `edit()` | `GET /student/notes/{note}/edit` | نموذج تعديل ملخص |
| `update()` | `PUT /student/notes/{note}` | تحديث الملخص |
| `destroy()` | `DELETE /student/notes/{note}` | حذف الملخص |

**تفصيل `store()`:**
1. التحقق من البيانات (PDF فقط، حد 25MB)
2. رفع الملف إلى `notes/pdfs`
3. فحص محتوى PDF بالذكاء الاصطناعي (`moderatePdf`)
4. إذا كان المحتوى غير آمن → حذف الملف وإرجاع رسالة خطأ
5. الموافقة التلقائية (`moderation_status = approved`)

**تفصيل `update()`:** إذا تم رفع ملف PDF جديد، يتم فحصه بالذكاء الاصطناعي أيضاً.

---

#### `TransactionController.php`

| الدالة | المسار | الوصف |
|--------|--------|-------|
| `index()` | `GET /student/transactions` | قائمة العمليات |
| `store()` | `POST /student/transactions` | إنشاء طلب جديد |
| `update()` | `PUT /student/transactions/{transaction}` | تحديث حالة العملية |

**تفصيل `index()`:**
- فلاتر:
  - نوع العملية: وارد (`incoming`) / صادر (`outgoing`) / الكل
  - حالة العملية
  - بحث نصي (اسم الكتاب أو اسم الطرف الآخر)

**تفصيل `store()`:**
1. التحقق من وجود الكتاب
2. منع طلب كتاب المستخدم نفسه
3. منع تكرار الطلب (إذا كان هناك طلب pending أو accepted لنفس الكتاب)
4. إنشاء العملية بحالة `pending`

**تفصيل `update()`:**
- حماية: فقط طرفي العملية يمكنهما التعديل
- تحديث حالة الكتاب تلقائياً:
  - `accepted` → حالة الكتاب تصبح `pending`
  - `completed` → حالة الكتاب تصبح `sold`
  - `cancelled` → حالة الكتاب تعود `available`

---

#### `FavoriteController.php`

| الدالة | المسار | الوصف |
|--------|--------|-------|
| `index()` | `GET /student/favorites` | عرض قائمة المفضلة |
| `store()` | `POST /student/favorites` | إضافة للمفضلة |
| `destroy()` | `DELETE /student/favorites/{favorite}` | حذف من المفضلة |

**تفصيل `store()`:**
- يقبل `favoritable_id` و `favoritable_type` (book أو note)
- يحول النوع إلى اسم الكلاس الكامل (`Book::class` أو `DigitalNote::class`)
- يتحقق من عدم وجود العنصر مسبقاً في المفضلة

---

#### `RatingController.php`

| الدالة | المسار | الوصف |
|--------|--------|-------|
| `index()` | `GET /student/ratings` | عرض التقييمات |
| `store()` | `POST /student/ratings` | إضافة تقييم |
| `destroy()` | `DELETE /student/ratings/{rating}` | حذف تقييم |

**تفصيل `store()`:**
1. التحقق من صحة المدخلات (نجوم 1-5، تعليق اختياري حد 500 حرف)
2. التحقق أن المستخدم طرف في العملية
3. التحقق أن العملية مكتملة (`status = completed`)
4. منع تكرار التقييم (تقييم واحد لكل عملية لكل مستخدم)
5. تحديد الطرف المُقيَّم تلقائياً (الطرف الآخر في العملية)

**تفصيل `index()`:**
- `receivedRatings` - التقييمات التي تلقاها الطالب
- `givenRatings` - التقييمات التي كتبها الطالب

---

#### `DashboardController.php`

| الدالة | المسار | الوصف |
|--------|--------|-------|
| `index()` | `GET /student/dashboard` | لوحة تحكم الطالب |

**البيانات المرسلة:**
- `booksCount` - عدد كتب الطالب
- `notesCount` - عدد ملخصات الطالب
- `pendingRequests` - عدد طلبات التسليم المعلقة (حيث الطالب هو المالك)

---

#### `ProfileController.php` (Student)

| الدالة | المسار | الوصف |
|--------|--------|-------|
| `edit()` | `GET /student/profile` | عرض نموذج تعديل الملف الشخصي |
| `update()` | `PUT /student/profile` | تحديث البيانات |

**البيانات القابلة للتعديل:** `full_name`, `email`, `phone_number`, `university_id`
**ملاحظة:** إذا تغير البريد → يتم إلغاء التحقق من البريد الإلكتروني.

---

### 5.3 متحكم API - `AiController.php`

واجهة برمجة تطبيقات للذكاء الاصطناعي - تعمل عبر AJAX من واجهة الطالب.

| الدالة | المسار | الوصف |
|--------|--------|-------|
| `extractBookDetails()` | `POST /api/ai/extract-book` | استخراج بيانات الكتاب من صورة الغلاف |
| `predictPrice()` | `POST /api/ai/predict-price` | اقتراح سعر عادل للكتاب |
| `moderatePdf()` | `POST /api/ai/moderate-pdf` | فحص محتوى PDF |
| `analyzePdfContent()` | `POST /api/ai/analyze-pdf-content` | تحليل محتوى PDF واستخراج وصف |

**تفصيل `extractBookDetails()`:**
1. التحقق من الصورة (صورة، حد 5MB)
2. رفع الصورة مؤقتاً إلى `temp/covers`
3. جلب قائمة التصنيفات الموجودة
4. استدعاء `GeminiAiService::extractBookDetails()`
5. إذا كانت الصورة مرفوضة → إرجاع خطأ 422
6. البحث عن تصنيف مطابق أو إنشاء تصنيف جديد عبر `findOrCreateCategory()`
7. حذف الملف المؤقت
8. إرجاع البيانات المستخرجة

**تفصيل `findOrCreateCategory()`:**
خوارزمية مطابقة ذكية للتصنيفات:
1. إذا كانت جميع الحقول فارغة → إرجاع null
2. المرور على جميع التصنيفات الموجودة ومقارنة كل حقل
3. إذا تطابق حقلان أو أكثر → استخدام التصنيف الموجود
4. إذا لم يتم العثور على تطابق → إنشاء تصنيف جديد بقيم افتراضية

---

### 5.4 متحكمات المصادقة `Auth/`

(9 ملفات من Laravel Breeze):

| المتحكم | الوظيفة |
|---------|---------|
| `RegisteredUserController` | تسجيل حساب جديد |
| `AuthenticatedSessionController` | تسجيل الدخول والخروج |
| `PasswordResetLinkController` | إرسال رابط إعادة تعيين كلمة المرور |
| `NewPasswordController` | إعادة تعيين كلمة المرور |
| `EmailVerificationPromptController` | عرض رسالة التحقق من البريد |
| `VerifyEmailController` | التحقق من البريد الإلكتروني |
| `EmailVerificationNotificationController` | إعادة إرسال رسالة التحقق |
| `ConfirmablePasswordController` | تأكيد كلمة المرور قبل العمليات الحساسة |
| `PasswordController` | تغيير كلمة المرور |

---

## 6. البرمجيات الوسيطة (Middleware)

### `CheckBanned.php`
يفحص في كل طلب هل المستخدم المحظور:
```
إذا المستخدم مسجل الدخول AND is_banned = true
  → تسجيل الخروج
  → إبطال الجلسة
  → تجديد رمز CSRF
  → إعادة توجيه لصفحة تسجيل الدخول مع رسالة خطأ
```

### `RedirectIfNotAdmin.php`
يحمي مسارات الإدارة:
```
إذا غير مسجل الدخول → إعادة توجيه لتسجيل الدخول
إذا ليس مدير → إعادة توجيه للصفحة الرئيسية
```

### `SetLocale.php`
يضبط لغة التطبيق من الجلسة:
```
إذا وجود locale في الجلسة
  → App::setLocale(locale)
```

---

## 7. خدمات الأعمال `app/Services/`

### `GeminiAiService.php` (567 سطر)

خدمة الذكاء الاصطناعي المركزية - تعمل عبر LiteLLM Proxy بتنسيق OpenAI-compatible.

**الإعدادات (من `config/ai.php`):**
| الإعداد | المصدر | القيمة الافتراضية |
|---------|--------|-------------------|
| `api_url` | AI_API_URL (env) | - |
| `api_key` | AI_API_KEY (env) | - |
| `model` | AI_MODEL (env) | gemini-3-flash-preview |
| `max_tokens` | AI_MAX_TOKENS (env) | 4096 |
| `temperature` | AI_TEMPERATURE (env) | 0.7 |

---

#### الوظيفة 1: `extractBookDetails($imageUrl, $categoriesList)`

**الغرض:** استخراج بيانات الكتاب من صورة الغلاف (Smart Auto-Fill)

**آلية العمل:**
1. التحقق من وجود إعدادات API
2. تحويل الصورة إلى Base64
3. تحديد نوع MIME
4. بناء prompt مفصل يطلب من AI:
   - التحقق من أن الصورة لغلاف كتاب/محاضرة (رفض أسئلة الامتحانات والصور الشخصية)
   - استخراج: العنوان، المؤلف، الحالة، السنة الدراسية، القسم، الكلية، الجامعة
   - مطابقة التصنيف مع القائمة المرفقة
5. إرسال الطلب مع الصورة كـ multimodal content
6. تحليل الاستجابة JSON
7. إذا كانت الصورة غير مقبولة → إرجاع `rejected = true` مع سبب الرفض
8. التحقق من صحة حالة الكتاب (condition) ضمن القيم المسموحة

**البيانات المستخرجة:**
```json
{
  "title": "عنوان الكتاب",
  "author": "المؤلف",
  "condition": "excellent/good/fair/poor",
  "study_year": "السنة الدراسية",
  "department_name": "القسم",
  "faculty_name": "الكلية",
  "university_name": "الجامعة",
  "category_id": null
}
```

**خوارزمية استنتاج القسم:**
- برمجة/حواسيب/شبكات → هندسة الحواسيب
- اتصالات/إشارات → هندسة الاتصالات
- كهرباء/إلكترونيات → هندسة كهربائية
- ميكانيكا/آلات → هندسة ميكانيكية
- بناء/تصميم → هندسة مدنية
- إدارة/محاسبة → إدارة أعمال
- طب/تشريح → طب بشري
- حقوق/قانون → قانون
- رياضيات/فيزياء → علوم أساسية
- لغات/ترجمة → آداب

---

#### الوظيفة 2: `predictPrice($title, $condition, $author, $pagesCount)`

**الغرض:** اقتراح سعر عادل للكتاب بالليرة السورية

**خوارزمية التسعير:**
| الحالة | سعر الصفحة | مثال (200 صفحة) |
|--------|------------|------------------|
| excellent | 1.25 ل.س | 250 ل.س |
| good | 1.0 ل.س | 200 ل.س |
| fair | 0.75 ل.س | 150 ل.س |
| poor | 0.5 ل.س | 100 ل.س |

**آلية العمل:**
1. حساب السعر الأساسي = عدد الصفحات × سعر الصفحة حسب الحالة
2. إرسال طلب للذكاء الاصطناعي مع:
   - اسم الكتاب والمؤلف والحالة
   - عدد الصفحات والسعر المحسوب
   - تعليمات بعدم الابتعاد عن السعر المحسوب
3. التحقق من أن السعر المقترح ضمن الحدود:
   - إذا كان السعر المحسوب معروفاً → أقصى 120% من السعر المحسوب
   - إذا لم يكن معروفاً → أقصى 500 ل.س

**القيم الاحتياطية (fallback) عند فشل AI:**
| الحالة | السعر الافتراضي |
|--------|-----------------|
| excellent | 250 ل.س |
| good | 175 ل.س |
| fair | 100 ل.س |
| poor | 50 ل.س |
| أخرى | 150 ل.س |

---

#### الوظيفة 3: `moderateImage($imageUrl)`

**الغرض:** فحص الصورة للتأكد من أنها لغلاف كتاب/محاضرة فقط

**آلية العمل:**
1. تحويل الصورة إلى Base64
2. إرسال طلب للذكاء الاصطناعي مع قواعد صارمة:
   - هل تحتوي على محتوى غير لائق؟
   - هل الصورة لغلاف كتاب أو محاضرة؟
   - رفض: أسئلة امتحانات، أوراق اختبارات، كشوف درجات، صور شخصية
3. إرجاع: `['safe' => bool, 'reason' => string]`

---

#### الوظيفة 4: `moderatePdf($pdfPath)`

**الغرض:** فحص ملف PDF للتأكد من أنه محتوى دراسي أكاديمي

**آلية العمل:**
1. استخراج النص من PDF باستخدام `smalot/pdfparser`
2. تنظيف النص (إزالة علامة CamScanner المائية)
3. إذا كان النص أقل من 20 حرف:
   - إذا كانت هناك صفحات → اعتباره آمناً
   - إذا لم توجد صفحات → رفض
4. إرسال عينة النص (4000 حرف أولى) للذكاء الاصطناعي
5. التحقق من أن المحتوى يحتوي على واحد على الأقل:
   - ملخص لمادة دراسية
   - كتاب أو فصل جامعي
   - ملاحظات دراسية أو حل مسائل
   - بحث علمي
   - شرح مفاهيم أكاديمية

---

#### الوظيفة 5: `analyzePdfContent($pdfPath, $originalName)`

**الغرض:** استخراج وصف مختصر لمحتوى PDF (بحد أقصى 300 حرف)

**3 طبقات احتياطية:**

**الطبقة 1:** استخراج النص المباشر
- استخدام `smalot/pdfparser` لاستخراج النص
- إرسال أول 6000 حرف للذكاء الاصطناعي
- طلب وصف مختصر بالمحتوى

**الطبقة 2:** استخدام FPDI لاستخراج الصفحات كصور
- إذا فشل استخراج النص (أقل من 20 حرف)
- استخراج أول 3 صفحات كـ PDF مصغر
- إرسال الصفحات كصور للذكاء الاصطناعي (multimodal)
- مهلة أطول: 120 ثانية

**الطبقة 3:** استخدام اسم الملف الأصلي
- إذا فشلت كل الطرق السابقة
- إرسال اسم الملف فقط للذكاء الاصطناعي
- طلب وصف متوقع بناءً على الاسم

---

#### الدالة المساعدة: `sendRequest($messages, $timeout)`

**آلية الاتصال:**
- تنسيق OpenAI-compatible عبر LiteLLM Proxy
- Headers: `Content-Type: application/json` + `x-litellm-api-key`
- Payload: `model`, `messages`, `max_tokens`, `temperature`
- إعادة المحاولة: 3 مرات مع تأخير 2000ms
- مهلة افتراضية: 60 ثانية (قابلة للتخصيص)
- تسجيل جميع الطلبات والاستجابات عبر Log

---

## 8. التوجيهات (Routes)

### `routes/web.php`

#### المسارات العامة (بدون تسجيل دخول):

| الطريقة | المسار | المتحكم | الوصف |
|---------|--------|---------|-------|
| GET | `/locale/{locale}` | Closure | تبديل اللغة (ar/en) |
| GET | `/` | HomeController@index | الصفحة الرئيسية |
| GET | `/books` | BrowseController@books | تصفح الكتب |
| GET | `/books/{book}` | BrowseController@showBook | تفاصيل كتاب |
| GET | `/notes` | BrowseController@notes | تصفح الملخصات |
| GET | `/notes/{note}` | BrowseController@showNote | تفاصيل ملخص |
| GET | `/users/{user}/ratings` | BrowseController@userRatings | تقييمات مستخدم |

#### مسارات الطالب (تتطلب تسجيل دخول):

| الطريقة | المسار | المتحكم | الوصف |
|---------|--------|---------|-------|
| GET | `/student/dashboard` | DashboardController@index | لوحة تحكم الطالب |
| GET | `/student/profile` | ProfileController@edit | الملف الشخصي |
| PUT | `/student/profile` | ProfileController@update | تحديث الملف الشخصي |
| GET/POST/PUT/DELETE | `/student/books` | BookController (Resource) | CRUD كتب الطالب |
| GET/POST/PUT/DELETE | `/student/notes` | DigitalNoteController (Resource) | CRUD ملخصات الطالب |
| GET/POST/PUT | `/student/transactions` | TransactionController (Resource) | عمليات التبادل |
| GET/POST/DELETE | `/student/favorites` | FavoriteController (Resource) | المفضلة |
| GET/POST/DELETE | `/student/ratings` | RatingController (Resource) | التقييمات |

#### مسارات API للذكاء الاصطناعي (AJAX، تتطلب تسجيل دخول):

| الطريقة | المسار | المتحكم | الوصف |
|---------|--------|---------|-------|
| POST | `/api/ai/extract-book` | AiController@extractBookDetails | استخراج بيانات كتاب |
| POST | `/api/ai/predict-price` | AiController@predictPrice | اقتراح سعر |
| POST | `/api/ai/moderate-pdf` | AiController@moderatePdf | فحص PDF |
| POST | `/api/ai/analyze-pdf-content` | AiController@analyzePdfContent | تحليل محتوى PDF |

### `routes/auth.php`

مسارات Laravel Breeze للتسجيل وتسجيل الدخول:
- تسجيل حساب جديد (`/register`)
- تسجيل الدخول (`/login`)
- إعادة تعيين كلمة المرور (`/forgot-password`, `/reset-password/{token}`)
- التحقق من البريد (`/verify-email`, `/email/verification-notification`)
- تأكيد كلمة المرور (`/confirm-password`)
- تغيير كلمة المرور (`/password`)
- تسجيل الخروج (`/logout`)

---

## 9. قاعدة البيانات (Migrations)

16 ملف تهجرة تشكل الجداول التالية:

| التهجرة | الجدول | الوصف |
|---------|--------|-------|
| `create_roles_table` | roles | أدوار المستخدمين |
| `create_categories_table` | categories | التصنيفات الأكاديمية |
| `create_users_table` | users | المستخدمين |
| `create_books_table` | books | الكتب |
| `create_digital_notes_table` | digital_notes | الملخصات الرقمية |
| `create_transactions_table` | transactions | عمليات التبادل |
| `create_ratings_table` | ratings | التقييمات |
| `create_favorites_table` | favorites | المفضلة (Polymorphic) |
| `create_cache_table` | cache | التخزين المؤقت |
| `create_jobs_table` | jobs | الطوابير |
| `add_soft_deletes_to_books_notes_and_users` | - | إضافة حذف ناعم |
| `modify_favorites_to_polymorphic` | favorites | تحويل المفضلة لنظام Polymorphic |
| `add_exchange_columns_to_books_and_transactions` | - | إضافة أعمدة التبادل |
| `add_pages_count_to_books_table` | - | إضافة عدد الصفحات |
| `add_content_description_and_payment_method_to_books_table` | - | إضافة وصف المحتوى وطريقة الدفع |
| `restructure_engineering_categories` | - | إعادة هيكلة التصنيفات الهندسية |

---

## 10. ملفات الإعدادات `config/`

### `config/ai.php` - إعدادات الذكاء الاصطناعي
```php
return [
    'api_url'     => env('AI_API_URL'),       // رابط خادم LiteLLM
    'api_key'     => env('AI_API_KEY'),        // مفتاح API
    'model'       => env('AI_MODEL', 'gemini-3-flash-preview'),  // النموذج
    'max_tokens'  => env('AI_MAX_TOKENS', 4096),     // الحد الأقصى للرموز
    'temperature' => env('AI_TEMPERATURE', 0.7),     // درجة الإبداع
];
```

### ملفات الإعدادات الأخرى:
| الملف | الوظيفة |
|-------|---------|
| `app.php` | إعدادات التطبيق العامة (الاسم، المنطقة الزمنية، اللغة) |
| `auth.php` | إعدادات المصادقة والحماية |
| `database.php` | إعدادات قاعدة البيانات (SQLite/MySQL) |
| `filesystems.php` | إعدادات نظام الملفات (local, public, s3) |
| `cache.php` | إعدادات التخزين المؤقت |
| `logging.php` | إعدادات التسجيل (log channels) |
| `mail.php` | إعدادات البريد الإلكتروني |
| `queue.php` | إعدادات الطوابير |
| `services.php` | إعدادات الخدمات الخارجية |
| `session.php` | إعدادات الجلسات |

---

## 11. مزودو الخدمات `app/Providers/`

### `AppServiceProvider.php`

1. **تهيئة Filament Language Switch:**
   - اللغات المدعومة: عربي (`ar`) وإنجليزي (`en`)
   - مرئي داخل وخارج لوحات Filament
   - مسارات خارج اللوحة: تسجيل الدخول، الملف الشخصي، التسجيل

2. **حقن كود RTL مخصص:**
   - تسجيل render hook في `panels::head.end`
   - يحقن قالب Blade `components.filament-rtl` لدعم RTL في لوحة الإدارة

### `AdminPanelProvider.php`

تهيئة لوحة إدارة Filament:
| الإعداد | القيمة |
|---------|--------|
| المعرف | `admin` |
| المسار | `/admin` |
| اللون الأساسي | Amber |
| تسجيل الدخول | RedirectToMainLogin (إعادة توجيه لصفحة الموقع) |
| اكتشاف تلقائي | Resources, Pages, Widgets |

**Middleware المطبقة:**
- EncryptCookies
- AddQueuedCookiesToResponse
- StartSession
- AuthenticateSession
- ShareErrorsFromSession
- VerifyCsrfToken
- SubstituteBindings
- DisableBladeIconComponents
- DispatchServingFilamentEvent
- SetLocale
- RedirectIfNotAdmin (auth middleware)

---

## 12. مكونات العرض `app/View/Components/`

| المكون | الوظيفة | القالب |
|--------|---------|--------|
| `AppLayout.php` | تخطيط للمستخدمين المسجلين | `layouts.app` |
| `GuestLayout.php` | تخطيط للزوار | `layouts.guest` |

---

## 13. هيكل القوالب `resources/views/`

```
views/
├── auth/                    ← قوالب تسجيل الدخول والتسجيل (Breeze)
│   ├── login.blade.php         ← نموذج تسجيل الدخول
│   ├── register.blade.php      ← نموذج إنشاء حساب
│   ├── verify-email.blade.php  ← صفحة التحقق من البريد
│   ├── forgot-password.blade.php ← طلب إعادة تعيين كلمة المرور
│   └── reset-password.blade.php  ← نموذج إعادة تعيين كلمة المرور
├── books/                   ← قوالب تصفح الكتب
│   ├── index.blade.php         ← قائمة الكتب المتاحة
│   └── show.blade.php          ← تفاصيل كتاب محدد
├── components/              ← مكونات Blade مشتركة
│   └── filament-rtl.blade.php  ← كود RTL مخصص لـ Filament
├── layouts/                 ← التخطيطات الرئيسية
│   ├── app.blade.php           ← تخطيط التطبيق (للمستخدمين المسجلين)
│   └── guest.blade.php         ← تخطيط الزوار
├── notes/                   ← قوالب تصفح الملخصات
│   ├── index.blade.php         ← قائمة الملخصات المتاحة
│   └── show.blade.php          ← تفاصيل ملخص محدد
├── profile/                 ← قوالب الملف الشخصي
│   └── edit.blade.php          ← تعديل الملف الشخصي
├── student/                 ← قوالب واجهة الطالب
│   ├── dashboard.blade.php     ← لوحة تحكم الطالب
│   ├── books/
│   │   ├── index.blade.php     ← قائمة كتب الطالب
│   │   ├── create.blade.php    ← نموذج إضافة كتاب
│   │   └── edit.blade.php      ← نموذج تعديل كتاب
│   ├── notes/
│   │   ├── index.blade.php     ← قائمة ملخصات الطالب
│   │   ├── create.blade.php    ← نموذج رفع ملخص
│   │   └── edit.blade.php      ← نموذج تعديل ملخص
│   ├── transactions/
│   │   └── index.blade.php     ← قائمة العمليات
│   ├── favorites/
│   │   └── index.blade.php     ← قائمة المفضلة
│   ├── ratings/
│   │   └── index.blade.php     ← قائمة التقييمات
│   └── profile/
│       └── edit.blade.php      ← تعديل الملف الشخصي
├── users/                   ← قوالب عرض تقييمات المستخدمين
│   └── ratings.blade.php       ← تقييمات مستخدم محدد
├── dashboard.blade.php      ← لوحة تحكم عامة
├── home.blade.php           ← الصفحة الرئيسية
└── welcome.blade.php        ← صفحة الترحيب الافتراضية
```

---

## 14. الحزم المستخدمة

### الحزم الأساسية (require):

| الحزمة | الإصدار | الوظيفة |
|--------|---------|---------|
| `laravel/framework` | ^12.0 | إطار العمل الأساسي |
| `filament/filament` | 3.3 | لوحة الإدارة |
| `bezhansalleh/filament-language-switch` | * | تبديل اللغة في Filament |
| `laravel/tinker` | ^2.10 | واجهة تفاعلية مع التطبيق |
| `setasign/fpdf` | ^1.8 | إنشاء ملفات PDF |
| `setasign/fpdi` | ^2.6 | استيراد صفحات PDF موجودة |
| `smalot/pdfparser` | * | تحليل واستخراج نص من PDF |

### حزم التطوير (require-dev):

| الحزمة | الإصدار | الوظيفة |
|--------|---------|---------|
| `laravel/breeze` | ^2.4 | نظام المصادقة والتسجيل |
| `fakerphp/faker` | ^1.23 | بيانات وهمية للاختبار |
| `laravel/pail` | ^1.2.2 | مراقبة السجلات |
| `laravel/pint` | ^1.24 | تنسيق الكود |
| `laravel/sail` | ^1.41 | بيئة تطبيق Docker |
| `mockery/mockery` | ^1.6 | كائنات وهمية للاختبار |
| `nunomaduro/collision` | ^8.6 | عرض الأخطاء بشكل جميل |
| `pestphp/pest` | ^3.8 | إطار الاختبار |
| `pestphp/pest-plugin-laravel` | ^3.2 | تكامل Pest مع Laravel |

---

## 15. مخطط تدفق العمليات الرئيسية

### 15.1 تدفق رفع كتاب جديد

```
الطالب يضغط "إضافة كتاب"
    ↓
عرض نموذج إضافة كتاب (مع تصنيفات)
    ↓
الطالب يرفع صورة الغلاف + يملأ البيانات
    ↓
[API] إرسال الصورة لـ GeminiAiService
    ↓
    ├── الصورة مقبولة → استخراج البيانات تلقائياً
    │       ↓
    │   [API] مطابقة التصنيف أو إنشاء تصنيف جديد
    │       ↓
    │   حفظ الكتاب (moderation_status = approved)
    │       ↓
    │   إعادة توجيه لقائمة الكتب مع رسالة نجاح
    │
    └── الصورة مرفوضة → حذف الملف + رسالة خطأ
```

### 15.2 تدفق عملية التبادل

```
الطالب (أ) يتصفح كتاباً متاحاً
    ↓
يضغط "طلب الكتاب" (اختيارياً: يعرض كتاباً للتبادل)
    ↓
إنشاء Transaction (status: pending)
    ↓
مالك الكتاب (ب) يرى الطلب الوارد
    ↓
    ├── قبول (status: accepted)
    │       ↓
    │   تحديد موعد ومكان التسليم
    │       ↓
    │   إتمام التسليم (status: completed)
    │   → حالة الكتاب: sold
    │       ↓
    │   الطرفان يقيّمان بعضهما ← Rating
    │
    ├── رفض (status: rejected)
    │
    └── إلغاء (status: cancelled)
        → حالة الكتاب تعود: available
```

### 15.3 تدفق رفع ملخص رقمي

```
الطالب يرفع ملف PDF
    ↓
[API] فحص محتوى PDF (moderatePdf)
    ↓
    ├── المحتوى آمن ← موافقة تلقائية (moderation_status = approved)
    │
    └── المحتوى غير آمن → حذف الملف + رسالة خطأ
```

---

## 16. ميزات الذكاء الاصطناعي

### 16.1 التعبئة التلقائية الذكية (Smart Auto-Fill)
- تصوير غلاف الكتاب → استخراج العنوان، المؤلف، الحالة، التصنيف الأكاديمي تلقائياً
- مطابقة التصنيف مع القائمة الموجودة أو إنشاء تصنيف جديد
- رفض الصور غير التعليمية (أسئلة امتحانات، صور شخصية، إلخ)

### 16.2 التسعير الذكي
- اقتراح سعر عادل بناءً على حالة الكتاب وعدد الصفحات بالليرة السورية
- خوارزمية: سعر الصفحة × عدد الصفحات حسب الحالة
- حدود الأسعار: أقصى 120% من السعر المحسوب أو 500 ل.س

### 16.3 مراقبة المحتوى
- **فحص الصور:** التأكد من أن الصورة لغلاف كتاب/محاضرة فقط
- **فحص PDF:** التأكد من أن المحتوى دراسي أكاديمي (رفض الروايات، الإعلانات، المحتوى غير اللائق)

### 16.4 تحليل المحتوى
- استخراج وصف تلقائي لمحتوى PDF بـ 3 طبقات احتياطية:
  1. استخراج النص المباشر
  2. تحويل الصفحات لصور (FPDI)
  3. استخدام اسم الملف

---

## 17. نظام الأمان والحماية

### 17.1 حماية المسارات
| Middleware | الوظيفة |
|-----------|---------|
| `auth` | التحقق من تسجيل الدخول |
| `CheckBanned` | فحص حالة الحظر |
| `RedirectIfNotAdmin` | حماية لوحة الإدارة |
| `SetLocale` | ضبط اللغة من الجلسة |
| `VerifyCsrfToken` | حماية CSRF |
| `EncryptCookies` | تشفير الكوكيز |

### 17.2 حماية البيانات
- **التحقق من الملكية:** التأكد أن الكتاب/الملخص يخص المستخدم قبل التعديل/الحذف
- **منع التكرار:** منع تكرار طلبات التبادل والتقييمات
- **الحذف الناعم:** Soft Deletes للكتب والملخصات والمستخدمين
- **منع طلب كتاب المستخدم نفسه:** في TransactionController

### 17.3 مراقبة المحتوى
- فحص كل صورة بالذكاء الاصطناعي قبل النشر
- فحص كل ملف PDF بالذكاء الاصطناعي قبل النشر
- رفع الملفات المؤقتة ثم حذفها بعد الفحص

### 17.4 حماية المصادقة
- تشفير كلمات المرور تلقائياً (Laravel hash)
- إلغاء التحقق من البريد عند تغييره
- تأكيد كلمة المرور قبل العمليات الحساسة
- حماية CSRF على جميع النماذج

---

## 18. الترجمة والتدويل

### اللغات المدعومة:
- **العربية (ar):** اللغة الافتراضية
- **الإنجليزية (en):** لغة ثانوية

### آلية التبديل:
- المسار: `GET /locale/{locale}` (ar أو en)
- التخزين: في الجلسة (Session)
- التطبيق: عبر Middleware `SetLocale`

### ملفات الترجمة:
```
lang/
├── ar/          ← ملفات الترجمة العربية
│   ├── admin.php    ← ترجمة لوحة الإدارة
│   ├── messages.php ← رسائل عامة
│   └── ...
└── en/          ← ملفات الترجمة الإنجليزية
    ├── admin.php
    ├── messages.php
    └── ...
```

---

## 19. ملخص التقنيات والمكتبات

| التقنية | الاستخدام |
|---------|-----------|
| Laravel 12 | إطار العمل الأساسي |
| Filament 3.3 | لوحة الإدارة |
| Laravel Breeze | نظام المصادقة |
| Tailwind CSS | تنسيق الواجهة |
| Vite | بناء الأصول الأمامية |
| Blade Templates | محرك القوالب |
| smalot/pdfparser | استخراج النص من PDF |
| setasign/fpdf + fpdi | إنشاء ومعالجة PDF |
| Gemini AI (via LiteLLM) | الذكاء الاصطناعي |
| SQLite | قاعدة البيانات (قابلة للتغيير) |
| Eloquent ORM | التعامل مع قاعدة البيانات |

---

*تم إنشاء هذا التقرير بتاريخ 2026-06-10*