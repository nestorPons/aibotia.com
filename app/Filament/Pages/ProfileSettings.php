<?php 
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class ProfileSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationLabel = 'Mi perfil';
    protected static ?string $title = 'Ajustes de perfil';
    protected static ?string $slug = 'mi-perfil';
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.profile-settings';

    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    public function mount(): void
    {
        $this->form->fill([
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Nombre')
                ->required(),

            Forms\Components\TextInput::make('email')
                ->label('Correo electrónico')
                ->email()
                ->required(),

            Forms\Components\TextInput::make('password')
                ->label('Nueva contraseña')
                ->password()
                ->minLength(8)
                ->same('password_confirmation')
                ->dehydrated(fn ($state) => filled($state)),

            Forms\Components\TextInput::make('password_confirmation')
                ->label('Confirmar contraseña')
                ->password()
                ->dehydrated(false),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = Auth::user();
        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        Notification::make()
            ->title('Perfil actualizado correctamente.')
            ->success()
            ->send();
    }
}
