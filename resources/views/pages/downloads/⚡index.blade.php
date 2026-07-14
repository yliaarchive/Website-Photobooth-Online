<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\PhotoboxResults;
use App\Livewire\Forms\DownloadsForm;
use Illuminate\Support\Facades\Auth;
use Flux\Flux;

new class extends Component
{
    public DownloadsForm $form;

    public ?string $previewUrl = null;

    public string $search = ''; 

    #[Computed]
    public function MyGallery()
    {
        $query = PhotoboxResults::with('frame')
            ->where('user_id', Auth::id());

        if ($this->search) {
            $query->whereHas('frame', function($q) {
                $q->where('nama_frame', 'like', '%' . $this->search . '%');
            });
        }

        return $query->latest()->get();
    }

    public function downloadImage($resultId)
    {
        $result = PhotoboxResults::find($resultId);

        if ($result) {
            $this->form->catatDownload($resultId);

            return response()->download(
                storage_path('app/public/' . $result->result_file)
            );
        }
    }

    public function showPreview($filename)
    {
        $this->previewUrl = asset('storage/' . $filename);

        Flux::modal('preview-modal')->show();
    }
};

?>

<div>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-rose-50">

    <div class="max-w-7xl mx-auto space-y-6 py-8">

        <div>
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-pink-50 via-rose-50 to-pink-100 border border-pink-100 shadow-xl p-8">

    <div class="absolute -top-10 -right-10 w-52 h-52 bg-pink-200 opacity-20 rounded-full blur-3xl"></div>

    <div class="flex justify-between items-center relative">

        <div>

            <h1 class="text-4xl font-bold text-pink-600">
                💖 Galeri Download
            </h1>

            <p class="text-gray-600 mt-3">
                Simpan hasil photobox favoritmu dengan kualitas terbaik ✨
            </p>

            <div class="mt-5 flex gap-4">

                <div class="bg-white rounded-xl shadow px-5 py-3">

                    <div class="text-gray-400 text-sm">
                        Total Photobox
                    </div>

                    <div class="text-2xl font-bold text-pink-600">
                        {{ count($this->MyGallery) }}
                    </div>

                </div>

            </div>

        </div>
        <div class="flex justify-end">

    <div class="relative w-80">

        <svg xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5 absolute left-3 top-3 text-pink-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-4.3-4.3m1.3-5.2a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>

        </svg>

        <input
            type="text"
            wire:model.live="search" 
            placeholder="Cari nama frame..."
            class="w-full rounded-xl border border-pink-200 pl-10 pr-4 py-3 focus:ring-2 focus:ring-pink-300">

    </div>

</div>

        <div class="hidden lg:block text-8xl">

            📸

        </div>

    </div>

</div>

        <flux:separator variant="subtle" />

        @if(count($this->MyGallery) > 0)

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-8">

                @foreach ($this->MyGallery as $item)

    <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-lg border border-pink-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition duration-300">

        <div class="absolute top-4 right-4 z-10">
            <span class="bg-gradient-to-r from-pink-500 to-fuchsia-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-lg">
                📸 Photobox
            </span>
        </div>

        <div class="bg-pink-50 dark:bg-zinc-800 flex justify-center items-center p-4 h-72">
                            @if($item->result_file)

                                <img
                                    src="{{ asset('storage/'.$item->result_file) }}"
                                    alt="Photobox Result"
                                    class="max-h-full object-contain rounded-xl transition duration-300 hover:scale-105">
                            
                            @else

                                <span class="text-gray-400">
                                    Gambar tidak ditemukan
                                </span>

                            @endif

                        </div>

                        <div class="p-4 space-y-3">

    <div class="flex justify-between items-center">

        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ $item->created_at->format('d M Y, H:i') }}
        </span>

        <span class="px-3 py-1 rounded-full bg-pink-100 text-pink-600 text-xs font-medium">
            {{ $item->frame->nama_frame ?? 'Frame' }}
        </span>

    </div>

    <div class="space-y-2">

<flux:button
    class="w-full rounded-xl"
    variant="filled"
    icon="eye"
    wire:click="showPreview('{{ $item->result_file }}')">

    Preview

</flux:button>

<flux:button
    class="w-full rounded-xl bg-pink-500 hover:bg-pink-600 text-white"
    icon="arrow-down-tray"
    wire:click="downloadImage({{ $item->id }})">

    Download

</flux:button>

</div>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

<div class="text-center py-20 bg-gradient-to-br from-pink-50 to-white rounded-3xl border border-pink-200 shadow-lg mt-8">

    <div class="text-7xl mb-4">
        💖
    </div>

    <h2 class="text-2xl font-bold text-pink-600">
        Belum Ada Photobox
    </h2>

    <p class="text-gray-500 mt-3 mb-8">
        Yuk buat photobox pertamamu dan simpan kenangan terbaikmu.
    </p>

    <flux:button
        href="/PhotoboxResults"
        icon="sparkles"
        variant="primary"
        class="rounded-full px-6">

        Buat Photobox

    </flux:button>

</div>

@endif

    </div>

    <flux:modal name="preview-modal" class="md:w-[700px]">

        <div class="p-5 bg-pink-50 rounded-2xl">

            @if($previewUrl)

                <img
                    src="{{ $previewUrl }}"
                    alt="Preview"
                    class="w-full rounded-xl shadow-lg">

            @endif

        </div>

    </flux:modal>

</div>