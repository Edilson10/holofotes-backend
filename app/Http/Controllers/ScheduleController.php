<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @return mixed
     */
    public function index()
    {
        // if ($request->search) {
        //     return ScheduleResource::collection(Schedule::where('title', 'like', '%' . $request->search . '%')
        //         ->orderBy("id", "DESC")->paginate(2));
        // } else {
        //     return ScheduleResource::collection(Schedule::orderBy("id", "DESC")->paginate(2));
        // }

        return ScheduleResource::collection(Schedule::when(request('search'), function ($query) {
            $query->where('title', 'like', '%' . request('search') . '%');
        })->when(request('date'), function ($query) {
            $query->where('schedule_date', request('date'));
        })->orderBy("id", "DESC")->paginate(2));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreScheduleRequest $request)
    {
        $schedule = Schedule::create($request->validated());
        return new ScheduleResource($schedule);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Schedule $schedule)
    {
        return new ScheduleResource($schedule);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        $schedule->update($request->validated());
        return new ScheduleResource($schedule);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule, Request $request)
    {

        $schedule->delete();
        return response('Eliminado com sucesso', 204);
    }
}
