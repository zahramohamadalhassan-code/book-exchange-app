<?php

return [
    'content_management' => 'Content Management',
    'user_management' => 'User Management',
    'monitoring' => 'Monitoring',
    'id' => 'ID',

    'book' => [
        'model_label' => 'Book',
        'model_label_plural' => 'Books',
        'currency_syp' => 'SYP',
        'section_data' => 'Book Data',
        'title' => 'Title',
        'author' => 'Author',
        'user' => 'User',
        'category' => 'Category',
        'condition' => 'Condition',
        'offer_type' => 'Offer Type',
        'price' => 'Price',
        'status' => 'Status',
        'moderation_status' => 'Moderation Status',
        'cover_image_url' => 'Cover Image URL',
        'date_added' => 'Date Added',
        'actions' => 'Actions',

        'conditions' => [
            'excellent' => 'Excellent',
            'good' => 'Good',
            'fair' => 'Fair',
            'poor' => 'Poor',
        ],

        'offer_types' => [
            'sale' => 'Sale',
            'exchange' => 'Exchange',
            'donate' => 'Donate',
        ],

        'statuses' => [
            'available' => 'Available',
            'pending' => 'Pending',
            'sold' => 'Sold',
        ],

        'moderation_statuses' => [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ],

        'approve_book' => 'Approve',
        'approve_book_heading' => 'Approve Book',
        'approve_book_description' => 'Are you sure you want to approve this book?',
        'book_approved' => 'Book Approved',

        'reject_book' => 'Reject',
        'reject_book_heading' => 'Reject Book',
        'reject_book_description' => 'Are you sure you want to reject this book?',
        'book_rejected' => 'Book Rejected',
    ],

    'user' => [
        'model_label' => 'User',
        'model_label_plural' => 'Users',
        'id' => 'ID',
        'section_data' => 'User Data',
        'full_name' => 'Full Name',
        'email' => 'Email',
        'password' => 'Password',
        'university_id' => 'University ID',
        'phone_number' => 'Phone Number',
        'role' => 'Role',
        'is_banned' => 'Banned',
        'date_registered' => 'Registration Date',

        'toggle_ban' => 'Ban',
        'toggle_unban' => 'Unban',
        'ban_heading' => 'Ban User',
        'unban_heading' => 'Unban User',
        'ban_description' => 'Are you sure you want to ban this user?',
        'unban_description' => 'Are you sure you want to unban this user?',
        'user_banned' => 'User Banned',
        'user_unbanned' => 'User Unbanned',

        'banned_filter' => 'Banned',
        'all' => 'All',
        'banned_only' => 'Banned Only',
        'active_only' => 'Active Only',
    ],

    'transaction' => [
        'model_label' => 'Transaction',
        'model_label_plural' => 'Transactions',
        'section_data' => 'Transaction Data',
        'book' => 'Book',
        'requester' => 'Requester',
        'owner' => 'Owner',
        'status' => 'Status',
        'meeting_date' => 'Meeting Date',
        'meeting_time' => 'Meeting Time',
        'meeting_location' => 'Meeting Location',
        'created_at' => 'Created At',

        'statuses' => [
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ],
    ],

    'note' => [
        'model_label' => 'Digital Note',
        'model_label_plural' => 'Digital Notes',
        'section_data' => 'Digital Note Data',
        'title' => 'Title',
        'description' => 'Description',
        'user' => 'User',
        'category' => 'Department',
        'pdf_file' => 'PDF File',
        'moderation_status' => 'Moderation Status',
        'created_at' => 'Date Added',

        'moderation_statuses' => [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ],

        'approve_note' => 'Approve',
        'approve_note_heading' => 'Approve Note',
        'approve_note_description' => 'Are you sure you want to approve this note?',
        'note_approved' => 'Note Approved',

        'reject_note' => 'Reject',
        'reject_note_heading' => 'Reject Note',
        'reject_note_description' => 'Are you sure you want to reject this note?',
        'note_rejected' => 'Note Rejected',
    ],

    'rating' => [
        'model_label' => 'Rating',
        'model_label_plural' => 'Ratings',
        'section_data' => 'Rating Data',
        'transaction_id' => 'Transaction ID',
        'reviewer' => 'Reviewer',
        'reviewed_user' => 'Reviewed User',
        'stars' => 'Stars',
        'comment' => 'Comment',
        'date' => 'Rating Date',
    ],

    'category' => [
        'model_label' => 'Category',
        'model_label_plural' => 'Categories',
        'id' => 'ID',
        'section_data' => 'Category Data',
        'university_name' => 'University Name',
        'faculty_name' => 'Faculty Name',
        'department_name' => 'Department Name',
        'study_year' => 'Study Year',
        'university' => 'University',
        'faculty' => 'Faculty',
        'department' => 'Department',
        'created_at' => 'Created At',
    ],

    'widget' => [
        'books_monthly' => 'Books Added Monthly',
        'books_added' => 'Books Added',
        'latest_transactions' => 'Latest Transactions',
        'book_col' => 'Book',
        'requester' => 'Requester',
        'owner' => 'Owner',
        'status' => 'Status',
        'date' => 'Date',

        'total_students' => 'Total Students',
        'registered_users' => 'Registered Users',
        'total_books' => 'Total Books',
        'books_added_count' => 'Books Added',
        'total_notes' => 'Total Digital Notes',
        'notes_added_count' => 'Notes Added',
        'pending_books' => 'Pending Books',
        'pending_review' => 'Books Pending Review',
    ],

    'relation' => [
        'transactions' => 'Transactions',
        'books' => 'Books',
        'requester' => 'Requester',
        'owner' => 'Owner',
        'status' => 'Status',
        'meeting_date' => 'Meeting Date',
        'meeting_location' => 'Meeting Location',
        'created_at' => 'Created At',
        'book' => 'Book',

        'statuses' => [
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ],
    ],
];
