<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Department;
use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Position::with('department');
        
        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }
        
        $positions = $query->orderBy('title')->paginate(10);
        
        return view('config.positions.index', compact('positions', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        
        return view('config.positions.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePositionRequest $request)
    {
        $data = $request->validated();
        $data['is_management'] = $request->has('is_management');
        
        Position::create($data);

        return redirect()->route('config.positions.index')->with('success', 'Cargo criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position)
    {
        $position->load('department');
        return view('config.positions.show', compact('position'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position)
    {
        $departments = Department::all();
        
        return view('config.positions.edit', compact('position', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePositionRequest $request, Position $position)
    {
        $data = $request->validated();
        $data['is_management'] = $request->has('is_management');
        
        $position->update($data);

        return redirect()->route('config.positions.index')->with('success', 'Cargo atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        // Add check if position has associated employees later
        $position->delete();

        return redirect()->route('config.positions.index')->with('success', 'Cargo eliminado com sucesso.');
    }
}
