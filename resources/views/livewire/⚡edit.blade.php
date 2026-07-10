<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Models\PhotoFrames;
use App\Livewire\Forms\PhotoboxResultsForm;
use Flux\Flux;

new class extends Component
{
    use WithFileUploads;

    public PhotoboxResultsForm $form;

    #[Computed]
    public function AvailableFrames()
    {
        return PhotoFrames::all();
    }

    #[On('save-final-image')]
    public function saveFinalImage($base64Image, $frameId)
    {
        $this->form->final_image_base64 = $base64Image;
        $this->form->frame_id = $frameId;

        $this->form->store();
        $this->dispatch('refresh-results');
        Flux::modal('create-photobox')->close();
    }
};
?>

<div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" rel="stylesheet">
    <flux:modal name="create-photobox" class="md:w-[700px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Canvas Editor</flux:heading>
                <flux:subheading class="mt-2 text-xs">Pilih frame, tambah foto, lalu geser/putar foto agar pas dengan bolongan frame.</flux:subheading>
            </div>

            <div wire:ignore>
                <label class="block text-sm font-medium text-gray-700 mb-1">1. Pilih Template Frame</label>
                <select id="frameSelect" class="w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">-- Pilih Frame --</option>
                    @foreach($this->AvailableFrames as $frame)
                        <option value="{{ $frame->id }}" data-url="{{ asset('storage/' . $frame->gambar_frame) }}">
                            {{ $frame->nama_frame }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div wire:ignore class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">2. Tambahkan Foto</label>
                <input type="file" id="photoInput" wire:model="form.photos" multiple accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#f472b6] file:text-white hover:file:bg-[#db60a0] transition-colors" />
                <div wire:loading wire:target="form.photos" class="text-xs text-blue-500 mt-1">Mengunggah foto ke server...</div>
            </div>

            <div wire:ignore class="flex justify-center bg-gray-100 rounded border overflow-hidden relative" style="height: 500px;">
                <canvas id="photoboothCanvas"></canvas>
                <div id="cropStage" class="absolute inset-0 flex-col" style="z-index:10;display:none">
                    <div class="flex-1 overflow-hidden flex items-center justify-center w-full">
                        <img id="cropImage" src="" alt="Crop" style="max-width:100%;max-height:100%">
                    </div>
                    <div class="flex justify-center gap-2 py-2 bg-white border-t">
                        <button type="button" onclick="applyCrop()" class="px-4 py-2 text-sm font-semibold rounded-full bg-[#f472b6] text-white hover:bg-[#db60a0] transition-colors">Apply Crop</button>
                        <button type="button" onclick="closeCropModal()" class="px-4 py-2 text-sm font-semibold rounded-full border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                    </div>
                </div>
            </div>

            <div class="flex gap-2 pt-4">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <button type="button" id="cropBtn" onclick="openCropModal()" variant="primary" icon="crop" class="hidden px-4 py-2 text-sm font-semibold rounded-full transition-colors shrink-0">
                    Crop Image
                </button>
                <flux:button type="button" variant="primary" icon="sparkles" onclick="generateMagic()" wire:loading.attr="disabled" wire:target="form.photos">
                    Generate Magic
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script>
        let cropper = null;
        let croppingImage = null;

        function initCanvas() {
            if (typeof fabric !== 'undefined' && !window.canvas) {
                let canvasEl = document.getElementById('photoboothCanvas');
                if (canvasEl) {
                    window.canvas = new fabric.Canvas('photoboothCanvas', {
                        width: 350,
                        height: 500,
                        backgroundColor: '#ffffff'
                    });
                    attachCropEvents();
                }
            }
        }

        function attachCropEvents() {
            window.canvas.on('selection:created', function(e) {
                if (e.selected && e.selected.length === 1 && e.selected[0].type === 'image') {
                    document.getElementById('cropBtn').classList.remove('hidden');
                }
            });
            window.canvas.on('selection:updated', function(e) {
                if (e.selected && e.selected.length === 1 && e.selected[0].type === 'image') {
                    document.getElementById('cropBtn').classList.remove('hidden');
                } else {
                    document.getElementById('cropBtn').classList.add('hidden');
                }
            });
            window.canvas.on('selection:cleared', function() {
                document.getElementById('cropBtn').classList.add('hidden');
            });
        }

        document.getElementById('frameSelect').addEventListener('change', function(e) {
            initCanvas();

            let selectedOption = this.options[this.selectedIndex];
            let frameUrl = selectedOption.getAttribute('data-url');

            if (frameUrl && window.canvas) {
                fabric.Image.fromURL(frameUrl, function(img) {
                    let scale = Math.min(window.canvas.width / img.width, window.canvas.height / img.height);
                    img.scale(scale);
                    window.canvas.setOverlayImage(img, window.canvas.renderAll.bind(window.canvas), {
                        originX: 'center',
                        originY: 'center',
                        left: window.canvas.width / 2,
                        top: window.canvas.height / 2
                    });
                }, { crossOrigin: 'anonymous' });
            } else if (window.canvas) {
                window.canvas.setOverlayImage(null, window.canvas.renderAll.bind(window.canvas));
            }
        });

        document.getElementById('photoInput').addEventListener('change', function(e) {
            initCanvas();

            let files = e.target.files;
            if (!window.canvas) return;

            for (let i = 0; i < files.length; i++) {
                let reader = new FileReader();
                reader.onload = function(f) {
                    let data = f.target.result;
                    fabric.Image.fromURL(data, function(img) {
                        img.scaleToWidth(150);
                        img.set({ left: 100, top: 100 });
                        window.canvas.add(img);
                        window.canvas.sendToBack(img);
                        window.canvas.setActiveObject(img);
                    });
                };
                reader.readAsDataURL(files[i]);
            }
        });

        function openCropModal() {
            let activeObj = window.canvas.getActiveObject();
            if (!activeObj || activeObj.type !== 'image') return;

            croppingImage = activeObj;
            let imgEl = document.getElementById('cropImage');
            let stage = document.getElementById('cropStage');

            stage.style.display = 'flex';

            imgEl.onload = function() {
                if (cropper) cropper.destroy();
                cropper = new Cropper(imgEl, {
                    aspectRatio: NaN,
                    viewMode: 1,
                    autoCropArea: 0.8,
                    responsive: true,
                    movable: true,
                    zoomable: true,
                    rotatable: false,
                    scalable: false,
                });
            };
            imgEl.onerror = function() {
                stage.style.display = 'none';
                alert('Gagal memuat gambar untuk crop.');
            };
            imgEl.src = activeObj.getSrc();
        }

        function applyCrop() {
            if (!cropper || !croppingImage) return;

            let croppedCanvas = cropper.getCroppedCanvas({
                maxWidth: 2048,
                maxHeight: 2048,
            });

            let croppedDataUrl = croppedCanvas.toDataURL('image/jpeg', 0.95);

            croppingImage.setSrc(croppedDataUrl, function() {
                window.canvas.renderAll();
            });

            closeCropModal();
        }

        function closeCropModal() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            document.getElementById('cropStage').style.display = 'none';
            croppingImage = null;
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                let stage = document.getElementById('cropStage');
                if (stage && stage.style.display !== 'none') closeCropModal();
            }
        });

        function generateMagic() {
            let frameId = document.getElementById('frameSelect').value;
            if (!frameId) {
                alert('Mohon pilih frame terlebih dahulu!');
                return;
            }

            if (window.canvas) {
                window.canvas.discardActiveObject();
                window.canvas.renderAll();

                let finalImageBase64 = window.canvas.toDataURL({
                    format: 'jpeg',
                    quality: 0.95,
                    multiplier: 3
                });

                Livewire.dispatch('save-final-image', { base64Image: finalImageBase64, frameId: frameId });
            }
        }
    </script>
</div>
