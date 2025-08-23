<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use App\Repositories\UserRepository;
use App\Models\Patient;

class UpdateUserProfileTest
{
    public function __invoke($_, array $args, GraphQLContext $context)
    {
        

        try {
           // Get user by ID from input instead of authentication
           if (!isset($args['input']['user_id'])) {
               throw new \Exception('User ID is required');
           }
           
           $user = User::find($args['input']['user_id']);
           if (!$user) {
               throw new \Exception('User not found');
           }
           $userAttributes = $user->toArray();
           // print_r($userAttributes);
           
            $input = $args['input'];
            
            // Handle field name mapping for backward compatibility
            // Map 'phone' to 'contact' and 'birth_date' to 'dob'
            if (isset($input['phone'])) {
                $input['contact'] = $input['phone'];
                unset($input['phone']);
            }
            
            if (isset($input['birth_date'])) {
                // Convert date from DD.MM.YYYY to YYYY-MM-DD format
                $input['dob'] = $this->convertDateFormat($input['birth_date']);
                unset($input['birth_date']);
            }
            
            // Also handle direct dob field conversion if needed
            if (isset($input['dob']) && preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $input['dob'])) {
                $input['dob'] = $this->convertDateFormat($input['dob']);
            }

            // Update basic info
            $updateData = collect($input)->only([
                'first_name',
                'last_name',
                'email',
                'contact',
                'dob',
                'gender',
                'language',
                'blood_group',
                'region_code',
                'time_zone',
                'dark_mode',
                'email_notification',
                'snils'
            ])->filter()->toArray();

            Log::debug(request()->allFiles());
            // Handle profile image upload if provided
            if (request()->hasFile('profile_image')) {
                Log::info('Profile image found in request files');
                $file = request()->file('profile_image');
                
                try {
                    if ($user->type === User::PATIENT) {
                        // Clear old media for patient
                        $user->patient->clearMediaCollection(Patient::PROFILE);
                        // Add the file to media library for patient
                        $media = $user->patient->addMedia($file)
                            ->toMediaCollection(Patient::PROFILE, config('app.media_disc'));
                    } else {
                        // Clear old media for other users
                        $user->clearMediaCollection(User::PROFILE);
                        // Add the file to media library for other users
                        $media = $user->addMedia($file)
                            ->toMediaCollection(User::PROFILE, config('app.media_disc'));
                    }

                    Log::info('Profile image uploaded', [
                        'media_id' => $media->id,
                        'url' => $media->getFullUrl(),
                        'path' => $media->getPath()
                    ]);

                    // Force media reload
                    $user->load('media');
                    $user->unsetRelation('media');
                    
                    

                } catch (\Exception $e) {
                    Log::error('Error uploading profile image: ' . $e->getMessage());
                    Log::error('Stack trace: ' . $e->getTraceAsString());
                    throw new \Exception('Failed to upload profile image: ' . $e->getMessage());
                }
            } else {
                Log::warning('No profile image in request', [
                    'files' => request()->allFiles(),
                    'has_file' => request()->hasFile('0'),
                    'input' => $input
                ]);
            }

            // Update user
            $user->update($updateData);
            Log::info('User data updated');

            // Fresh user with media
            $user = $user->fresh(['media']);

            return [
                'user' => $user,
                'status' => 'SUCCESS',
                'role'=>$userAttributes['role_name'],
                'token'=>request()->bearerToken(),
               // 'patient_id'=>$user->patient->id ?? '-1',
               // 'doctorId'=>$user->doctor->id
            ];

        } catch (\Exception $e) {
            Log::error('Error updating user profile: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw new \Exception('Failed to update user profile: ' . $e->getMessage());
        }
    }
    
    /**
     * Convert date from DD.MM.YYYY to YYYY-MM-DD format
     *
     * @param string $date
     * @return string
     */
    private function convertDateFormat($date)
    {
        if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $date, $matches)) {
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }
        
        return $date;
    }
}
