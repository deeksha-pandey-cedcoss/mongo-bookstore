<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Events\Event;
use Phalcon\Events\Manager;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;



// Use Loader() to autoload our model
$loader = new Loader();

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/html/');

require_once APP_PATH . '/vendor/autoload.php';

$loader->registerDirs(
    [
        APP_PATH . "/models/",
    ]
);

$loader->registerNamespaces(
    [
        'Store\Toys' => APP_PATH . '/models/',
    ]
);

$loader->register();

$container = new FactoryDefault();

// Set up the database service


$container->set(
    'mongo',
    function () {
        $mongo = new MongoDB\Client(
            'mongodb+srv://deekshapandey:Deeksha123@cluster0.whrrrpj.mongodb.net/?retryWrites=true&w=majority'
        );

        return $mongo->bookstore;
    },
    true
);

$app = new Micro($container);



// // Retrieves all products

$app->get(
    '/api/books',
    function () use ($app) {
        $robot = $this->mongo->books->find();
        $data = [];
        foreach ($robot as $value) {

            $data[] = [
                "name" => $value->name,
                "id" => $value->id,
                "type" => $value->type,
                "price" => $value->price,
                'img' => $value->img
            ];
        }
        echo json_encode($data);
    }
);
// Searches for products with $name in their name
$app->get(
    '/api/books/search/{name}',
    function ($name) use ($app) {
        $robot = $this->mongo->books->findOne(["name" => $name]);

        $data = [];
        $data[] = [
            "name" => $robot->name,
            "id" => $robot->id,
            "type" => $robot->type,
            "price" => $robot->price,
            'img' => $robot->img
        ];


        echo json_encode($data);
    }

);

// Retrieves products based on primary key
$app->get(
    '/api/books/{id:[0-9]+}',
    function ($id) use ($app) {
        $product = $this->mongo->books->findOne(['id' => $id]);
        $data = [];
        $data[] = [
            'id'   => $product->id,
            'name' => $product->name,
            'type' => $product->type,
            'price' => $product->price,
            'img' => $product->img
        ];
        echo json_encode($data);
    }
);

// Adds a new product
$app->post(
    '/api/books',
    function () use ($app) {

        $payload = [
            "id" => $_POST['id'],
            "name" => $_POST['name'],
            "type" => $_POST['type'],
            "price" => $_POST['price'],
            "img" => "https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1456943909i/29401270.jpg",
        ];
        $collection = $this->mongo->books;
        $status = $collection->insertOne($payload);
        print_r($status->getInsertedCount());
    }
);

// Updates product based on primary key
$app->put(
    '/api/books/{id:[0-9]+}',
    function ($id) use ($app) {

        $robot = $app->request->getJsonRawBody();

        $payload = [
            "name" => $robot->name,
            "type" => $robot->type,
            "price" =>  $robot->price,

        ];
        $collection = $this->mongo->books;
        $updateResult = $collection->updateOne(
            ['id'  =>  $id],
            ['$set' =>  $payload]
        );
        print_r($updateResult);
    }
);

// Deletes product based on primary key
$app->delete(
    '/api/books/{id:[0-9]+}',
    function ($id) use ($app) {
        $collection = $this->mongo->books;
        $deleted = $collection->deleteOne(['id' => $id]);
        print_r($deleted);
        die;
    }
);
$app->post(
    '/order/create',
    function () {
        $payload = [
            "name" => json_encode($_POST['name']),
            "address" => json_encode($_POST['address']),
            "product_id" => $_POST['product'],
            "quantity" => $_POST['quantity'],
            "status" => "placed",
            "order_id" => uniqid()
        ];
        $collection = $this->mongo->orders;
        $status = $collection->insertOne($payload);
        var_dump($status);
    }
);
$app->put(
    '/order/update/{id:[0-9]+}',
    function () {
        $robot = $app->request->getJsonRawBody();
        $collection = $this->mongo->orders;
        $updateResult = $collection->updateOne(
            ['id'  =>  $id],
            ['$set' => [
                "name" => $robot->name,
                "address" => $robot->address,
                "quantity" => $robot->quantity,
                "status" => "declined",

            ]]
        );
        print_r($updateResult);
        die;
    }
);
$app->get(
    '/api/order',
    function () use ($app) {
        $collection = $this->mongo->orders->find();
        $data = [];
        foreach ($collection as $robot) {

            $data[] = [
                'id'   => $robot->_id,
                'product_id'   => $robot->product_id,
                'name' => $robot->name,
                'price' => $robot->price,
                "address" => $robot->address,
                "quantity" => $robot->quantity,
                "status" => $robot->status,
            ];
        }

        echo json_encode($data);
    }
);
$app->handle(
    $_SERVER["REQUEST_URI"]
);
