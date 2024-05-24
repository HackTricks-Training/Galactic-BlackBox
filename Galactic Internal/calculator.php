<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: index.php");
    exit();
}

$result = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $expression = $_POST['expression'];
    // Vulnerable eval injection
    eval("\$result = $expression;");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Human-Galactic Internal Calculator</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .calculator {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            max-width: 250px;
            margin: auto;
        }
        .calculator input[type="text"] {
            grid-column: span 4;
            text-align: right;
            padding: 10px;
            font-size: 1.2em;
        }
        .calculator button {
            padding: 20px;
            font-size: 1.2em;
        }
        .calculator button.equal {
            grid-column: span 4;
            background-color: #4CAF50;
            color: white;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
        }
        .result {
            margin-top: 20px;
            font-size: 1.5em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Human-Galactic Internal Calculator</h2>
        <form method="post">
            <div class="calculator">
                <input type="text" id="expression" name="expression" required readonly>
                <button type="button" onclick="appendToExpression('1')">1</button>
                <button type="button" onclick="appendToExpression('2')">2</button>
                <button type="button" onclick="appendToExpression('3')">3</button>
                <button type="button" onclick="appendToExpression('+')">+</button>
                <button type="button" onclick="appendToExpression('4')">4</button>
                <button type="button" onclick="appendToExpression('5')">5</button>
                <button type="button" onclick="appendToExpression('6')">6</button>
                <button type="button" onclick="appendToExpression('-')">-</button>
                <button type="button" onclick="appendToExpression('7')">7</button>
                <button type="button" onclick="appendToExpression('8')">8</button>
                <button type="button" onclick="appendToExpression('9')">9</button>
                <button type="button" onclick="appendToExpression('*')">*</button>
                <button type="button" onclick="appendToExpression('0')">0</button>
                <button type="button" onclick="appendToExpression('.')">.</button>
                <button type="button" onclick="clearExpression()">C</button>
                <button type="button" onclick="appendToExpression('/')">/</button>
                <button type="submit" class="equal">=</button>
            </div>
        </form>
        <div class="result"><?php echo htmlspecialchars($result); ?></div>
    </div>
    <script>
        function appendToExpression(value) {
            document.getElementById('expression').value += value;
        }

        function clearExpression() {
            document.getElementById('expression').value = '';
        }
    </script>
</body>
</html>
