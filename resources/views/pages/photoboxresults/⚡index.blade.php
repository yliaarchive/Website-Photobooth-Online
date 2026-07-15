<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\PhotoboxResults;
use Illuminate\Support\Facades\Storage;
use Flux\Flux;

new class extends Component
{
    use WithPagination;
    public string $search = '';
    public function updatingSearch() { $this->resetPage(); }
    public ?string $previewUrl = null;

    #[Computed]
    public function Results()
    {
        return PhotoboxResults::with(['user','frame'])
            ->when($this->search, function ($query) {
                $search = $this->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($user) use ($search) {
                        $user->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('frame', function ($frame) use ($search) {
                        $frame->where('nama_frame', 'like', "%{$search}%");
                    });
                });
            })
            ->latest()
            ->paginate(12);
    }

    #[On('refresh-results')]
    public function refresh() {}

    public function showPreview($filename)
    {
        $this->previewUrl = asset('storage/' . $filename);
        Flux::modal('preview-modal')->show();  
    }

    #[On('confirm-delete')]
    public function delete($id)
    {
        $result = PhotoboxResults::find($id);
        if ($result) {
            \App\Models\Downloads::where('result_id', $result->id)->delete();
            if ($result->result_file && Storage::disk('public')->exists($result->result_file)) {
                Storage::disk('public')->delete($result->result_file);
            }
            $result->delete();
        }
    }
};
?>

<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-4xl font-extrabold text-zinc-800 dark:text-white tracking-tight flex items-center gap-2">
                📸 Photobox Results
            </h1>
            <p class="text-zinc-500 mt-2 text-sm sm:text-base">
                Kelola seluruh hasil photobox yang telah dibuat oleh pengguna.
            </p>
        </div>

        <flux:modal.trigger name="create-photobox">
            <flux:button
                icon="sparkles"
                class="rounded-full bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 shadow-lg shadow-pink-200 dark:shadow-none transition-all hover:scale-105 active:scale-95">
                Buat Photobox
            </flux:button>
        </flux:modal.trigger>
    </div>

    <flux:separator variant="subtle"/>

    <!-- Main Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-zinc-200 dark:border-zinc-700 overflow-hidden">

        <!-- Toolbar Section -->
        <div class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-700 bg-zinc-50/80 dark:bg-zinc-800/80 backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">
                        Daftar Hasil
                    </h2>
                    <p class="text-sm text-zinc-500 font-medium">
                        Total : <span class="text-pink-600 font-bold">{{ $this->Results->total() }}</span> gambar
                    </p>
                </div>

                <div class="w-full sm:w-80 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama user atau frame..."
                    class="w-full rounded-full border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-200 placeholder-zinc-400 dark:placeholder-zinc-500 pl-10 pr-4 py-2.5 text-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-200 outline-none shadow-sm transition-all">
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <flux:table :paginate="$this->Results">
                <flux:table.columns>
                    <flux:table.column>User</flux:table.column>
                    <flux:table.column>Thumbnail</flux:table.column>
                    <flux:table.column>Frame</flux:table.column>
                    <flux:table.column>Dibuat Pada</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($this->Results as $result)
                    <flux:table.row :key="$result->id" class="hover:bg-pink-50/60 dark:hover:bg-zinc-800/60 transition-colors group">
                        
                        <!-- User Column -->
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-100 to-pink-200 text-pink-700 flex items-center justify-center font-bold shadow-sm">
                                    {{ strtoupper(substr($result->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-800 dark:text-zinc-200 group-hover:text-pink-600 transition-colors">
                                        {{ $result->user->name ?? 'Unknown User' }}
                                    </div>
                                    <div class="text-xs text-zinc-400 font-medium">Pengguna</div>
                                </div>
                            </div>
                        </flux:table.cell>

                        <!-- Thumbnail Preview Column (Statis) -->
                        <flux:table.cell>
                            @if($result->result_file)
                                <div class="block w-16 h-20 rounded-xl overflow-hidden shadow-sm border border-zinc-200 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800">
                                    <img src="{{ asset('storage/' . $result->result_file) }}" alt="Result Thumbnail" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-16 h-20 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex flex-col items-center justify-center border border-dashed border-zinc-300 dark:border-zinc-600 text-zinc-400">
                                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-[10px]">No Img</span>
                                </div>
                            @endif
                        </flux:table.cell>

                        <!-- Frame Column -->
                        <flux:table.cell>
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 text-xs font-bold border border-pink-100 dark:border-pink-800/50 shadow-sm">
                                {{ $result->frame->nama_frame ?? 'Unknown' }}
                            </span>
                        </flux:table.cell>

                        <!-- Created At Column -->
                        <flux:table.cell>
                            <div class="flex items-center gap-1.5 text-zinc-500 dark:text-zinc-400 text-sm font-medium">
                                <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $result->created_at->diffForHumans() }}
                            </div>
                        </flux:table.cell>

                        <!-- Action Column -->
                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" class="hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors" />
                                <flux:menu>
                                    <flux:menu.item variant="danger" icon="trash" 
                                        wire:click="$dispatch('confirm-delete',{ id: {{ $result->id }} })" 
                                        wire:confirm="Yakin ingin menghapus hasil photobox ini?">
                                        Hapus Data
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>

                    </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </div>

    @livewire('edit')
</div>