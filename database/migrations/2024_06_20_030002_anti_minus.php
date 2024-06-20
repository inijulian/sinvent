<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER antiminus_barangkeluar BEFORE INSERT ON barangkeluar
            FOR EACH ROW
            BEGIN
                DECLARE barang_stok INT;

                -- Ambil stok saat ini dari tabel barang
                SELECT stok INTO barang_stok FROM barang WHERE id = NEW.barang_id;

                -- Periksa apakah stok mencukupi
                IF (barang_stok - NEW.qty_keluar) < 0 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Stok tidak mencukupi untuk barang ini";
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS antiminus_barangkeluar');
    }
};