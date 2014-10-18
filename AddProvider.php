<?php
namespace AddProvider;


use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AddProvider extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'provider:add';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Adds a vendor or workbench package to app config list of providers.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//
		
		$package = $this->argument('package');
		$providers = \Config::get( 'app.providers' );
		$provider  = $this->providerNameFromPackageName($package);

		if ( $provider == '' )
		{
			$error = "  Error: $package not found.   ";
			$this->info( "" );
			$this->error ( str_repeat(' ', strlen($error) ));
			$this->error ( $error );
			$this->error ( str_repeat(' ', strlen($error) ));
			$this->info( "" );
			exit();
		}

		if ( !in_array( $provider , $providers ))
		{
			if ( $this->option('verbose') )
			{
				$this->info( "                 " );
				$this->info( "  Add $provider  " );
				$this->info( "                 " );
			}
			
			$code = File::get( app_path('config/app.php') );
			// fetch provider block
			$re = "/((\\'providers\\')(\\C*?)(array\\(|\\[))(.*\\n)((\\C)*?)(\\)|\\])/";  // find the providers
			preg_match($re, $code, $matches );
			$provider_string = $matches[6];

			if ( $provider_string !== '' )
			{
				$provider_string .= "\t'$provider',";  		  // add service provider
				$subst = "$1\n $provider_string \n\t$8";          // build replace regex
				$result = preg_replace($re, $subst, $code, 1);    // replace

				if ( strpos( $result, $provider ) > -1 )
				{
					$written = File::put( app_path('config/app.php'), $result );
					if ( $written )
					{
						$providers[] = $provider;
						if (!$this->option('verbose'))
						{
							$this->info( "Added $provider to app/config/app.php." );
						}
					}
				}
			}

		}	
		if ( $this->option('verbose') )
		{
			$this->listProviders( $providers, $provider );
		}
		
	}

	// 
	// 	example:
 	//  package:  centagon/topdf
 	//  provider: Centagon\ToPdf\ToPdfServiceProvider
 	//  --note the case in ToPdf ...
 	//

	private function providerNameFromPackageName( $package )
	{
		
		$parts   = explode( '/', $package );
		
		$vendor    = file_exists( base_path() . '/vendor/' . $package );
		$workbench = file_exists( base_path() . '/workbench/' . $package );

		if (( $workbench ) or ( $vendor ))
		{
			if ( $vendor )
				$root = base_path() . '/vendor/' . $package . '/src/'; 
			else
				$root = base_path() . '/workbench/' . $package . '/src/'; 


			$dir = scandir( $root );
			$low = array_map('strtolower', $dir);
			if ( !in_array( $parts[0], $low ) ) return '';

			$publisher = $dir[ array_search( $parts[0], $low )];
			$root .= $publisher . '/';

			$dir = scandir( $root );
			$low = array_map('strtolower', $dir);
			if ( !in_array( $parts[1], $low ) ) return '';

			$package = $dir[ array_search( $parts[1], $low )];
			$root .= $package . '/';
			
			$dir = scandir( $root );
			$low = array_map('strtolower', $dir);
			$service = $parts[1] . 'serviceprovider.php';
			if ( !in_array( $service, $low ) ) return '';

			$provider = substr( $dir[ array_search( $service, $low )], 0, -4 );

			return $publisher . '\\' . $package . '\\' . $provider;

		}
		
		return '';		
		//return ucfirst($parts[0]) . '\\' . ucfirst($parts[0]) . '\\' . ucfirst($parts[1]) . 'ServiceProvider';
	}

	private function listProviders( $providers, $me )
	{
		$this->info( "" );
		foreach ($providers as $key => $provider) {
			if ( substr( $provider, 0, 10 ) !== 'Illuminate' )
			{
				if ( $provider == $me )
					$this->info( "- $provider" );
				else	
					$this->line( "- $provider" );
			}
		}
		$this->info( "" );
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('package', InputArgument::REQUIRED, 'vendor/package'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('silent', null, InputOption::VALUE_OPTIONAL, 'bverbose', null),
		);
	}

}
