<?php 
  class DB {

    protected $host    = 'localhost';
    protected $db      = 'relational_db_exercise';
    protected $user    = 'root';
    protected $pass    = 'root';
    protected $charset = 'utf8';
    public $pdo;
    public $id;

    public function __construct() {
      $this->open_connection();
    }

    public function get_pdo_obj($pdo) {
      return $this->pdo;
    }

    public function open_connection() {
      $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
      $options = [
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES   => false,
      ];
      try {
        $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
      }
      catch(Exception $e) {
        echo $e->getMessage();
      }
    }
  }
  // set var in global scope as it will be 'passed by reference' through functions as it will be altered each time lastInsertId() is ran
  $id = 0;
  function add_user_to_db($f_name, $l_name, $age, &$the_prev_id) {
    $db   = new DB();
    $stmt = $db->pdo->prepare("INSERT INTO users (f_name, l_name, age) VALUES (:f_name, :l_name, :age)");
    $stmt->execute([$f_name, $l_name, $age]);
    $the_prev_id = $db->pdo->lastInsertId();
  }

  function add_post_to_db($post_title, &$the_prev_id) {
    $db   = new DB();
    $stmt = $db->pdo->prepare("INSERT INTO posts (title, user_id) VALUES (?,?)");
    $stmt->execute([$post_title, $the_prev_id]);
    $the_prev_id = $db->pdo->lastInsertId();
  }

  function add_comment_to_db($comment_title, &$the_prev_id) {
    $db   = new DB();
    $stmt = $db->pdo->prepare("INSERT INTO comments (title, post_id) VALUES (?,?)");
    $stmt->execute([$comment_title, $the_prev_id]);
  }

  function join_posts_and_user() {
    $data = [];
    $db   = new DB();
    $stmt = $db->pdo->query("SELECT users.age, posts.title, posts.post_id FROM users INNER JOIN posts ON users.id = posts.user_id");
    $obj  = $stmt->fetch();
    print_r($obj);
  }

  function full_join_posts_and_user() {
    $data = [];
    $db   = new DB();
    $stmt = $db->pdo->query("SELECT * FROM users JOIN posts ON users.id = posts.user_id");
    $obj  = $stmt->fetch();
    print_r($obj);
  }

  function where_posts_and_user() {
    $data = [];
    $db = new DB();
    $stmt = $db->pdo->query("SELECT * FROM posts p, users u WHERE p.user_id = u.id");
    $obj = $stmt->fetch();
    print_r($obj);
  }

  if (isset($_POST['add'])) {
    $f_name        = $_POST['f_name'];
    $l_name        = $_POST['l_name']; 
    $age           = $_POST['age']; 
    $post_title    = $_POST['post_title']; 
    $comment_title = $_POST['comment_title']; 
    add_user_to_db($f_name, $l_name, $age, $id);
    add_post_to_db($post_title, $id);
    add_comment_to_db($comment_title, $id);
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
</head>
<body>
  <form action="" method="post">
    <input type="text" placeholder="f_name" name="f_name">
    <input type="text" placeholder="l_name" name="l_name">
    <input type="number" name="age">
    <input type="text" name="post_title" placeholder="post title">
    <input type="text" name="comment_title" placeholder="comment title">
    <input type="submit" name="add">
  </form>
  <pre>
    <h3>WHERE POSTS and USERS</h3>
    <?php where_posts_and_user(); ?>
  </pre>
  <pre>
    <h3>INNER JOIN POSTS and USERS</h3>
    <?php join_posts_and_user(); ?>
  </pre>
  <pre>
    <h3>FULL JOIN POSTS and USERS</h3>
    <?php full_join_posts_and_user(); ?>
  </pre>
</body>
</html>
