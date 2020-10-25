<?php if (is_file($this->install)) : ?>
<div class="alert alert-danger">为了网站安全，请删除安装文件：<?php echo $this->install; ?></div>
<?php endif; ?>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><?php echo $this->MOD_SYSTEM_SYSTEM_SYSINFO_LABEL; ?></h3>
  </div>
  <div class="panel-body">
    <table class="table">
      <tbody>
      <tr>
        <td><?php echo $this->MOD_SYSTEM_SYSTEM_SYSINFO_TFCVERSION; ?></td>
        <td><?php echo $this->sys_info['tfcversion']; ?></td>
      </tr>
      <tr>
        <td><?php echo $this->MOD_SYSTEM_SYSTEM_SYSINFO_DBVERSION; ?></td>
        <td><?php echo $this->sys_info['dbversion']; ?></td>
      </tr>
      <tr>
        <td><?php echo $this->MOD_SYSTEM_SYSTEM_SYSINFO_PHPVERSION; ?></td>
        <td><?php echo $this->sys_info['phpversion']; ?></td>
      </tr>
      <tr>
        <td><?php echo $this->MOD_SYSTEM_SYSTEM_SYSINFO_SOFTWARE; ?></td>
        <td><?php echo $this->sys_info['software']; ?></td>
      </tr>
      <tr>
        <td><?php echo $this->MOD_SYSTEM_SYSTEM_SYSINFO_MAXUPSIZE; ?></td>
        <td><?php echo $this->sys_info['maxupsize']; ?></td>
      </tr>
      </tbody>
    </table>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><?php echo $this->MOD_SYSTEM_SYSTEM_DEVINFO_LABEL; ?></h3>
  </div>
  <div class="panel-body">
    <table class="table">
      <tbody>
      <tr>
        <td><?php echo $this->MOD_SYSTEM_SYSTEM_DEVINFO_AUTHOR; ?></td>
        <td><?php echo $this->dev_info['author']; ?></td>
      </tr>
      <tr>
        <td><?php echo $this->MOD_SYSTEM_SYSTEM_DEVINFO_COPYRIGHT; ?></td>
        <td><?php echo $this->dev_info['copyright']; ?></td>
      </tr>
      <tr>
        <td><?php echo $this->MOD_SYSTEM_SYSTEM_DEVINFO_LICENSE; ?></td>
        <td><?php echo $this->dev_info['license']; ?></td>
      </tr>
      <tr>
        <td><?php echo $this->MOD_SYSTEM_SYSTEM_DEVINFO_TEAM; ?></td>
        <td><?php echo $this->dev_info['team']; ?></td>
      </tr>
      <tr>
        <td><?php echo $this->MOD_SYSTEM_SYSTEM_DEVINFO_SKINS; ?></td>
        <td><?php echo $this->dev_info['skins']; ?></td>
      </tr>
      <tr>
        <td><?php echo $this->MOD_SYSTEM_SYSTEM_DEVINFO_THANKS; ?></td>
        <td><?php echo $this->dev_info['thanks']; ?></td>
      </tr>
      <tr>
        <td><?php echo $this->MOD_SYSTEM_SYSTEM_DEVINFO_LINKS; ?></td>
        <td><?php echo implode('&nbsp;&nbsp;', $this->dev_info['links']); ?></td>
      </tr>
      </tbody>
    </table>
  </div>
</div>