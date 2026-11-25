@extends('layouts.app')

@section('title', __('notifications.title'))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
    <!-- Left Sticky Notification Control Rail -->
    <aside class="lg:col-span-4 lg:sticky lg:top-20 space-y-4">
        <div class="nex-card p-6 space-y-4">
            <div class="flex items-start justify-between gap-3">
                <div class="w-10 h-10 rounded-full bg-[#FDF2F6] text-[#C93565] border border-[#F8C9DC] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                @if($unreadCount > 0)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC]">
                        {{ __('notifications.unread_count', ['count' => $unreadCount]) }}
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ __('notifications.all_caught_up') }}
                    </span>
                @endif
            </div>

            <div>
                <h1 class="text-2xl font-bold tracking-tight text-[#4A2C35]">{{ __('notifications.title') }}</h1>
                <p class="text-xs text-[#8C737B] mt-1 leading-relaxed">
                    {{ __('notifications.subtitle') }}
                </p>
            </div>

            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2.5 rounded-full bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-xs font-semibold shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('notifications.mark_all_read') }}
                    </button>
                </form>
            @endif
        </div>

        <!-- Alert Channel Legend -->
        <div class="nex-card p-5 space-y-3">
            <div class="text-xs font-bold text-[#4A2C35]">{{ __('common.labels.status') }}</div>
            <div class="space-y-2 text-[11px]">
                <div class="nex-inset px-3 py-2 flex items-center justify-between">
                    <span class="font-mono font-semibold text-[#9E224B]">STATUS_UPDATED</span>
                    <span class="w-2 h-2 rounded-full bg-[#E04879]"></span>
                </div>
                <div class="nex-inset px-3 py-2 flex items-center justify-between">
                    <span class="font-mono font-semibold text-amber-800">DELAY_DETECTED</span>
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                </div>
                <div class="nex-inset px-3 py-2 flex items-center justify-between">
                    <span class="font-mono font-semibold text-red-700">EXCEPTION_LOGGED</span>
                    <span class="w-2 h-2 rounded-full bg-red-600"></span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Right Notifications Stream -->
    <div class="lg:col-span-8 space-y-3">
        @forelse($notifications as $notif)
            @php
                $isUnread = is_null($notif->read_at);
                $typeColors = [
                    'STATUS_UPDATED' => 'bg-[#FDF2F6] text-[#9E224B] border-[#F8C9DC]',
                    'DELAY_DETECTED' => 'bg-amber-50 text-amber-800 border-amber-200',
                    'EXCEPTION_LOGGED' => 'bg-red-50 text-red-700 border-red-200',
                    'EXCEPTION_RESOLVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                ];
                $badge = $typeColors[$notif->type] ?? 'bg-[#FAF7F9] text-[#6E545D] border-[#ECE4E8]';
            @endphp
            <div class="p-4 sm:p-5 rounded-[22px] border transition flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $isUnread ? 'bg-[#FDF2F6]/70 border-[#F8C9DC] shadow-sm' : 'bg-white border-[#BE2B5B]/[0.04] shadow-sm text-[#7D626B]' }}">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 mt-0.5 {{ $isUnread ? 'bg-[#FCE5EE] text-[#C93565] border border-[#F8C9DC]' : 'bg-[#FAF7F9] text-[#8C737B] border border-[#ECE4E8]' }}">
                        @if(str_contains($notif->type, 'DELAY'))
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif(str_contains($notif->type, 'EXCEPTION'))
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        @else
                            <svg class="w-5 h-5 text-[#E04879]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badge }}">
                                {{ $notif->type }}
                            </span>
                            @if($isUnread)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-[#C93565] uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#E04879]"></span>
                                    {{ __('common.badges.new') }}
                                </span>
                            @endif
                            <span class="text-xs text-[#8C737B] font-mono">
                                {{ $notif->created_at->translatedFormat('d M Y, H:i') }} ({{ $notif->created_at->diffForHumans() }})
                            </span>
                        </div>

                        <p class="text-sm font-medium {{ $isUnread ? 'text-[#4A2C35]' : 'text-[#6E545D]' }} leading-relaxed">
                            {{ $notif->message }}
                        </p>

                        @if($notif->shipment)
                            <div class="text-xs text-[#8C737B] flex items-center gap-2 pt-1 font-mono">
                                <span>Ref:</span>
                                <a href="{{ route('shipments.show', $notif->shipment) }}" class="text-[#C93565] hover:underline font-semibold">
                                    {{ $notif->shipment->shipment_number }}
                                </a>
                                @if($notif->shipment->originLocation && $notif->shipment->destinationLocation)
                                    <span class="text-zinc-300">&bull;</span>
                                    <span class="text-[#7D626B]">{{ $notif->shipment->originLocation->city }} &rarr; {{ $notif->shipment->destinationLocation->city }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 sm:self-center pl-14 sm:pl-0">
                    @if($notif->shipment)
                        <a href="{{ route('shipments.show', $notif->shipment) }}" class="px-3.5 py-1.5 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-xs font-semibold text-[#6E545D] transition">
                            {{ __('notifications.open_shipment') }} &rarr;
                        </a>
                    @endif

                    @if($isUnread)
                        <form method="POST" action="{{ route('notifications.read', $notif) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3.5 py-1.5 rounded-full bg-[#C93565] hover:bg-[#b32956] text-white text-xs font-semibold transition cursor-pointer" title="{{ __('notifications.mark_read') }}">
                                {{ __('notifications.mark_read') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="nex-card p-12 text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-[#FDF2F6] border border-[#F8C9DC] flex items-center justify-center text-[#C93565] mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 class="text-sm font-semibold text-[#4A2C35]">{{ __('notifications.empty.title') }}</h3>
                <p class="text-xs text-[#8C737B] mt-1">{{ __('notifications.empty.description') }}</p>
            </div>
        @endforelse

        <div>
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection
