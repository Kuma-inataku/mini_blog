<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php if(isset($title)): echo $this->escapr($title) . ' - ';endif ?> Mini Blog</title>
</head>
<body>
  <div id="header">
    <h1><a href="<?php echo $base_url; ?>">Mini Blog</a></h1>
  </div>
  <div id="mail">
    <?php echo $_content; ?>
  </div>
</body>
</html>