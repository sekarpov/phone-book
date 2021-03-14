<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\UseCases\Contacts\FavoriteService;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    private $service;

    public function __construct(FavoriteService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
    }

    public function index()
    {
        $contacts = Contact::favoredByUser(Auth::user())->orderByDesc('id')->paginate(20);

        return view('cabinet.favorites.index', compact('contacts'));
    }

    public function add(Contact $contact)
    {
        try {
            $this->service->add(Auth::id(), $contact->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('cabinet.contacts.show', $contact)->with('success', 'Контакт доавлен в избранные.');
    }

    public function remove(Contact $contact)
    {
        try {
            $this->service->remove(Auth::id(), $contact->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('cabinet.contacts.show', $contact)->with('success', 'Контакт удален из избарнных.');
    }
}
