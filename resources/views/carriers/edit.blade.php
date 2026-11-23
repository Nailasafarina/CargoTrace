@extends('layouts.app')

@section('title', __('carriers.edit_carrier') . ' ' . $carrier->code)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="pb-4 border-b border-slate-200 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('carriers.edit_carrier') }}: {{ $carrier->name }}</h1>
            <p class="text-xs text-slate-500 mt-1">{{ __('carriers.edit_subtitle') }}</p>
        </div>
        <a href="{{ route('carriers.index') }}" class="text-xs font-semibold text-slate-600 hover:text-blue-600 transition">&larr; {{ __('carriers.back_to_carriers') }}</a>
    </div>

    <form method="POST" action="{{ route('carriers.update', $carrier) }}" class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('carriers.fields.name') }} *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $carrier->name) }}"
                    required
                    class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                />
                @error('name') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="code" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('carriers.fields.code') }} *</label>
                <input
                    type="text"
                    id="code"
                    name="code"
                    value="{{ old('code', $carrier->code) }}"
                    required
                    maxlength="10"
                    class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 font-mono uppercase focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                />
                @error('code') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="contact_email" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('carriers.fields.contact_email') }}</label>
                <input
                    type="email"
                    id="contact_email"
                    name="contact_email"
                    value="{{ old('contact_email', $carrier->contact_email) }}"
                    class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                />
                @error('contact_email') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="contact_phone" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('carriers.fields.contact_phone') }}</label>
                <input
                    type="text"
                    id="contact_phone"
                    name="contact_phone"
                    value="{{ old('contact_phone', $carrier->contact_phone) }}"
                    class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                />
                @error('contact_phone') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    {{ old('is_active', $carrier->is_active) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20"
                />
                {{ __('carriers.enable_carrier_help') }}
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('carriers.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
                {{ __('common.actions.cancel') }}
            </a>
            <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm hover:shadow transition cursor-pointer">
                {{ __('carriers.edit_carrier') }}
            </button>
        </div>
    </form>
</div>
@endsection
