<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        $ch = curl_init();
        $url = "http://172.23.0.5/api/books/?role=$_SESSION[role]";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $contents = curl_exec($ch);

        $collection = $this->mongo->Users;
        $data = $collection->find([]);

        $collectionn = $this->mongo->order;
        $datan = $collectionn->find([]);

        $this->view->user = $data;
        $this->view->order = $datan;
        $this->view->data = json_decode($contents);
    }
}
