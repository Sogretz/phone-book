<?php

namespace PhoneBookModel;

use mysqli;
use mysqli_result;

class PhoneBookModel
{
    /** @var string $firstName */
    private $firstName;
    /** @var string $firstNameT9 */
    private $firstNameT9;
    /** @var string $lastName */
    private $lastName;
    /** @var string $lastNameT9 */
    private $lastNameT9;
    /** @var string $phoneNumber */
    private $phoneNumber;
    /** @var string[] $t9Mapping */
    private $t9Mapping = [
        'a' => '2',
        'b' => '2',
        'c' => '2',
        'd' => '3',
        'e' => '3',
        'f' => '3',
        'g' => '4',
        'h' => '4',
        'i' => '4',
        'j' => '5',
        'k' => '5',
        'l' => '5',
        'm' => '6',
        'n' => '6',
        'o' => '6',
        'p' => '7',
        'q' => '7',
        'r' => '7',
        's' => '7',
        't' => '8',
        'u' => '8',
        'v' => '8',
        'w' => '9',
        'x' => '9',
        'y' => '9',
        'z' => '9',
    ];

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * I decided to convert the firstName into a T9 string for an easier and more performant search through massive data
     * @param string $firstName
     * @return PhoneBookModel
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        $this->setFirstNameT9($firstName);
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstNameT9()
    {
        return $this->firstNameT9;
    }

    /**
     * @param string $firstName
     * @return PhoneBookModel
     */
    public function setFirstNameT9($firstName)
    {
        $this->firstNameT9 = $this->mapToT9($firstName);
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * I decided to convert the lastName into a T9 string for an easier and more performant search through massive data
     * @param string $lastName
     * @return PhoneBookModel
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        $this->setLastNameT9($lastName);
        return $this;
    }

    /**
     * @return string
     */
    public function getLastNameT9()
    {
        return $this->lastNameT9;
    }

    /**
     * @param string $lastName
     * @return PhoneBookModel
     */
    public function setLastNameT9($lastName)
    {
        $this->lastNameT9 = $this->mapToT9($lastName);
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     * @return PhoneBookModel
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getT9Mapping()
    {
        return $this->t9Mapping;
    }

    /**
     * @param string $string
     * @return string
     */
    private function mapToT9($string)
    {
        // I mapped the characters and numbers like they are on T9
        $mapping = $this->getT9Mapping();
        $stringToArray = str_split(strtolower($string));
        $resultT9 = '';
        /** here I loop through the given string that I converted into an array to convert each character into the matching number
         *
         */
        foreach ($stringToArray as $character) {
            $resultT9 .= $mapping[$character] ?: '0';
        }
        return $resultT9;
    }

    /**
     * @param mysqli_result $contacts
     * @param int $page
     * @param int $totalPages
     * @param string $search
     * @return string
     */
    public function buildContactsTable($contacts, $page, $totalPages,$search)
    {
        if (mysqli_num_rows($contacts) > 0) {
            $tableString = "
                <table border='1'>
                    <tr>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>Phone-Number</th>
                    </tr>
            ";

            while ($contact = $contacts->fetch_assoc()) {
                $tableString .= "
                    <tr>
                        <th>{$contact['firstname']}</th>
                        <th>{$contact['lastname']}</th>
                        <th>{$contact['phone_number']}</th>
                    </tr>
                ";
            }

            // here I started adding the pagination to the output.
            $tableString .= "
                </table>
                <div>
                <a href='restPhoneBook.php?page=1&search=$search' style='text-decoration:none'>First</a><span> - </span>
            ";

            if ($page > 1) {
                $tableString .= "<a href='restPhoneBook.php?page=" . ($page - 1) . "&search=$search' style='text-decoration:none'><<</a><span> - </span>";
            }

            /** here I calculate the last page that should be displayed to prevent displaying more than 3 numbers in
             * the pagination below the table
             */
            $maxPagesDisplay = 3;
            if (($page + $maxPagesDisplay) <= $totalPages) {
                $lastPageToDisplay = $page + $maxPagesDisplay;
            } else {
                $lastPageToDisplay = $totalPages;
            }
            for ($i = $page; $i <= $lastPageToDisplay; $i++) {
                if ($i !== $page) {
                    $tableString .= "<a href='restPhoneBook.php?page=$i&search=$search' style='text-decoration:none'>" . $i . "</a>";
                } else {
                    $tableString .= "<a href='restPhoneBook.php?page=$i&search=$search'>" . $i . "</a>";
                }
                if ($i < $totalPages) {
                    $tableString .= "<span> - </span>";
                }
            }

            if ($page < $totalPages) {
                $tableString .= "<a href='restPhoneBook.php?page=" . ($page + 1) . "&search=$search' style='text-decoration:none'>>></a>";
            }

            $tableString .= "<span> - </span><a href='restPhoneBook.php?page=$totalPages&search=$search' style='text-decoration:none'>Last</a>";
            $tableString .= "
                </div>
                <p><a href='viewContacts.php'>View contacts</a></p>
                <p><a href='phoneBook.php'>Add a new contact</a></p>
            ";

            return $tableString;
        } else {
            return "No contacts given.";
        }
    }

    /**
     * @param mysqli $connection
     * @return string
     */
    public function addContact($connection)
    {
        $statement = $connection->prepare("INSERT INTO phone_numbers (firstname, firstname_t9, lastname, lastname_t9, phone_number) VALUES (?, ?, ?, ?, ?)");
        /** @var string $firstname */
        $firstname = $this->getFirstName();
        /** @var string $firstnameT9 */
        $firstnameT9 = $this->getFirstNameT9();
        /** @var string $lastname */
        $lastname = $this->getLastName();
        /** @var string $lastnameT9 */
        $lastnameT9 = $this->getLastNameT9();
        /** @var string $phoneNumber */
        $phoneNumber = $this->getPhoneNumber();
        $statement->bind_param("sssss", $firstname, $firstnameT9, $lastname, $lastnameT9, $phoneNumber);
        if ($statement->execute()) {
            return "Contact added successfully!";
        } else {
            return "Error: " . $statement->error;
        }
    }

    /**
     * @param mysqli $connection
     * @param string $search
     * @param int $offset
     * @param int $contactsPerPage
     * @return array
     */
    public function search($connection, $search, $offset, $contactsPerPage)
    {
        $query = "SELECT firstname, lastname, phone_number FROM phone_numbers WHERE firstname_t9 LIKE '$search%' OR lastname_t9 LIKE '$search%'";
        $totalContacts = mysqli_num_rows($connection->query($query));
        if (isset($offset, $contactsPerPage)) {
            $query .= " LIMIT $offset, $contactsPerPage";
        }
        $searchResult = $connection->query($query);

        /** here I returned the contacts and totalPages as an array to prevent writing the same query twice in different functions.
         * So I first count the total found contacts and then add the limit and offset for the pagination and execute it again.
         */
        return [
            "contacts" => $searchResult,
            "totalPages" => ceil($totalContacts / $contactsPerPage)
        ];
    }
}