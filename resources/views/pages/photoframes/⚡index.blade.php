<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\PhotoFrames;
use App\Models\FrameCategories;
use App\Livewire\Forms\PhotoFrameForm;
use Illuminate\Support\Facades\Storage;
use Flux\Flux;

new class extends Component
{
    use WithPagination, WithFileUploads;

    public PhotoFrameForm $form;
    public bool $isEdit = false;

    #[Computed]
    public function PhotoFrames()
    {
        return PhotoFrames::with('frameCategories')->latest()->paginate(10);
    }

    #[Computed]
    public function Categories()
    {
        return FrameCategories::all();
    }

    public function create()
    {
        $this->isEdit = false;
        $this->form->reset();
    }

    public function save()
    {
        if ($this->isEdit) {
            $this->form->update();
        } else {
            $this->form->store();
        }

        Flux::modal('create-photoframe')->close();
    }

    public function edit(PhotoFrames $photoframe)
    {
        $this->isEdit = true;
        $this->form->setPhotoFrame($photoframe);
        Flux::modal('create-photoframe')->show();
    }

    #[On('confirm-delete')]
    public function delete($id)
    {
        $frame = PhotoFrames::find($id);
        if ($frame) {
            if ($frame->gambar_frame && Storage::disk('public')->exists($frame->gambar_frame)) {
                Storage::disk('public')->delete($frame->gambar_frame);
            }
            $frame->delete();
        }
    }
};
?>

<div class="max-w-7xl mx-auto space-y-8 p-4 md:p-6">
    
    <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 md:p-8 shadow-sm border border-pink-100 dark:border-zinc-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 to-rose-400">
                📷 Photo Frames
            </h1>
            <p class="text-zinc-500 dark:text-zinc-400 font-medium text-lg">
                Upload and manage your cute photobooth templates ✨
            </p>
        </div>

        <flux:modal.trigger name="create-photoframe">
            <flux:button
                wire:click="create"
                icon="plus"
                class="rounded-full bg-gradient-to-r from-pink-400 to-fuchsia-400 hover:from-pink-500 hover:to-fuchsia-500 text-white shadow-lg shadow-pink-200 dark:shadow-none px-8 py-3 transition-all duration-300 hover:scale-105 font-bold border-none">
                Add Photo Frame
            </flux:button>
        </flux:modal.trigger>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm border border-pink-50 dark:border-zinc-800 overflow-hidden p-2">
        <div class="overflow-x-auto">
            <flux:table :paginate="$this->PhotoFrames">
                <flux:table.columns>
                    <flux:table.column>Nama Frame</flux:table.column>
                    <flux:table.column>Tema</flux:table.column>
                    <flux:table.column>Kategori</flux:table.column>
                    <flux:table.column>Gambar Frame</flux:table.column>
                    <flux:table.column>Created At</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->PhotoFrames as $photoframe)
                        <flux:table.row :key="$photoframe->id" class="hover:bg-pink-50/50 dark:hover:bg-zinc-800/50 transition-colors duration-200">
                            
                            <flux:table.cell class="font-bold text-zinc-800 dark:text-zinc-200 text-base">
                                {{ $photoframe->nama_frame }}
                            </flux:table.cell>

                            <flux:table.cell>
                                @if($photoframe->tema)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">
                                        {{ $photoframe->tema }}
                                    </span>
                                @else
                                    <span class="text-zinc-400 italic text-sm">-</span>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                @php
                                    $kategori = $photoframe->frameCategories->name ?? $photoframe->frameCategories->nama_kategori ?? null;
                                @endphp
                                @if($kategori)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-400 border border-fuchsia-200 dark:border-fuchsia-800">
                                        {{ $kategori }}
                                    </span>
                                @else
                                    <span class="text-zinc-400 italic text-sm">-</span>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="bg-slate-50 dark:bg-zinc-800 rounded-xl p-2 inline-block border border-slate-100 dark:border-zinc-700 relative group">
                                    @if($photoframe->gambar_frame)
                                        <img src="{{ asset('storage/' . $photoframe->gambar_frame) }}" alt="Frame" 
                                             class="h-20 w-auto object-contain drop-shadow-md transition-transform duration-300 group-hover:scale-110">
                                    @else
                                        <div class="h-20 w-20 flex items-center justify-center text-zinc-400 text-xs italic">No Image</div>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap text-zinc-500 text-sm">
                                {{ $photoframe->created_at->diffForHumans() }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" class="hover:bg-pink-100 dark:hover:bg-zinc-700 rounded-full"></flux:button>
                                    <flux:menu>
                                        <flux:menu.item icon="pencil" wire:click="edit({{ $photoframe->id }})">Edit</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item variant="danger" icon="trash" wire:click="$dispatch('confirm-delete', { id: {{ $photoframe->id }} })" wire:confirm="Delete this lovely frame?">Delete</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
   
    <flux:modal name="create-photoframe" class="md:w-[28rem]">
        <form wire:submit="save" class="space-y-6">
            <div class="text-center space-y-2 mb-6">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-pink-100 dark:bg-zinc-800 mb-2">
                    <span class="text-2xl">✨</span>
                </div>
                <flux:heading size="xl">{{ $isEdit ? 'Edit Photo Frame' : 'Add New Frame' }}</flux:heading>
                <flux:subheading>Upload your template in PNG format.</flux:subheading>
            </div>

            <div class="space-y-5">
                <flux:input wire:model="form.nama_frame" label="Nama Frame" placeholder="Contoh: Summer Vibes" />
                <flux:input wire:model="form.tema" label="Tema (Opsional)" placeholder="Contoh: Holiday Addict" />
                
                <flux:select wire:model="form.category_id" label="Kategori Frame">
                    <option value="">Pilih Kategori...</option>
                    @foreach($this->Categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name ?? $category->nama_kategori }}</option>
                    @endforeach
                </flux:select>

                <div class="space-y-2">
                    <flux:input type="file" wire:model="form.gambar_frame" label="Upload Template (.png)" accept="image/png" />
                    <div wire:loading wire:target="form.gambar_frame" class="text-sm text-pink-500 font-medium flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Uploading...
                    </div>
                </div>

                @if ($form->gambar_frame && !is_string($form->gambar_frame))
                    <div class="p-4 bg-slate-50 dark:bg-zinc-800 rounded-2xl border-2 border-dashed border-pink-200 dark:border-zinc-700 flex justify-center">
                        <img src="{{ $form->gambar_frame->temporaryUrl() }}" class="max-h-40 w-auto object-contain drop-shadow-md">
                    </div>
                @endif
            </div>

            <div class="flex gap-3 pt-4">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="rounded-full">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" class="rounded-full bg-pink-500 hover:bg-pink-600 text-white border-none">
                    Save changes
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>