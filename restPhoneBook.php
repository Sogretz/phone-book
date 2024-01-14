<?php

include "PhoneBookModel.php";
include "Database.php";

use PhoneBookModel\PhoneBookModel;
use Database\Database;

/** @var PhoneBookModel $phoneBook */
$phoneBook = new PhoneBookModel();
/** @var Database $database */
$database = new Database();
/** @var mysqli $connection */
$connection = $database->connect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['firstname'])) {
        $phoneBook->setFirstName($_POST['firstname']);
    }
    if (isset($_POST['lastname'])) {
        $phoneBook->setLastName($_POST['lastname']);
    }
    if (isset($_POST['phonenumber'])) {
        $phoneBook->setPhoneNumber($_POST['phonenumber']);
    }
    echo $phoneBook->addContact($connection);
    $connection->close();
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['search']) || isset($_GET['page'])) {
        $search = $_GET['search'];
        // I added a pagination and therefor I set a maximum of 25 contacts per page
        $contactsPerPage = 25;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        // here I calculate the offset for the pagination
        $offset = ($page - 1) * $contactsPerPage;
        $contactsArray = $phoneBook->search($connection, $search, $offset, $contactsPerPage);
        echo $phoneBook->buildContactsTable($contactsArray['contacts'], $page, $contactsArray['totalPages'], $search);
    } else {
        echo "No search-parameter given.";
    }
}
