<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register via multi-step registration', function () {
    // Step 1: Submit Organization Data
    $step1Response = $this->post(route('register.organization'), [
        'org_name' => 'Al-Khair Foundation',
        'org_address' => 'Dhaka, Bangladesh',
    ]);

    $step1Response->assertRedirect(route('register', ['step' => 2]));

    // Step 2: Submit User Data
    $step2Response = $this->post(route('register.user'), [
        'name' => 'Shakil Miah',
        'email' => 'shakil@alkhair.org',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $this->assertAuthenticated();
    $step2Response->assertRedirect(route('dashboard', absolute: false));
    $this->assertDatabaseHas('organizations', ['name' => 'Al-Khair Foundation']);
    $this->assertDatabaseHas('users', ['email' => 'shakil@alkhair.org']);
});