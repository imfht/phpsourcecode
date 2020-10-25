# pinject
Inject in PHP !

## usage

[English]
[Chinese](README_CN.md)

### Install

pinject in packagist:[https://packagist.org/packages/jesusslim/pinject](https://packagist.org/packages/jesusslim/pinject)

Install:

	composer require jesusslim/pinject

If your composer not allowed dev-master,add this config

	"minimum-stability": "dev"
	
into your composer.json.

### Injector

The InjectorInterface decalare some function for Inject,like 
	
	map //map a concrete or class or object or closure to the inject container
	get //get things your mapped
	produce //produce concrete
	
The Injector class implements InjectorInterface.Make your own class which needs to use inject extends the Injector class.Then use the functions:

	//map a data or object
	$injector->mapData('test',12345);
	$injector->mapData(Student::class,new Student('Slim'));
	
	//map a class
	$injector->mapSingleton(StudentInterface::class,GoodStudent::class);
	
	//produce
	//if the key is found in mapped data,objects,it will return the things we mapped.
	//if the key is found in mapped classes,it will check if this class is been produced,if it's been produced,it return the concrete that produced before,else return a new concrete of this class.
	//if this key not found in any map,it will try to reflect this class unless we use the function mustReg() to make sure all the things can be produced should be mapped first.
	$injector->produce(StudentInterface::class);
	
	//call an function
	//it will fill the paramters of this function with the concrete produced by pinject.
	$injector->call(function(Student $std){
		...
	});
	
	//call an function in class
	//it will call a function in class.it will try to find the class from pinject if it has been produced or reflect it.and fill the paramters with concrete produced by pinject.
	$injector->callInClass($class_name,$action,$out_params);
	
### Chains

We can use Chains to do some chaining operations.

Example:

	$chains = new Chains($app);
	//here $app is an Injector
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
    
We can chain a Closure or a class into the chains.If it's a class,it will call the method named 'handle'.Every Closure or method for handle,should have two paramters:the data passing by in the chains,and the next handler.And at last of each chain,we shoule call the next handler if it's success.

Another way to use chains:

Another way to use chains is that use runWild instead of run/runWith,and it's more like the useage of Martini/Injector in golang.

Example:

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
	
As we see here,the difference between runWild and run/runWith is that,runWild support any kind of handlers,and any handler return anything will break the loop and return the result.