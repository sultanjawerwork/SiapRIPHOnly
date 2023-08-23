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
		Schema::create('aju_verif_produksis', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('npwp');
			$table->unsignedBigInteger('commitment_id');
			$table->string('no_pengajuan');
			$table->string('no_ijin');
			$table->string('status')->nullable(); //
			$table->text('note')->nullable();

			//file upload
			$table->string('baproduksi')->nullable();
			$table->string('spvp')->nullable(); //surat pengajuan verifikasi produksi
			$table->string('rpo')->nullable();
			$table->string('formLa')->nullable();
			$table->string('sphpoduksi')->nullable();
			$table->string('spdsp')->nullable(); //surat pengantar dinas telah selesai produksi
			$table->string('logbookproduksi')->nullable();
			$table->string('ndhprp')->nullable(); //nota dinas hasil pemeriksaan realisasi produksi

			$table->bigInteger('check_by')->nullable();
			$table->date('verif_at')->nullable();
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
