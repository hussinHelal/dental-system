<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientPictureHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $doctor;
    private User $receptionist;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => User::ROLE_DOCTOR, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => User::ROLE_RECEPTIONIST, 'guard_name' => 'web']);

        $this->doctor = User::factory()->create();
        $this->doctor->assignRole(User::ROLE_DOCTOR);

        $this->receptionist = User::factory()->create();
        $this->receptionist->assignRole(User::ROLE_RECEPTIONIST);
    }

    public function test_receptionist_cannot_create_a_doctor(): void
    {
        $response = $this->actingAs($this->receptionist)->post('/doctors', [
            'name' => 'Dr. Blocked',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('doctors', ['name' => 'Dr. Blocked']);
    }

    public function test_doctor_can_create_a_doctor(): void
    {
        $response = $this->actingAs($this->doctor)->post('/doctors', [
            'name' => 'Dr. Allowed',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('doctors', ['name' => 'Dr. Allowed']);
    }

    public function test_receptionist_cannot_delete_a_patient(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->actingAs($this->receptionist)->delete("/patients/{$patient->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('patients', ['id' => $patient->id]);
    }

    public function test_receptionist_can_create_a_patient(): void
    {
        $response = $this->actingAs($this->receptionist)->post('/patients', [
            'full_name' => 'New Patient',
            'phone' => '01000000000',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('patients', ['full_name' => 'New Patient']);
    }

    public function test_receptionist_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->receptionist)->get('/users');

        $response->assertForbidden();
    }

    public function test_receptionist_cannot_trigger_a_backup(): void
    {
        $response = $this->actingAs($this->receptionist)->post('/backups', [
            'type' => 'pdf',
        ]);

        $response->assertForbidden();
    }

    public function test_receptionist_can_view_backup_history(): void
    {
        $response = $this->actingAs($this->receptionist)->get('/backups');

        $response->assertOk();
    }

    public function test_doctor_can_replace_a_patient_picture_with_a_new_upload(): void
    {
        Storage::fake('public');

        $patient = Patient::factory()->create();
        $oldFile = UploadedFile::fake()->image('old-card.jpg', 200, 200);
        $oldPath = $oldFile->storeAs("patients/{$patient->id}/pictures", 'old-card.jpg', 'public');

        $picture = PatientPictureHistory::create([
            'patient_id' => $patient->id,
            'picture_type' => 'patient_card',
            'picture_path' => $oldPath,
            'notes' => 'Old note',
            'uploaded_by' => $this->doctor->id,
        ]);

        $newFile = UploadedFile::fake()->image('new-card.jpg', 400, 300);

        $response = $this->actingAs($this->doctor)->put("/patient-pictures/{$picture->id}", [
            'notes' => 'Updated note',
            'picture' => $newFile,
        ]);

        $response->assertSessionHasNoErrors();
        $picture->refresh();
        $this->assertNotSame($oldPath, $picture->picture_path);
        $this->assertSame('Updated note', $picture->notes);
        $this->assertTrue(Storage::disk('public')->exists($picture->picture_path));
        $this->assertFalse(Storage::disk('public')->exists($oldPath));
    }

    public function test_receptionist_cannot_edit_inventory_thresholds_via_full_update_route(): void
    {
        $item = \App\Models\InventoryItem::factory()->create(['low_stock_threshold' => 5]);

        $response = $this->actingAs($this->receptionist)->put("/inventory/{$item->id}", [
            'name' => $item->name,
            'quantity' => 10,
            'unit' => $item->unit,
            'low_stock_threshold' => 999,
        ]);

        $response->assertForbidden();
    }
}
