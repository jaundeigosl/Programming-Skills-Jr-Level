<?php

use App\Models\User;
use App\Models\Referral;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;


new #[Layout('layouts.guest')] class extends Component
{
    public string $name;
    public string $surname;
    public string $country;
    public string $city;
    public string $birth;
    public string $email;
    public string $password;
    public string $password_confirmation;



    public function mount(){
        $this->name ='';
        $this->surname ='';
        $this->country ='';
        $this->city ='';
        $this->birth ='';
        $this->email ='';
        $this->password ='';
        $this->password_confirmation =''; 
        
    }

    public function getTimeFromString($date, &$year, &$month, &$day){
        $birth = $date;
        $i = 0;
        $year = '';
        $month = '';
        $day = '';
        while($i<=9){
            if($i == 4 || $i == 7){
                $i++;
            }
            if($i <4){
                $year = $year . $birth[$i];
                $i++;
            }else{
                if($i<7){
                    $month = $month . $birth[$i];
                    $i++;
                }else{
                    $day = $day . $birth[$i];
                    $i++;
                }
            }
        }
    }

    public function calculateAge($actualyear, $actualMonth, $actualDay , $userYear, $userMonth, $userDay){
        $years = $actualyear-$userYear;
        if($actualMonth < $userMonth){
            return intval($years);
        }else{
            if($actualMonth == $userMonth){
                if($actualDay<$userDay){
                    return intval($years);
                }else{
                    return intval($years) + 1;
                }
            }else{
                return intval($years) + 1;
            }
        }
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required','string','max:255'],
            'country' => ['required','string','max:255'],
            'city' => ['required','string','max:255'],
            'birth' => ['required','string'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        //Creating the User and referral code register

        event(new Registered($user = User::create($validated)));

        Referral::create([
            'user_id'=>$user->id,
            'referral_code'=>Str::random(8)
        ]);

        Auth::login($user);

        //calculating the age

        $userBirth = $validated['birth'];
        $yearUser='';
        $monthUser='';
        $dayUser='';
        $this->getTimeFromString($userBirth, $yearUser, $monthUser, $dayUser);
        
        $actualYear='';
        $actualMonth='';
        $actualDay='';
        $actualTime = now()->toDateString();
        $this->getTimeFromString($actualTime, $actualYear,$actualMonth,$actualDay);

        $age = $this->calculateAge($actualYear,$actualMonth,$actualDay,$yearUser,$monthUser,$dayUser);

        //assigning the welcome gift based on the age

        if($age > 35){
            DB::table('users')->where('id', auth()->user()->id)->update(['tokens'=> auth()->user()->tokens + 5]);
        }else{
            DB::table('users')->where('id', auth()->user()->id)->update(['tokens'=> auth()->user()->tokens + 10]);
        }

        session()->flash('registerSuccess','Register Successful');

        $this->redirect(route('referral',absolute: false) , navigate:true);
    }
}; ?>

<div>
    <form wire:submit="register" class="mb-1">
        <!-- Name -->
        <div>
            <x-input-label for="name" value="Name" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="surname" value="Surname"/>
            <x-text-input wire:model="surname" id="surname" class="block mt-1 w-full" type="text" name="surname" />
            <x-input-error :messages="$errors->get('surname')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="country" value="Country"/>
            <x-text-input wire:model="country" id="country" class="block mt-1 w-full" type="text" name="country" />
            <x-input-error :messages="$errors->get('country')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="birth" value="Birth"/>
            <x-date-input type="date" wire:model="birth" id="birth" class="block mt-1 w-full" type="text" name="birth"/>
            <x-input-error :messages="$errors->get('birth')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="city" value="City"/>
            <x-text-input wire:model="city" id="city" class="block mt-1 w-full" type="text" name="city" />
            <x-input-error :messages="$errors->get('city')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Password" />

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirm Password" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{'Already registered?' }}
            </a>

            <x-primary-button class="ms-4">
                {{'Register' }}
            </x-primary-button>
        </div>
    </form>
</div>
