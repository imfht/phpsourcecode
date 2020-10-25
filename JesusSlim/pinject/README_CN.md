# pinject
Inject in PHP !

PHP依赖注入实现.

## usage

[English](README.md)
[Chinese]

### Install

pinject in packagist:[https://packagist.org/packages/jesusslim/pinject](https://packagist.org/packages/jesusslim/pinject)

Install:

	composer require jesusslim/pinject

If your composer not allowed dev-master,add this config

	"minimum-stability": "dev"
	
into your composer.json.

### Injector

InjectorInterface定义了以下接口方法
	
	map //map一个实例或者类或者闭包函数到容器内
	get //get things your mapped
	produce //生产实例
	
使用时 将自己的类继承Inject即可使用以下方法

	//map一个实例(数值或object等)到容器中
	$injector->mapData('test',12345);
	$injector->mapData(Student::class,new Student('Slim'));
	
	//map一个类(未实例化)到容器中
	$injector->mapSingleton(StudentInterface::class,GoodStudent::class);
	
	//生产实例
	//如果参数key能在已map的实例中找到 则直接返回实例
	//如果参数key能在已map的类名中找到 则判断是否实例化过 如果是则直接返回之前实例化的该类的实例 否则实例化 并返回
	//如果都没有找到 则默认会尝试反射该类 并实例化 如果我们调用了mustReg()方法 则强制任何实例化的类都必须被map过 否则不进行反射 这个根据具体使用场景决定是否设置
	$injector->produce(StudentInterface::class);
	
	//call an function
	//调用一个方法 方法的入参会根据容器内的内容自动填充
	$injector->call(function(Student $std){
		...
	});
	
	//call an function in class
	//调用一个类中的方法 类会根据pinject自动实例化 方法的入参会根据容器内的内容自动填充
	$injector->callInClass($class_name,$action,$out_params);
	
### Chains

Chains提供链式操作的实现

Example:

	$chains = new Chains($app);
	//$app是一个Injector类或子类
	$chains->chain(RequestHandler::class)
    ->chain(function($data,$next){
        $r = Auth::checkToken($data['token']);
        if($r !== true){
            dump("Token wrong");
        }else{
            $next($data);
        }
    })
    ->chain(Student::class)
    ->chain(function($data){
        dump($data);
    })
    ->action('handle')
    ->run();
    //or use runWith($your_last_chain_func);
    
chain接收一个闭包函数或者一个类名，如果是类名则会用pinject去实例化，并调用该实例的handle方法(默认handle 可用action方法去设置具体调用什么方法)。所有闭包或者处理方法都必须接收以下两个参数:一个是在链式操作内传递的对象/数组,第二个是下一个处理chain。如果处理完毕没有问题并且可用继续，则必须调用下一个方法并传递参数。

另一种用法:

另一种用法是使用runWild方法替代run/runWith,类似于golang Martini/Injector的用法。

例子:

	$chains = new \Inject\Chains($app);
	$app->mapData('any_param_here','Example');
	$the_given_params_for_handlers = [
    	'seems_wild' => 'OK'
	];
	$rtn = $chains->chain(function($any_param_here,$seems_wild){
    	var_dump($any_param_here.' is '.$seems_wild);
	})->chain(function(){
    	return "END";
	})->data($the_given_params_for_handlers)
    ->runWild();
	var_dump($rtn);
	
两者的区别在于，run/runWith限定了每一环的handler的入参，必须为传递的对象以及下一环调用，而runWild可以接收任意形式的handler作为一环，但是只要其中某一环的handler返回了任何内容，循环就会结束并返回结果。
