<x-guest-layout>
    <form method="POST" action="{{ route('verification.code.verify') }}">
        @csrf
        <div class="mb-4 text-sm text-gray-600">
            Enter the 6-digit verification code sent to your email.
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-md bg-cyan-50 p-3 text-sm text-cyan-800">{{ session('status') }}</div>
        @endif

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ old('email', $email) }}" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="code" value="Verification Code" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" maxlength="6" required />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end">
            <x-primary-button>Verify Account</x-primary-button>
        </div>
    </form>
</x-guest-layout>
