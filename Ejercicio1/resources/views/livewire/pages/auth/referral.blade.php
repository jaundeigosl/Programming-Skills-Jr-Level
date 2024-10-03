<?php 

use App\Models\Referral;
use App\Models\User;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.guest')] class extends Component {

    public string $code;

    public function mount(){
        $this->code = '';
    }

    public function referral(){

        //Validating the referral code

        $validate = $this->validate([
            'code' => ['string','required','max:8','exists:referrals,referral_code']]);

        //Updating values 

        $referral_user = User::find(Referral::where('referral_code',$validate['code'])->first()->user_id);

        $referral_user->referral->update(['total_referred_users' => $referral_user->referral->total_referred_users + 1]);
        
        DB::table('users')->where('id' , $referral_user->id)->update(['tokens' => $referral_user->tokens + 5]);

        DB::table('users')->where('id', auth()->user()->id)->update(
        ['referral_by'=>$referral_user->referral,'referral_status' => 'on' , 'tokens' => auth()->user()->tokens + 5]);

        session()->flash('redeemSuccess','Referral Code redeemed');
            
        return redirect(route('dashboard'));

    }

}
?>

<div>
    @if(session()->has('registerSuccess'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <span class="font-medium">{{session('registerSuccess')}}</span>
        </div>
    @endif

    <form wire:submit ="referral" class="mb-1">
        <div>

            <x-input-label for="code" value="Referral Code" />
            <x-text-input wire:model="code" id="name" type="text" class="block mt-1 w-full" name="code" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />

        </div>

        <div class="mt-4">
            <x-primary-button>
                    {{'Redeem' }}
            </x-primary-button>

            <x-skip-button value="dashboard">
                {{'Skip to profile'}}
            </x-skip-button>
        </div>
    </form>

</div>