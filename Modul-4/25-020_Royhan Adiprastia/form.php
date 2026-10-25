<?php
$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'validate.inc';

    validateForm($errors, $_POST);

    if ($errors) {
        echo '<h2>Invalid, correct the following errors:</h2>';

        foreach ($errors as $field => $error) {
            echo htmlspecialchars($field) . ' '
                . htmlspecialchars($error) . '<br>';
        }

        $values = $_POST;
        include 'form.inc';
    } else {
        echo 'Form submitted successfully with no errors';
    }
} else {
    $values = array();
    include 'form.inc';
}
?>