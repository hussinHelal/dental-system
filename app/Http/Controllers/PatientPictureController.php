<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RespondsToModals;
use App\Models\Patient;
use App\Models\PatientPictureHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientPictureController extends Controller
{
    use RespondsToModals;

    /**
     * Upload a picture for a patient
     * 
     * Picture types: 'xray', 'patient_card'
     * Crown color is text-based only, not a picture
     */
    public function uploadPicture(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $validated = $request->validate([
            'picture_type' => 'required|in:xray,patient_card',
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $path = $request->file('picture')->store(
                "patients/{$patient->id}/pictures",
                'public'
            );

            PatientPictureHistory::create([
                'patient_id' => $patient->id,
                'picture_type' => $validated['picture_type'],
                'picture_path' => $path,
                'notes' => $validated['notes'] ?? null,
                'uploaded_by' => $request->user()->id,
            ]);

            return $this->respondSuccess(
                $request,
                __('messages.picture_uploaded'),
                'patients.show',
                ['patient' => $patient]
            );
        } catch (\Exception $e) {
            // Clean up uploaded file if database operation fails
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        }
    }

    /**
     * Get picture gallery for a specific type (paginated)
     * Valid types: 'xray', 'patient_card'
     */
    public function gallery(Request $request, Patient $patient, $pictureType)
    {
        $this->authorize('view', $patient);

        // Validate picture type
        $validated = $request->validate([
            'picture_type' => 'in:xray,patient_card',
        ], [
            'picture_type.in' => 'Invalid picture type specified.',
        ]);

        // Validate against URL parameter as well
        if (!in_array($pictureType, ['xray', 'patient_card'])) {
            abort(404, 'Picture type not found');
        }

        $pictures = $patient->pictureHistory()
            ->ofType($pictureType)
            ->paginate(12);

        return view('patients.partials.picture-gallery', [
            'patient' => $patient,
            'pictures' => $pictures,
            'pictureType' => $pictureType,
        ]);
    }

    /**
     * Update the notes attached to a patient picture
     */
    public function updatePicture(Request $request, PatientPictureHistory $picture)
    {
        $this->authorize('update', $picture->patient);

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $data = [
            'notes' => $validated['notes'] ?? null,
        ];

        if ($request->hasFile('picture')) {
            $oldPath = $picture->picture_path;
            $newPath = $request->file('picture')->store("patients/{$picture->patient_id}/pictures", 'public');

            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $data['picture_path'] = $newPath;
        }

        $picture->update($data);

        return $this->respondSuccess(
            $request,
            __('messages.picture_updated'),
            'patients.show',
            ['patient' => $picture->patient]
        );
    }

    /**
     * Delete a picture from history
     * Includes automatic file cleanup from storage
     */
    public function deletePicture(Request $request, PatientPictureHistory $picture)
    {
        $this->authorize('update', $picture->patient);

        $patient = $picture->patient;
        
        try {
            // Delete from storage first, then database
            // If file deletion fails, the database record is still removed to maintain consistency
            $picture->deletePictureFile();
        } catch (\Exception $e) {
            // Log file deletion error but continue with database deletion
            \Log::warning("Failed to delete picture file: {$picture->picture_path}", ['error' => $e->getMessage()]);
        }
        
        $picture->delete();

        return $this->respondSuccess(
            $request,
            __('messages.picture_deleted'),
            'patients.show',
            ['patient' => $patient]
        );
    }
}
