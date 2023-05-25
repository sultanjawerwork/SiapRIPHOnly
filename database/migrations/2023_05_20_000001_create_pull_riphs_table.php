<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;

return new class extends Migration
{
	use SoftDeletes;
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pull_riphs', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('user_id');
			$table->string('keterangan')->nullable();
			$table->string('nama')->nullable();
			$table->string('npwp')->nullable();
			$table->string('no_ijin')->unique()->nullable();
			$table->integer('periodetahun')->nullable();
			$table->date('tgl_ijin')->nullable();
			$table->date('tgl_akhir')->nullable();
			$table->string('no_hs')->nullable();
			$table->double('volume_riph')->nullable();
			$table->double('volume_produksi')->nullable();
			$table->double('luas_wajib_tanam')->nullable();
			$table->decimal('stok_mandiri')->nullable();
			$table->decimal('pupuk_organik', 12, 2)->nullable();
			$table->decimal('npk')->nullable();
			$table->decimal('dolomit')->nullable();
			$table->decimal('za')->nullable();
			$table->decimal('mulsa')->nullable();
			$table->decimal('poktan_share')->nullable();
			$table->decimal('importir_share')->nullable();
			$table->string('formRiph')->nullable();
			$table->string('formSptjm')->nullable();
			$table->string('logbook')->nullable();
			$table->string('formRt')->nullable();
			$table->string('formRta')->nullable();
			$table->string('formRpo')->nullable();
			$table->string('formLa')->nullable();
			$table->text('datariph')->nullable();
			$table->string('skl')->nullable();
			$table->timestamps();
			$table->softDeletes()->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('pull_riphs');
	}
};
