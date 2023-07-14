<?php

    include_once("conn.php");

    $method = $_SERVER["REQUEST_METHOD"];

    if ($method === 'GET') {
        
        $bordasQuery = $conn->query("SELECT * FROM bordas;");

        $bordas = $bordasQuery->fetchAll();

        $massasQuery = $conn->query("SELECT * FROM massas;");

        $massas = $massasQuery->fetchAll();

        $saboresQuery = $conn->query("SELECT * FROM sabores;");

        $sabores = $saboresQuery->fetchAll();

    } else if ($method === "POST") {

        $data = $_POST;

        $borda = $data['borda'];
        $massa = $data['massa'];
        $sabores = $data['sabores'];

        if (count($sabores) > 3) {

            $_SESSION['msg'] = 'Selecione no máximo 3 sabores!';
            $_SESSION['status'] = 'warning';

        } else {
            
            $stmt = $conn->prepare("INSERT INTO pizza (borda_id, massa_id) VALUES (:borda, :massa)");

            $stmt->bindParam(":borda", $borda, PDO::PARAM_INT);
            $stmt->bindParam(":massa", $massa, PDO::PARAM_INT);

            $stmt->execute();

            $pizza_id = $conn->lastInsertId();

            $stmt = $conn->prepare("INSERT INTO sabores_pizza (pizza_id, sabor_id) VALUES (:pizza, :sabor)");

            foreach ($sabores as $sabor) {

                $stmt->bindParam(":pizza", $pizza_id, PDO::PARAM_INT);
                $stmt->bindParam(":sabor", $sabor, PDO::PARAM_INT);
   
                $stmt->execute();
            }


            $stmt = $conn->prepare("INSERT INTO pedidos (status_id, pizza_id) VALUES (:status, :pizza)");
            $status = 1;
            $stmt->bindParam(":status", $status, PDO::PARAM_INT);
            $stmt->bindParam(":pizza", $pizza_id, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['msg'] = 'Pedido realizado com sucesso!';
            $_SESSION['status'] = 'success';

        }

        header('Location: ..');
        
    }
?>