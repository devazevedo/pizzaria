<?php

    session_start();

    $host = 'localhost';
    $user = 'devazevedo';
    $pass = 'Av@i1923';
    $db = 'pizzaria_do_joao';

    try {
        $conn = new PDO("mysql:host={$host};dbname={$db}", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage() . "<br/>";
        die();
    }

?>
