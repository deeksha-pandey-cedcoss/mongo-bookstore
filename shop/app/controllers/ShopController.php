<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;

class ShopController extends Controller
{
    public function indexAction()
    {
        $ch = curl_init();
        $url = "http://172.23.0.5/api/books/?role=$_SESSION[role]";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $contents = curl_exec($ch);


        $this->view->data = json_decode($contents);
    }
    public function cartAction()
    {

        $product = $this->mongo->books->findOne(['id' => $_GET['id']]);

        $collection = $this->mongo->cart;
        $data = $collection->insertOne([
            "name" => $product->name, "type" => $product->type, "price" => $product->price,
            "img" => $product->img, "pid" => $_GET['id']
        ]);

        if ($data->getInsertedCount() == 1) {
            $this->response->redirect("/shop/view");
        } else {
            echo "Invalid details found";
            die;
        }
    }
    public function viewAction()
    {

        $product = $this->mongo->cart->find([]);
        $this->view->data = $product;
    }
    public function deleteAction()
    {
        $collection = $this->mongo->cart;
        $deleted = $collection->deleteOne(['pid' => $_GET['id']]);
        $this->response->redirect("/shop/view");
    }
    public function puschaseAction()
    {
        $product = $this->mongo->cart->findOne(['pid' => $_GET['id']]);

        $uid = $_COOKIE['login'];
        $collection = $this->mongo->order;
        $data = $collection->insertOne([
            "name" => $product->name, "type" => $product->type, "price" => $product->price,
            "img" => $product->img, "pid" => $_GET['id'], "uid" => $uid
        ]);
        $collectionn = $this->mongo->cart;
        $deleted = $collectionn->deleteOne(['pid' => $_GET['id']]);
        $this->response->redirect("/shop/view");
    }
    public function myordersAction()
    {
        $product = $this->mongo->order->find([]);
        $this->view->data = $product;
    }
}
