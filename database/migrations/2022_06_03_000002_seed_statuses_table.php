<?php

use App\Status;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Status::create([
            'name' => 'Order (đặt cơm)',
            'column_name' => 'order',
            'percent' => 10,
        ]);

        Status::create([
            'name' => 'Booked (đã đặt)',
            'column_name' => 'booked',
            'percent' => 50,
        ]);

        Status::create([
            'name' => 'Unpaid (chưa thanh toán)',
            'column_name' => 'unpaid',
            'percent' => 90,
        ]);

        Status::create([
            'name' => 'Paid (đã thanh toán)',
            'column_name' => 'paid',
            'percent' => 100,
        ]);

        Status::create([
            'name' => 'Cancel (hủy)',
            'column_name' => 'cancel',
            'percent' => 0,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
