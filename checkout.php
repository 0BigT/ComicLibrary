<?php
include_once("connect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>
<body>
    <h2>Checkout</h2>
    <p>Thank you for your purchase.</p>
    <p>Click <a href='library.php'>here</a> to view your books.</p>

    <?php
    // check if user is logged in before checkout
    if (isset($_POST['buy'])) {
        session_start();
        if (!isset($_SESSION['id'])) {
            echo "<script>alert('You must create an account to buy books');
            window.location.href = 'register.php';</script>";
        }
    } else {
        // redirect to cart page if cart post data is empty
        header('Location: cart.php');
    }

    // add books to users library
    $query = "SELECT * FROM library WHERE user_id = :user_id";
    $statement = $conn->prepare($query);
    $statement->bindValue(':user_id', $_SESSION['id']);
    $statement->execute();
    $library = $statement->fetchAll();
    $statement->closeCursor();

    // check if books are already in library
    foreach ($_POST['id'] as $book) {
        $inLibrary = false;
        foreach ($library as $item) {
            if ($item['book_id'] == $book) {
                $inLibrary = true;
            }
        }
        // if not in library add to library
        if (!$inLibrary) {
            $query = "INSERT INTO library (user_id, book_id) VALUES (:user_id, :book_id)";
            $statement = $conn->prepare($query);
            $statement->bindValue(':user_id', $_SESSION['id']);
            $statement->bindValue(':book_id', $book);
            $statement->execute();
            $statement->closeCursor();
        }
    }
    ?>

    <!-- delete cart data -->
    <script>localStorage.removeItem('cart');</script>
</body>
</html>