<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Book</title>
</head>
<body>
    <h2>Add Contact</h2>

    <form action="restPhoneBook.php" method="post">
        <label for="firstname">First Name:</label>
        <input type="text" name="firstname" required><br>

        <label for="lastname">Last Name:</label>
        <input type="text" name="lastname" required><br>

        <label for="phonenumber">Phone Number:</label>
        <input type="text" name="phonenumber" required><br>

        <input type="submit" value="Add Contact">
    </form>

    <p><a href="viewContacts.php">View contacts</a></p>
    <p><a href="insertDummyContacts.php">Insert Dummy Contacts</a></p>
</body>
</html>