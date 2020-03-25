# Tester

Get new tester for today and send notification to the team.

## Run

1. Copy `.env.dist` to `.env`, uncomment and fill variables.
2. Run commands below

```bash
docker image build -t tester-app .
docker run -it -v `pwd`:/var/app/tester tester-app sh
```

then install composer packages after first run

```bash
composer install
```

and run migrations

```bash
php app.php migration:run
```

### Tests

```bash
# just tests
./vendor/phpunit/phpunit/phpunit tests
# html coverage report
./vendor/phpunit/phpunit/phpunit --coverage-html coverage/ tests
# text coverage report in CLI
./vendor/phpunit/phpunit/phpunit --coverage-text tests
```

## Kernel

`src/AppKernel.php` is the central unit of application. All services, parameters
and commands are registered there.

### Services

In whole application there is a lot of places when we need logger, repositories etc.
We can create new service instance and register it in Kernel to share provided functionality.

`registerService(string $serviceName, $service)` allows to register new service
and use inside of application.

`getService(string $serviceName)` returns service if registered, null otherwise.

### Parameters

Sometimes we need to use parameters inside services, e.g. email credentials.
We can add parameters to Kernel and use them inside application.

`addParameter(string $name, value)` - adds/overrides parameter registered by name

`getParameter(string $name)` - returns registered parameter, null otherwise.

`hasParameter(string $name)` - return true if parameter exist, false otherwise.

### Commands

Our application runs in command line, so we need commands to manage resources.
Commands can be registered and handled in CommandBus which can be accessed by Kernel.

Commands are registered in Kernel method called `bootstrapCommandBus()`.

#### Creating new command

New command has to extend `AbstractCommand`. `AbstractCommand` gives us some useful methods:

`mapArgs(array $args)` - parses command args

`validateArgs(array $requiredArgs)` - validates are all required parameters set

`getArg(string $name)` - return value of command argument

`hasArg(string $name)` - checks is arg set

`hasHelpArg()` - checks is `--help` arg set 

Each command has to have unique `protected $commandName = 'COMMAND:NAME'` property.
This property lets us identify and call command.

#### Registered commands list

To get all registered commands run `php app.php --help`. You can get help for each command
by runing `php app.php [command:name] --help`

## Migrations

Application uses migrations to change database scheme without recreating database.
Migrations are PHP classes extending `Migration`. All migrations are placed in
`src/Migration/Migrations`.

### Creating new migration

New migration should has name built by concatenation of:
- prefix: Migration_
- current datetime in format: yyyymmddhhiiss
- short description (only letters and underscores)

For example: `Migration_20200320210200_create_schema`

Each migration has to implement methods:

`up()` - returns SQL which has to be executed to provide new functionality

`down()` - returns SQL to revert changes from `up()`

`getName()` - returns unique string identifying migration. It can be same as class name.

### Running migrations

We can run all migrations not applied yet using `php app.php migration:run` command.

If we want to run only one migration we can use `php app.php migration:run --name:Migration_name`.
Migration from class `Migration_name` migration will be applied in result.

If we want to revert changes applied in migration we can use `--revert` flag.
It can be used with single migration and with all migrations too.

**Attention!** `php app.php migration:run --revert` will revert **ALL** applied migrations!
It can cause data loss if database changes (e.g. inserts) were made in affected tables!


## TODO

* make interfaces for injected classes
* write unit tests
* add helper for pretty console printing
* move from Sqlite lib to PDO lib
* create instantiator for AppKernel services dependencies
* split src directories to packages
* __uncomment notification send in TesterSwitch command!__