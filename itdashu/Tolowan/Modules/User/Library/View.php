public static $user;
//获取用户信息
public static function user($id, $att = null)
{
if (!isset(self::$user[$id])) {
$user = self::findFirst($id);
if ($user) {
self::$user[$id] = $user;
}
}
if (isset(self::$user[$id])) {
if ($att != null && isset(self::$user[$id]->{$att})) {
return self::$user[$id]->{$att};
}
return self::$user[$id];
}
return false;
}
public static function login($formEntity)
{
global $di;

}
//获取用户列表
public static function find($query = array())
{
$query += array(
'from' => array('id' => 'user'),
'order' => 'user.created DESC',
'active' => 1,
);
$where = 'where';
if (isset($query['where']) && !empty($query['where'])) {
$where = 'andWhere';
}
if ($query['active'] != 11) {
$query[$where][] = array(
'conditions' => 'user.active = :active:',
'bind' => array('active' => $query['active']),
);
}
foreach (array('attach', 'info', 'log', 'roles') as $value) {
$entityName = 'user_' . $value;
$query['leftJoin'][] = array(
'id' => $entityName,
'conditions' => "user.id = $entityName.uid",
);
}
return Cmodel::find($query);
}
//获取用户
public static function findFirst($id, $query = array())
{
$query += array(
'limit' => 1,
'where' => array(
array(
'conditions' => "user.id = $id",
'bind' => array('id' => $id),
),
),
);
return self::find($query);
}

//获取用户角色信息@
public static function userRoles($id)
{
$roles_user = UserRoles::findByUid($id);
$rolesList = array();
foreach ($roles_user as $value) {
$rolesList[$value->role] = $value->role;
}
return $rolesList;
}
public static function userDelete($user){
global $di;
$db = $di->getShared('db');
$db->begin();
if(!is_object($user)){
$user = User::findFirst($user);
}
if($user){
$userRoles = UserRoles::findByUid($user->id);
$userInfo = UserInfo::findFirst($user->id);
if($userInfo){
$userInfo->delete();
}
foreach($userRoles as $item){
$item->delete();
}
if($user->delete()){
$db->commit();
$di->getShared('flash')->success('用户删除成功');
return true;
}else{
$db->rollback();
$di->getShared('flash')->error('用户删除失败');
return false;
}
}else{
$di->getShared('flash')->error('用户删除失败');
}
return false;
}