<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');
header('Content-type: application/json');


require 'connect.php';
require 'function.php';












class crud {
    
    public function crud_operation(&$connect) {
        global $_SERVER;
        global $_GET;
        $method = $_SERVER['REQUEST_METHOD'];

        $tasks = mysqli_query($connect, "SELECT * FROM `tasks`");
        $q = $_GET['q'];
        $params = explode('/', $q);
        $type = $params[0];

        if (isset($params[1]) === True) {
            $id = $params[1];

        }

        if ($method === 'GET') {

            if ($type === 'tasks') {


                if (isset($id)) {
                    getTask($connect, $id);

                } else {
                    getTasks($connect); //все Записи
                }




            }



        } elseif ($method === 'POST') {

            if ($type === 'tasks') {
                addTask($connect, $_POST);
            }

        } elseif ($method === 'PATCH') {

            if ($type === 'tasks') {
                if (isset($id)) {
                    $data = file_get_contents('php://input');
                    $data = json_decode(($data), true);
                    updateTask($connect, $id, $data);
                }



            }

        } elseif ($method === 'DELETE') {

                if ($type === 'tasks') {
                if (isset($id)) {

                    deleteTask($connect, $id);
                }



            }




        }





    }
}



?>

