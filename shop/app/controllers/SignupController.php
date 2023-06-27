<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;

class SignupController extends Controller
{
    public function indexAction()
    {
        //    default action

    }
    public function registerAction()
    {

        if ($this->request->isPost()) {
            $name = $this->request->getPost('name');
            $email = $this->request->getPost('email');
            $pass = $this->request->getPost("password");
            $role = $this->request->getPost("role");


            $collection = $this->mongo->Users;
            $data = $collection->insertOne(["name" => $name, "email" => $email, "password" => $pass, "role" => $role]);

            if ($data->getInsertedCount() == 1) {
                $this->response->redirect("login");
            } else {
                echo "Invalid details found";
                die;
            }
        }
    }
}
