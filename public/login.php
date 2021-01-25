<?php

use App\Core\App;
use App\Dashboard;
use App\Layout\Login;
use App\Model\User;
use App\Singleton\Database;
use App\Singleton\Http\IncomingRequest;
use App\Singleton\Session;
use App\Singleton\Site;
use Atk4\Ui\Form;
use Atk4\Ui\View;

require_once('../bootstrap.php');

if (!is_null(Session::of('Dashboard')->get('user'))) {
    (new Dashboard())->redirect([
        Site::url('dashboard/index')
    ]);
}

$app = new App();
$app->initLayout([
    Login::class,
    'image' => ''
]);

$container = View::addTo($app);

$form = Form::addTo($container);
$form->setModel(new User(Database::connect()), [
    'email', 'password',
]);

$form->buttonSave->set('Log In');
$form->buttonSave->addClass('fluid');

$form->onSubmit(function (Form $form) {
    $email = $form->model->get('email');
    $password = $form->model->get('password');

    $user = $form->model->addCondition('email', $email)->action('select')->getRow();
    if (is_null($user)) {
        return $form->error('email', 'Account not found');
    } else {
        $passwordMatch = password_verify($password, $user['password']);
        if (!$passwordMatch) {
            return $form->error('email', 'Account not found');
        } else {
            unset($user['password']);
            Session::of('Dashboard')->set('user', $user);
            Session::of('App')->set('actor', $user['id']);

            return $form->getApp()->jsRedirect([
                IncomingRequest::query()->get('redirect', Site::url('dashboard/index')),
            ]);
        }
    }
});
