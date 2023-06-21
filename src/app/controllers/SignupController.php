<?php
use Phalcon\Mvc\Controller;
use App\Components\myescaper;

class SignupController extends Controller
{
    public function IndexAction()
    {
        // nothing here
    }

    public function registerAction()
    {
        // creating a new user, with name and email obtained by post method
        $san = new Myescaper();
        $arr = array(
            'name' => $san->sanitize($this->request->getPost('name')),
            'email' => $san->sanitize($this->request->getPost('email')),
            'password' => $san->sanitize($this->request->getPost('password'))
        );
        $user = new Users();
        $user->assign(
            $arr,
            [
                'name',
                'email',
                'password'
            ]
        );
        // if the user details is saved, then return success
        $success = $user->save();
        $this->session->set('user-name', $user->email);
        echo "<pre>";
        print_r($this->session->get('user-name'));
        $this->view->success = $success;
        if ($success) {
            $this->view->message = "Register succesfully";
        } else {
            $this->logger
                ->excludeAdapters(['login'])
                ->info('Incomplete details filled : email => \''
                . $arr['name'] . '\' password => \'' . $arr['password'] . '\'');
            $this->view->message = "Not Register due to following reason: <br>" . implode("<br>", $user->getMessages());
        }
    }
}
