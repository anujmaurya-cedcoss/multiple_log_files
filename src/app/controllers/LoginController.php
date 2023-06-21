<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use App\Components\myescaper;

class LoginController extends Controller
{
    public function IndexAction()
    {
        // redirected to index
    }
    public function loginAction()
    {
        $user = new Users();
        $san = new Myescaper();
        $arr = array(
            'email' => $san->sanitize($this->request->getPost('email')),
            'password' => $san->sanitize($this->request->getPost('password'))
        );
        $user->assign(
            $arr,
            [
                'email',
                'password',
            ]
        );
        // query to find the user by name and email
        $sql = 'SELECT * FROM Users WHERE password = :password: AND email = :email:';
        $query = $this->modelsManager->createQuery($sql);
        $usr = $query->execute([
            'email' => $user->email,
            'password' => $user->password
        ]);
        if (isset($_POST['remember']) && isset($usr[0])) {
            $this->cookies->set('email', $user->email);
            $this->cookies->set('password', $user->password);
        }
        // if some result is found, then return as logged in, else user doesn't exist
        $response = new Response();
        if (isset($usr[0])) {
            // if the user logged in, mark loggedIn as true in session
            $this->session->loggedIn = true;
            $this->response->redirect('login/dashboard');
            $this->view->disable();
        } else {
            // if credentials were invalid, then log in login.log
            $this->logger
                ->excludeAdapters(['signup'])
                ->info('Unauthorized access attempt by : email => \''
                . $arr['name'] . '\' password => \'' . $arr['password'] . '\'');
            $response->setStatusCode(403, 'User Not Found');
            $response->setContent('Authentication Failed!');
            $response->send();
            die;
        }
    }
    public function DashboardAction()
    {
        // if it's not marked as loggedIn in session, redirect to login
        if (!$this->session->loggedIn) {
            $this->response->redirect('login/index');
        }
    }
    public function LogoutAction()
    {
        $this->session->destroy();
        $this->response->redirect('login/index');
        $this->session->loggedIn = false;
    }
}
