@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-g border shadow-0">
                <div class="card-header p-0">
                    <div class="p-3 d-flex flex-row">
                        <div class="d-block flex-shrink-0">
                            <img src="/img/avatars/farmer.png" class="img-fluid img-thumbnail" alt=""
                                style="height:50px; width:100%;">
                        </div>
                        <div class="d-block ml-2">
                            <span class="h6 font-weight-bold text-uppercase d-block m-0">
                                <a href="javascript:void(0);">{{ $post->title }}</a>
                            </span>
                            <div>
                                <span>Created by: </span>
                                <a>{{ $post->user->name }}</a>
                            </div>
                        </div>
                        <div class="d-block align-items-right ml-auto align-self-start mb-1">
                            <div>
                                <a class="badge btn-sm btn-primary text-white"
                                    title="Kategori Artikel">{{ ($post->category->name ?? '') }}</a>
                                @if (!empty($post->published_at))
                                    <a class="badge btn-sm btn-success text-white"
                                        title="telah dipublikasikan">Published</a>
                                    <a href="javascript:void(0);" class="text-muted fs-xs font-italic">
                                        Published at: {{ $post->published_at }}
                                    </a>
                                @else
                                    <a class="badge btn-sm btn-warning text-white"
                                        title="belum dipublikasikan">Unpublished</a>
                                @endif
                            </div>
                            
                        </div>

                    </div>
                </div>
                <div class="panel-content mt-3">
                    <div class="col">
                        <div class="text-center mb-2">
                            @if (is_null($post->img_cover))
                                <div></div>
                            @else
                                <img src="{{ asset(old('img_cover', 'img/' . $post->img_cover)) }}" class="img-thumbnail"
                                    alt="">
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body ">
                    {!! $post->body !!}
                </div>

                <div class="card-footer">
                    <div class="d-flex align-items-center">
                        <!-- visible to Administrator/creator only-->

                        @if (\Auth::user()->roleaccess == '1')
                            <a href="{{ route('admin.posts.edit', $post->id) }}" class="badge btn-sm btn-danger mr-1"
                                role="button" title="Ubah Artikel"><i class="fal fa-edit"></i>
                                Edit</a>
                        @endif
                        <a href="{{ route('admin.posts.index') }}" class="badge btn-sm btn-info">Kembali</a>
                        <!-- visible to everyone/reader -->
                        <div class="custom-control custom-switch flex-shrink-0 ml-auto">
                            <input type="checkbox" class="custom-control-input" id="customSwitch1">
                            <label class="custom-control-label" for="customSwitch1">Tandai sudah dibaca</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<!-- @parent -->
<!-- start script for this page -->
@section('scripts')
@endsection
