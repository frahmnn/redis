<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $auth = auth()->check();
        $contact = \App\Models\ContactPerson::find(1);
        $wa_number = $contact && $contact->whatsapp_number ? preg_replace('/[^0-9]/', '', $contact->whatsapp_number) : null;
        return view('components.header', [
            "user_name" =>    $auth ? auth()->user()->identity->name : null,
            "special_role" => $auth ? auth()->user()->identity->special_role : null,
            "wa_number" => $wa_number
        ]);
    }
}
