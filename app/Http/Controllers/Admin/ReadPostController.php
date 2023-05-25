<?php

namespace App\Http\Controllers\Admin;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReadPost;

class ReadPostController extends Controller
{
	public function star(Request $request, $id)
	{
		$post = ReadPost::findOrFail($id);
		$post->starred = !$post->starred;
		$post->save();

		return response()->json(['starred' => $post->starred]);
	}
}
