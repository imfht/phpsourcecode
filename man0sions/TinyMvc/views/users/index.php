<a href="<?php echo $this->createUrl('/users/create')?>" class="btn btn-primary">创建</a>
<br/>
<br/>
<table class="table table-hover">
    <tbody>
    <tr>

        <th>ID</th>
        <th>用户名</th>
        <th>密码</th>
        <th>操作</th>
    </tr>
    <?php foreach ($users as $key => $item): ?>
        <?php $user = $item->getAttributes(); ?>
        <tr>
            <td><?php echo $user['id'] ?></td>
            <td><a href="<?php echo $this->createUrl("/users/",['id'=>$user['id']])?>"><?php echo $user['name'] ?></a></td>
            <td><?php echo $user['password'] ?></td>
            <td>
                <div class="btn-group">
                    <a href="<?php echo $this->createUrl("/users/update/",['id'=>$user['id']])?>"
                       class="btn btn-xs btn-primary">更改</a>
                    <a href="<?php echo $this->createUrl("/users/delete/",['id'=>$user['id']])?>"
                       class="btn btn-xs btn-success delete_item">删除</a>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>


    </tbody>
</table>


<div class="page">
    <ul class="pagination">
        <li class="first"><a href="<?php echo $this->createUrl('/users?page=1')?>">&lt;&lt;</a></li>

        <?php for($i=1;$i<=$pages['page_num'];$i++):?>
        <li   <?php if($pages['page']==$i):?>class="active"<?php endif;?>><a href="<?php echo $this->createUrl('/users?page='.$i)?>"><?php echo $i?></a></li>
        <?php endfor;?>

        <li class="last"><a href="<?php echo $this->createUrl('/users?page='.$pages['page_num'])?>">&gt;&gt;</a></li>
    </ul>
    <div>共 <?php echo $pages['page_num']?> 页,  <?php echo $pages['count']?> 条.</div>
</div>