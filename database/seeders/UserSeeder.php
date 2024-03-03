<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      
        /* $faker = Faker\Factory::create();
        for ($i =1; $i<=20; $i++){
            $User = new User;
            $User->name = $faker->name;
            $User->email = $faker->email;
            $User->mobile = $faker->phoneNumber;
            $User->role = 2;
            $User->password = 'Orange_2611';
            $User->IsActive = true;
            $User->dob = $faker->date($format = 'Y-m-d', $max = 'now');
            $User->gender = $faker->randomElement(['male', 'female']);
            $User->photo = $faker->randomElement(['male.jpg', 'female.jpg']);
        } */
        
        $json = Storage::disk('local')->get('/json/yami.json');
        $userNames = json_decode($json,true);
        foreach ($userNames as $name){
            User::query()->updateOrCreate([
                'name' => $name['name'],
                'gender' => $name['gender'],
                'email' => $name['email'],
                'dob' => $name['dob'],
                'photo' => $name['photo'],
                'role' => $name['role'],
                'password' => Hash::make($name['password']),
                'mobile' => $name['mobile'],
                'IsActive' => true,
            ]);
        }
    }
}
