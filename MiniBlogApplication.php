<?php
// 自動で読み込み
require './vendor/autoload.php';

// .envを使用する
Dotenv\Dotenv::createImmutable(__DIR__)->load();

class MiniBlogApplication extends Application
{
    protected $login_action = ['account', 'signin'];

    public function getRootDir()
    {
      return dirname(__FILE__);
    }

    protected function registerRoutes()
    {
      return [

      ];
    }

    protected function configure()
    {
      $this->db_manager->connect('master', [
        'dsn' => 'mysql:dbname=mini_blog;host=localhost',
        // TODO: DB接続エラーがでたら変更
        // 'user' => '',
        // 'password' => '',
        'user' => $_ENV['DB_USER_NAME'],
        'password' => $_ENV['DB_PASSWORD'],
      ]);
    }
}