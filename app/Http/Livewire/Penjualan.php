<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Penjualan extends Component
{
    public $name;

    public $email;

    public $body;






    public function submit()

    {

        $validatedData = $this->validate([

            'name' => 'required|min:6',

            'email' => 'required|email',

            'body' => 'required',

        ]);
        Penjualan::create($validatedData);
        return redirect()->to('/form');

    }

    public function render()
    {
        return view('livewire.penjualan');
    }
}
