<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Phone Book</title>
    </head>
    <body>
        <h2>Search</h2>

        <form action="restPhoneBook.php" method="get">
            <label for="search">Search:</label>
            <input type="number" name="search" required><br>

            <input type="submit" value="Search">
        </form>

        <p><a href="phoneBook.php">Add a new contact</a></p>
    </body>
</html>