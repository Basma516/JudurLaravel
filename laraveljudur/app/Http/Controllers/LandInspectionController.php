<?php


namespace App\Http\Controllers;
use App\Models\Land;
use App\Models\LandInspection;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use App\Models\LandStatus;
use Illuminate\Support\Facades\Auth;


class LandInspectionController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role_id != 3) {
            return response()->json([
                'message' => 'Only volunteers can submit a land inspection report.'
            ], 403);
        }

        $volunteer = Volunteer::where('user_id', $user->id)->first();
        if (!$volunteer || $volunteer->examiner != 1) {
            return response()->json([
                'message' => 'Only examiners can submit a land inspection report.'
            ], 403);
        }

        $validated = $request->validate([
            'land_id' => 'required|exists:lands,id',
            'date' => 'required|date',
            'hygiene' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'electricity_supply' => 'required|boolean', 
            'general_condition' => 'required|string|max:255',
            'photo' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ]);

        
        $landInspection = LandInspection::create([
            'land_id' => $validated['land_id'],
            'date' => $validated['date'],
            'examiner_id' => $user->id,
            'hygiene' => $validated['hygiene'],
            'capacity' => $validated['capacity'],
            'electricity_supply' => $validated['electricity_supply'],
            'general_condition' => $validated['general_condition'],
            'photo_path' => $request->file('photo') ? $request->file('photo')->store('land_inspection_photos', 'public') : null,
        ]);

        return response()->json([
            'message' => 'Land inspection report submitted successfully.',
            'land_inspection' => $landInspection,
        ], 200);
    }


    public function getLands()
    {
        $acceptedStatus = LandStatus::where('name', 'Pending')->first();

        if (!$acceptedStatus) {
            return response()->json(['error' => 'Accepted status not found.'], 500);
        }

        $lands = Land::where('status_id', $acceptedStatus->id)->get();

        return response()->json($lands);
}
}
