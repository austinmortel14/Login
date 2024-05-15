<?php
session_start();

$foodItems = array(
  "Pizza" => 300,
  "Burger" => 100,
  "Fries" => 50,
  "Salad" => 75
);

$users = array(
  "user1" => "password1",
  "user2" => "password2"
);

if (file_exists('users.json')) {
  $users = json_decode(file_get_contents('users.json'), true);
}

$message = "";

if (isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  if (array_key_exists($username, $users) && $users[$username] == $password) {
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $username;
  } else {
    $message = "Invalid username or password.";
  }
}

if (isset($_POST['logout'])) {
  session_unset();
  session_destroy();
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}


if (isset($_POST['register'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  if (!array_key_exists($username, $users)) {
    $users[$username] = $password;

    file_put_contents('users.json', json_encode($users));
    $message = "Registration successful. Please log in.";
  } else {
    $message = "Username already exists.";
  }
}

if (isset($_POST['submit'])) {
  if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $selectedItem = $_POST['food_item'];
    $quantity = (int)$_POST['quantity'];
    $cashPaid = (float)$_POST['cash_paid'];

    if (!empty($selectedItem) && $quantity > 0 && $cashPaid >= 0) {
      $totalCost = $foodItems[$selectedItem] * $quantity;

      if ($cashPaid >= $totalCost) {
        $change = $cashPaid - $totalCost;
        $message = "Your order for " . $quantity . " " . $selectedItem . " has been placed! Change due: PHP" . number_format($change, 2);
      } else {
        $message = "Insufficient cash. Total cost is PHP" . number_format($totalCost, 2) . ". Please enter a higher amount.";
      }
    } else {
      $message = "Please select a food item, enter a valid quantity, and cash amount.";
    }
  } else {
    $message = "Please log in to place an order.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Management System</title>
  <style>
    h1 {
      color: red;
      text-align: center;
    }
    div {
      color: orange;
      text-align: center;
    }
    body {
      margin: 1;
      background-color: whitesmoke;
      text-align: left;
    }
  </style>
</head>
<body>
  <h1>WcDonalds</h1>
  
  <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) : ?>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    <p>Hello welcome to WcDonalds, here is the menu :)</p>
    <p>Pizza - 300 PHP</p>
    <p>Burger - 100 PHP</p>
    <p>Fries - 50 PHP</p>
    <p>Salad - 75 PHP</p>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <label for="food_item">Food Item:</label>
      <select name="food_item" id="food_item">
        <?php foreach ($foodItems as $item => $price) : ?>
          <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
        <?php endforeach; ?>
      </select><br>
      <label for="quantity">Quantity:</label>
      <input type="number" name="quantity" id="quantity" min="1" required><br>
      <label for="cash_paid">Cash Paid:</label>
      <input type="number" name="cash_paid" id="cash_paid" min="0" step="0.01" required><br><br>
      <input type="submit" name="submit" value="Order Food">
    </form>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <input type="submit" name="logout" value="Logout">
    </form>
  
  <?php else : ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <h2>Login</h2>
      <label for="username">Username:</label>
      <input type="text" name="username" id="username" required><br>
      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required><br>
      <input type="submit" name="login" value="Login">
    </form>
    
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <h2>Register</h2>
      <label for="username">Username:</label>
      <input type="text" name="username" id="username" required><br>
      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required><br>
      <input type="submit" name="register" value="Register">
    </form>
  <?php endif; ?>

  <?php if (!empty($message)) : ?>
    <p><?php echo $message; ?></p>
  <?php endif; ?>

</body>
</html>
