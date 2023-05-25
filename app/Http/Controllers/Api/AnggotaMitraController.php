<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnggotaMitra;
use Illuminate\Http\Request;

class AnggotaMitraController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function index()
	{
		$anggotaMitras = AnggotaMitra::with([
			'pksmitra' => function ($query) {
				$query->with('commitmentbackdate');
			},
			'pksmitra',
			'masteranggota'
		])->get();

		$result = [];

		foreach ($anggotaMitras as $anggotaMitra) {
			// $tglTanam = $anggotaMitra->tgl_tanam ? $anggotaMitra->tgl_tanam->format('Y-m-d') : 'belum tanam';
			// $tglPanen = $anggotaMitra->tgl_panen ? $anggotaMitra->tgl_panen->format('Y-m-d') : 'belum panen';
			$luasTanam = $anggotaMitra->luas_tanam ? $anggotaMitra->luas_tanam : 'belum tanam';
			$volume = $anggotaMitra->volume ? $anggotaMitra->volume : 'belum panen';

			$result[] = [
				'id' => $anggotaMitra->id,
				'latitude' => $anggotaMitra->latitude,
				'longitude' => $anggotaMitra->longitude,
				'polygon' => $anggotaMitra->polygon,

				'pks_mitra_id' => $anggotaMitra->pks_mitra_id,
				'no_ijin' => $anggotaMitra->pksmitra->commitmentbackdate->no_ijin,
				'periodetahun' => $anggotaMitra->pksmitra->commitmentbackdate->periodetahun,
				'no_perjanjian' => $anggotaMitra->pksmitra->no_perjanjian,
				'nama_petani' => $anggotaMitra->masteranggota->nama_petani,
				'nama_kelompok' => $anggotaMitra->pksmitra->masterkelompok->nama_kelompok,
				'nama_lokasi' => $anggotaMitra->nama_lokasi,

				'altitude' => $anggotaMitra->altitude,
				'luas_kira' => $anggotaMitra->luas_kira,
				'tgl_tanam' => $anggotaMitra->tgl_tanam,
				'luas_tanam' => $luasTanam,
				'varietas' => $anggotaMitra->varietas,
				'tgl_panen' => $anggotaMitra->tgl_panen,
				'volume' => $volume,
				'tanam_pict' => $anggotaMitra->tanam_pict,
				'panen_pict' => $anggotaMitra->panen_pict,
			];
		}

		return response()->json($result);
	}

	public function ByYears($periodeTahun)
	{
		$anggotaMitras = AnggotaMitra::with([
			'pksmitra' => function ($query) {
				$query->with('commitmentbackdate');
			},
			'masteranggota'
		])->get();

		$result = [];

		foreach ($anggotaMitras as $anggotaMitra) {
			$periodetahun = $anggotaMitra->pksmitra->commitmentbackdate->periodetahun;
			if ($periodetahun == $periodeTahun) {
				$luasTanam = $anggotaMitra->luas_tanam ? $anggotaMitra->luas_tanam : 'belum tanam';
				$volume = $anggotaMitra->volume ? $anggotaMitra->volume : 'belum panen';

				$result[] = [
					'periodetahun' => $periodetahun,
					'id' => $anggotaMitra->id,
					'latitude' => $anggotaMitra->latitude,
					'longitude' => $anggotaMitra->longitude,
					'polygon' => $anggotaMitra->polygon,

					'pks_mitra_id' => $anggotaMitra->pks_mitra_id,
					'no_ijin' => $anggotaMitra->pksmitra->commitmentbackdate->no_ijin,
					'periodetahun' => $anggotaMitra->pksmitra->commitmentbackdate->periodetahun,
					'no_perjanjian' => $anggotaMitra->pksmitra->no_perjanjian,
					'nama_petani' => $anggotaMitra->masteranggota->nama_petani,
					'nama_kelompok' => $anggotaMitra->pksmitra->masterkelompok->nama_kelompok,
					'nama_lokasi' => $anggotaMitra->nama_lokasi,

					'altitude' => $anggotaMitra->altitude,
					'luas_kira' => $anggotaMitra->luas_kira,
					'tgl_tanam' => $anggotaMitra->tgl_tanam,
					'luas_tanam' => $luasTanam,
					'varietas' => $anggotaMitra->varietas,
					'tgl_panen' => $anggotaMitra->tgl_panen,
					'volume' => $volume,
					'tanam_pict' => $anggotaMitra->tanam_pict,
					'panen_pict' => $anggotaMitra->panen_pict,
				];
			}
		}

		return response()->json($result);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		// the url for the REST Api of anggotaMitra : http://127.0.0.1:8000/api/getAPIAnggotaMitra/{id}
		$anggotaMitra = AnggotaMitra::with([
			'pksmitra' => function ($query) {
				$query->with('commitmentbackdate');
			},
			'pksmitra',
			'masteranggota'
		])->find($id);

		$result[] = [
			'id' => $anggotaMitra->id,
			// 'latitude' => $anggotaMitra->latitude,
			// 'longitude' => $anggotaMitra->longitude,
			// 'polygon' => $anggotaMitra->polygon,

			'pks_mitra_id' => $anggotaMitra->pks_mitra_id,
			'no_ijin' => $anggotaMitra->pksmitra->commitmentbackdate->no_ijin,
			'no_perjanjian' => $anggotaMitra->pksmitra->no_perjanjian,
			'nama_petani' => $anggotaMitra->masteranggota->nama_petani,
			'nama_kelompok' => $anggotaMitra->pksmitra->masterkelompok->nama_kelompok,
			'nama_lokasi' => $anggotaMitra->nama_lokasi,

			'altitude' => $anggotaMitra->altitude,
			'luas_kira' => $anggotaMitra->luas_kira,
			'tgl_tanam' => $anggotaMitra->tgl_tanam,
			'luas_tanam' => $anggotaMitra->luas_tanam,
			'varietas' => $anggotaMitra->varietas,
			'tgl_panen' => $anggotaMitra->tgl_panen,
			'volume' => $anggotaMitra->volume,
			'tanam_pict' => $anggotaMitra->tanam_pict,
			'panen_pict' => $anggotaMitra->panen_pict,
		];
		return response()->json($result);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}
}
