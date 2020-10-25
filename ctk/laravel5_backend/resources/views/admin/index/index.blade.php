<?php echo widget('Admin.Common')->header(); ?>
    <?php echo widget('Admin.Common')->top(); ?>
    <?php echo widget('Admin.Menu')->leftMenu(); ?>
    <div class="content">
        <?php echo widget('Admin.Common')->crumbs(); ?>
        <div class="main-content">
          <div class="row">
              <div class=" col-md-12">
                  <div class="panel panel-default">
                      <a href="#widget1container" class="panel-heading" data-toggle="collapse">开源协议 </a>
                      <div id="widget1container" class="panel-body collapse in">
                          <h2>The MIT License (MIT)</h2>

                          <p>Copyright (c) 2015 风一样的世界 mylampblog@163.com</p>

                          <p>Permission is hereby granted, free of charge, to any person obtaining a copy of
          this software and associated documentation files (the "Software"), to deal in
          the Software without restriction, including without limitation the rights to
          use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
          the Software, and to permit persons to whom the Software is furnished to do so,
          subject to the following conditions:</p>

                          <p>The above copyright notice and this permission notice shall be included in all
          copies or substantial portions of the Software.</p>

                          <p>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
          IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
          FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
          COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
          IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
          CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.</p>
                      </div>
                  </div>
              </div>
          </div>
        <?php echo widget('Admin.Common')->footer(); ?>
            
        </div>
    </div>
<?php echo widget('Admin.Common')->htmlend(); ?>