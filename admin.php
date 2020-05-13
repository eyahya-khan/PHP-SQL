<?php
//database connection
require('dbconnect.php');

$pageTitle = 'Administration page';

session_start();
//check username and password have value or not
if(isset($_SESSION['username'])){
    $loginUsername = $_SESSION['username'];
}else{
    header('Location: login.php');
}
//remove username and password
if(isset($_POST['logout'])){
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

// Delete post
$message = '';
if (isset($_POST['deleteBtn'])) {
  try {
    $query = "
      DELETE FROM posts
      WHERE id = :id;
    ";

    $stmt = $dbconnect->prepare($query);
    $stmt->bindValue(':id', $_POST['hidId']);
    $stmt->execute();
      
    $message = 
      '<div class="alert alert-success" role="alert">
        Your post deleted successfully.
      </div>';
  } catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
  }
}

// Add new post
if (isset($_POST['addBtn'])) {
  $title = trim($_POST['title']);
  $content = trim($_POST['content']);
  $author = trim($_POST['author']);

  if (empty($title) || empty($content) || empty($author)) {
    $message = 
      '<div class="alert alert-danger" role="alert">
        Title, Content, Author: All field must have value
      </div>';
  } else if(is_numeric($title) || is_numeric($content) || is_numeric($author)){
       $message = 
      '<div class="alert alert-danger" role="alert">
        Title, Content, Author: Only number is not allowed.
      </div>';
  }else if(!preg_match("/^[a-zA-Z ]*$/",$title)){
       $message = 
      '<div class="alert alert-danger" role="alert">
        Title: Only letter and whitespace are allowed.
      </div>';
  }else if(!preg_match("/^[a-zA-Z]$/",$content[0])){
       $message = 
      '<div class="alert alert-danger" role="alert">
        Content: Start with letter.
      </div>';
  }else if(!preg_match("/^[a-zA-Z ]*$/",$author)){
       $message = 
      '<div class="alert alert-danger" role="alert">
        Author: Only letter and whitespace are allowed.
      </div>';
  }else if(strlen($title) > 30){
       $message = 
      '<div class="alert alert-danger" role="alert">
        Title must have less than 30 characters.
      </div>';
  }else if(strlen($author)> 30){
       $message = 
      '<div class="alert alert-danger" role="alert">
        Author name must have less than 30 characters.
      </div>';
  }else {
    try {
      $query = "
        INSERT INTO posts (title, content, author)
        VALUES (:title, :content, :author);
      ";

      $stmt = $dbconnect->prepare($query);
      $stmt->bindValue(':title', $title);
      $stmt->bindValue(':content', $content);
      $stmt->bindValue(':author', $author);
      $stmt->execute();
        
    $message = 
      '<div class="alert alert-success" role="alert">
        Your post uploaded successfully.
      </div>';
        
    } catch (\PDOException $e) {
      throw new \PDOException($e->getMessage(), (int) $e->getCode());
    }
  }
}

// Update blog
if (isset($_POST['updateBtn'])) { 
  $title = trim($_POST['title']);
  $content = trim($_POST['content']);
  $author = trim($_POST['author']);

  if (empty($title)) {
      
    $message = 
      '<div class="alert alert-danger" role="alert">
        Update: Title field must not be empty
      </div>';
  }else if (empty($content)) {
    $message = 
      '<div class="alert alert-danger" role="alert">
        Update: Content field must not be empty
      </div>';
  }else if (empty($author)) {
    $message = 
      '<div class="alert alert-danger" role="alert">
        Update: Author field must not be empty
      </div>';
  } else {
    try {
      $query = "
        UPDATE posts
        SET content = :content,title = :title,author = :author
        WHERE id = :id;
      ";

      $stmt = $dbconnect->prepare($query);
      $stmt->bindValue(':title', $title);
      $stmt->bindValue(':content', $content);
      $stmt->bindValue(':author', $author);
      $stmt->bindValue(':id', $_POST['id']);
      $stmt->execute();
       
    $message = 
      '<div class="alert alert-success" role="alert">
        Your update is successfull.
      </div>';
        
    } catch (\PDOException $e) {
      throw new \PDOException($e->getMessage(), (int) $e->getCode());
    }
  }
}

// Fetch all posts to display on page
try {
  $query = "SELECT * FROM posts;";
  $stmt = $dbconnect->query($query);
  $puns = $stmt->fetchAll();
} catch (\PDOException $e) {
  throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

?>
<?php include('head.php'); ?>

<body>

    <div class="container">
        <div class="row">
            <div class="offset-1 col-10">
                <form action="" method="POST">
                    <div class="input-group" style="display:flex;justify-content:flex-end;">
                      
                        <!--display user name-->
                       <label style="margin-top:15px;margin-right:2px;"><?php echo 'Welcome '.ucfirst($loginUsername); ?></label>
                       
                        <input type="submit" name="logout" value="Log out" class="btn btn-outline-dark" style="margin-top:5px;">
                    </div>
                </form>     
                

                <h1>Blog Administration</h1>

                <form action="" method="POST">
                    <div class="input-group mb-3">
                        <input type="text" name="title" class="form-control" placeholder="Blog tilte">
                        <input type="text" name="author" class="form-control" placeholder="Author name"><br>
                    </div>
                    <textarea name="content" class="form-control" placeholder="Blog content" rows="5" cols="30"></textarea>

                    <div class="input-group-append" style="float:right;">
                        <input type="submit" name="addBtn" value="Add" class="btn btn-success" id="button-addon2" style="width:80px; margin-right:30px;margin-top:3px;">
                    </div>
                </form>

                <?=$message?>
                    <br>
                <h3>All posts list at a glance</h3>
                <hr>
                <ul class="list-group">
                    <?php foreach ($puns as $key => $pun) { ?>
                    <li class="list-group-item">
                        <p class="float-left">
                            <h3><?=htmlentities($pun['title'])?></h3>
                            <?=htmlentities($pun['content'])?>
                            <h4><?=htmlentities($pun['author'])?></h4>
                            <?=htmlentities($pun['published_date'])?>
                            <p>

                                <!--Delete post-->
                                <button type="button" class="btn btn-danger float-right" name="deleteBtn" data-toggle="modal" data-target="#deleteModal">Delete</button>

                                <!--Update post-->
                                <button type="button" class="btn btn-warning float-right" data-toggle="modal" data-target="#exampleModal" data-title="<?=htmlentities($pun['title'])?>" data-author="<?=htmlentities($pun['author'])?>" data-content="<?=htmlentities($pun['content'])?>" data-id="<?=htmlentities($pun['id'])?>">Update</button>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>

    <!--update modal-->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="background-color:lightblue;">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Update Blog</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <div class="form-group">

                            <label for="recipient-name" class="col-form-label">Update Title: </label>
                            <input type="text" class="form-control" name="title" for="recipient-name">

                            <label for="recipient-name" class="col-form-label">Update content: </label>
                            <textarea class="form-control" name="content" for="recipient-name" rows="6"></textarea>

                            <label for="recipient-name" class="col-form-label">Update author: </label>
                            <input type="text" class="form-control" name="author" for="recipient-name">

                            <input type="hidden" class="form-control" name="id">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <input type="submit" name="updateBtn" value="Update" class="btn btn-success">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--delete modal-->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <form action="" method="POST">
                    <div class="modal-body">
                        <div class="form-group">

                            <label>Do you want to delete? </label>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <form action="" method="POST" class="float-right">
                            <input type="hidden" name="hidId" value="<?=$pun['id']?>">
                            <button type="button" class="btn btn-dark" data-dismiss="modal">No</button>
                            <input type="submit" name="deleteBtn" value="Yes" class="btn btn-dark">
                        </form>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>


    <script>
        $('#exampleModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var title = button.data('title'); // Extract info from data-* attributes
            var content = button.data('content'); // Extract info from data-* attributes
            var author = button.data('author'); // Extract info from data-* attributes
            var id = button.data('id'); // Extract info from data-* attributes

            var modal = $(this);
            modal.find(".modal-body input[name='title']").val(title);
            modal.find(".modal-body textarea[name='content']").val(content);
            modal.find(".modal-body input[name='author']").val(author);
            modal.find(".modal-body input[name='id']").val(id);
        });

    </script>

    <?php include('footer.php'); ?>
