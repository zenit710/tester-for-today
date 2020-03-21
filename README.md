# Tester

Get new tester for today and send notification to the team.

## Run

```bash
docker image build -t tester-app .
docker run --name=tester-app -it -v `pwd`:/var/app/tester tester-app sh
```

then install composer packages after first run

```bash
composer install
```

### TODO

* move mail credentials to .env
* add absence table to avoid absent testers
* make interfaces for injected classes
* write unit tests
* add helper for pretty console printing
* split src directories to packages
* __uncomment notification send in TesterSwitch command!__