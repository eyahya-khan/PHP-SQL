<?php
// database connection
require('dbconnect.php'); 

$pageTitle = 'All blog posts';

//show all posts
try {
	
	$stmt = $dbconnect->query("SELECT * FROM posts");
	$posts = $stmt->fetchAll(); 
} catch (\PDOException $e) {
	throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

?>

<?php include('head.php');?>

<body>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <a href="login.php" style="float:right;">Log in</a>
            </div>
        </div>

        <div class="row">
            <div class="col-12" id="bkgdImg">
                <img src="img/bookbackground.jpg">
            </div>

            <div class="offset-1 col-10">
                <h1>Collection of Book Blog</h1>

                <?php foreach ($posts as $key => $post) { ?>
                <h3 style="background-color:Tomato;text-align:center;">
                    <?php echo $post['title'] ?>
                </h3>
                <p style="text-align:center;">Posted on: <?php echo $post['published_date'] ?></p>
                <!--Counting the first sentence-->
                <?php
                    $pos = strpos($post['content'], '.');
                    $firstSentence = substr($post['content'], 0, max($pos+1, 40));
                    echo $firstSentence;
                    ?>
                <!--sending id to individual.php page for fetching specific data-->
                <br><a href="individual.php?hidID=<?=$post['id']?>">read more</a>
                <p style="text-align:">Written by: <strong><?php echo $post['author'] ?></strong></p>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
