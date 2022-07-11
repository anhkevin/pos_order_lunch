<?php

use App\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Product::create([
            'id' => 1,
            'name' => 'Sườn Nướng',
            'price' => 25000,
            'type' => 1,
            'shop_id' => 1,
        ]);

        Product::create([
            'id' => 2,
            'name' => 'Ốp La',
            'price' => 25000,
            'type' => 1,
            'shop_id' => 1,
        ]);

        Product::create([
            'id' => 3,
            'name' => 'Bì - Chả',
            'price' => 25000,
            'type' => 1,
            'shop_id' => 1,
        ]);

        Product::create([
            'id' => 4,
            'name' => 'Chả - Ốp La',
            'price' => 25000,
            'type' => 1,
            'shop_id' => 1,
        ]);

        Product::create([
            'id' => 5,
            'name' => 'Ốp La - Bì',
            'price' => 25000,
            'type' => 1,
            'shop_id' => 1,
        ]);

        Product::create([
            'id' => 6,
            'name' => 'Bì - Chả - Ốp La',
            'price' => 30000,
            'type' => 1,
            'shop_id' => 1,
        ]);

        Product::create([
            'id' => 7,
            'name' => 'Đùi Gà Nướng Tỏi',
            'price' => 27000,
            'type' => 1,
            'shop_id' => 1,
        ]);

        Product::create([
            'id' => 8,
            'name' => 'Cá Thác Lác Thì Là',
            'price' => 30000,
            'type' => 1,
            'shop_id' => 1,
        ]);

        Product::create([
            'id' => 9,
            'name' => 'Ba Rọi Muối Ớt',
            'price' => 30000,
            'type' => 1,
            'shop_id' => 1,
        ]);

        Product::create([
            'id' => 10,
            'name' => 'Sườn Que (sườn non)',
            'price' => 35000,
            'type' => 1,
            'shop_id' => 1,
        ]);

        Product::create([
            'id' => 11,
            'name' => 'Canh',
            'price' => 9000,
            'type' => 2,
            'shop_id' => 1,
        ]);

        Product::create([
            'id' => 12,
            'name' => 'Bì',
            'price' => 8000,
            'type' => 2,
            'shop_id' => 1,
        ]);

        Product::create([
            'id' => 13,
            'name' => 'Chả',
            'price' => 8000,
            'type' => 2,
            'shop_id' => 1,
        ]);

        Product::create([
            'id' => 14,
            'name' => 'Ốp La',
            'price' => 8000,
            'type' => 2,
            'shop_id' => 1,
        ]);

        Product::create([
            'id' => 15,
            'name' => 'Cơm Thêm',
            'price' => 2000,
            'type' => 2,
            'shop_id' => 1,
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
