<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseTransactions, WithFaker;


    /**
     * Test To See collection of products
     *
     *
     */
    public function testToSeeCollectionOfProducts()
    {
        $this->getJson(route("products.index"))->assertOk();
    }

    /**
     * Test To See each individual product
     *
     *
     */

    public function testToSeeIndividualProduct()
    {
        $data = Product::query()->create([
            "name" => $this->faker->sentence(),
            "description" => $this->faker->paragraph(5),
            "category_id" => Category::query()->inRandomOrder()->first()->id,
            "brand_id" => Brand::query()->inRandomOrder()->first()->id,
            "price" => $this->faker->randomFloat(2, 10, 1000)
        ]);

        $this->getJson(route("products.show", $data->id))->assertOk();

        $product = Product::query()->where("name", "=", $data->name)->exists();

        $this->assertTrue($product);
    }

    /**
     * Test To Create New Product
     *
     *
     */

    public function testToCreateNewProduct()
    {
        $data = [
            "name" => $this->faker->sentence(),
            "description" => $this->faker->paragraph(5),
            "category_id" => Category::query()->inRandomOrder()->first()->id,
            "brand_id" => Brand::query()->inRandomOrder()->first()->id,
            "price" => $this->faker->randomFloat(2, 10, 1000)
        ];

        $this->postJson(route("products.store"), $data, ["Accept" => "application/json"])
            ->assertOk()
            ->assertJsonStructure([
                "data",
                "message"
            ]);

        $product = Product::query()->where("name", "=", $data["name"])->exists();

        $this->assertTrue($product);
    }

    /**
     * Test To Update Existing Product
     *
     *
     */

    public function testToUpdateProduct()
    {
        $data = Product::query()->create([
            "name" => $this->faker->sentence(),
            "description" => $this->faker->paragraph(5),
            "category_id" => Category::query()->inRandomOrder()->first()->id,
            "brand_id" => Brand::query()->inRandomOrder()->first()->id,
            "price" => $this->faker->randomFloat(2, 10, 1000)
        ]);


        $data->name = "something new";


        $this->patchJson(route("products.update", $data->id), $data->toArray(), ["Accept" => "application/json"])
            ->assertOk()
            ->assertJsonStructure([
                "message",
                "data"
            ]);

        $product = Product::query()->where("id", "=", $data->id)->exists();

        $this->assertTrue($product);
    }


    /**
     * Test To Delete Existing Product
     *
     *
     */

    public function testToDestroyProduct()
    {

        $data = Product::query()->create([
            "name" => $this->faker->sentence(),
            "description" => $this->faker->paragraph(5),
            "category_id" => Category::query()->inRandomOrder()->first()->id,
            "brand_id" => Brand::query()->inRandomOrder()->first()->id,
            "price" => $this->faker->randomFloat(2, 10, 1000)
        ]);

        $this->deleteJson(route("products.destroy", $data->id), $data->toArray())
            ->assertOk()
            ->assertJsonStructure([
                "message"
            ]);

        $product = Product::query()->where("name", "=", $data->name)->doesntExist();

        $this->assertTrue($product);
    }

}
