<?php
namespace app\clan\controller;
use think\Controller;
class Center extends Controller
{
    private $genNo;
    private $genData;
    use \app\clan\controller\ClanFnTra;
    public function index()
    {
        $this->genNo = getUrlBind('index');
        if($this->genCheckVisit($this->genNo)) $this->error('您还没有创建任何家谱信用，去新增家谱应用吧!','index/edit');
        $gcter = model('Gcenter');
        $data = $gcter->get($this->genNo);
        $this->loadScript([
            'title'=>$data['gen_title']. ' - Conero','bootstrap'=>true,'css'=>['center/index'],'js'=>['center/index']
        ]);
        $this->assign('data',$data);
        $clan = [];
        // 右菜单加载
        $menu = model('Menu')->getMenuList('clan');
        $xhtml = '';
        $gno = $this->genNo;
        foreach($menu as $v){
            $url = $v['url'];
            $url = str_replace('{$no}',$gno,$url);
            $xhtml .= '<li><a href="javascript:void(0)" dataurl="'.$url.'" dataid="'.$v['code_mk'].'">'.$v['descrip'].'</a></li>';
        }
        $clan['siderBar'] = $xhtml;
        $clan['editurl'] = urlBuild('!.index/edit/'.$gno);
        $this->assign('clan',$clan);
        return $this->fetch();
    }    
    public function home()
    {
        $this->loadScript([
            'title'=>'祖公源居 - Conero','bootstrap'=>true
        ]);
        $this->genNo = getUrlBind('home');
        $this->assign([
            'clan' => [
                'name'  => request()->controller(),
                'count' => $this->aboutVisit()
            ]
        ]);
        $gcter = model('Gcenter');
        $data = $gcter->get($this->genNo);
        $fdata = model('Sysfile')->getFileList(['gen_no'=>$this->genNo]);
        if($fdata){
            $filelist = '';
            $ctt = 1;
            foreach($fdata as $v){
                $filelist .= '<li class="list-group-item">'.$ctt.'. <a href="/Conero/Files/'.$v['url'].'" target="_blank">'.$v['name'].'</a><span style="float:right;">'.$v['edittm'].'</span></li>';
            }
            if($filelist){
                $filelist = '<ul class="list-group">'.$filelist.'</ul>';
                $data['filelist'] = $filelist;
            }
        }
        $clan_analyse = '';
        $map = ['gen_no'=>$data['gen_no']];
        $gnMd = model('Gnode');
        $nodeCtt = $gnMd->where($map)->count();
        $zbMd = model('Gzibei');
        $zbCount = $zbMd->where($map)->count();           
        if($zbCount){
            $zibeiStr = $zbMd->zibei2Str($data['gen_no']);
            $zbCount = '设置字辈共'.$zbCount.'个，字辈排序如:<br>'.$zibeiStr;
        }
        else $zbCount = '没有相关的字辈记录';
        $map = array_merge($map,['sex'=>'M']);
        $nodeMCtt = $gnMd->where($map)->count();
        $caData = [
            'clan_name'     => $data['gen_title'],
            'node_count'    => $nodeCtt,
            'node_mcount'   => $nodeMCtt,
            'node_fcount'   => ($nodeCtt - $nodeMCtt),
            'zibei_count'   => $zbCount
        ];
        $clan_analyse = model('Textpl')->renderContent('clan_analyse',$caData);
        $data['clan_analyse'] = $clan_analyse;
        $this->assign('data',$data);
        return $this->fetch();
    }    
}
