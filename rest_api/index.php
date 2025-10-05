
<?php




require 'connect.php';
require 'lib.php';



$form = new crud();
echo $form->crud_operation($connect);




?>

