<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;

session_start();
class LoginController extends Controller
{
    public function indexAction()
    {

        //  default
    }
    public function loginAction()
    {
        if ($this->request->isPost()) {
            $email = $this->request->getPost('email');
            $pass = $this->request->getPost("password");
            $role = $this->request->getPost("role");


            $collection = $this->mongo->Users;
            $data = $collection->findOne(["email" => $email, "password" => $pass]);

            setcookie("login", $data->_id, time() + (86400 * 30), "/");
            $admin = $data['role'];
            if ($admin) {
                if ($data['role'] == 'admin') {
                    $_SESSION['role'] = 'admin';
                    $this->response->redirect('/shop/index');
                } elseif ($data['role'] == 'user') {
                    $_SESSION['role'] = 'user';
                    $this->response->redirect('/place/new');
                } else {
                    $this->response->redirect('login');
                }
            }
        }
    }
}
