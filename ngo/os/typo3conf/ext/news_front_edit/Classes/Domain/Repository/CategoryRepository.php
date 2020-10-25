<?php
namespace TaoJiang\NewsFrontEdit\Domain\Repository;
class CategoryRepository extends \GeorgRinger\News\Domain\Repository\CategoryRepository {
    
    /**
     * 获取分类
     * @param string $categories
     */
    public function findByUids($categories){
    
        $categories = explode(',',$categories);
        
        $query = $this->createQuery();
        
        $constraints = array();
        foreach($categories as $category){
            $constraints[] = $query->equals('uid',$category);
        }
        
        $query->matching($query->logicalOr($constraints));
        $result = $query->execute();
        return $result;
    }
    
}
