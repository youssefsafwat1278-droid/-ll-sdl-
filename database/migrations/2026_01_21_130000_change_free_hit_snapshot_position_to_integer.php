<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlsrv') {
            if (Schema::hasColumn('free_hit_snapshots', 'position_in_squad')) {
                DB::statement("
                    DECLARE @sql NVARCHAR(MAX) = '';
                    SELECT @sql += 'ALTER TABLE free_hit_snapshots DROP CONSTRAINT ' + QUOTENAME(name) + ';'
                    FROM sys.check_constraints
                    WHERE parent_object_id = OBJECT_ID('free_hit_snapshots')
                      AND definition LIKE '%position_in_squad%';
                    EXEC sp_executesql @sql;
                ");

                DB::statement("
                    DECLARE @sql NVARCHAR(MAX) = '';
                    SELECT @sql += 'ALTER TABLE free_hit_snapshots DROP CONSTRAINT ' + QUOTENAME(dc.name) + ';'
                    FROM sys.default_constraints dc
                    INNER JOIN sys.columns c
                        ON c.object_id = dc.parent_object_id
                       AND c.column_id = dc.parent_column_id
                    WHERE dc.parent_object_id = OBJECT_ID('free_hit_snapshots')
                      AND c.name = 'position_in_squad';
                    EXEC sp_executesql @sql;
                ");

                DB::statement('ALTER TABLE free_hit_snapshots DROP COLUMN position_in_squad');
            }

            DB::statement('ALTER TABLE free_hit_snapshots ADD position_in_squad INT NOT NULL');
            return;
        }

        DB::statement('ALTER TABLE free_hit_snapshots MODIFY position_in_squad INT NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlsrv') {
            if (Schema::hasColumn('free_hit_snapshots', 'position_in_squad')) {
                DB::statement("
                    DECLARE @sql NVARCHAR(MAX) = '';
                    SELECT @sql += 'ALTER TABLE free_hit_snapshots DROP CONSTRAINT ' + QUOTENAME(name) + ';'
                    FROM sys.check_constraints
                    WHERE parent_object_id = OBJECT_ID('free_hit_snapshots')
                      AND definition LIKE '%position_in_squad%';
                    EXEC sp_executesql @sql;
                ");

                DB::statement("
                    DECLARE @sql NVARCHAR(MAX) = '';
                    SELECT @sql += 'ALTER TABLE free_hit_snapshots DROP CONSTRAINT ' + QUOTENAME(dc.name) + ';'
                    FROM sys.default_constraints dc
                    INNER JOIN sys.columns c
                        ON c.object_id = dc.parent_object_id
                       AND c.column_id = dc.parent_column_id
                    WHERE dc.parent_object_id = OBJECT_ID('free_hit_snapshots')
                      AND c.name = 'position_in_squad';
                    EXEC sp_executesql @sql;
                ");

                DB::statement('ALTER TABLE free_hit_snapshots DROP COLUMN position_in_squad');
            }

            DB::statement("ALTER TABLE free_hit_snapshots ADD position_in_squad NVARCHAR(255) NOT NULL");
            return;
        }

        DB::statement("ALTER TABLE free_hit_snapshots MODIFY position_in_squad ENUM('goalkeeper','defender1','defender2','defender3','midfielder1','midfielder2','midfielder3','forward1','forward2','forward3','bench') NOT NULL");
    }
};
