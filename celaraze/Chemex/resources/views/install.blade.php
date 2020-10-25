<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
      integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
        crossorigin="anonymous"></script>

<div class="container">
    <div class="card" style="width: 400px;margin: 100px auto 0 auto;padding: 10px">
        <div class="form-group">
            <label for="db_host">数据库地址</label>
            <input type="text" class="form-control" id="db_host">
        </div>
        <div class="form-group">
            <label for="db_port">数据库端口</label>
            <input type="number" class="form-control" id="db_port">
        </div>
        <div class="form-group">
            <label for="db_username">数据库用户名</label>
            <input type="text" class="form-control" id="db_username">
        </div>
        <div class="form-group">
            <label for="db_password">数据库密码</label>
            <input type="password" class="form-control" id="db_password">
        </div>
        <div class="form-group">
            <label for="db_name">数据库名称</label>
            <input type="text" class="form-control" id="db_name">
        </div>
        <div class="form-group">
            <label for="app_url">应用地址</label>
            <input type="text" class="form-control" id="app_url">
        </div>
        <button type="submit" class="btn btn-primary" onclick="initDB()">安装</button>
    </div>
</div>

<script>
    function initDB() {
        $.ajax({
            type: 'post',
            url: '/api/install/init_db',
            success: function (res) {
                console.log(res);
            }
        })
    }
</script>
