step for custom laravel package(contact)
1.install new laravel(composer create-project laravel/laravel:^10.0 example-app)
2.make folder on root(package)
	make folder with packagename(contact)
	run composer init in (D:\Newproject_laravel10\demo\LaravelPackage\package\contact)

		Package name (<vendor>/<name>) [bidis/contact]: snehasurmyewards/laravelpackage(php artisan package:make YourVendorName/YourPackageName)
		php artisan make:provider CustomServiceProvider

		Description []: This will send email to admin and save contact query in database
		Author [= <=>, n to skip]: snehasur
		Minimum Stability []: dev
		Package Type (e.g. library, project, metapackage, composer-plugin) []: library
		License []: MIT

		Define your dependencies.

		Would you like to define your dependencies (require) interactively [yes]? n
		Would you like to define your dev dependencies (require-dev) interactively [yes]? n
		Add PSR-4 autoload mapping? Maps namespace "Snehasurmyewards\Laravelpackage" to the entered relative path. [src/, n to skip]: n

3.make src folder and make serviceprovider
D:\Newproject_laravel10\demo\LaravelPackage\package\contact\src
ContactServiceProvider.php
<?php
namespace CodeCaptain/Contact;
use Illuminate\Support\ServiceProvider;
class ContactServiceProvider extends ServiceProvider{
	public function boot(){
	}
	public function register(){
	}
} 
add on config/app.php->CodeCaptain\Contact\ContactServiceProvider::class,
4.composer dump-autoload in main project (D:\Newproject_laravel10\demo\LaravelPackage)
5.for add route
	D:\Newproject_laravel10\demo\LaravelPackage\package\contact\src\routes then on ContactServiceProvider add on boot
    $this->loadRoutesFrom(__DIR__.'/routes/web.php');  
6.for add views
D:\Newproject_laravel10\demo\LaravelPackage\package\contact\src\routes then on ContactServiceProvider add on boot
$this->loadViewsFrom(__DIR__.'/views','contact');
7.make controllers in D:\Newproject_laravel10\demo\LaravelPackage\package\contact\src\Http\Controllers

php artisan make:controller ContactController
change namespace
8.make model php artisan make:model Contact -m
change namespace
9.for migration add D:\Newproject_laravel10\demo\LaravelPackage\package\contact\src\routes then on ContactServiceProvider add on boot
php artisan migrate

problem-
php artisan migrate

   INFO  Running migrations.  

  2014_10_12_000000_create_users_table ............................................................................ 3ms FAIL

   Illuminate\Database\QueryException 

  SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'users' already exists (Connection: mysql, SQL: create table `users` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(191) not null, `email` varchar(191) not null, `email_verified_at` timestamp null, `password` varchar(191) not null, `remember_token` varchar(100) null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci')

  at vendor\laravel\framework\src\Illuminate\Database\Connection.php:829
    825▕                     $this->getName(), $query, $this->prepareBindings($bindings), $e
    826▕                 );
    827▕             }
    828▕ 
  ➜ 829▕             throw new QueryException(
    830▕                 $this->getName(), $query, $this->prepareBindings($bindings), $e
    831▕             );
    832▕         }
    833▕     }

  1   vendor\laravel\framework\src\Illuminate\Database\Connection.php:587
      PDOException::("SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'users' already exists")

  2   vendor\laravel\framework\src\Illuminate\Database\Connection.php:587
      PDOStatement::execute()

solve-
(php artisan tinker

Then

Schema::drop('books')

(and exit with q)

Now, you can successfully php artisan migrate:rollback and php artisan migrate)
10.php artisan make:mail ContactMailable --markdown=contact.email 
move to D:\Newproject_laravel10\demo\LaravelPackage\package\contact\src\Mail from main folder
11.open account for dummy mail on mailtrap
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=8fde7f552a8e73
MAIL_PASSWORD=9a1d9dc251613b
12.for configaration make folder on D:\Newproject_laravel10\demo\LaravelPackage\package\contact\src\config add on ContactServiceProvider add on boot
$this->mergeConfigFrom(
        __DIR__.'/config/contact.php', 'contact'
    );
13.for vendor public add on ContactServiceProvider add on boot
$this->publishes([
        __DIR__.'/config/contact.php' => config_path('contact.php'),
    ]);
	then php artisan vendor:publish
for views public add on ContactServiceProvider add on boot add on prev publishes
$this->publishes([
        __DIR__.'/views' => resource_path('views/vendor/contact'),
    ]);	
	then php artisan vendor:publish
14.uplode only the package on github
git init
git config --global user.name "sneha sur"
git config --global user.email "snehasur.myewards@gmail.com"
add readme
git status
git add .
git commit -m "Initial commit"
git remote add origin git@github.com:snehasurMyewards/laravel-contact-package.git
git push -u origin main
error-
git@github.com: Permission denied (publickey).
fatal: Could not read from remote repository.

Please make sure you have the correct access rights
and the repository exists.
solve-
ssh-keygen
get public key from id_rsa.pub and add it on giyhub profile->settings->ssh gdg key
15.open account with github in packagist
submit the git url of the project
(not found)=>
for auto update(if changes in code it will auto update on git package)
profile take api key baf3ed6a573ef7e3f038
on github intrigation services add packagelist whuch is now 
https://github.com/snehasurMyewards/laravel-contact-package/settings/hooks
Payload URL: https://packagist.org/api/github?username=snehasurMyewards
Content Type: application/json
Secret: your Packagist API Token
16.install new laravel then
(check package name)
exp-composer require codecaptain/laravelpackagecontact
composer require codecaptain/laravelcontactpackage
17.add on config/app of new project
CodeCaptain\Contact\ContactServiceProvider::class(as it is not auto discovary package)
for auto discovary package step-(after that remove prev line)
add on the custom package's own composerjson 
"extra": {
    "laravel": {
        "providers": [
            "CodeCaptain\\Contact\\ContactServiceProvider"
        ]
    }
},
from package git tag v1.0.0
git add .
git commit -m "Add auto discovary"
git push --tag
git push
18.remove package 
exp-composer remove codecaptain/laravelcontactpackage
(check package name)
and install once
19.git tag v1.0.01
composer update
20.add badges
after changes in package the install in new project then change the version to check auto update
[![Latest Version](https://img.shields.io/github/release/snehasurMyewards/laravel-contact-package.svg?style=flat-square)](https://github.com/snehasurMyewards/laravel-contact-package/releases)
[![Build Status](https://img.shields.io/github/actions/workflow/status/snehasurMyewards/laravel-contact-package/ci.yml?label=ci%20build&style=flat-square)](https://github.com/snehasurMyewards/laravel-contact-package/actions?query=workflow%3ACI)
[![Total Downloads](https://img.shields.io/packagist/dt/guzzlehttp/guzzle.svg?style=flat-square)](https://packagist.org/packages/guzzlehttp/guzzle)


https://packagist.org/about#how-to-update-packages

if need to overwrite package
in new project make config/contact.php
D:\Newproject_laravel10\demo\laravelpackagetest\resources\views\vendor contact/contact.blade.php
by only insall we can use contact 