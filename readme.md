# Api Responder

Api Responder Is A Simple Package For Api Response Using Laravel Resources With Some Features With Some Useful Readable And Chainable Methods

> **This Package Was Made For My Personal Usage**

* [Prerequisite](#Prerequisite)
* [Getting Started](#getting-started)
* [Installation](#installation)
* [Usage](#usage)
    * [Basic Usage](#basic-usage)
    * [Response Usage](#response-usage)
        * [Response Alias](#response-alias)
    * [Get Paginate Limit Usage](#get-paginate-limit-usage)
    * [Set Paginate Limit Usage](#set-paginate-limit-usage)
    * [Error Usage](#error-usage)
    * [Safe Error Usage](#safe-error-usage)
    * [Validate Usage](#validate-usage)
    * [With Usage](#with-usage)
    * [Get Wrapping Usage](#get-wrapping-usage)
    * [Set Wrapping Usage](#set-wrapping-usage)
* [Example](#example)
* [Todo](#todo)


## Prerequisites
> This Package Required Laravel 5.5 (or higher)

## Getting Started
Remember This Package Not Perfect It's Just Like Some Helpers For Me During Development.
You Can Visit [Laravel Official Docs](https://laravel.com/docs/master/eloquent-resource) About Api Resources For Deeper Understanding
All The Rest Of The Documentation WIll Explain How To Install And Every Method Provided To You

## Installation

Via Composer

```bash
$ composer require MoaAlaa/api-responder
```

If You Do Not Run Laravel 5.5 (Or Higher), Then Add The Service Provider In **`config/app.php`**:

```php
MoaAlaa\ApiResponder\ApiResponderServiceProvider::class,
```

If you do run the package on Laravel 5.5+, [package auto-discovery](https://medium.com/@taylorotwell/package-auto-discovery-in-laravel-5-5-ea9e3ab20518) takes care of the magic of adding the service provider.

## Usage
Using ApiResponder Very Easy And Straights Forward Just Use **`ApiResponder` Trait** Anywhere (I Usually Use It In Controllers So The Usage And Examples Will Be Also).

```php
<?php

namespace App\Http\Controllers;

use MoaAlaa\ApiResponder\ApiResponder;

class HomeController extends Controller
{
    use ApiResponder;

    public function index()
    {

    }
}
```

### Basic Usage

By Using **`ApiResponder` Trait** You Will Have Access To Method Called **`this->api()`** and It Will Make All The Magic For You It Give You Access For Many Useful Methods Important One Is **`response()`** That Will Sernd the Response For You

```php
<?php

namespace App\Http\Controllers;

use App\User;
use MoaAlaa\ApiResponder\ApiResponder;

class HomeController extends Controller
{
    use ApiResponder;

    public function index()
    {
        return $this->api()->response(User::first());
    }
}
```

### Response Usage

**`$this->api()->response(...)`** Is Responsible For Responding All The Data For You And It Accepts More Than One Parameter

Parameters  |Desc           | Accepts      | Defaults 
:-----------|:--------------|:-------------|:------------
$data       | Your Data That You Want To Send       | `Laravel Collections, Laravel Models, Laravel Pagination, Array, String, Integer`, `Null` | None `But If Null Is Passed It Will Return Empty Array []` 
$error      | Error Messages                        | `String, Array, Null`                                                                     | Null
$code       | The Response Status Code              | `Integer`                                                                                 | 200
$additional | Additional Data To Send With Response | `Array`, `Closure`                                                                               | []
$wrap       | Wrapping Key For Returned Response    | `String`                                                                                  | "payload"
```php
<?php

namespace App\Http\Controllers;

use App\User;
use MoaAlaa\ApiResponder\ApiResponder;

class HomeController extends Controller
{
    use ApiResponder;

    public function index()
    {
        // $this->api()->response($data, $error = null, int $code = 200, $additional = [], $wrap = 'payload');

        $this->api()->response(User::first(), null, 200, function () {
            return ['foo' => 'bar'];
        }, 'baz');

        return $this->api()->response(User::first());
    }
}
```

#### Response Alias
Response Has An Alias Called `$this->api()->responseWith()` For Clear Readability

```php
<?php

namespace App\Http\Controllers;

use App\User;
use MoaAlaa\ApiResponder\ApiResponder;

class HomeController extends Controller
{
    use ApiResponder;

    public function index()
    {
        $users = User::where(...)->take(...)->get();
        return $this->api()->responseWith($users);
    }
}
```

### Get Paginate Limit Usage

**`$this->api()->getPaginateLimit()`** Method `$this->api()->getPaginateLimit()` Used To Return The Pagination Limit When Using Pagination Default Is 10 If Not Using `$this->api()->setPaginateLimit($limit)` To Change It.

Parameters  |Desc           | Accepts      | Defaults 
:-----------|:--------------|:-------------|:------------
None        | None          | None         | None

```php
<?php

namespace App\Http\Controllers;

use App\User;
use MoaAlaa\ApiResponder\ApiResponder;

class HomeController extends Controller
{
    use ApiResponder;

    public function index()
    {
        return $this->api()->response(User::paginate($this->api()->getPaginateLimit()));
    }
}
```

### Set Paginate Limit Usage

**`$this->api()->setPaginateLimit(50)->response(...)`** Method `$this->api()->setPaginateLimit(50)` Used To Adjust The Pagination Limit When Using Pagination 
You Can Define It In Any Service Provider Like `AppServiceProvider` To Apply The Limit In All Places You Used `$this->api()->getPaginationLimit()`
Also You Can Edit It On Runtime As Will 

Parameters  |Desc           | Accepts      | Defaults 
:-----------|:--------------|:-------------|:------------
$limit      | Pagination Length ByDefault Pagination Length Is 10  | `Integer` | None

`AppServiceProvider`
```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoaAlaa\ApiResponder\ApiResponder;

class AppServiceProvider extends ServiceProvider
{
    use ApiResponder;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->api()->setPaginationLimit(20);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
```

`HomeController`
```php
<?php

namespace App\Http\Controllers;

use App\User;
use MoaAlaa\ApiResponder\ApiResponder;

class HomeController extends Controller
{
    use ApiResponder;

    public function index()
    {   
        // Default Pagination Limit 
        return $this->api()->response(User::paginate($this->api()->getPaginateLimit()));
        
        // Run-Time Set Pagination Limit 
        return $this->api()->setPaginateLimit(50)->response(User::paginate($this->api()->getPaginateLimit()));
    }
}
```

### Error Usage

**`$this->api()->error(...)`** Is Responsible For Responding Errors Messages

Parameters  |Desc           | Accepts      | Defaults 
:-----------|:--------------|:-------------|:------------
$error      | Error Messages                        | `String, Array` | None
$code       | The Response Status Code              | `Integer`       | 500

```php
<?php

// Simple User Api Login Example
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\User;
use MoaAlaa\ApiResponder\ApiResponder;

class LoginController extends Controller
{
    use ApiResponder;

    public function login(Request $request)
    {
        if (! $token = auth()->attempt($request->only(['email', 'password']))) {
            return $this->api()->error($token, Response::HTTP_NOT_FOUND)
        }

        return $this->api()->response(['token' => $token]);
    }
}
```

### Safe Error Usage

**`$this->api()->safeError(...)`** When Validation Laravel Applications And Don't Want To Use `Validator Class` Manually And Use `$this->validate() Or request()->validate() Or $this->api()->validate()` Then This Method Is For You But You Must Put Your Code In A `Try Catch Block`, The Method Will Handle The Exception And Get The Message Event If It's Normal Or Custom Or Even Laravel Validation Errors ( The Most Method I Love :) ) 

> Note `$this->api()->validate()` Will Be Described Below 

Parameters  |Desc           | Accepts      | Defaults 
:-----------|:--------------|:-------------|:------------
$exception  | Exception Variable                    | Exception         | None
$code       | The Response Status Code              | `Integer`         | 500

```php
<?php

// Simple User Api Login Example
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\User;
use MoaAlaa\ApiResponder\ApiResponder;

class LoginController extends Controller
{
    use ApiResponder;

    public function login(Request $request)
    {
        try {
            $request->validate(...);

            if (! $token = auth()->attempt($request->only(['email', 'password']))) {
                return $this->api()->error($token, Response::HTTP_NOT_FOUND)
            }

            return $this->api()->response(['token' => $token]);
        
        } catch(\Exception $ex) {
            return $this->api()->safeError($ex);
        }

    }
}
```

### Validate Usage

**`$this->api()->validate(...Roles...)`** It Validate The Request Inputs And Return The Validated Inputs 
> Note It's Just A Wrapper On `request()->validate()` Function 

Parameters  |Desc           | Accepts      | Defaults 
:-----------|:--------------|:-------------|:------------
$attribute  | Array Of Roles For Validation  | `Array` | None

```php
<?php

// Simple User Api Login Example
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\User;
use MoaAlaa\ApiResponder\ApiResponder;

class LoginController extends Controller
{
    use ApiResponder;

    public function login(Request $request)
    {
        try {
            $validated = $this->api()->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (! $token = auth()->attempt($validated)) {
                return $this->api()->error($token, Response::HTTP_NOT_FOUND)
            }

            return $this->api()->response(['token' => $token]);
        
        } catch(\Exception $ex) {
            return $this->api()->safeError($ex);
        }

    }
}
```

### With Usage
**`$this->api()->with(...)->response(...)`** This Method Is Very Useful When Sending Additional Data With The Response Or To Make The Code More Readable

Parameters  |Desc           | Accepts      | Defaults 
:-----------|:--------------|:-------------|:------------
$additional | Array Of Additional Data     | `Array` | None

```php
<?php

namespace App\Http\Controllers;

use App\User;
use MoaAlaa\ApiResponder\ApiResponder;

class HomeController extends Controller
{
    use ApiResponder;

    public function index()
    {
        return $this->api()->with(['foo' => 'bar'])->response(User::first());
    }
}
```

You Can Also Make A Dynamic Naming

>  But It Still Had Some Issues Be Carful HWne Using It 

```php
<?php

namespace App\Http\Controllers;

use App\User;
use MoaAlaa\ApiResponder\ApiResponder;

class HomeController extends Controller
{
    use ApiResponder;

    public function index()
    {
        return $this->api()->withFoo('bar')->response(User::first()); // ->withFoo('bar') Will Convert To ['foo' => 'bar']
        return $this->api()->withFoo(['bar', 'baz'])->response(User::first()); // ->withFoo(['bar', 'baz']) Will Convert To ['foo' => ['bar', 'baz']]
    }
}
```

### Get Wrapping Usage
**`$this->api()->getWrapping`** This Method Gets The Wrapping Key String Around Your Data

Parameters  |Desc           | Accepts      | Defaults 
:-----------|:--------------|:-------------|:------------
None        | None          | None         | None

```php
<?php

namespace App\Http\Controllers;

use App\User;
use MoaAlaa\ApiResponder\ApiResponder;

class HomeController extends Controller
{
    use ApiResponder;

    public function index()
    {
        return $this->api()->getWrapping(); // "payload" is The Default
    }
}
```

### Set Wrapping Usage
Method `$this->api()->setWrapping('foo')` Used To Adjust The Wrapping Key String Around Data Response
You Can Define It In Any Service Provider Like `AppServiceProvider` To Apply The New Wrapping Key String In All Places You Used `$this->api()->response()` Method 
Also You Can Edit It On Runtime As Will

> Note It Have An Alias Called `$this->api()->wrapping('foo')`

Parameters  |Desc           | Accepts      | Defaults 
:-----------|:--------------|:-------------|:------------
$wrapping   | The Wrapping Key String Around Data Response Default Is "payload"  | `String` | None

`AppServiceProvider`
```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoaAlaa\ApiResponder\ApiResponder;

class AppServiceProvider extends ServiceProvider
{
    use ApiResponder;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->api()->setWrapping('foo');
        $this->api()->wrapping('foo');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
```

`HomeController`
```php
<?php

namespace App\Http\Controllers;

use App\User;
use MoaAlaa\ApiResponder\ApiResponder;

class HomeController extends Controller
{
    use ApiResponder;

    public function index()
    {
        // return $this->api()->getWrapping(); // "foo"

        return $this->api()->response(...); // Now All Response Data Will Be In "foo" Key String
        
        // Run-Time Set Wrapping
        return $this->api()->setWrapping('bar')->response(...); // Now All Response Data Will Be In "bar" Key String
        
        // Run-Time Set Wrapping
        return $this->api()->wrapping('baz')->response(...); // Now All Response Data Will Be In "baz" Key String
    }
}
```

## Example

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Post;
use App\Setting;
use MoaAlaa\ApiResponder\ApiResponder;

class HomeController extends Controller
{
    use ApiResponder;

    public function index()
    {
        $this->api()
        ->with(['posts'=> Post::all()])
        ->withSettings(Setting::all())
        ->wrapping('myData')
        ->response(User::paginate($this->api()->getPaginateLimit()), null, 200, function () {
            return ['token' => auth()->user()->token];
        });
    }
}
```

## Todo

- [ ] TestCases