@extends('layouts.admin')

@section('title', 'Log Aktivitas Admin')
@section('page_title', 'Audit Log & Riwayat Aktivitas Administrator')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Riwayat Tindakan & Keamanan Sistem</h3>
                <p class="text-xs text-slate-400 mt-0.5">Mencatat verifikasi pembayaran, perubahan status, pembatalan, dan modifikasi data secara permanen.</p>
            </div>
            <span class="text-xs text-slate-500 font-semibold">{{ $logs->total() }} Log Tercatat</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                        <th class="p-3.5 pl-5">Waktu</th>
                        <th class="p-3.5">Administrator</th>
                        <th class="p-3.5">Aksi / Event</th>
                        <th class="p-3.5">Target Entitas</th>
                        <th class="p-3.5">Detail & Parameter</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-3.5 pl-5 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="p-3.5">
                                <span class="font-bold text-slate-800 block">{{ $log->user?->name ?? 'System' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $log->user?->email ?? '-' }}</span>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-md font-mono text-[10px] font-bold 
                                    {{ str_contains($log->action, 'confirm') ? 'bg-emerald-100 text-emerald-800' : 
                                       (str_contains($log->action, 'reject') ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-600 font-mono text-[11px]">
                                {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                            </td>
                            <td class="p-3.5">
                                @if(!empty($log->details))
                                    <pre class="bg-slate-50 p-2 rounded-lg text-[10px] text-slate-700 font-mono max-w-xs overflow-x-auto border border-slate-100">{{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">
                                Belum ada riwayat aktivitas log.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
