<?php

function getId($id) {

    if ($id === '') {
        http_response_code(400);
        echo json_encode(["error" => "Все поля обязательны."]);
        exit;
    } elseif (!filter_var($id, FILTER_VALIDATE_INT)) {

        http_response_code(400);
        echo json_encode(["error" => "ID должен быть целым числом."]);
        exit;

    } 


    $id = (int)$id;
    return $id;


}


function valid_addTasks($tittle, $description, $status){

    if ($tittle === '' || $description === '' || $status === '') {
        http_response_code(400);
        echo json_encode(["error" => "Все поля обязательны."]);
        exit;
    }



    if (strlen($tittle) > 255) {
        http_response_code(400);
        echo json_encode(["error" => "Title слишком длинный (макс. 255 символов)."]);
        exit;
    }

    if (strlen($status) > 40) {
        http_response_code(400);
        echo json_encode(["error" => "status слишком длинный (макс. 255 символов)."]);
        exit;
    }

    $tittle = htmlspecialchars($tittle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $description = htmlspecialchars($description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $status = htmlspecialchars($status, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    return [$tittle, $description, $status];
}


function valid_updateTask($tittle, $description, $status, $id){

    if ($tittle === '' and $description === '' and $status === '') {
        http_response_code(204);
        echo json_encode(["error" => "Нет изменений"]);
        exit;
    }

    $update = "UPDATE `tasks` SET";
    if ($tittle != '') {
        
        if (strlen($tittle) > 255) {
            http_response_code(400);
            echo json_encode(["error" => "Title слишком длинный (макс. 255 символов)."]);
            exit;
        }

        $tittle = htmlspecialchars($tittle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $update .= " `tittle` = '$tittle',";
    }
    if ($description != '') {
        $description = htmlspecialchars($description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $update .= " `description` = '$description',";


    }

    if ($status!= '') {
        if (strlen($status) > 40) {
            http_response_code(400);
            echo json_encode(["error" => "status слишком длинный (макс. 255 символов)."]);
            exit;
        }
        $status = htmlspecialchars($status, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $update .= " `status` = '$status',";
    }

    $update = substr($update, 0, -1);
    $update .= " WHERE `tasks`.`id` = '$id'";

    return $update;
    
    

}




function getTasks ($connect){
    
    $tasks = mysqli_query($connect, "SELECT * FROM `tasks`");

    $tasksList = [];

    while($task = mysqli_fetch_assoc($tasks)) {
    $tasksList[] = $task;
}

echo json_encode($tasksList);

}


function getTask ($connect, $id) {
    $task = mysqli_query($connect, "SELECT * FROM `tasks` WHERE `id` = '$id'");
    $id = getId($id);



    

    if (mysqli_num_rows($task) === 0) {
        http_response_code(404);
        $res = [
            "status" => false,
            "message" => "Task not found"
        ];
        echo json_encode($res);

    } else {

        $task = mysqli_fetch_assoc($task);
        echo json_encode($task);

    }


}


function addTask ($connect, $data) {


    $tittle = $data['tittle'];
    $description = $data['description'];
    $status = $data['status'];


    $result = valid_addTasks($tittle, $description, $status);
    $tittle = $result[0];
    $description = $result[1];
    $status = $result[2];








    mysqli_query($connect, "INSERT INTO `tasks` (`id`, `tittle`, `description`, `status`) VALUES (NULL, '$tittle', '$description', '$status')");

    http_response_code(201);

    $res = [
        "status" => true,
        "task_id" => mysqli_insert_id($connect)
    ];

    echo json_encode($res);


}



function updateTask($connect, $id, $data) {

    $tittle = $data['tittle'];
    $description = $data['description'];
    $status = $data['status'];
   


    $id = getId($id);
    $update = valid_updateTask($tittle, $description, $status, $id);
    mysqli_query($connect, $update);


    http_response_code(200);

    $res = [
        "status" => true,
        "message" => "Task is updated"
    ];

    echo json_encode($res);


}


function deleteTask($connect, $id) {


    $id = getId($id);

    mysqli_query($connect, "DELETE FROM tasks WHERE `tasks`.`id` = '$id'");

    http_response_code(200);
    $res = [
        "status" => true,
        "message" => "Task is deleted"
    ];

    echo json_encode($res);


}







?>