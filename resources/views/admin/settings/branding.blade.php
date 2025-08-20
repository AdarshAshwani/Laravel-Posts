
@extends('layouts.app')

@section('title','Branding')
@section('page_title','Edit your Site Branding')

@section('content')
<div class="max-w-xl space-y-6">
  @if(session('message'))
    <div class="soft-card p-3 text-emerald-700 dark:text-emerald-300">{{ session('message') }}</div>
  @endif

  <form class="soft-card p-5 space-y-5" method="POST" action="{{ route('settings.branding.update') }}" enctype="multipart/form-data">
    @csrf

    <div>
      <label class="block text-sm font-semibold mb-1">Site name</label>
      <input name="site_name" class="input" value="{{ old('site_name',$site_name) }}" required maxlength="30">
      @error('site_name')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="flex justify-end">
      <button class="btn btn-primary">Save</button>
    </div>
  </form>
</div>
@endsection
