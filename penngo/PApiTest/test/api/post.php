<?php
$id = $_POST['id'];
$data = array('state'=>'error');
if(intval($id) == 1){
    $data['state'] = 'success';
    $data['data'] = array(
        'a',
        'b',
        'c'
    );
}
echo json_encode($data);
?>