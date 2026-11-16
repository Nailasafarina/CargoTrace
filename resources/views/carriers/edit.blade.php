@extends('layouts.app')

@section('title', 'Edit Carrier ' . $carrier->code)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="pb-4 border-b border-slate-800 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Edit Carrier: {{ $carrier->name }}</h1>
            <p class="text-xs text-slate-400 mt-1">Update carrier operational details and contact credentials.</p>
        </div>
        <a href="{{ route('carriers.index') }}" class="text-xs text-slate-400 hover:text-white transition">&larr; Back to Carriers</a>
    </div>

    <form method="POST" action="{{ route('carriers.update', $carrier) }}" class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label for="name" class="block text-xs font-medium text-slate-300 mb-1">Carrier Name *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $carrier->name) }}"
                    required
                    class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                />
                @error('name') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="code" class="block text-xs font-medium text-slate-300 mb-1">Code (Unique) *</label>
                <input
                    type="text"
                    id="code"
                    name="code"
                    value="{{ old('code', $carrier->code) }}"
                    required
                    maxlength="10"
                    class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white font-mono uppercase focus:outline-none focus:ring-2 focus:ring-sky-500"
                />
                @error('code') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="contact_email" class="block text-xs font-medium text-slate-300 mb-1">Dispatch / Support Email</label>
                <input
                    type="email"
                    id="contact_email"
                    name="contact_email"
                    value="{{ old('contact_email', $carrier->contact_email) }}"
                    class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                />
                @error('contact_email') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="contact_phone" class="block text-xs font-medium text-slate-300 mb-1">Emergency Operations Phone</label>
                <input
                    type="text"
                    id="contact_phone"
                    name="contact_phone"
                    value="{{ old('contact_phone', $carrier->contact_phone) }}"
                    class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                />
                @error('contact_phone') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    {{ old('is_active', $carrier->is_active) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-slate-700 bg-slate-950 text-sky-500"
                />
                Enable this carrier for shipment assignment
            </label>
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('carriers.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-300">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition cursor-pointer">
                Update Carrier
            </button>
        </div>
    </form>
</div>
@endsection
