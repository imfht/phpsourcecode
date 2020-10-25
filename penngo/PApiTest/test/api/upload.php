<?php
$id = $_POST['id'];
$file = $_FILES['img'];
$data = array('state'=>'error');
if(intval($id) == 1){
    $data['state'] = 'success';
    $data['post'] = $_POST;
    $data['image'] = $_FILES;
    $data['data'] = array(
        'a',
        'b',
        'c'
    );
}
echo json_encode($data);
?>