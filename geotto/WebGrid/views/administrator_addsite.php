<?php
include(INCLUDES."/header.php");
include(INCLUDES."/nav.php");
?>
<div class="main">
    <div class="main-content">
        <div class="page-wraper">
            <div class="page">
                
                <!-- 表单 -->
                <div class="line">
                    <label>网址名称</label>
                </div>
                <div class="line">
                    <input class="textbox" type="text" name="site_name" maxlength="64" />
                </div>
                <div class="line">
                    <label>网址链接</label>
                </div>
                <div class="line">
                    <input class="textbox" type="text" name="site_url" maxlength="256" />
                </div>
                <div class="line">
                    <label>网址分类</label>
                </div>
                <div class="line">
                    <select id="select-category">
                        <option value="-1">--请选择类别--</option>
                        <?php 
                        if(isset($categories) && $categories != null){
                            foreach($categories as $category){
                                echo "<option value=\"{$category['id']}\">{$category['name']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="line">
                    <label>选择图标</label>
                </div>
                <div class="line">
                    <input class="textbox" type="text" name="search_icon" placeholder="搜索图标" />
                    <div class="list" id="list-icons"></div>
                </div>
                <div class="line">
                    <button class="button" id="btn-add">提交</button>
                </div>
                
            </div>
        </div>
        <div class="sidebar"></div>
    </div>
</div>

<!-- 脚本区域 -->
<script src="<?php echo INCLUDES."/administrator_addsite.js"; ?>"></script>

<?php
include(INCLUDES."/footer.php");
?>