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

    public ?string $previewUrl = null;

    #[Computed]
    public function Results()
    {
        return PhotoboxResults::with(['user', 'frame'])->latest()->paginate(10);
    }

    #[On('refresh-results')]
    public function refresh()
    {
        // Fungsi ini kosong, hanya bertugas me-refresh tabel secara otomatis
    }

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

<div class="max-w-7xl mx-auto space-y-4">
    <flux:heading size="xl" class="text-zinc-800 dark:text-white">Photobox Editor</flux:heading>
    <flux:subheading size="lg" class="text-zinc-600 dark:text-zinc-400">Pilih frame, geser foto kamu, dan sesuaikan posisinya!</flux:subheading>
    <flux:separator variant="subtle" />

    <flux:modal.trigger name="create-photobox">
        <flux:button variant="primary" icon="sparkles" color="primary">Buatlah Photoboxmu sendiri</flux:button>
    </flux:modal.trigger>

    <div class="overflow-x-auto">
       <flux:table :paginate="$this->Results">
            <flux:table.columns>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Frame Used</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->Results as $result)
                    <flux:table.row :key="$result->id">
                        <flux:table.cell class="font-medium">{{ $result->user->name ?? '-' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">{{ $result->frame->nama_frame ?? 'Unknown Frame' }}</flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">{{ $result->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                                <flux:menu>
                                    <flux:menu.item icon="eye" wire:click="showPreview('{{ $result->result_file }}')">Preview Image</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('confirm-delete', { id: {{ $result->id }} })" wire:confirm="Delete this result?">Delete</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
   
    @livewire('edit')

    <flux:modal name="preview-modal" class="md:w-[600px] p-4">
        <div class="w-full flex justify-center bg-gray-50 rounded-lg p-2 border border-gray-200">
            @if($previewUrl)
                <img src="{{ $previewUrl }}" alt="Photobox Preview" class="max-w-full max-h-[75vh] object-contain shadow-sm rounded">
            @else
                <span class="text-gray-400">No image selected</span>
            @endif
        </div>
    </flux:modal>
</div>