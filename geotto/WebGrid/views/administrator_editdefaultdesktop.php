<?php
include(INCLUDES."/header.php");
include(INCLUDES."/nav.php");
?>

<div class="main">
    <div class="main-content">
        <div class="page-wraper">
            <div class="page">
            
                <!-- 展示桌面 -->
                <div class="list" id="list-desktop"></div>
                <div class="line">
                    <div class="button-danger" id="btn-delete">删除</div>
                </div>
                
                <!-- 添加网址 -->
                <div class="line">
                    <label>添加网址</label>
                </div>
                <div class="line">
                    <input class="textbox" type="text" name="search_site" placeholder="搜索" />
                    <select id="select-category" style="float:right">
                        <option value="-1">--请选择分类--</option>
                        <?php
                        if(isset($categories) && $categories != null){
                            foreach($categories as $category){
                                echo "<option value=\"{$category['id']}\">{$category['name']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="list" id="list-sites"></div>
                <div class="line">
                    <button class="button" id="btn-add">添加</button>
                </div>
                
            </div>
        </div>
        <div class="sidebar"></div>
    </div>
</div>

<!-- 脚本区域 -->
<script src="<?php echo INCLUDES."/administrator_editdefaultdesktop.js"; ?>"></script>

<?php
include(INCLUDES."/footer.php");
?>