
<div class="lpn-pinfo">
    <div class="lpnp-label">Project</div>
    <div class="lpnp-title">{{=it.info.name}}</div>
</div>

<ul class="lpn-group" style="margin-left: 1px;">
    {{ for(var i in it.menus) { }}
    <li class="lpng-core">
        <a href="javascript:{{=it.menus[i].fn}}" title="{{=it.menus[i].title}}">
            <i class="lcx-ico-std lcx-icowhite" style="background-image:url(/lesscreator/static/img/{{=it.menus[i].ico}})"></i>
        </a>
    </li>
    {{ } }}
</ul>

<ul class="lpn-group">
    {{ for(var i in it.exts) { }}
    <li class="lpng-core">
        <a href="javascript:{{=it.exts[i].fn}}" title="{{=it.exts[i].title}}">
            <i class="lcx-ico-std lcx-icowhite" style="background-image:url(/lesscreator/static/img/{{=it.exts[i].ico}})"></i>
        </a>
    </li>
    {{ } }}
</ul>

