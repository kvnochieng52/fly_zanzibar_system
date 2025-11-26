<?php

namespace App\Jobs;

use App\Models\Staff;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProcessProfilePhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Staff $staff,
        public string $tempPhotoPath,
        public string $originalName
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Initialize Image Manager
            $manager = new ImageManager(new Driver());

            // Read the temporary file
            $image = $manager->read(Storage::path($this->tempPhotoPath));

            // Resize and optimize the image
            $image->scale(300, 300); // Resize to 300x300 max
            $image->toJpeg(85); // Convert to JPEG with 85% quality

            // Generate filename
            $filename = 'profile_photos/' . $this->staff->staff_number . '_' . time() . '.jpg';

            // Save the processed image
            Storage::put($filename, $image->encode());

            // Update staff record with the final photo path
            $this->staff->update([
                'profile_photo' => $filename,
                'photo_processing_status' => 'completed'
            ]);

            // Delete the temporary file
            Storage::delete($this->tempPhotoPath);

        } catch (\Exception $e) {
            // Update status to failed
            $this->staff->update(['photo_processing_status' => 'failed']);

            // Clean up temporary file
            Storage::delete($this->tempPhotoPath);

            // Re-throw the exception to mark job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Update staff record to indicate processing failed
        $this->staff->update(['photo_processing_status' => 'failed']);

        // Clean up temporary file
        Storage::delete($this->tempPhotoPath);
    }
}
