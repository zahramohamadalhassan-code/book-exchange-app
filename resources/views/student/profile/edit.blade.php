@extends('layouts.app')
@section('title', __('messages.student.profile.title') . ' - ' . __('messages.app_name'))

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center gap-2"><x-heroicon name="user-circle" class="w-8 h-8 text-indigo-600" /> {{ __('messages.student.profile.title') }}</h1>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">{{ __('messages.student.profile.personal_info') }}</h2>
            
            <form method="POST" action="{{ route('student.profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.profile.full_name') }}</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.profile.email') }}</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 text-start" dir="ltr">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.profile.phone') }}</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 text-start" dir="ltr">
                        @error('phone_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.profile.university_id') }}</label>
                        <input type="text" name="university_id" value="{{ old('university_id', $user->university_id) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 text-start" dir="ltr">
                        @error('university_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">
                        {{ __('messages.student.profile.save_changes') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">{{ __('messages.student.profile.change_password') }}</h2>
            
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.profile.current_password') }}</label>
                        <input type="password" name="current_password" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 text-start" dir="ltr">
                        @error('current_password', 'updatePassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.profile.new_password') }}</label>
                        <input type="password" name="password" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 text-start" dir="ltr">
                        @error('password', 'updatePassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.profile.confirm_password') }}</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 text-start" dir="ltr">
                        @error('password_confirmation', 'updatePassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg font-bold hover:bg-gray-900 transition">
                        {{ __('messages.student.profile.update_password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
