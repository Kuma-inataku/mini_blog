<?php
class UserRepository extends DbRepository
{
  public function insert($user_name, $password)
  {
    $password = $this->hashPassword($password);
    $now = new DateTime();

    $spl = "INSERT INTO user(user_name, password, created_at) VALUES(:user_name, :password, :created_at)";

    $stmt = $this->execute($sql, [
      ':user_name' => $user_name,
      ':password' => $password,
      ':created_at' => $now->format('Y-m-d H:i:s'),
    ]);
  }

  public function hashPassword($password)
  {
    // ランダムな文字列追加
    return sha1($password.'ttttiiii');
  }
}