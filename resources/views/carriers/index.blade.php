@extends('layouts.app')

@section('title', 'Carriers Directory')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Freight Carriers</h1>
            <p class="text-sm text-slate-400 mt-1">Manage global shipping lines and multi-modal logistics partners.</p>
        </div>
        @can('create', App\Models\Carrier::class)
            <a href="{{ route('carriers.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Add New Carrier
            </a>
        @endcan
    </div>

    <!-- Search Bar -->
    <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800 flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('carriers.index') }}" class="w-full sm:w-80 flex gap-2">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search name, code, email..."
                class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500"
            />
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-300">
                Filter
            </button>
            @if(request('search'))
                <a href="{{ route('carriers.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-400">Clear</a>
            @endif
        </form>
        <div class="text-xs text-slate-400">
            Showing <span class="font-semibold text-white">{{ $carriers->total() }}</span> carriers
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 shadow-xl">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-800">
                <tr>
                    <th class="px-5 py-3.5">Code</th>
                    <th class="px-5 py-3.5">Carrier Name</th>
                    <th class="px-5 py-3.5">Contact Information</th>
                    <th class="px-5 py-3.5 text-center">Active Shipments</th>
                    <th class="px-5 py-3.5 text-center">Status</th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($carriers as $carrier)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-5 py-3.5 font-mono font-bold text-sky-400">
                            {{ $carrier->code }}
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-white">
                            <a href="{{ route('carriers.show', $carrier) }}" class="hover:text-sky-400 transition">
                                {{ $carrier->name }}
                            </a>
                        </td>
                        <td class="px-5 py-3.5 text-slate-400">
                            <div>{{ $carrier->contact_email ?? '—' }}</div>
                            <div class="text-[11px] text-slate-500">{{ $carrier->contact_phone ?? '—' }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-center font-semibold text-white">
                            {{ $carrier->shipments_count }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($carrier->is_active)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Active</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-800 text-slate-400 border border-slate-700">Inactive</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('carriers.show', $carrier) }}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition">
                                View
                            </a>
                            @can('update', $carrier)
                                <a href="{{ route('carriers.edit', $carrier) }}" class="px-2.5 py-1 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 transition">
                                    Edit
                                </a>
                            @endcan
                            @can('delete', $carrier)
                                <form method="POST" action="{{ route('carriers.destroy', $carrier) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete carrier {{ $carrier->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 transition cursor-pointer">
                                        Delete
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                            No freight carriers found matching the criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        {{ $carriers->links() }}
    </div>
</div>
@endsection
