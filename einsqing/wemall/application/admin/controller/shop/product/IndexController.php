<?php
namespace app\admin\controller\shop\product;
use app\admin\controller\BaseController;

class IndexController extends BaseController
{
	//商品列表
	public function index(){
		$map = array();
		$search = '?';
		if(input('param.id') != ''){
            $map['id']  = input('param.id');
            $search .= 'id='.input('param.id').'&';
        }
        if(input('param.name') != ''){
            $map['name|id']  = ['like','%'.input('param.name').'%'];
            $search .= 'name='.input('param.name').'&';
        }
        if(input('param.status') != '' && input('param.status') != '-10'){
            $map['status']  = input('param.status');
            $search .= 'status='.input('param.status').'&';
        }
        if(input('param.category_id') != '' && input('param.category_id') != '-10'){
            $map['category_id']  = input('param.category_id');
            $search .= 'category_id='.input('param.category_id').'&';
        }

		$productlist = model('Product')->with('file,category')->where($map)->order('rank', 'desc')->paginate();

		$page = str_replace("?",$search,$productlist->render());
        $this->assign("page", $page);

		cookie("prevUrl", request()->url());
		$menulist = model('ProductCategory')->all()->toArray();
		$tree = list_to_tree($menulist, 'id', 'pid', 'sub');
        $this->assign("menulist", $tree);

		$this->assign('productlist', $productlist);
		$this->assign('productPost', input('param.'));
		$this->assign('url', request()->root(true));
		return view();
	}
	//新增修改商品
	public function add(){
		if (request()->isPost()){
			$data = input('post.');
			$data['files'] = input('?post.files') ? implode(",", $data['files']) : '';
			$data['sku_status'] = input('?post.sku_status') ? $data['sku_status'] : 0;
			$data['labels'] = input('?post.labels') ? implode(",", $data['labels']) : '';

			if(input('post.id')){
				$result = model('Product')->update($data);	
			}else{
				$result = model('Product')->create($data);
			}

			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$id = input('param.id');
			if($id){
				$product = model('Product')->with('file,skus')->find($id);
				$product_r = $product->toArray();
				$product_r['labels'] = $product->getData('labels');
				$product_r['sku'] = $product->getData('sku');
				$product_r['skus'] = json_encode($product_r['skus']);
				$this->assign('product', $product_r);
			}

			//标签
			$labellist = model('ProductLabel')->all();
			$this->assign('labellist', $labellist);
			//菜单
			$menulist = model('ProductCategory')->all()->toArray();
			$menutree = list_to_tree($menulist, 'id', 'pid', 'sub');
            $this->assign("menulist", $menutree);
			return view();
		}
	}

	//新增修改商品sku
	public function sku(){
		if (request()->isPost()){
			$data = input('post.');
			if(!input('?post.skus')){
				$this->error('请选择属性', cookie("prevUrl"));
			}
			$data['sku'] = implode(",", $data['sku']);
			model('Product')->update(['id' => $data['id'], 'sku' => $data['sku']]);
			//删除多余的
			$sku_ids = model('ProductSku')->where('product_id', $data['id'])->column('id');
			if($sku_ids){
				if(input('?post.sku_ids')){
					$sku_ids = array_diff($sku_ids,array_intersect($data['sku_ids'],$sku_ids));
				}
			}
			model('ProductSku')->destroy(implode(",", $sku_ids));

			if(input('?post.skus')){
				foreach ($data['skus'] as &$sku) {
					if($sku['id']){
						$result = model('ProductSku')->update($sku);	
					}else{
						$sku['product_id'] = $data['id'];
						$result = model('ProductSku')->create($sku);
					}
				}
			}
			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$id = input('param.id');
			if($id){
				$product = model('Product')->with('file,skus')->find($id);
				$product_r = $product->toArray();
				$product_r['sku'] = $product->getData('sku');
				$product_r['skus'] = json_encode($product_r['skus']);
				$this->assign('product', $product_r);
			}

			//sku
			$skulist = model('Sku')->all()->toArray();
			$skutree = list_to_tree($skulist, 'id', 'pid', 'sub');
			$this->assign("skulist", $skutree);
			return view();
		}
	}

	//删除商品
	public function del(){
		$ids = input('param.id');
		
		$result = model('Product')->destroy($ids);
		if($result){
			$this->success("删除成功", cookie("prevUrl"));
		}else{
			$this->error('删除失败', cookie("prevUrl"));
		}
	}
	//改变商品状态
	public function update(){
		$data = input('param.');
		$result = model('Product')->where('id','in',$data['id'])->update(['status' => $data['status']]);
		if($result){
			$this->success("修改成功", cookie("prevUrl"));
		}else{
			$this->error('修改失败', cookie("prevUrl"));
		}
	}

	//导出全部商品
	public function export(){
		$map = array();
        if(input('param.id') != ''){
            $map['id']  = ['in',input('param.id')];
        }
		$productlist = model('Product')->with('file,category,skus')->where($map)->select()->toArray();

		$data = array(
			'0' => array(
                '1' => '编号',
                '2' => '商品名称',
                '3' => '商品分类',
                '4' => '商品sku',
                '5' => '商品单位',
                '6' => '商品规格',
                '7' => '商品标签',
                '8' => '赠送积分',
                '9' => '商品排序',
                '10' => '备注',
                '11' => '商品状态',
            ),
        );
        foreach ($productlist as &$v) {
        	$sku = '';
            foreach ($v['sku'] as &$d) {
                $sku .= '['.$d['text'].':';
                foreach ($d['sub'] as &$sub) {
                	$sku .= $sub['text'].'|';
                }
                $sku .= ']';
            }
            $skus = '';
            foreach ($v['skus'] as &$ds) {
            	$skus .= '[';
            	$skus .= '编号:'.$ds['ids'];
            	$skus .= ',名称:'.$ds['name'];
            	$skus .= ',售价:'.$ds['price'];
            	$skus .= ',原价:'.$ds['old_price'];
            	$skus .= ',库存:'.$ds['store'];
            	$skus .= ',销量:'.$ds['sales'];
            	$skus .= ']';
            }
            $labels = '';
            if($v['labels']){
            	foreach ($v['labels'] as &$ls) {
	            	$labels .= $ls['name'].'|';
	            }
            }
            switch ($v['status']) {
                case '0':
                    $v['status'] = '隐藏';
                    break;
                case '1':
                    $v['status'] = '显示';
                    break;
                default:
                    $v['status'] = '未知状态';
                    break;
            }
        	array_push($data, array(
				'1' => $v['id'],
                '2' => $v['name'],
                '3' => $v['category']['name'],
                '4' => $sku,
                '5' => $v['unit'],
                '6' => $skus,
                '7' => $labels,
                '8' => $v['score'],
                '9' => $v['rank'],
                '10' => $v['remark'],
                '11' => $v['status'],
			));
        }
        export_to($data,'全部商品');//导出excle
	}


	

}