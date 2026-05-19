@php
    $isRtl = app()->getLocale() === 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
@endphp
@if($isRtl)
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/rtlcss@4.1.0/rtlcss.min.js">
<script>
    document.documentElement.dir = 'rtl';
    document.documentElement.lang = 'ar';
</script>
<style>
    [dir="rtl"] .fi-sidebar,
    [dir="rtl"] .fi-topbar,
    [dir="rtl"] .fi-main,
    [dir="rtl"] .fi-header {
        direction: rtl;
        text-align: right;
    }
    [dir="rtl"] .fi-section > div,
    [dir="rtl"] .fi-ta > div,
    [dir="rtl"] .fi-table > div {
        direction: rtl;
    }
    [dir="rtl"] .fi-badge,
    [dir="rtl"] .fi-btn {
        direction: rtl;
    }
    [dir="rtl"] input,
    [dir="rtl"] select,
    [dir="rtl"] textarea {
        direction: rtl;
        text-align: right;
    }
</style>
@else
<script>
    document.documentElement.dir = 'ltr';
    document.documentElement.lang = 'en';
</script>
@endif
