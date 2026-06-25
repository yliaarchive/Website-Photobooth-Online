<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\PhotoboxResults;
use App\Models\PhotoFrames;
use App\Livewire\Forms\PhotoboxResultsForm;
use Illuminate\Support\Facades\Storage;
use Flux\Flux;

new class extends Component
{
    use WithPagination, WithFileUploads;

    public PhotoboxResultsForm $form;
    
    // Variabel baru untuk menyimpan URL gambar yang sedang di-preview
    public ?string $previewUrl = null;

    #[Computed]
    public function Results()
    {
        return PhotoboxResults::with(['user', 'frame'])->latest()->paginate(10);
    }

    #[Computed]
    public function AvailableFrames()
    {
        return PhotoFrames::all();
    }

    public function create()
    {
        $this->form->reset();
    }

    public function generate()
    {
        $this->form->store();
        Flux::modal('create-photobox')->close();
    }

    // Method baru untuk membuka modal preview besar
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
            if ($result->result_file && Storage::disk('public')->exists($result->result_file)) {
                Storage::disk('public')->delete($result->result_file);
            }
            $result->delete();
        }
    }
};
?>

<div class="max-w-7xl mx-auto space-y-4">
    <flux:heading size="xl" class="text-zinc-800 dark:text-white">Photobox Previews</flux:heading>
    <flux:subheading size="lg" class="text-zinc-600 dark:text-zinc-400">Generate your photobooth strip directly from your device.</flux:subheading>
    <flux:separator variant="subtle" />

    <flux:modal.trigger name="create-photobox">
        <flux:button variant="primary" icon="sparkles" color="primary" wire:click="create">Generate Photobox</flux:button>
    </flux:modal.trigger>

    <div class="overflow-x-auto">
       <flux:table :paginate="$this->Results">
            <flux:table.columns>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Frame Used</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->Results as $result)
                    <flux:table.row :key="$result->id">
                        
                        <flux:table.cell class="font-medium">
                            {{ $result->user->name ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            {{ $result->frame->nama_frame ?? 'Unknown Frame' }}
                        </flux:table.cell>

                        <flux:table.cell class="text-zinc-500 dark:text-zinc-400">
                            @if($result->result_file)
                                <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                    Ready
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-md text-xs font-medium bg-red-100 text-red-800">
                                    Error
                                </span>
                            @endif
                        </flux:table.cell>

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
   
    <flux:modal name="create-photobox" class="md:w-[500px]">
        <form wire:submit="generate" class="space-y-6">
            <div>
                <flux:heading size="lg">Generate New Photobox</flux:heading>
                <flux:subheading class="mt-2">Upload 1 to 6 photos from your device.</flux:subheading>
            </div>

            <flux:select wire:model="form.frame_id" label="1. Select Template Frame">
                <option value="">-- Choose a frame --</option>
                @foreach($this->AvailableFrames as $frame)
                    <option value="{{ $frame->id }}">{{ $frame->nama_frame }}</option>
                @endforeach
            </flux:select>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">2. Upload Photos (Max 6)</label>
                <input type="file" wire:model="form.photos" multiple accept="image/*" class="block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-full file:border-0
                  file:text-sm file:font-semibold
                  file:bg-[#f472b6] file:text-white
                  hover:file:bg-[#db60a0] transition-colors" />
                <p class="text-xs text-gray-400 mt-1">You can select multiple photos at once. They will be stacked automatically.</p>
                
                @error('form.photos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                @error('form.photos.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div wire:loading wire:target="form.photos" class="text-sm text-blue-500 mb-2">Uploading files...</div>
            <div wire:loading wire:target="generate" class="text-sm text-pink-500 font-semibold flex items-center gap-2">
                Processing your photobox strip... Please wait.
            </div>

            <div class="flex gap-2 pt-4">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" icon="sparkles">Create Magic</flux:button>
            </div>
        </form>
    </flux:modal>

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