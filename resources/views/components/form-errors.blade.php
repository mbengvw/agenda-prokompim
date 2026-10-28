@props(['title' => 'Data belum bisa disimpan. Periksa kembali isian berikut:'])

@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800']) }} role="alert">
        <p class="font-semibold">{{ $title }}</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
