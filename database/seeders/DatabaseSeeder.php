<?php

namespace Database\Seeders;

use App\Models\Cam;
use App\Models\Person as ModelsPerson;
use App\Models\Semester;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Faker\Provider\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {



        // Settings
        Setting::query()->create([
            'warning_thresh' => 3,
            'suspension_thresh' => 4,
            'attendance_pre' => 15,
            'attendance_post' => 15,
            'attendance_present' => 15,
            'should_send_sms' => false,
            'sms_number' => ''
        ]);


        // users
        $headAdmin =  User::query()->create(['username' => 'admin', 'password' => bcrypt('admin'), 'isAdmin' => 1, 'head' => 1]);
        $nonHeadAdmin = User::query()->create(['username' => 'worker', 'password' => bcrypt('worker'), 'isAdmin' => 1, 'head' => 0]);
        $python_main =  User::query()->create(['username' => 'python_main', 'password' => bcrypt('Breaking the habit 2n8'), 'isAdmin' => 0, 'head' => 0, 'python' => 1]);

        // semester
        $semester = Semester::create([
            'name_identifier' => 'صيفي',
            'year' => '2022-2023',
            'semester_start' => '2022-09-30',
            'number_of_weeks' => 16,
        ]);

        // students
        $andrew = $this->storePerson('1753001', ' اندرو عيسى', 'andrew.png', config('global.identity_student'));
        $checkbeans = $this->storePerson('1753002', 'احمد باجوق', 'checkbeans.png', config('global.identity_student'));
        $elie = $this->storePerson('1753003', 'إيلي عاد', 'elie.png', config('global.identity_student'));
        $naeem = $this->storePerson('1753004', 'نعيم نعمة', 'naeem.png', config('global.identity_student'));


        // professors
        $kamil = $this->storePerson('1753025', 'kamil', 'kamil.png', config('global.identity_professor'));
        $bashour = $this->storePerson('1753013', 'bashour', 'bashour.png', config('global.identity_professor'));


        // subjects
        $advancedProgramming1 = Subject::create([
            'name' => 'برمجة متقدمة 1',
            'department' => 'الهندسة المعلوماتية'
        ]);

        $advancedProgramming2 = Subject::create([
            'name' => 'برمجة متقدمة 2',
            'department' => 'الهندسة المعلوماتية'
        ]);

        // cameras
        Cam::create([
            'cam_url' => 0,
            'location' => '4518',
            'type' => 0
        ]);

        $entrance = Cam::create([
            'cam_url' => 1,
            'location' => 'مدخل رئيسي',
            'type' => 0
        ]);
        $entrance->schedule()->create([
            'day' => 5,
            'start' => '08:00:00',
            'end' => '16:00:00',
        ]);
        $exit = Cam::create([
            'cam_url' => 2,
            'location' => 'مخرج رئيسي',
            'type' => 0
        ]);
        $exit->schedule()->create([
            'day' => 5,
            'start' => '08:00:00',
            'end' => '16:00:00',
        ]);
        Cam::create([
            'cam_url' => 3,
            'location' => 'مكتبة',
            'type' => 0
        ]);
    }






    public function storePerson($id, $name, $imagePath, $identity)
    {

        $person = ModelsPerson::query()->create([
            'id_number' => $id,
            'name' => $name,
            'track' => 0,
            'on_blacklist' => 0,
            'recognize' => 1,
            'on_campus' => 0,
            'identity' => $identity,
        ]);
        // $destinationPath = storage_path('app/public/profiles');
        // $name = $person->id . '-image1' . '.jpg';
        // $img = Image::make(File::get('./storage/assets/' . $imagePath));
        // $img->resize(220, 220)->save($destinationPath . '/' . $name);
        // $person->images()->create([
        //     'url' => asset('storage/profiles/' . $person->id . '-image1' .  '.jpg'),
        //     'name' => $person->id . '-image1' .  '.png',
        // ]);
        if ($identity === config('global.identity_professor')) {
            User::create(
                [
                    'username' => $name, 'password' => bcrypt($name), 'isAdmin' => 0, 'head' => 0, 'python' => 0, 'person_id' => $person->id
                ]
            );
        }
        return $person;
    }
}
