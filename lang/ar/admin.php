<?php

return [
    'content_management' => 'إدارة المحتوى',
    'user_management' => 'إدارة المستخدمين',
    'monitoring' => 'المراقبة',
    'id' => 'المعرف',

    'book' => [
        'model_label' => 'كتاب',
        'model_label_plural' => 'الكتب',
        'currency_syp' => 'ل.س',
        'section_data' => 'بيانات الكتاب',
        'title' => 'العنوان',
        'author' => 'المؤلف',
        'user' => 'المستخدم',
        'category' => 'التصنيف',
        'condition' => 'حالة الكتاب',
        'offer_type' => 'نوع العرض',
        'price' => 'السعر',
        'status' => 'الحالة',
        'moderation_status' => 'حالة المراجعة',
        'cover_image_url' => 'رابط صورة الغلاف',
        'date_added' => 'تاريخ الإضافة',
        'actions' => 'إجراءات',

        'conditions' => [
            'excellent' => 'ممتاز',
            'good' => 'جيد',
            'fair' => 'مقبول',
            'poor' => 'سيء',
        ],

        'offer_types' => [
            'sale' => 'بيع',
            'exchange' => 'تبادل',
            'donate' => 'تبرع',
        ],

        'statuses' => [
            'available' => 'متاح',
            'pending' => 'قيد الانتظار',
            'sold' => 'مباع',
        ],

        'moderation_statuses' => [
            'pending' => 'معلق',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
        ],

        'approve_book' => 'قبول',
        'approve_book_heading' => 'قبول الكتاب',
        'approve_book_description' => 'هل أنت متأكد من قبول هذا الكتاب؟',
        'book_approved' => 'تم قبول الكتاب',

        'reject_book' => 'رفض',
        'reject_book_heading' => 'رفض الكتاب',
        'reject_book_description' => 'هل أنت متأكد من رفض هذا الكتاب؟',
        'book_rejected' => 'تم رفض الكتاب',
    ],

    'user' => [
        'model_label' => 'مستخدم',
        'model_label_plural' => 'المستخدمين',
        'id' => 'المعرف',
        'section_data' => 'بيانات المستخدم',
        'full_name' => 'الاسم الكامل',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'university_id' => 'الرقم الجامعي',
        'phone_number' => 'رقم الهاتف',
        'role' => 'الدور',
        'is_banned' => 'محظور',
        'date_registered' => 'تاريخ التسجيل',

        'toggle_ban' => 'حظر',
        'toggle_unban' => 'إلغاء الحظر',
        'ban_heading' => 'حظر المستخدم',
        'unban_heading' => 'إلغاء حظر المستخدم',
        'ban_description' => 'هل أنت متأكد من حظر هذا المستخدم؟',
        'unban_description' => 'هل أنت متأكد من إلغاء حظر هذا المستخدم؟',
        'user_banned' => 'تم حظر المستخدم',
        'user_unbanned' => 'تم إلغاء حظر المستخدم',

        'banned_filter' => 'محظور',
        'all' => 'الكل',
        'banned_only' => 'محظورين فقط',
        'active_only' => 'غير محظورين فقط',
    ],

    'transaction' => [
        'model_label' => 'معاملة',
        'model_label_plural' => 'المعاملات',
        'section_data' => 'بيانات المعاملة',
        'book' => 'الكتاب',
        'requester' => 'الطالب',
        'owner' => 'المالك',
        'status' => 'الحالة',
        'meeting_date' => 'تاريخ اللقاء',
        'meeting_time' => 'وقت اللقاء',
        'meeting_location' => 'مكان اللقاء',
        'created_at' => 'تاريخ الإنشاء',

        'statuses' => [
            'pending' => 'معلق',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ],
    ],

    'note' => [
        'model_label' => 'ملاحظة رقمية',
        'model_label_plural' => 'الملاحظات الرقمية',
        'section_data' => 'بيانات الملاحظات الرقمية',
        'title' => 'العنوان',
        'description' => 'الوصف',
        'user' => 'المستخدم',
        'category' => 'القسم',
        'pdf_file' => 'ملف PDF',
        'moderation_status' => 'حالة المراجعة',
        'created_at' => 'تاريخ الإضافة',

        'moderation_statuses' => [
            'pending' => 'معلق',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
        ],

        'approve_note' => 'قبول',
        'approve_note_heading' => 'قبول الملاحظة',
        'approve_note_description' => 'هل أنت متأكد من قبول هذه الملاحظة؟',
        'note_approved' => 'تم قبول الملاحظة',

        'reject_note' => 'رفض',
        'reject_note_heading' => 'رفض الملاحظة',
        'reject_note_description' => 'هل أنت متأكد من رفض هذه الملاحظة؟',
        'note_rejected' => 'تم رفض الملاحظة',
    ],

    'rating' => [
        'model_label' => 'تقييم',
        'model_label_plural' => 'التقييمات',
        'section_data' => 'بيانات التقييم',
        'transaction_id' => 'رقم المعاملة',
        'reviewer' => 'المقيّم',
        'reviewed_user' => 'المقيَّم',
        'stars' => 'النجوم',
        'comment' => 'التعليق',
        'date' => 'تاريخ التقييم',
    ],

    'category' => [
        'model_label' => 'تصنيف',
        'model_label_plural' => 'التصنيفات',
        'id' => 'المعرف',
        'section_data' => 'بيانات التصنيف',
        'university_name' => 'اسم الجامعة',
        'faculty_name' => 'اسم الكلية',
        'department_name' => 'اسم القسم',
        'study_year' => 'السنة الدراسية',
        'university' => 'الجامعة',
        'faculty' => 'الكلية',
        'department' => 'القسم',
        'created_at' => 'تاريخ الإنشاء',
    ],

    'widget' => [
        'books_monthly' => 'الكتب المضافة شهرياً',
        'books_added' => 'الكتب المضافة',
        'latest_transactions' => 'آخر المعاملات',
        'book_col' => 'الكتاب',
        'requester' => 'الطالب',
        'owner' => 'المالك',
        'status' => 'الحالة',
        'date' => 'التاريخ',

        'total_students' => 'إجمالي الطلاب',
        'registered_users' => 'عدد المستخدمين المسجلين',
        'total_books' => 'إجمالي الكتب',
        'books_added_count' => 'عدد الكتب المضافة',
        'total_notes' => 'إجمالي الملاحظات الرقمية',
        'notes_added_count' => 'عدد الملاحظات المضافة',
        'pending_books' => 'كتب معلقة',
        'pending_review' => 'كتب بانتظار المراجعة',
    ],

    'relation' => [
        'transactions' => 'المعاملات',
        'books' => 'الكتب',
        'requester' => 'الطالب',
        'owner' => 'المالك',
        'status' => 'الحالة',
        'meeting_date' => 'تاريخ اللقاء',
        'meeting_location' => 'مكان اللقاء',
        'created_at' => 'تاريخ الإنشاء',
        'book' => 'الكتاب',

        'statuses' => [
            'pending' => 'معلق',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ],
    ],
];
