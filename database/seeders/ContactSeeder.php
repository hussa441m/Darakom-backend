<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\ContactType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $contactTypes = ContactType::all();

        if ($users->isEmpty() || $contactTypes->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            foreach ($contactTypes as $contactType) {
                $typeName = strtolower($contactType->name);

                if (str_contains($typeName, 'email') || str_contains($typeName, 'mail')) {
                    $value = "user{$user->id}@example.com";
                } elseif (
                    str_contains($typeName, 'phone')
                    || str_contains($typeName, 'mobile')
                    || str_contains($typeName, 'tel')
                    || str_contains($contactType->name, 'هاتف')
                    || str_contains($contactType->name, 'جوال')
                ) {
                    $value = '050' . str_pad((string) $user->id, 7, '0', STR_PAD_LEFT);
                } else {
                    $value = "contact-{$user->id}-{$contactType->id}";
                }

                Contact::create([
                    'value' => substr($value, 0, 20),
                    'user_id' => $user->id,
                    'contact_type_id' => $contactType->id,
                ]);
            }
        }
    }
}
