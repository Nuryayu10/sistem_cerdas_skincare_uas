@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-rose-600">Chat AI Skincare</h1>
                <p class="text-sm text-gray-500">Tanyakan apa saja tentang kulit, bahan skincare, dan rutinitas kecantikan.</p>
            </div>
            <div class="text-right text-xs text-gray-400">Percakapan tersimpan di database akun Anda.</div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-rose-100 p-6">
        @if ($errors->any())
            <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-4 max-h-[54vh] overflow-y-auto rounded-3xl bg-rose-50 p-4 shadow-inner">
            @forelse ($history as $message)
                @if ($message->sender === 'user')
                    <div class="flex justify-end">
                        <div class="max-w-xl rounded-3xl bg-rose-500 px-4 py-3 text-right text-sm text-white shadow-sm">
                            <div>{{ $message->content }}</div>
                            <div class="mt-2 text-[11px] text-rose-100">Anda · {{ $message->created_at->format('H:i') }}</div>
                        </div>
                    </div>
                @else
                    <div class="flex justify-start">
                        <div class="max-w-xl rounded-3xl bg-white px-4 py-3 text-sm text-gray-700 shadow-sm border border-rose-100">
                            <div>{{ $message->content }}</div>
                            <div class="mt-2 text-[11px] text-gray-400">AI Skincare · {{ $message->created_at->format('H:i') }}</div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="rounded-3xl border border-dashed border-rose-200 bg-white/60 p-8 text-center text-sm text-gray-500">
                    Belum ada percakapan. Silakan ketik pertanyaan di bawah untuk mulai chat.
                </div>
            @endforelse
        </div>

        <form action="{{ route('chat.send') }}" method="POST" class="mt-5 flex flex-col gap-3 md:flex-row md:items-end">
            @csrf
            <label class="sr-only" for="message">Pesan</label>
            <textarea id="message" name="message" rows="2" placeholder="Tanyakan tentang skincare..." class="min-h-[80px] w-full rounded-3xl border border-rose-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-rose-300 focus:outline-none focus:ring-2 focus:ring-rose-100">{{ old('message') }}</textarea>
            <button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-3xl bg-rose-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-200">Kirim</button>
        </form>
    </div>
</div>
@endsection
