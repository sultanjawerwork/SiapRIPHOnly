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
		Schema::dropIfExists('aju_verif_produksis');
		Schema::dropIfExists('avproduksis');
		Schema::create('avproduksis', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('npwp');
			$table->unsignedBigInteger('commitment_id');
			$table->string('no_ijin');
			$table->string('no_pengajuan');
			$table->string('status')->nullable(); //
			$table->text('note')->nullable();

			//file upload
			$table->string('baproduksi')->nullable();
			$table->string('spvp')->nullable(); //surat pengajuan verifikasi produksi
			$table->string('rpo')->nullable();
			$table->string('formLa')->nullable();
			$table->string('sphproduksi')->nullable();
			$table->string('spdsp')->nullable(); //surat pengantar dinas telah selesai produksi
			$table->string('logbookproduksi')->nullable();
			$table->string('ndhprp')->nullable(); //nota dinas hasil pemeriksaan realisasi produksi

			$table->bigInteger('check_by')->nullable();
			$table->date('verif_at')->nullable();
			$table->string('metode')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('aju_verif_produksis');
	}
};
