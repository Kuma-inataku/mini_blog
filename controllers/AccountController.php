<?php

class AccountController extends Controller
{
  protected $auth_actions = ['index', 'signout', 'follow'];

  public function indexAction()
  {
    $user = $this->session->get('user');
    $followings = $this->db_manager->get('User')->fetchAllFollowingsByUserId($user['id']);

    return $this->render([
      'user' => $user,
      'followings' => $followings,
    ]);
  }

  public function signinAction()
  {
    if($this->session->isAuthenticated()) {
      return $this->redirect('/account');
    }

    return $this->render([
      'user_name' => '',
      'password' => '',
      '_token' => $this->generateCsrfToken('account/signin'),
    ]);
  }

  public function signupAction()
  {
    if($this->session->isAuthenticated()) {
      return $this->redirect('/account');
    }

    return $this->render([
      'user_name' => '',
      'password' => '',
      '_token' => $this->generateCsrfToken('account/signup'),
    ]);
  }

  public function authenticateAction()
  {
    if ($this->session->isAuthenticated()) {
      return $this->redirect('/account');
    }

    if (! $this->request->isPost()) {
      $this->forward404();
    }

    $token = $this->request->getPost('_token');
    if (! $this->checkCsrfToken('account/signin', $token)) {
      return $this->redirect('/account/signin');
    }

    $user_name = $this->request->getPost('user_name');
    $password = $this->request->getPost('password');

    $errors = [];

    if (! strlen($user_name)) {
      $errors[] = 'ユーザーIDを入力してください。';
    }

    if (! strlen($password)) {
      $errors[] = 'パスワードを入力してください。';
    }

    if (count($errors) === 0) {
      $user_repository = $this->db_manager->get('User');
      $user = $user_repository->fetchByUserName($user_name);

      if (! $user || $user['password'] !== $user_repository->hashPassword($password)) {
        $errors[] = 'ユーザーＩＤかパスワードが不正です。';
      } else {
        $this->session->setAuthenticated(true);
        $this->session->set('user', $user);

        return $this->redirect('/');
      }
    }

    return $this->render([
      'user_name' => $user_name,
      'password' => $password,
      'errors' => $errors,
      '_token' => $this->generateCsrfToken('account/signin'),
    ], 'singnin');
  }

  public function registerAction()
  {
    if($this->session->isAuthenticated()) {
      return $this->redirect('/account');
    }

    if(! $this->request->isPost()){
      $this->forward404();
    }

    $token = $this->request->getPost('_token');
    if(! $this->checkCsrfToken('account/signup', $token)){
      return $this->redirect('/account/signup');
    }

    $user_name = $this->request->getPost('user_name');
    $password = $this->request->getPost('password');

    $errors = [];

    if (! strlen($user_name)) {
      $errors[] = 'ユーザー名を入力してください。';
    } elseif (! preg_match('/^\w{3,20}$/', $user_name)) {
      $errors[] = 'ユーザー名は半角英数字およびアンダースコアを3～20文字以内で入力してください。';
    } elseif (! $this->db_manager->get('User')->isUniqueUserName($user_name)) {
      $errors[] = 'ユーザー名は既に使用されています。';
    }

    if(! strlen($password)) {
      $errors[] = 'パスワードを入力してください。';
    } elseif (4 > strlen($password) || strlen($password) > 30) {
      $errors[] = 'パスワードは4～30文字以内で入力してください。';
    }

    if (count($errors) === 0) {
      // TODO: 'U'小文字？
      $this->db_manager->get('User')->insert($user_name, $password);
      $this->session->setAuthenticated(true);

      // TODO: 'U'小文字？
      $user = $this->db_manager->get('User')->fetchByUserName($user_name);
      $this->session->set('user', $user);

      return $this->redirect('/');
    }

    return $this->render([
      'user_name' => $user_name,
      'password' => $password,
      'errors' => $errors,
      '_token' => $this->generateCsrfToken('account/signup')
    ], 'signup');
  }

  public function signoutAction()
  {
    $this->session->clear();
    $this->session->setAuthenticated(false);
    
    return $this->redirect('/account/signin');
  }

  public function followAction()
  {
    if (! $this->request->isPost()) {
      $this->forward404();
    }

    $following_name = $this->request->getPost('following_name');
    if (! $following_name) {
      $this->forward404();
    }

    $token = $this->request->getPost('_token');
    if (! $this->checkCsrfToken('account/follow', $token)) {
      return $this->redirect('/user/'.$following_name);
    }

    $follow_user = $this->db_manager->get('User')->fetchByUserName($following_name);

    if (! $follow_user) {
      $this->forward404();
    }

    $user = $this->session->get('User');

    $following_repository = $this->db_manager->get('Following');
    if ($user['id'] !== $follow_user['id'] && ! $following_repository->isFollowing($user['id'], $follow_user['id'])) {
      $following_repository->insert($user['id'], $follow_user['id']);
    }

    return $this->redirect('/account');
  }
}