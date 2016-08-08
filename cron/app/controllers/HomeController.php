<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
    private $connection;
    private $path;
    private $handle;
    private $cron_file;


    /*function __construct($host=NULL, $port=NULL, $username=NULL, $password=NULL)
	{
	    $path_length     = strrpos(__FILE__, "/");      
	    $this->path      = substr(__FILE__, 0, $path_length) . '/';
	    $this->handle    = 'crontab.txt';        
	    $this->cron_file = "{$this->path}{$this->handle}";
	 	echo $host."A";
	 	exit();
	    try
	    {
	        if (is_null($host) || is_null($port) || is_null($username) || is_null($password)) throw new Exception("Please specify the host, port, username and password!");
	         
	        $this->connection = @ssh2_connect($host, $port);
	        if ( ! $this->connection) throw new Exception("The SSH2 connection could not be established.");
	 
	        $authentication = @ssh2_auth_password($this->connection, $username, $password);
	        if ( ! $authentication) throw new Exception("Could not authenticate '{$username}' using password: '{$password}'.");
	    }
	    catch (Exception $e)
	    {
	        $this->error_message($e->getMessage());
	    }
	}*/

    private function crontab_file_exists()
	{
    	return file_exists($this->cron_file);
	}


	private function error_message($error)
	{
    	die("<pre style='color:#EE2711'>ERROR: {$error}</pre>");
	}

	public function index()
	{   

		$output = shell_exec('crontab -l');
		$cronData = explode("\n", $output);
		array_pop($cronData);
		return View::make('hello')->with(array('cronData' => $cronData));
	}

	public function write_to_file($path=NULL, $handle=NULL) 
	{
	    if ( ! $this->crontab_file_exists())
	    {   
	        $this->handle = (is_null($handle)) ? $this->handle : $handle;
	        $this->path   = (is_null($path))   ? $this->path   : $path;
	 
	        $this->cron_file = "{$this->path}{$this->handle}";
	 
	        $init_cron = "crontab -l > {$this->cron_file} && [ -f {$this->cron_file} ] || > {$this->cron_file}";
	 
	        $this->exec($init_cron);
	    }
	 
    return $this;
	}

	public function remove_file()
	{
	    if ($this->crontab_file_exists()) $this->exec("rm {$this->cron_file}");
	 
	    return $this;
	}

	public function append_cronjob($cron_jobs=NULL)
	{
	    if (is_null($cron_jobs)) $this->error_message("Nothing to append!  Please specify a cron job or an array of cron jobs.");
	     
	    $append_cronfile = "echo '";        
	     
	    $append_cronfile .= (is_array($cron_jobs)) ? implode("\n", $cron_jobs) : $cron_jobs;
	     
	    $append_cronfile .= "'  >> {$this->cron_file}";
	     
	    $install_cron = "crontab {$this->cron_file}";
	 
	    $this->write_to_file()->exec($append_cronfile, $install_cron)->remove_file();
	     
	    return $this;
	}

	public function remove_cronjob($cron_jobs=NULL)
	{
	    if (is_null($cron_jobs)) $this->error_message("Nothing to remove!  Please specify a cron job or an array of cron jobs.");
	     
	    $this->write_to_file();
	 
	    $cron_array = file($this->cron_file, FILE_IGNORE_NEW_LINES);
	     
	    if (empty($cron_array)) $this->error_message("Nothing to remove!  The cronTab is already empty.");
	     
	    $original_count = count($cron_array);
	     
	    if (is_array($cron_jobs))
	    {
	        foreach ($cron_jobs as $cron_regex) $cron_array = preg_grep($cron_regex, $cron_array, PREG_GREP_INVERT);
	    }
	    else
	    {
	        $cron_array = preg_grep($cron_jobs, $cron_array, PREG_GREP_INVERT);
	    }   
	     
	    return ($original_count === count($cron_array)) ? $this->remove_file() : $this->remove_crontab()->append_cronjob($cron_array);
	}

	public function remove_crontab()
	{
	    $this->exec("crontab -r")->remove_file();
	     
	    return $this;
	}

	public function addSingleJobToCron() {
		$cron = Input::get("cron");
		$crontab = new HomeController('remote_server', 'port', 'myusername', 'mypassword');
		$crontab->append_cronjob($cron);
	}

	public function addMultipleJobToCron() {
		$crontab = new HomeController('remote_server', 'port', 'myusername', 'mypassword');

		$new_cronjobs = array(
    			'0 0 1 * * home/path/to/command/the_command.sh',
    			'30 8 * * 6 home/path/to/command/the_command.sh >/dev/null 2>&1'
		);
 
		$crontab->append_cronjob($new_cronjobs);
	}

	public function removeSingleJobToCron($cron) {
		$crontab = new HomeController('remote_server', 'port', 'myusername', 'mypassword');
		$cron_regex = $cron;
 
		$crontab->remove_cronjob($cron_regex);
	}

	public function removeMutipleJobToCron() {
		$crontab = new HomeController('remote_server', 'port', 'myusername', 'mypassword');
 
		$cron_regex = array(
		    '/0 0 1 \* \*/',
		    '/home\/path\/to\/command\/the_command\.sh\/'
		);
 
		$crontab->remove_cronjob($cron_regex);
	}

	public function edit() {

		$cron = Input::get("cron");
		$this->removeSingleJobToCron($cron);

	}

	public function delete($cron) {

		$cron = Input::get("cron");
		$this->removeSingleJobToCron($cron);

	}

}
