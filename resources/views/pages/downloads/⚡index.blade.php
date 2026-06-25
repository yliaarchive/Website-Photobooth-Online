<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\PhotoboxResults;
use App\Livewire\Forms\DownloadsForm;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public DownloadsForm $form;

    #[Computed]
    public function MyGallery()
    {
        return PhotoboxResults::where('user_id', Auth::id())->latest()->get();
    }

    public function downloadImage($resultId)
    {
        $result = PhotoboxResults::find($resultId);

        if ($result) {
            $this->form->catatDownload($resultId);

            return response()->download(storage_path('app/public/' . $result->result_file));
        }
    }
};
?>

<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <flux:heading size="xl" class="text-zinc-800 dark:text-white">Galeri & Unduhan</flux:heading>
        <flux:subheading size="lg" class="text-zinc-600 dark:text-zinc-400">Pilih hasil photobox favoritmu dan simpan langsung ke perangkat.</flux:subheading>
    </div>
    
    <flux:separator variant="subtle" />

    @if(count($this->MyGallery) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($this->MyGallery as $item)
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm flex flex-col hover:shadow-md transition-shadow">
                    
                    <div class="bg-zinc-100 dark:bg-zinc-800 flex justify-center items-center p-4 h-72">
                        @if($item->result_file)
                            <img src="{{ asset('storage/' . $item->result_file) }}" alt="Photobox Result" class="max-h-full w-auto object-contain drop-shadow-lg">
                        @else
                            <span class="text-zinc-400">Gambar Hilang</span>
                        @endif
                    </div>
                    
                    <div class="p-4 flex flex-col gap-3">
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            Digenerate: {{ $item->created_at->format('d M Y, H:i') }}
                        </div>
                        
                        <flux:button variant="primary" icon="arrow-down-tray" class="w-full font-semibold" wire:click="downloadImage({{ $item->id }})">
                            Unduh Gambar
                        </flux:button>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700">
            <svg class="w-16 h-16 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <flux:heading size="lg">Galeri Masih Kosong</flux:heading>
            <flux:subheading class="mt-2">Ayo buat karya *photobox* pertamamu di menu Photobox Results!</flux:subheading>
        </div>
    @endif
</div>