<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Price;
use App\Models\Product;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use Faker\Generator as Faker;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $service;

    protected function getHtmlProductPage(string $productName, array $productCategories, string $productPrice)
    {
        $crawler = new Crawler();
        $titleNode = "<span id='productTitle'>$productName</span>";

        $categoryNode = '<div id="wayfinding-breadcrumbs_feature_div"><ul>';
        foreach ($productCategories as $cat) {
            $categoryNode .= "<li>$cat</li>";
            $categoryNode .= "<li class='divider'></li>";
        }
        $categoryNode .= "</ul></div>";
        $priceNode = "<span id='price'>Prezzo: $productPrice&nbsp;€</span>";

        $node = "<body>$titleNode $priceNode $categoryNode</body>";
        $crawler->add($node);

        return $crawler;
    }


    /**
     * Test find Product Name
     * 
     * @return void
     */
    public function test_find_product_name()
    {
        $name = "Test Name Product.";
        $categories = [
            "Informatica",
            "Portatili"
        ];
        $price = "320,99";
        $this->assertEquals(
            $name,
            $this->service->findNameProduct($this->getHtmlProductPage($name, $categories, $price))
        );
    }

    /**
     * Test find Categories
     * 
     * @return void
     */
    public function test_find_product_categories()
    {
        $name = "Test Categories Product.";
        $expectedCategories = [
            "Informatica",
            "Computer",
            "Portatili",
            "Accessori"
        ];
        $price = "180,59";

        $foundCategories = $this->service->findCategories($this->getHtmlProductPage($name, $expectedCategories, $price));

        $this->assertEquals($expectedCategories, $foundCategories);
    }

    /**
     * Test find Product Price
     */
    public function test_find_product_price()
    {
        $name = "Test Price Product.";
        $categories = [
            "Informatica",
            "Accessori"
        ];
        $expectedPrice = 18.99;

        $price = $this->service->findPrice($this->getHtmlProductPage($name, $categories, number_format($expectedPrice, 2, ",", ".")));

        $this->assertNotNull($price);
        $this->assertEquals(
            $expectedPrice,
            $price
        );
    }

    /**
     * Test handle scrape action.
     *
     * @return void
     */
    public function test_handle_scrape_product()
    {
        $asinProduct = "BAX4321";
        $nameProduct = "Product Scaped from Service Test.";
        $categoriesProduct = [
            "Casa e cucina",
            "Tè e caffè",
            "Macchine da caffè",
            "Macchine per espresso e cappuccino",
            "Macchine da caffè manuali"
        ];
        $priceProduct = 183.29;

        $mockClient = Mockery::mock(\Goutte\Client::class)->makePartial();
        $mockClient->shouldReceive('request')
            ->andReturn($this->getHtmlProductPage($nameProduct, $categoriesProduct, number_format($priceProduct, 2, ",", ".")));

        $this->service = new ProductService(new CategoryRepository(), new ProductRepository(), $mockClient);

        $response = $this->service->handleScrapeProduct($asinProduct);

        $this->assertTrue($response);

        $product = Product::with('category')->find($asinProduct);

        $this->assertEquals(
            $nameProduct,
            $product->name
        );

        $this->assertEquals(
            $priceProduct,
            $product->last_price
        );

        $this->assertEquals(
            last($categoriesProduct),
            $product->category->name
        );
    }


    /**
     * Test delete existing product
     *
     * @return void
     */
    public function test_delete_existing_product(): void
    {
        $faker = new Faker();

        $asin = 'BX26AU';
        $category = Category::create(['name' => 'Category Test']);
        $product = Product::create([
            'asin' => $asin,
            'name' => 'Product Test',
            'category_id' => $category->id
        ]);

        $product->prices()->create([
            'price' => rand(10, 80)
        ]);

        $result = $this->service->deleteProduct($asin);

        $this->assertTrue($result);

        $this->assertEmpty(Price::where(['product_asin' => $asin])->get());
        $this->assertNull(Product::find($asin));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ProductService(new CategoryRepository(), new ProductRepository(), new \Goutte\Client());

        Log::shouldReceive('error');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
