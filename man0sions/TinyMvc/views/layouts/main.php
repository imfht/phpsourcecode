<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $this->page_title?></title>

    <!-- Core CSS - Include with every page -->
    <link href="/static/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- Page-Level Plugin CSS - Blank -->

    <!-- SB Admin CSS - Include with every page -->
    <link href="/static/sb-admin/css/sb-admin.css" rel="stylesheet">
    <link href="/static/toast/jquery.toast.min.css" rel="stylesheet">

    <!-- Core Scripts - Include with every page -->
    <script src="/static/javascripts/jquery-1.11.1.min.js"></script>
    <script src="/static/bootstrap/js/bootstrap.min.js"></script>
    <script src="/static/sb-admin/js/jquery.metisMenu.js"></script>

    <!-- Page-Level Plugin Scripts - Blank -->

    <!-- SB Admin Scripts - Include with every page -->
    <script src="/static/sb-admin/js/sb-admin.js"></script>
    <script src="/static/toast/jquery.toast.min.js"></script>
    <?php if($this->toast):?>
    <script>
        $(function(){
            $.toast({
                text : "<?php echo $this->toast['message']?>",
                position : 'top-center'
            })
            window.setTimeout(function(){
              location.href='<?php echo $this->toast['url']?>';
            }, 3000);
        })


    </script>
    <?php endif;?>

</head>

<body>

<div id="wrapper">

    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.html">Tiny MVC</a>
        </div>
        <!-- /.navbar-header -->


        <!-- /.navbar-top-links -->

    </nav>
    <!-- /.navbar-static-top -->

    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav" id="side-menu">
                <li class="sidebar-search">
                    <div class="input-group custom-search-form">
                        <input type="text" class="form-control" placeholder="Search...">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                    </div>
                    <!-- /input-group -->
                </li>
                <li>
                    <a href="<?php echo $this->createUrl('/') ?>"><i class="fa fa-dashboard fa-fw"></i> 系统主页 </a>
                </li>

                <li>
                    <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> 用户管理 <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="<?php echo $this->createUrl('/users') ?>">用户列表</a>
                        </li>

                    </ul>
                    <!-- /.nav-second-level -->
                </li>


            </ul>
            <!-- /#side-menu -->
        </div>
        <!-- /.sidebar-collapse -->
    </nav>
    <!-- /.navbar-static-side -->

    <div id="page-wrapper">
        <div class="row">

            <div class="col-lg-12">
                <h2 class="page-header">
                    <?php echo $this->page_title?>
                    <!-- breadcrumbs -->
                </h2>

                <?php echo $content ?>
            </div>
            <!-- /.row -->
        </div>
     </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->


    <!-- Page-Level Demo Scripts - Blank - Use for reference -->

</body>

</html>
