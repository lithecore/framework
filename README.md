# Lithe

Lithe is a PHP framework inspired by Express.js, known for its lightweight and flexible nature. It offers simplicity, flexibility, and expressiveness for developers, enabling the creation of everything from small applications to complex web platforms efficiently and effectively.

## Installation

To start using Lithe in your project, you can install it via Composer. Run the following command at the root of your project:

```bash
composer require lithecore/framework
```

## Configuration

After installation, you can begin using Lithe in your project. Here is a basic example of configuration and usage:

1. **Create an `index.php` file** at the root of your project:

    ```php
    <?php

    // Define PROJECT_ROOT
    define('PROJECT_ROOT', __DIR__);

    require 'vendor/autoload.php';

    // Create a new instance of the Lithe app
    $app = new Lithe\App();

    // Define a sample route
    $app->get('/users/:name', function ($req, $res) {
        $name = $req->params->name;

        $res->json(['message' => "Hello, $name!"]);
    });

    // Start the app
    $app->listen();
    ```

2. **Configure the `.htaccess` file**:

    Add the following content to your `.htaccess` file at the root of your project to ensure that all requests are handled by `index.php`:

    ```apache
    # Disable MultiViews
    Options -MultiViews

    # Enable URL rewriting
    RewriteEngine On

    # Disable directory listing
    Options -Indexes

    # Custom error document for 403 errors
    ErrorDocument 403 /403

    # Rewrite rules
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-l
    RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]
    ```

## What is Lithe?

Lithe is a PHP framework known for its simplicity, flexibility, and efficiency. Inspired by Express.js, Lithe is designed to help developers build web applications quickly and effectively. The name "Lithe" reflects the core characteristics of the framework: flexible and agile.

## Simple and Flexible Routing

In Lithe, defining routes is very simple. You can use methods like `get()`, `post()`, and others to create routes that respond to different types of HTTP requests:

```php
$app->get('/hello/:name', function ($req, $res) {
    $res->send('Hello, ' . $req->params->name);
});
```

Discover how [routing in Lithe](/docs/the-basics/routing) can simplify your development and offer complete control over your application's routes.

## Powerful Middleware

In Lithe, middleware is your line of defense, allowing you to inspect, filter, and manipulate HTTP requests before they reach the final routes. Imagine adding functionalities like authentication and logging in a modular and reusable way!

Here’s how easy it is to define and use middleware:

```php
// Middleware to check if the token is valid
$EnsureTokenIsValid = function ($req, $res, $next) {
    $token = $req->params->token;

    if ($token !== 'my-secret-token') {
        $res->send('Invalid token.');
    }

    $next();
};

// Protected route using the middleware
$app->get('/protected/:token', $EnsureTokenIsValid, function ($req, $res) {
    $res->send('Protected content accessed successfully!');
});
```

Learn more about how [middlewares in Lithe](/docs/the-basics/middleware) can transform the way you develop and maintain your applications.

## View Engine Choice

Lithe offers flexibility by allowing you to choose from various template engines, such as pure PHP, Blade, and Twig. In addition to the standard engines, you can configure others to optimize the creation and rendering of dynamic interfaces.

```php
$app->set('view engine', 'blade');
```

Explore the possibilities of [view engines](/docs/the-basics/template-engines) and learn how to integrate them into your project efficiently.

## Database Integration

Connecting to databases is straightforward with Lithe. The framework supports popular ORMs like Eloquent and native PHP drivers such as MySQLi and PDO. Configure your connections in the `.env` file and manage schema migrations easily.

```
DB_CONNECTION_METHOD=eloquent
DB_CONNECTION=mysql
DB_HOST=localhost
DB_NAME=lithe
DB_USERNAME=root
DB_PASSWORD=
DB_SHOULD_INITIATE=true
```

Learn more about [database integration in Lithe](/docs/database/integration) and see how easy it is to manage your data.

## Database Migrations

Maintain consistency and integrity of data in your applications with automated migrations. With Lithe, you can create and apply migrations quickly and easily using any ORM interface or database driver.

```bash
php line make:migration CreateUsersTable --template=eloquent
php line migrate
```

Learn more about [migrations in Lithe](/docs/database/migrations) and make the most of this feature to build robust and scalable applications.

## Contributing

Contributions are welcome! If you find an issue or have a suggestion, feel free to open an [issue](https://link-to-issues) or submit a [pull request](https://link-to-pull-requests).

## License

Lithe is licensed under the [MIT License](https://opensource.org/licenses/MIT). See the [LICENSE](LICENSE) file for more details.

## Contact

If you have any questions or need support, get in touch:

- **Instagram**: [@williamhumbwavali](https://instagram.com/williamhumbwavali)