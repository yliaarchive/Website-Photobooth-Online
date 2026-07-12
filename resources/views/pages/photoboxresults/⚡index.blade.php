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
    public function updatingSearch()
{
    $this->resetPage();
}
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
        ->paginate(10);
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
<div class="max-w-7xl mx-auto space-y-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-pink-500 via-fuchsia-500 to-purple-500 bg-clip-text text-transparent">
                📸 Photobox Results
            </h1>

            <p class="text-gray-500 mt-2">
                Kelola seluruh hasil photobox yang telah dibuat pengguna.
            </p>
        </div>

        <flux:modal.trigger name="create-photobox">
            <flux:button
                icon="sparkles"
                class="rounded-full bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 shadow-lg">
                Buat Photobox
            </flux:button>
        </flux:modal.trigger>

    </div>

    <flux:separator variant="subtle"/>

    <!-- CARD -->
    <div class="bg-white rounded-3xl shadow-xl border border-pink-100 overflow-hidden">

        <div class="px-6 py-5 border-b bg-pink-50">

    <div class="flex items-center justify-between">

        <div>
            <h2 class="text-lg font-semibold text-pink-600">
                Hasil Photobox
            </h2>

            <p class="text-sm text-gray-500">
                Total : {{ $this->Results->total() }}
            </p>
        </div>

        <div class="w-72">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari user atau frame..."
                class="w-full rounded-xl border border-pink-200 px-4 py-2 focus:border-pink-500 focus:ring-2 focus:ring-pink-300 outline-none">
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

                    <flux:table.row
                        :key="$result->id"
                        class="hover:bg-pink-50 transition">

                        <!-- USER -->
                        <flux:table.cell>

                            <div class="flex items-center gap-3">

                                <div
                                    class="w-10 h-10 rounded-full bg-pink-500 text-white flex items-center justify-center font-bold">

                                    {{ strtoupper(substr($result->user->name ?? 'U',0,1)) }}

                                </div>

                                <div>

                                    <div class="font-semibold">

                                        {{ $result->user->name ?? '-' }}

                                    </div>

                                    <div class="text-xs text-gray-400">

                                        User

                                    </div>

                                </div>

                            </div>

                        </flux:table.cell>

                        <!-- FRAME -->
                        <flux:table.cell>

                            <span
                                class="px-3 py-1 rounded-full bg-pink-100 text-pink-600 text-xs font-semibold">

                                {{ $result->frame->nama_frame ?? 'Unknown' }}

                            </span>

                        </flux:table.cell>
                        

                        <!-- CREATED -->
                        <flux:table.cell>

                            <span class="text-gray-500">

                                {{ $result->created_at->diffForHumans() }}

                            </span>

                        </flux:table.cell>

                        <!-- ACTION -->
                        <flux:table.cell>

                            <flux:dropdown>

                                <flux:button
                                    variant="ghost"
                                    icon="ellipsis-horizontal">
                                </flux:button>

                                <flux:menu>

                                    <flux:menu.item
                                        variant="danger"
                                        icon="trash"
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

    <!-- MODAL -->
    <flux:modal
        name="preview-modal"
        class="md:w-[700px]">

        <div class="bg-pink-50 rounded-3xl p-5">

            @if($previewUrl)

                <img
                    src="{{ $previewUrl }}"
                    class="rounded-2xl shadow-xl max-h-[75vh] mx-auto">

            @else

                <div class="text-center text-gray-400">

                    Tidak ada gambar.

                </div>

            @endif

        </div>

    </flux:modal>

</div>