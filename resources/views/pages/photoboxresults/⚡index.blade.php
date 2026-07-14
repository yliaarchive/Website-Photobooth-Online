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

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-zinc-800 dark:text-white tracking-tight">
                📸 Photobox Results
            </h1>
            <p class="text-zinc-500 mt-2">
                Kelola seluruh hasil photobox yang telah dibuat pengguna.
            </p>
        </div>

        <flux:modal.trigger name="create-photobox">
            <flux:button
                icon="sparkles"
                class="rounded-full bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 shadow-lg shadow-pink-200">
                Buat Photobox
            </flux:button>
        </flux:modal.trigger>
    </div>

    <flux:separator variant="subtle"/>

    <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">

        <div class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-200">
                        Hasil Photobox
                    </h2>
                    <p class="text-sm text-zinc-500">
                        Total : {{ $this->Results->total() }}
                    </p>
                </div>

                <div class="w-72">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari user atau frame..."
                        class="w-full rounded-full border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-4 py-2 focus:border-pink-500 focus:ring-2 focus:ring-pink-300 outline-none shadow-sm">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <flux:table :paginate="$this->Results">
                <flux:table.columns>
                    <flux:table.column>User</flux:table.column>
                    <flux:table.column>Frame</flux:table.column>
                    <flux:table.column>Created</flux:table.column>
                    <flux:table.column>Action</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($this->Results as $result)
                    <flux:table.row :key="$result->id" class="hover:bg-pink-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                        
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($result->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-800 dark:text-zinc-200">
                                        {{ $result->user->name ?? '-' }}
                                    </div>
                                    <div class="text-xs text-zinc-400">User</div>
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-pink-50 text-pink-600 text-xs font-semibold border border-pink-100">
                                {{ $result->frame->nama_frame ?? 'Unknown' }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="text-zinc-500 dark:text-zinc-400 text-sm">
                                {{ $result->created_at->diffForHumans() }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                <flux:menu>
                                    <flux:menu.item variant="danger" icon="trash" 
                                        wire:click="$dispatch('confirm-delete',{ id: {{ $result->id }} })" 
                                        wire:confirm="Yakin ingin menghapus?">
                                        Delete
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

    <flux:modal name="preview-modal" class="md:w-[700px]">
        <div class="p-2">
            @if($previewUrl)
                <img src="{{ $previewUrl }}" class="rounded-2xl shadow-xl w-full">
            @else
                <div class="text-center text-zinc-400 py-10">Tidak ada gambar.</div>
            @endif
        </div>
    </flux:modal>
</div>