<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ConfirmModal extends Component
{
    public $show = false;
    public $title = '¿Estás seguro?';
    public $message = 'Esta acción no se puede deshacer.';
    
    public $confirmMethod = 'confirmAction';
    public $cancelMethod = 'cancelAction';

    protected $listeners = ['openConfirmModal'];

    public function openConfirmModal($title, $message, $confirmMethod = 'confirmAction', $cancelMethod = 'cancelAction')
    {
        $this->title = $title;
        $this->message = $message;
        $this->confirmMethod = $confirmMethod;
        $this->cancelMethod = $cancelMethod;
        $this->show = true;
    }

    public function render()
    {
        return view('livewire.confirm-modal');
    }
}
