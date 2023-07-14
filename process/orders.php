<?php

    include_once("conn.php");

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        
        $pedidosQuery = $conn->query("SELECT * FROM pedidos;");

        $pedidos = $pedidosQuery->fetchAll();

        $pizzas = [];

        foreach ($pedidos as $pedido) {

            $pizza = [];
            $pizza['id'] = $pedido['pizza_id'];

            $pizzaQuery = $conn->prepare("SELECT * FROM pizza WHERE id = :pizza_id");
            
            $pizzaQuery->bindParam(':pizza_id', $pizza['id']);
            
            $pizzaQuery->execute();
            
            $pizzaData = $pizzaQuery->fetch(PDO::FETCH_ASSOC);

            
            //Resgatando a borda da pizza
            
            $bordaQuery = $conn->prepare("SELECT * FROM bordas WHERE id = :borda_id");
            
            $bordaQuery->bindParam(':borda_id', $pizzaData['borda_id']);
            
            $bordaQuery->execute();
            
            $borda = $bordaQuery->fetch(PDO::FETCH_ASSOC);
            
            $pizza['borda'] = $borda['tipo'];
            
            
            //Resgatando a massa da pizza
            
            $massaQuery = $conn->prepare("SELECT * FROM massas WHERE id = :massa_id");
            
            $massaQuery->bindParam(':massa_id', $pizzaData['massa_id']);
            
            $massaQuery->execute();
            
            $massa = $massaQuery->fetch(PDO::FETCH_ASSOC);
            
            $pizza['massa'] = $massa['tipo'];
            
            
            //Resgatando os sabores da pizza
            
            $saboresQuery = $conn->prepare("SELECT * FROM sabores_pizza WHERE pizza_id = :pizza_id");
            
            $saboresQuery->bindParam(':pizza_id', $pizza['id']);
            
            $saboresQuery->execute();
            
            $sabores = $saboresQuery->fetchAll(PDO::FETCH_ASSOC);
            
            //Resgatando o nome dos sabores
            $saboresDaPizza = [];
            
            $saborQuery = $conn->prepare('SELECT * FROM sabores WHERE id = :sabor_id');
            
            foreach ($sabores as $sabor) {
                
                $saborQuery->bindParam('sabor_id', $sabor['sabor_id']);
                
                $saborQuery->execute();
                
                $saborPizza = $saborQuery->fetch(PDO::FETCH_ASSOC);
                
                array_push($saboresDaPizza, $saborPizza['tipo']);
                
            }

            $pizza['sabores'] = $saboresDaPizza;
            $pizza['status'] = $pedido['status_id'];

            array_push($pizzas, $pizza);

        }

        // var_dump($pizzas);
        // print_r($pizzas);

        $statusQuery = $conn->query('SELECT * FROM status;');
        $status = $statusQuery->fetchAll();
        
    } else if ($method === 'POST') {

        $type = $_POST['type'];

        if ($type === 'delete') {
            
            $pizzaId = $_POST['id'];
            
            $deleteQuery = $conn->prepare('DELETE FROM pedidos WHERE pizza_id = :pizza_id');

            $deleteQuery->bindParam(':pizza_id', $pizzaId);
            
            $deleteQuery->execute();

            $_SESSION['msg'] = 'Pedido deletado com sucesso!';
            $_SESSION['status'] = 'success';

        } else if ($type === 'update') { 
            $pizzaId = $_POST['id'];
            $statusId = $_POST['status'];
            
            $updateQuery = $conn->prepare('UPDATE pedidos SET status_id = :status_id WHERE pizza_id = :pizza_id');

            $updateQuery->bindParam(':pizza_id', $pizzaId, PDO::PARAM_INT);
            $updateQuery->bindParam(':status_id', $statusId, PDO::PARAM_INT);

            $updateQuery->execute();

            $_SESSION['msg'] = 'Status de pedido atualizado com sucesso!';
            $_SESSION['status'] = 'success';
        }

        header('Location: ../dashboard.php');

    }

?>