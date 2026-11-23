@extends('layouts.app')

@section('title', __('notifications.title'))

@section('content')
<div class="space-y-6">
    <!-- Header with Quick Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">{{ __('notifications.title') }}</h1>
                @if($unreadCount > 0)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        {{ __('notifications.unread_count', ['count' => $unreadCount]) }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ __('notifications.all_caught_up') }}
                    </span>
                @endif
            </div>
            <p class="text-sm text-slate-500 mt-1">
                {{ __('notifications.subtitle') }}
            </p>
        </div>

        @if($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 shadow-2xs transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('notifications.mark_all_read') }}
                </button>
            </form>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="space-y-3">
        @forelse($notifications as $notif)
            @php
                $isUnread = is_null($notif->read_at);
                $typeColors = [
                    'STATUS_UPDATED' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'DELAY_DETECTED' => 'bg-amber-50 text-amber-800 border-amber-200',
                    'EXCEPTION_LOGGED' => 'bg-rose-50 text-rose-700 border-rose-200',
                    'EXCEPTION_RESOLVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                ];
                $badge = $typeColors[$notif->type] ?? 'bg-slate-100 text-slate-700 border-slate-200';
            @endphp
            <div class="p-4 sm:p-5 rounded-2xl border transition flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $isUnread ? 'bg-blue-50/40 border-blue-200 shadow-sm' : 'bg-white border-slate-200 shadow-2xs text-slate-600' }}">
                <div class="flex items-start gap-4">
                    <!-- Icon Indicator -->
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5 {{ $isUnread ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                        @if(str_contains($notif->type, 'DELAY'))
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif(str_contains($notif->type, 'EXCEPTION'))
                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        @else
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>

                    <!-- Message Body -->
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badge }}">
                                {{ $notif->type }}
                            </span>
                            @if($isUnread)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                    {{ __('common.badges.new') }}
                                </span>
                            @endif
                            <span class="text-xs text-slate-500 font-mono">
                                {{ $notif->created_at->translatedFormat('d M Y, H:i') }} ({{ $notif->created_at->diffForHumans() }})
                            </span>
                        </div>

                        <p class="text-sm font-medium {{ $isUnread ? 'text-slate-900' : 'text-slate-700' }} leading-relaxed">
                            {{ $notif->message }}
                        </p>

                        @if($notif->shipment)
                            <div class="text-xs text-slate-500 flex items-center gap-2 pt-1 font-mono">
                                <span>Ref:</span>
                                <a href="{{ route('shipments.show', $notif->shipment) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-semibold">
                                    {{ $notif->shipment->shipment_number }}
                                </a>
                                @if($notif->shipment->originLocation && $notif->shipment->destinationLocation)
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="text-slate-600">{{ $notif->shipment->originLocation->city }} &rarr; {{ $notif->shipment->destinationLocation->city }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions: Mark as Read & Open Shipment -->
                <div class="flex items-center gap-2 shrink-0 sm:self-center pl-14 sm:pl-0">
                    @if($notif->shipment)
                        <a href="{{ route('shipments.show', $notif->shipment) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
                            {{ __('notifications.open_shipment') }} &rarr;
                        </a>
                    @endif

                    @if($isUnread)
                        <form method="POST" action="{{ route('notifications.read', $notif) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-semibold transition cursor-pointer" title="{{ __('notifications.mark_read') }}">
                                {{ __('notifications.mark_read') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-12 text-center rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="w-12 h-12 mx-auto rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 class="text-sm font-semibold text-slate-900">{{ __('notifications.empty.title') }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ __('notifications.empty.description') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $notifications->links() }}
    </div>
</div>
@endsection
