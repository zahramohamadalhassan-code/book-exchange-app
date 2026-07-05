<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $uni = 'الجامعة الوطنية الخاصة';
        $years = ['السنة الأولى', 'السنة الثانية', 'السنة الثالثة', 'السنة الرابعة', 'السنة الخامسة'];

        $oldInfoIds = DB::table('categories')
            ->where('faculty_name', 'هندسة معلوماتية')
            ->pluck('id')->toArray();

        $oldTelecomIds = DB::table('categories')
            ->where('faculty_name', 'هندسة اتصالات')
            ->pluck('id')->toArray();

        $infoIdMap = [];
        if (!empty($oldInfoIds)) {
            foreach ($years as $i => $year) {
                if (!isset($oldInfoIds[$i])) continue;
                $newId = DB::table('categories')->insertGetId([
                    'university_name' => $uni,
                    'faculty_name' => 'كلية الهندسة',
                    'department_name' => 'معلوماتية',
                    'study_year' => $year,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $infoIdMap[$oldInfoIds[$i]] = $newId;
            }
        }

        $telecomIdMap = [];
        if (!empty($oldTelecomIds)) {
            foreach ($years as $i => $year) {
                if (!isset($oldTelecomIds[$i])) continue;
                $newId = DB::table('categories')->insertGetId([
                    'university_name' => $uni,
                    'faculty_name' => 'كلية الهندسة',
                    'department_name' => 'اتصالات',
                    'study_year' => $year,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $telecomIdMap[$oldTelecomIds[$i]] = $newId;
            }
        }

        foreach ($infoIdMap as $oldId => $newId) {
            DB::table('books')->where('category_id', $oldId)->update(['category_id' => $newId]);
            DB::table('digital_notes')->where('category_id', $oldId)->update(['category_id' => $newId]);
        }
        foreach ($telecomIdMap as $oldId => $newId) {
            DB::table('books')->where('category_id', $oldId)->update(['category_id' => $newId]);
            DB::table('digital_notes')->where('category_id', $oldId)->update(['category_id' => $newId]);
        }

        DB::table('categories')->whereIn('id', array_merge($oldInfoIds, $oldTelecomIds))->delete();

        DB::table('categories')
            ->where('faculty_name', 'هندسة مدنية')
            ->update(['faculty_name' => 'كلية الهندسة', 'department_name' => 'مدنية']);

        DB::table('categories')
            ->where('faculty_name', 'هندسة معمارية والتخطيطي المعماري')
            ->update(['faculty_name' => 'كلية الهندسة', 'department_name' => 'معمارية']);

        DB::table('categories')
            ->where('faculty_name', 'العلوم الادارية والمالية')
            ->update(['faculty_name' => 'كلية العلوم الإدارية والمالية', 'department_name' => 'علوم إدارية']);

        DB::table('categories')
            ->where('faculty_name', 'طب الاسنان')
            ->update(['faculty_name' => 'كلية طب الأسنان', 'department_name' => 'طب أسنان']);

        DB::table('categories')
            ->where('faculty_name', 'الصيدلة')
            ->update(['faculty_name' => 'كلية الصيدلة', 'department_name' => 'صيدلة']);
    }

    public function down(): void
    {
    }
};