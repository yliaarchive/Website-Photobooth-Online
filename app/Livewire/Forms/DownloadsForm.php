<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Downloads;
use Illuminate\Support\Facades\Auth;

class DownloadsForm extends Form
{
    public function catatDownload($resultId)
    {
        Downloads::create([
            'user_id' => Auth::id(),
            'result_id' => $resultId,
            'download_date' => now(), 
        ]);
    }
}