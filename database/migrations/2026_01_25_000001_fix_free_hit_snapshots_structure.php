<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // حذف البيانات القديمة لأن التركيبة غير متوافقة
        DB::table('free_hit_snapshots')->truncate();

        Schema::table('free_hit_snapshots', function (Blueprint $table) {
            // حذف العمود القديم position_in_squad
            $table->dropColumn('position_in_squad');
        });

        Schema::table('free_hit_snapshots', function (Blueprint $table) {
            // إضافة position_in_squad كـ integer بدلاً من enum
            $table->integer('position_in_squad')->after('scout_id');

            // إضافة أسعار الشراء والحالية
            $table->decimal('purchase_price', 3, 1)->default(9.0)->after('is_vice_captain');
            $table->decimal('current_price', 3, 1)->default(9.0)->after('purchase_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('free_hit_snapshots', function (Blueprint $table) {
            $table->dropColumn(['purchase_price', 'current_price']);
            $table->dropColumn('position_in_squad');
        });

        Schema::table('free_hit_snapshots', function (Blueprint $table) {
            $table->enum('position_in_squad', [
                'goalkeeper', 'defender1', 'defender2', 'defender3',
                'midfielder1', 'midfielder2', 'midfielder3',
                'forward1', 'forward2', 'forward3', 'bench'
            ])->after('scout_id');
        });
    }
};
