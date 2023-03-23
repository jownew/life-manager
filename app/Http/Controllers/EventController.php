<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Currency;
use App\Models\Event;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Events/Index', [
            'items' => Event::orderBy('event_date')->paginate(),
            'currencies' => Currency::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEventRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\App\Models\Event
     */
    public function store(StoreEventRequest $request)
    {
        $request
            ->validate(
                [
                    'title' => 'required',
                    'description' => 'required',
                    'event_date' => 'required',
                    'status' => 'required',
                    'intervals' => 'required|integer',
                    'reminder' => 'required|integer',
                ],
                [
                    'intervals' => 'A valid interval in months is required.'
                ]
            );

        $user = $request->user();

        $event = new Event($request->all());
        $event->user_id = $user->id;
        $event->save();
        if (request()->wantsJson()) {
            return $event;
        }

        return redirect()->back()->with('message', "Event $event->title added successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::find($id);

        if (request()->wantsJson()) {
            return $event;
        }

        return Inertia::render('Events/Show', [
            'item' => $event
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEventRequest  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateEventRequest $request, string $id)
    {
        $request->validate([
            'title' => ['required'],
            'description' => ['required'],
            'event_date' => ['required'],
        ]);

        $event = Event::findOrFail($id);
        $event->update($request->all());

        if ($request->next) {
            if ($this->createNext($event)) {
                return redirect()->back()->with('message', "Next event created.");
            } else {
                return redirect()->back()->with('message', "There was an error creating the next event.");
            }
        }

        return redirect()->back()->with('message', "Event $event->title updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::find($id);

        if ($event->delete()) {
            return redirect()->back()->with('message', "$event->title deleted successfully");
        }

        return redirect()->back()->with('message', "$event->title delete failed due an error.");
    }
}
