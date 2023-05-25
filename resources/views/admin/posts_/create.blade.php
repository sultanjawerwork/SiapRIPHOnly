@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
<link rel="stylesheet" media="screen, print" href="{{ asset('css/formplugins/summernote/summernote-bs4.css') }}">
        
@can('feeds_create')
<div class="row">
    <div class="col-md-12">
        <form method="post" action="{{ route('admin.posts.store') }}">
            @csrf
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>
                        Artikel/berita | <span class="fw-300"><i>Tambah</i></span>
                    </h2>
                    <div class="panel-toolbar">
                        <div class="form-group">
                            <button class="btn btn-success  waves-effect waves-themed btn-sm mr-2 btnsave" type="submit">
                                {{ trans('global.save') }}
                            </button>
                            <a class="btn btn-danger  waves-effect waves-themed btn-sm mr-2" href="{{ route('admin.posts.index') }}">
                                {{ trans('global.cancel') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="panel-container show">
                    <div class="panel-content">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="required" for="title">{{ __('Judul artikel') }}<span class="text-danger">*</span></label>
                                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', '') }}" required>
                                    @if($errors->has('title'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('title') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ __('Isikan judul artikel.') }}</span>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="form-label" for="body">{{ __('Isi artikel') }} <span class="text-danger">*</span></label>
                                        <textarea name="summernoteInput" class="summernote"></textarea>
                                        <div class="help-block">{{ __('Tuliskan artikel anda disini.') }}</div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="d-flex mb-3">
                                    <span class="mr-auto">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch" data-toggle="tooltip" title
                                                data-original-title="Aktif/deaktivasi artikel ini untuk seluruh pengguna">
                                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active">
                                                <label class="custom-control-label" for="is_active">Draft / Publish</label>
                                            </div>
                                        </div>
                                    </span>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="tags">Tags</label>
                                    <div class="input-group input-group-sm input-group-multi-transition">
                                        <input type="text" class="form-control form-control-sm" aria-label="" name="tags" id="tags"
                                            placeholder="tambahkan tag.." value="">
                                    </div>
                                    <span class="help-block">Tag untuk artikel ini.</span>
                                </div>
                                        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection

<!-- start script for this page -->
@section('scripts')
@parent
<script src="{{ asset('js/formplugins/summernote/summernote-bs4.js') }}"></script>
    <script>
        $(document).ready(function() {
            //init default
            $('.summernote').summernote(
            {
                height: 200,
                dialogsFade: true,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

        });
    </script>
@endsection
