<?php

class StatusController extends Controller
{
  public function indexAction()
  {
    // TODO: 'u'大文字？
    $user = $this->session->get('user');
    $statuses = $this->db_manager->get('Status')->fetchAllPersonalArchivesByUserId($user['id']);

    return $this->render([
      'statuses' => $statuses,
      'body' => '',
      '_token' => $this->generateCsrfToken('status/post'),
    ]);
  }
}