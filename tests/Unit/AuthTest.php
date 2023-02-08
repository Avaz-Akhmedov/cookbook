<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 */
class AuthTest extends TestCase
{
    use DatabaseTransactions, WithFaker;


    protected array $responseData = [];
    protected array $loginData = [];
    protected array $registerData = [];


    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->responseData = [
            "username",
            "email",
            "firstname",
            "lastname"
        ];

        $this->loginData = [
            "username" => $this->faker->userName(),
            "email" => $this->faker->email(),
            "firstname" => $this->faker->firstName(),
            "lastname" => $this->faker->lastName(),
            "password" => bcrypt("1111"),
        ];

        $this->registerData = [
            "username" => $this->faker->userName(),
            "email" => $this->faker->email(),
            "firstname" => $this->faker->firstName(),
            "lastname" => $this->faker->lastName(),
            "password" => "2222",
            "password_confirmation" => "2222"
        ];
    }




    public function testIfUserCanRegister()
    {


        $data = $this->registerData;

        $this->json("POST", "api/register", $data, ["Accept" => "application/json"])
            ->assertStatus(200)
            ->assertJsonStructure([
                "data" => $this->responseData,
                "message"
            ]);

    }


    public function testIfUserCanLogin()
    {
        $user = User::query()->create($this->loginData);

        $credentials = [
            "email" => $user->email,
            "password" => "1111"
        ];

        $response = $this->json("POST", "api/login", $credentials, ["Accept" => "application/json"])
            ->assertStatus(200)
            ->assertJsonStructure([
                "token",
                "data" => $this->responseData,
                "message"
            ]);
        $response->assertOk();

        $this->assertArrayHasKey("token", $response->json());

        $this->assertAuthenticated();
    }



    public function testIfUserCanLogout()
    {
        $user = User::query()->create($this->loginData);

        $this->post("/api/login", [
            "email" => $user->email,
            "password" => $user->password
        ]);


        Sanctum::actingAs($user);


        $response = $this->json("POST", "api/logout", [])->assertJsonStructure(["message"]);

        $response->assertStatus(200);

    }

}