## Amazon Scraping made with Laravel

This Amazon Scraper is made with Laravel and developed with Sail.

## Installation

1. Clone the repo

    ```
    $ git clone https://github.com/DevShoesed/amazon-scrape.git
    ```

2. Install the project

    ```
    $ composer install
    $ ./vendor/bin/sail up -d
    $ ./vendor/bin/sail php artisan migrate
    ```

## Testing API

The avaible endpoints:

-   `YOUR_HOST/api/scape/{asin}`: Search product page on Amazon and Update or Create if exist in DB.
-   `YOUR_HOST/api/products/`: Fetch all products order by price.
-   `YOUR_HOST/api/product/{asin}`: Fetch a single product.

## Contact

Francesco Scarpato - francesco.scarpato@gmail.com

Project Link https://github.com/DevShoesed/amazon-scrape
