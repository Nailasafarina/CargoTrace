@extends('layouts.app')

@section('title', __('carriers.edit_carrier') . ' ' . $carrier->code)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    <!-- Left Brief Card -->
    <aside class="lg:col-span-4 nex-card p-6 space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <span class="px-2.5 py-0.5 rounded-full font-mono text-[11px] font-bold bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC]">
                    {{ $carrier->code }}
                </span>
                <h1 class="text-xl font-bold tracking-tight text-[#1F171B] mt-2">{{ __('carriers.edit_carrier') }}: {{ $carrier->name }}</h1>
            </div>
            <img src="{{ asset('images/fleet-truck-mini.png') }}" alt="{{ $carrier->name }}" class="w-20 h-10 object-contain shrink-0 select-none" />
        </div>
        <p class="text-xs text-zinc-500 leading-relaxed">{{ __('carriers.edit_subtitle') }}</p>
        <a href="{{ route('carriers.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-xs font-semibold text-zinc-700 transition">
            &larr; {{ __('carriers.back_to_carriers') }}
        </a>
    </aside>

    <!-- Right Form Card -->
    <form method="POST" action="{{ route('carriers.update', $carrier) }}" class="lg:col-span-8 nex-card p-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label for="name" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('carriers.fields.name') }} *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $carrier->name) }}" required class="nex-input text-sm py-2.5" />
                @error('name') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="code" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('carriers.fields.code') }} *</label>
                <input type="text" id="code" name="code" value="{{ old('code', $carrier->code) }}" required maxlength="10" class="nex-input text-sm py-2.5 font-mono uppercase" />
                @error('code') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="contact_email" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('carriers.fields.contact_email') }}</label>
                <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $carrier->contact_email) }}" class="nex-input text-sm py-2.5" />
                @error('contact_email') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="contact_phone" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('carriers.fields.contact_phone') }}</label>
                <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $carrier->contact_phone) }}" class="nex-input text-sm py-2.5" />
                @error('contact_phone') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="nex-inset p-4">
            <label class="flex items-center gap-2.5 text-xs font-medium text-zinc-700 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $carrier->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded border-[#E6DCE1] text-[#C93565] focus:ring-[#E04879]/20" />
                {{ __('carriers.enable_carrier_help') }}
            </label>
        </div>

        <div class="pt-4 border-t border-[#F5F0F3] flex justify-end gap-3">
            <a href="{{ route('carriers.index') }}" class="px-5 py-2.5 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-xs font-semibold text-zinc-700 transition">
                {{ __('common.actions.cancel') }}
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-white text-xs font-semibold shadow-sm transition cursor-pointer">
                {{ __('carriers.edit_carrier') }}
            </button>
        </div>
    </form>
</div>
@endsection
