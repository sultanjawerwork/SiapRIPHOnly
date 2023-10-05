<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Verifikator\SklOldController;


Route::get('/', function () {
	return redirect()->route('login');
});

Route::get('/v2/register', function () {
	return view('v2register');
});

Route::get('/home', function () {
	if (session('status')) {
		return redirect()->route('admin.home')->with('status', session('status'));
	}
	return redirect()->route('admin.home');
});


Auth::routes(['register' => true]); // menghidupkan registration

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
	// landing
	Route::get('/', 'HomeController@index')->name('home');
	// Dashboard
	Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
	Route::get('/dashboard/monitoring', 'DashboardController@monitoring')->name('dashboard.monitoring');
	Route::get('/dashboard/map', 'DashboardController@map')->name('dashboard.map');

	Route::get('mapDataAll', 'UserMapDashboard@index')->name('mapDataAll');
	Route::get('mapDataByYear/{periodeTahun}', 'UserMapDashboard@ByYears')->name('mapDataByYear');
	Route::get('mapDataById/{id}', 'UserMapDashboard@show')->name('mapDataById');

	//dashboard data for admin
	Route::get('monitoringDataByYear/{periodetahun}', 'DashboardDataController@monitoringDataByYear')->name('monitoringDataByYear');

	//dashboard data for verifikator
	Route::get('verifikatorMonitoringDataByYear/{periodetahun}', 'DashboardDataController@verifikatorMonitoringDataByYear')->name('verifikatormonitoringDataByYear');

	//dashboard data for user
	Route::get('usermonitoringDataByYear/{periodeTahun}', 'DashboardDataController@userMonitoringDataByYear')->name('userMonitoringDataByYear');
	Route::get('rekapRiphData', 'DashboardDataController@rekapRiphData')->name('get.rekap.riph');

	// Permissions
	Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
	Route::resource('permissions', 'PermissionsController');

	// Roles
	Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
	Route::resource('roles', 'RolesController');

	// Users
	Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
	Route::resource('users', 'UsersController');

	// Audit Logs
	Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

	Route::get('global-search', 'GlobalSearchController@search')->name('globalSearch');

	Route::get('profile', 'ProfileController@index')->name('profile.show');
	Route::post('profile', 'ProfileController@store')->name('profile.store');
	Route::post('profile/{id}', 'ProfileController@update')->name('profile.update');
	Route::get('profile/pejabat', 'AdminProfileController@index')->name('profile.pejabat');
	Route::post('profile/pejabat/store', 'AdminProfileController@store')->name('profile.pejabat.store');

	//posts
	Route::put('posts/{post}/restore', 'PostsController@restore')->name('posts.restore');
	Route::resource('posts', 'PostsController');
	Route::get('allblogs', 'PostsController@allblogs')->name('allblogs');
	Route::post('posts/{post}/star', 'StarredPostController@star')->name('posts.star');
	Route::delete('posts/{post}/unstar', 'StarredPostController@unstar')->name('posts.unstar');

	//posts categories
	Route::resource('categories', 'CategoryController');

	//messenger
	Route::get('messenger', 'MessengerController@index')->name('messenger.index');
	Route::get('messenger/create', 'MessengerController@createTopic')->name('messenger.createTopic');
	Route::post('messenger', 'MessengerController@storeTopic')->name('messenger.storeTopic');
	Route::get('messenger/inbox', 'MessengerController@showInbox')->name('messenger.showInbox');
	Route::get('messenger/outbox', 'MessengerController@showOutbox')->name('messenger.showOutbox');
	Route::post('messenger/{topic}/update', 'MessengerController@updateTopic')->name('messenger.updateTopic');
	Route::get('messenger/{topic}', 'MessengerController@showMessages')->name('messenger.showMessages');
	Route::delete('messenger/{topic}', 'MessengerController@destroyTopic')->name('messenger.destroyTopic');
	Route::post('messenger/{topic}/reply', 'MessengerController@replyToTopic')->name('messenger.reply');
	Route::get('messenger/{topic}/reply', 'MessengerController@showReply')->name('messenger.showReply');

	//verifikasi
	Route::get('dir_check_b', 'MessengerController@showReply')->name('verifikasi.dir_check_b');
	Route::get('dir_check_c', 'MessengerController@showReply')->name('verifikasi.dir_check_c');


	Route::resource('riphAdmin', 'RiphAdminController');
	Route::post('riphadmin/storefetched', 'RiphAdminController@storefetched')->name('riphadmin.storefetched');

	//daftar pejabat penandatangan SKL
	Route::get('daftarpejabats', 'PejabatController@index')->name('pejabats');
	Route::get('pejabat/create', 'PejabatController@create')->name('pejabat.create');
	Route::post('pejabat/store', 'PejabatController@store')->name('pejabat.store');
	Route::get('pejabat/{id}/show', 'PejabatController@show')->name('pejabat.show');
	Route::get('pejabat/{id}/edit', 'PejabatController@edit')->name('pejabat.edit');
	Route::put('pejabat/{id}/update', 'PejabatController@update')->name('pejabat.update');
	Route::delete('pejabat/{id}/delete', 'PejabatController@destroy')->name('pejabat.delete');
	Route::put('pejabat/{id}/activate', 'PejabatController@activate')->name('pejabat.activate');

	//daftar varietas
	Route::get('varietas', 'VarietasController@index')->name('varietas');
	Route::get('varietas/create', 'VarietasController@create')->name('varietas.create');
	Route::get('varietas/{id}/edit', 'VarietasController@edit')->name('varietas.edit');
	Route::get('varietas/{id}/show', 'VarietasController@show')->name('varietas.show');
	Route::post('varietas/store', 'VarietasController@store')->name('varietas.store');
	Route::put('varietas/{id}/update', 'VarietasController@update')->name('varietas.update');
	Route::delete('varietas/{id}/delete', 'VarietasController@destroy')->name('varietas.delete');
	Route::patch('varietas/{id}/restore', 'VarietasController@restore')->name('varietas.restore');

	//user task
	Route::group(['prefix' => 'task', 'as' => 'task.'], function () {

		Route::get('pull', 'PullRiphController@index')->name('pull');
		Route::get('getriph', 'PullRiphController@pull')->name('pull.getriph');
		Route::post('pull', 'PullRiphController@store')->name('pull.store');

		Route::get('commitment', 'CommitmentController@index')->name('commitment');
		Route::get('commitment/{id}/show', 'CommitmentController@show')->name('commitment.show');
		Route::put('commitment/{id}/update', 'CommitmentController@update')->name('commitment.update');
		Route::delete('commitment/{pullriph}', 'CommitmentController@destroy')->name('commitment.destroy');
		Route::delete('commitmentmd', 'CommitmentController@massDestroy')->name('commitment.massDestroy');

		//master penangkar
		Route::get('penangkar', 'MasterPenangkarController@index')->name('penangkar');
		Route::get('penangkar/create', 'MasterPenangkarController@create')->name('penangkar.create');
		Route::post('penangkar/store', 'MasterPenangkarController@store')->name('penangkar.store');
		Route::get('penangkar/{id}/edit', 'MasterPenangkarController@edit')->name('penangkar.edit');
		Route::put('penangkar/{id}/update', 'MasterPenangkarController@update')->name('penangkar.update');
		Route::delete('penangkar/{id}/delete', 'MasterPenangkarController@destroy')->name('penangkar.delete');

		//pengisian data realisasi
		Route::get('commitment/{id}/realisasi', 'CommitmentController@realisasi')->name('commitment.realisasi');
		Route::post('commitment/{id}/realisasi/storeUserDocs', 'CommitmentController@storeUserDocs')->name('commitment.realisasi.storeUserDocs');
		Route::get('commitment/{id}/penangkar', 'PenangkarRiphController@mitra')->name('commitment.penangkar');
		Route::post('commitment/{id}/penangkar/store', 'PenangkarRiphController@store')->name('commitment.penangkar.store');
		Route::delete('mitra/{id}/delete', 'PenangkarRiphController@destroy')->name('mitra.delete');

		// daftar pks
		Route::get('pks/{id}/edit', 'PksController@edit')->name('pks.edit');
		Route::put('pks/{id}/update', 'PksController@update')->name('pks.update');

		//daftar lokasi tanam
		Route::get('pks/{id}/lokasitanam', 'PksController@anggotas')->name('pks.anggotas');

		//saprodi
		Route::get('pks/{id}/saprodi', 'PksController@saprodi')->name('pks.saprodi');
		Route::post('pks/{id}/saprodi', 'SaprodiController@store')->name('saprodi.store');
		route::get('pks/{pksId}/saprodi/{id}/edit', 'SaprodiController@edit')->name('saprodi.edit');
		route::put('pks/{pksId}/saprodi/{id}', 'SaprodiController@update')->name('saprodi.update');
		route::delete('saprodi/{id}', 'SaprodiController@destroy')->name('saprodi.delete');
		Route::get('saprodi', 'SaprodiController@index')->name('saprodi.index');

		Route::get('pks/create/{noriph}/{poktan}', 'PksController@create')->name('pks.create');
		Route::delete('pksmd', 'PksController@massDestroy')->name('pks.massDestroy');

		//realisasi lokasi tanam & produksi
		Route::get('realisasi/lokasi/{anggota_id}', 'LokasiController@show')->name('lokasi.tanam');
		Route::post('realisasi/lokasi/{id}/update', 'LokasiController@update')->name('lokasi.tanam.update');
		Route::put('realisasi/lokasi/{id}/storeTanam', 'LokasiController@storeTanam')->name('lokasi.tanam.store');
		Route::put('realisasi/lokasi/{id}/storeProduksi', 'LokasiController@storeProduksi')->name('lokasi.produksi.store');

		Route::get('pengajuan', 'PengajuanController@index')->name('pengajuan.index');

		//new pengajuan tanam
		Route::get('commitment/{id}/formavt', 'PengajuanController@ajuVerifTanam')->name('commitment.avt');
		Route::get('commitment/{id}/formavt/lokasi', 'PengajuanController@ajuVerifTanam')->name('commitment.avt.lokasi');
		Route::post('commitment/{id}/formavt/store', 'PengajuanController@ajuVerifTanamStore')->name('commitment.avt.store');
		Route::get('commitment/{id}/pengajuan/tanam/show', 'PengajuanController@showAjuTanam')->name('pengajuan.tanam.show');

		//new pengajuan produksi
		Route::get('commitment/{id}/formavp', 'PengajuanController@ajuVerifProduksi')->name('commitment.avp');
		Route::get('commitment/{id}/formavp/lokasi', 'PengajuanController@ajuVerifProduksi')->name('commitment.avp.lokasi');
		Route::post('commitment/{id}/formavp/store', 'PengajuanController@ajuVerifProduksiStore')->name('commitment.avp.store');
		Route::get('commitment/{id}/pengajuan/produksi/show', 'PengajuanController@showAjuProduksi')->name('pengajuan.produksi.show');

		//new pengajuan skl
		Route::get('commitment/{id}/formavskl', 'PengajuanController@ajuVerifSkl')->name('commitment.avskl');
		Route::post('commitment/{id}/formavskl/store', 'PengajuanController@ajuVerifSklStore')->name('commitment.avskl.store');
		Route::get('commitment/{id}/formavskl/lokasi', 'PengajuanController@ajuVerifSkl')->name('commitment.avskl.lokasi');
		Route::get('commitment/{id}/pengajuan/skl/show', 'PengajuanController@showAjuSkl')->name('pengajuan.skl.show');


		Route::get('submission/{id}/show', 'PengajuanController@show')->name('submission.show');
		Route::delete('pengajuan/destroy', 'PengajuanController@massDestroy')->name('pengajuan.massDestroy');

		//daftar seluruh skl yang telah terbit (lama & baru)
		Route::get('skl/arsip', function () {
			return redirect()->route('skl.arsip');
		})->name('skl.arsip');

		Route::get('user/oldskl/index', 'OldSklController@index')->name('user.oldskl.index');
		Route::get('user/oldskl/{id}/show', 'OldSklController@show')->name('user.oldskl.show');

		//berkas
		Route::get('berkas', 'BerkasController@indexberkas')->name('berkas');

		//galeri
		Route::get('galeri', 'BerkasController@indexgaleri')->name('galeri');

		//template
		Route::delete('template/destroy', 'BerkasController@massDestroy')->name('template.massDestroy');
		Route::delete('template/{id}', 'BerkasController@destroytemplate')->name('template.destroy');
		Route::get('template/{berkas}/edit', 'BerkasController@edittemplate')->name('template.edit');
		Route::put('template/{berkas}', 'BerkasController@updatetemplate')->name('template.update');
		Route::get('template', 'FileManagementController@templateindex')->name('template');
		Route::get('template/create', 'FileManagementController@templatecreate')->name('template.create');
		Route::post('template', 'FileManagementController@templatestore')->name('template.store');

		//dihapus
		// Route::post('commitment/unggah', 'CommitmentController@store')->name('commitment.store');
		// Route::get('commitment/{pullriph}', 'CommitmentController@show')->name('commitment.show');
		// Route::get('commitment/{id}/edit', 'CommitmentController@edit')->name('commitment.edit');
		// pengajuan
		// Route::post('commitment/{id}/review/submit', 'PengajuanController@store')->name('commitment.review.submit');

		//Daftar SKL untuk user
		// Route::get('user/skl', 'UserSklController@index')->name('user.skl');
		// Route::get('user/skl/{id}/show', 'UserSklController@show')->name('user.skl.show');
		// Route::get('user/skl/{id}/print', 'UserSklController@print')->name('user.skl.print');

		// Route::get('template', 'BerkasController@indextemplate')->name('template');
		//Route::get('template/{berkas}', 'BerkasController@showtemplate')->name('template.show');
		// Route::get('template/create', 'BerkasController@createtemplate')->name('template.create');
		// Route::post('template', 'BerkasController@storetemplate')->name('template.store');

	});
	Route::get('lokasiTanamByCommitment/{id}', 'DataLokasiTanamController@lokasiTanamByCommitment')->name('lokasiTanamByCommitment');
	Route::get('listLokasi/{id}', 'DataLokasiTanamController@listLokasi')->name('ajutanam.listlokasi');
	Route::get('produksi/listLokasi/{id}', 'DataLokasiTanamController@listLokasiTanamProduksi')->name('ajuproduksi.listlokasi');
});

Route::group(['prefix' => 'importir', 'as' => 'importir.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
});

Route::group(['prefix' => 'verification', 'as' => 'verification.', 'namespace' => 'Verifikator', 'middleware' => ['auth']], function () {

	//verifikasi data lokasi tanam
	Route::get('{noIjin}/lokasitanam', 'LokasiTanamController@index')->name('lokasitanam');
	Route::get('{noIjin}/lokasitanam/{anggota_id}', 'LokasiTanamController@show')->name('lokasitanam.show');
	Route::get('{id}/summary', 'VerifSklController@dataCheck')->name('data.summary');

	//new verifikasi tanam
	Route::get('tanam', 'VerifTanamController@index')->name('tanam');
	Route::group(['prefix' => 'tanam', 'as' => 'tanam.'], function () {
		Route::get('{id}/check', 'VerifTanamController@check')->name('check');
		Route::put('{id}/storeCheck', 'VerifTanamController@storeCheck')->name('storeCheck');
		Route::get('{id}/show', 'VerifTanamController@show')->name('show');
		Route::post('{id}/checkBerkas', 'VerifTanamController@checkBerkas')->name('checkBerkas');
		Route::get('{noIjin}/poktan/{poktan_id}/check', 'VerifTanamController@verifPks')->name('check.pks');
		Route::put('pks/{id}/store', 'VerifTanamController@verifPksStore')->name('check.pks.store');
		Route::put('{id}/checkPksSelesai', 'VerifTanamController@checkPksSelesai')->name('checkPksSelesai');
		// Route::get('{noIjin}/lokasi/{anggota_id}', 'VerifTanamController@lokasicheck')->name('lokasicheck');
	});

	//new verifikasi produksi
	Route::get('produksi', 'VerifProduksiController@index')->name('produksi');
	Route::group(['prefix' => 'produksi', 'as' => 'produksi.'], function () {
		Route::get('{id}/check', 'VerifProduksiController@check')->name('check');
		Route::post('{id}/storeCheck', 'VerifProduksiController@storeCheck')->name('storeCheck');
		Route::get('{id}/show', 'VerifProduksiController@show')->name('show');
		Route::post('{id}/checkBerkas', 'VerifProduksiController@checkBerkas')->name('checkBerkas');
		Route::get('{noIjin}/poktan/{poktan_id}/check', 'VerifProduksiController@verifPks')->name('check.pks');
		Route::put('pks/{id}/store', 'VerifProduksiController@verifPksStore')->name('check.pks.store');
		Route::post('{id}/checkPksSelesai', 'VerifProduksiController@checkPksSelesai')->name('checkPksSelesai');

		//unused
		Route::put('{id}/store', 'VerifProduksiController@store')->name('store');
	});

	//new verifikasi skl
	Route::get('skl', 'VerifSklController@index')->name('skl');
	Route::group(['prefix' => 'skl', 'as' => 'skl.'], function () {
		Route::get('{id}/check', 'VerifSklController@check')->name('check');
		Route::post('{id}/checkBerkas', 'VerifSklController@checkBerkas')->name('checkBerkas');
		Route::get('{noIjin}/poktan/{poktan_id}/check', 'VerifSklController@verifPks')->name('check.pks');
		Route::put('pks/{id}/store', 'VerifSklController@verifPksStore')->name('check.pks.store');
		Route::post('{id}/checkPksSelesai', 'VerifSklController@checkPksSelesai')->name('checkPksSelesai');
		Route::get('{noIjin}/lokasi/{anggota_id}', 'VerifTanamController@lokasicheck')->name('lokasicheck');
		Route::post('{id}/storeCheck', 'VerifSklController@storeCheck')->name('storeCheck');
		Route::get('{id}/verifSklShow', 'VerifSklController@verifSklShow')->name('verifSklShow');

		//rekomendasi penerbitan
		Route::post('{id}/recomend', 'VerifSklController@recomend')->name('recomend');

		//daftar rekomendasi skl untuk pejabat
		Route::get('recomendations', 'VerifSklController@recomendations')->name('recomendations');
		Route::group(['prefix' => 'recomendation', 'as' => 'recomendation.'], function () {
			//detail rekomendasi untuk pejabat
			Route::get('{id}/show', 'VerifSklController@showrecom')->name('show');
			//preview draft skl untuk pejabat
			Route::get('{id}/draft', 'VerifSklController@draftSKL')->name('draft');
			//fungsi untuk pejabat menyetujui penerbitan.
			Route::put('{id}/approve', 'VerifSklController@approve')->name('approve');
		});

		//daftar skl diterbitkan
		Route::get('recomendations', 'VerifSklController@recomendations')->name('recomendations');
	});

	Route::get('{noIjin}/lokasi/{anggota_id}', 'VerifTanamController@lokasicheck')->name('lokasicheck');

	//verifikasi online/data
	// Route::get('data', 'VerifOnlineController@index')->name('data');
	// Route::get('data/{id}/show', 'VerifOnlineController@show')->name('data.show');
	// Route::get('data/pengajuan/{id}', 'VerifOnlineController@check')->name('data.check');
	// Route::get('data/commitment/{id}', 'VerifOnlineController@commitmentcheck')->name('data.commitmentcheck');
	// Route::put('data/commitment/{id}/store', 'VerifOnlineController@commitmentstore')->name('data.commitmentcheck.store');
	// Route::get('data/pks/{poktan_id}', 'VerifOnlineController@pkscheck')->name('data.pkscheck');
	// Route::post('data/pks/{poktan_id}/store', 'VerifOnlineController@pksstore')->name('data.pkscheck.store');
	// Route::get('data/pks/{id}/edit', 'VerifOnlineController@pksedit')->name('data.pkscheck.edit');
	// Route::put('data/pks/{id}/update', 'VerifOnlineController@pksupdate')->name('data.pkscheck.update');
	// Route::get('data/{noIjin}/lokasi/{anggota_id}', 'VerifOnlineController@lokasicheck')->name('data.lokasicheck');
	// Route::post('data/lokasi/store', 'VerifOnlineController@lokasistore')->name('data.lokasicheck.store');
	// Route::put('data/baonline/{id}/store', 'VerifOnlineController@baonline')->name('data.baonline.store');

	//verifikasi onfarm/lapangan
	// Route::get('onfarm', 'VerifOnfarmController@index')->name('onfarm');
	// Route::get('onfarm/{id}/show', 'VerifOnfarmController@show')->name('onfarm.show');
	// Route::get('onfarm/{id}/farmlist', 'VerifOnfarmController@farmlist')->name('onfarm.farmlist');
	// Route::get('onfarm/{noIjin}/lokasi/{anggota_id}', 'VerifOnfarmController@farmcheck')->name('onfarm.farmcheck');
	// Route::put('onfarm/lokasi/{id}', 'VerifOnfarmController@farmupdate')->name('onfarm.farmcheck.update');
	// Route::put('onfarm/{id}/update', 'VerifOnfarmController@update')->name('onfarm.update');

	// Route::resource('skl', 'SklController');
	// Route::get('skl', 'SklController@index')->name('skl'); //daftar siap rekomendasi

	Route::get('skl/{id}/show', 'SklController@show')->name('skl.show'); //summary skl
	// Route::get('arsip/completeds', 'SklController@completedindex')->name('arsip.completed'); //daftar seluruh skl yang telah terbit (lama & baru)

	//ke bawah ini mungkin di hapus
	Route::get('skl/publishes', 'SklController@publishes')->name('skl.publishes');
	Route::get('skl/published/{id}/print', 'SklController@published')->name('skl.published');
});

Route::group(['prefix' => 'skl', 'as' => 'skl.', 'namespace' => 'Verifikator', 'middleware' => ['auth']], function () {
	// daftar rekomendasi (index rekomendasi dan skl untuk verifikator)
	Route::get('recomended/list', 'VerifSklController@recomended')->name('recomended.list');
	Route::get('{id}/print', 'VerifSklController@printReadySkl')->name('print'); //form view skl untuk admin
	Route::put('{id}/upload', 'VerifSklController@Upload')->name('upload'); //fungsi upload untuk admin
	Route::get('arsip', 'VerifSklController@arsip')->name('arsip');
});

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
	// Change password
	if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
		Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
		Route::post('password', 'ChangePasswordController@update')->name('password.update');
		Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
		Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
	}
});

Route::group(['prefix' => 'digisign', 'as' => 'digisign.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
	Route::get('index', 'DigitalSign@index')->name('index');
});
