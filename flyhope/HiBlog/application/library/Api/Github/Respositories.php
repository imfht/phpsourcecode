<?php

/**
 * Github资源库API
 *
 * @package Api
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Api\Github; 
class Respositories extends Abs {
    
    /**
     * 默认提交者
     * 
     * @var array
     */
    protected $_default_committer = array(
        'name'  => 'Hiblog',
        'email' => 'hiblog@chengxuan.li',
    );
    
    /**
     * 获取当前用户的资源库
     * 
     * @param string $visibility  可见性（all, public, or private. Default: all）
     * @param string $affiliation owner,collaborator,organization_member
     * @param string $type        all, owner, public, private, member. Default: all
     * @param string $sort        排序字段：created, updated, pushed, full_name，默认：full_name
     * @param string $direction   排序方式:asc or desc. 默认: when using full_name: asc; otherwise desc
     * 
     * @return \array
     */
    public function userRepos($visibility = null, $affiliation = null, $type = null, $sort = null, $direction = null) {
        return $this->_get('user/repos', array(
            'visibility'  => $visibility,
            'affiliation' => $affiliation,
            'type'        => $type,
            'sort'        => $sort,
            'direction'   => $direction,
        ));
    }
    
    /**
     * 获取一个repos的分支列表
     * 
     * @param string $owner
     * @param string $repo
     * 
     * @return \array
     */
    public function reposBranches($owner, $repo) {
        $url = 'repos/%s/%s/branches';
        $url = sprintf($url, $owner, $repo);
        return $this->_get($url);
    }
    
    /**
     * 获取一个Repos信息
     * 
     * @param string $owner 作者
     * @param string $repo  资源库
     * 
     * @return \array
     */
    public function getRepos($owner, $repo) {
        $url = 'repos/%s/%s';
        $url = sprintf($url, $owner, $repo);
        return $this->_get($url);
    }
    
    /**
     * 获取一个文件内容
     * 
     * @param string $owner 作者
     * @param string $repo  资源库
     * @param string $path  路径
     * 
     * @return \array
     */
    public function getContent($owner, $repo, $path) {
        $url = 'repos/%s/%s/contents/%s';
        $url = sprintf($url, $owner, $repo, $path);
        return $this->_get($url);
    }
    
    /**
     * 替换一个文件内容（存在更新，不存在创建）
     * 
     * @param string $owner     所有者
     * @param string $repo      资源库
     * @param string $path      路径
     * @param string $content   文件内容
     * @param string $message   注释
     * @param string $branche   分支
     * @param array  $committer 作者信息
     * 
     * @return \stdClass
     */
    public function replace($owner, $repo, $path, $content, $message, $branche = null, array $committer = null) {
        $sha = '';
        try {
            $content_data = self::getContent($owner, $repo, $path);
            empty($content_data->sha) || $sha = $content_data->sha;
        } catch(\Exception $e) {
            
        }
        
        $committer || $committer = $this->_default_committer;
        $url = sprintf('repos/%s/%s/contents/%s', $owner, $repo, $path);
        $param = array(
            'path'      => $path,
            'content'   => base64_encode($content),
            'message'   => $message,
            'committer' => $committer,
            'sha'       => $sha,
        );
        return $this->_post($url, $param, 'PUT');
        
    }
    
    /**
     * 删除一个文件内容（存在更新，不存在创建）
     *
     * @param string $owner     所有者
     * @param string $repo      资源库
     * @param string $path      路径
     * @param string $message   注释
     *
     * @return \stdClass
     */
    public function delete($owner, $repo, $path, $message) {
        $sha = '';
        try {
            $content_data = self::getContent($owner, $repo, $path);
            empty($content_data->sha) || $sha = $content_data->sha;
        } catch(\Exception $e) {
        
        }
        
        $url = sprintf('repos/%s/%s/contents/%s', $owner, $repo, $path);
        $param = array(
            'path'      => $path,
            'message'   => $message,
            'sha'       => $sha,
        );
        return $this->_post($url, $param, 'DELETE');
    }
    
    /**
     * 初始化对象
     *
     * @return \Api\Github\Respositories
     */
    static public function init() {
        return parent::init();
    }
    
    
    
}