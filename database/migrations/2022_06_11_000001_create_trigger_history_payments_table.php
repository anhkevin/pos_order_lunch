<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriggerHistoryPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('CREATE TRIGGER money_calculation AFTER INSERT ON `history_payments` FOR EACH ROW
            BEGIN
                IF NEW.amount > 0 THEN
                    UPDATE users SET users.total_deposit = (users.total_deposit + NEW.amount), users.total_money = (users.total_money + NEW.amount)
                    WHERE users.id = NEW.user_id;
                ELSEIF NEW.amount < 0 THEN
                    UPDATE users SET users.total_paid = (users.total_paid - NEW.amount), users.total_money = (users.total_money + NEW.amount)
                    WHERE users.id = NEW.user_id;
                END IF;
            END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `money_calculation`');
    }
}
