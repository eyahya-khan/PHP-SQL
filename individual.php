<?php
// database connection
require('dbconnect.php'); 

$pageTitle = 'Individual blog post';

//show specific data
try {
	$stmt = $dbconnect->prepare("SELECT title,content,author,published_date FROM posts
    WHERE id = :id");
    $stmt->bindValue(':id',$_GET['hidID']);
    $stmt->execute();
	$post = $stmt->fetch(); 
    
} catch (\PDOException $e) {
	throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

?>

<?php include('head.php'); ?>

<body>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <a href="login.php" style="float:right;">Log in</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12" id="bkgdImg">
                <img src="img/bookbackground1.jpg">
            </div>
            <div class="offset-1 col-10">

                <h1>Blog deatils</h1>

                <h3 style="background-color:lightblue;">
                    <?php echo $post['title'] ?>
                </h3>
                <p style="text-align:justify;text-indent: 50px;">
                    <?php
                    echo $post['content'];
                    ?>
                </p>
                <h4 style="text-align:right;"><?php echo $post['author'] ?></h4>
                <p style="text-align:right;"><?php echo $post['published_date'] ?></p>

            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
