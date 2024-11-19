<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // When the user selects the difficulty level
    if (isset($_POST['level'])) {
        $level = $_POST['level'];
        $_SESSION['level'] = $level;

        // Set initial chances
        $_SESSION['chances'] = 5;

        // Generate a random math problem based on the difficulty level
        switch ($level) {
            case 'easy':
                $min = 1;
                $max = 10;
                break;
            case 'medium':
                $min = 10;
                $max = 20;
                break;
            case 'hard':
                $min = 20;
                $max = 50;
                break;
        }

        // Generate numbers and store them in the session
        $num1 = rand($min, $max);
        $num2 = rand($min, $max);
        $_SESSION['num1'] = $num1;
        $_SESSION['num2'] = $num2;
        $_SESSION['answer'] = $num1 + $num2;
    }

    // When the user submits an answer
    if (isset($_POST['user_answer'])) {
        $user_answer = $_POST['user_answer'];
        
        // Reduce chances
        $_SESSION['chances']--;

        // Provide feedback
        if ($user_answer < $_SESSION['answer']) {
            $message = "Your answer is too low! Try again!";
        } elseif ($user_answer > $_SESSION['answer']) {
            $message = "Your answer is too high! Try again!";
        } else {
            $message = "Correct! Well done!";
            $_SESSION['chances'] = 0; // End the game if correct
        }

        // Check if chances are over
        if ($_SESSION['chances'] == 0 && $user_answer != $_SESSION['answer']) {
            $message = "Game Over! The correct answer was " . $_SESSION['answer'] . ". Try again!";
        }
    }

    // Reset the game when Back button is pressed
    if (isset($_POST['back'])) {
        session_unset();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
} else {
    $message = "";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Guessing Game</title>
</head>
<body>

<h1>Math Guessing Game</h1>

<?php if (empty($_SESSION['level'])): ?>
    <!-- Difficulty selection form -->
    <form method="post">
        <label for="level">Choose Difficulty Level:</label><br>
        <input type="radio" name="level" value="easy"> Easy (1-10)<br>
        <input type="radio" name="level" value="medium"> Medium (10-20)<br>
        <input type="radio" name="level" value="hard"> Hard (20-50)<br><br>
        <input type="submit" value="Start Game">
    </form>
<?php else: ?>
    <h2>Level: <?php echo ucfirst($_SESSION['level']); ?></h2>

    <?php if (isset($_SESSION['num1']) && isset($_SESSION['num2'])): ?>
        <p>Question: <?php echo $_SESSION['num1'] . " + " . $_SESSION['num2'] . " = ?"; ?></p>
    <?php endif; ?>

    <?php if ($_SESSION['chances'] > 0): ?>
        <form method="post">
            <label for="user_answer">Your Answer:</label>
            <input type="number" name="user_answer" required><br><br>
            <input type="submit" value="Submit Answer">
        </form>
    <?php else: ?>
        <p>You can restart the game by going back to the difficulty selection!</p>
    <?php endif; ?>

    <p>Chances left: <?php echo $_SESSION['chances']; ?></p>

    <?php if (!empty($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Back button -->
    <form method="post">
        <input type="submit" name="back" value="Back to Difficulty Selection">
    </form>
<?php endif; ?>

</body>
</html>