<?php

namespace App\Http\Controllers\Admin;

use App\Models\Berkas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBerkasRequest;
use App\Http\Requests\UpdateBerkasRequest;
use App\Http\Requests\MassDestroyBerkasRequest;
use App\Models\FileManagement;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileManagementController extends Controller
{
	public function index()
	{
		$module_name = 'File Management';
		$page_title = 'Templates Master';
		$page_heading = 'Templates Master';
		$heading_class = 'fab fa-stack-overflow';

		$templates = FileManagement::all();

		return view('admin.filemanagement.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'templates'));
	}

	public function create()
	{
		$module_name = 'File Management';
		$page_title = 'Templates Master';
		$page_heading = 'New Template';
		$heading_class = 'fal fa-edit';

		return view('admin.filemanagement.create', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
	}

	public function store(Request $request)
	{
		$template = new FileManagement();
		$template->berkas = $request->input('berkas');
		$template->nama_berkas = $request->input('nama_berkas');
		$template->deskripsi = $request->input('deskripsi');

		if ($request->hasFile('lampiran')) {
			$file = $request->file('lampiran');
			$filename = 'template_' . $file->getClientOriginalName();
			$file->storeAs('uploads/master/', $filename, 'public');
			$template->lampiran = $filename;
		}
		$template->save();
		return redirect()->route('admin.task.template.index')->with('success', 'Template berhasil diunggah.');
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		//
	}

	public function update(Request $request, $id)
	{
		//
	}

	public function download($id)
	{
		$file = FileManagement::find($id);

		if (!$file) {
			return redirect()->back()->with('error', 'Berkas tidak ditemukan');
		}

		$filename = $file->lampiran;
		$path = 'uploads/master/' . $filename;

		// Check if the file exists in the storage
		// if (Storage::exists($path)) {
		// 	// Generate a public URL for the file
		$url = Storage::url($path);

		// Download the file
		return Response::download(public_path($url), $filename);
		// } else {
		// 	// Or you can redirect or display an error message
		// 	return redirect()->back()->with('error', 'Berkas tidak ditemukan');
		// }
	}

	public function destroy($id)
	{
		$template = FileManagement::findOrFail($id);
		$template->delete();
		return redirect()->route('admin.task.template.index')->with('success', 'tempplate berhasil dihapus.');
	}
}
