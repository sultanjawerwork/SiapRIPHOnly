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
		Schema::create('aju_verif_tanams', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('npwp');
			$table->unsignedBigInteger('commitment_id');
			$table->string('no_pengajuan');
			$table->string('no_ijin');
			$table->string('status')->nullable(); //
			$table->text('note')->nullable();

			//file upload
			$table->string('batanam')->nullable();
			$table->string('spvt')->nullable(); //surat pengajuan verifikasi tanam
			$table->string('sptjm')->nullable();
			$table->string('rta')->nullable();
			$table->string('sphtanam')->nullable();
			$table->string('spdst')->nullable(); //surat pengantar dinas telah selesai tanam
			$table->string('logbooktanam')->nullable();
			$table->string('ndhprt')->nullable(); //nota dinas hasil pemeriksaan realisasi tanam

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
		Schema::dropIfExists('aju_verif_tanams');
	}
};
