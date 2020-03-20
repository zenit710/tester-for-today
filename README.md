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

* add absence table to avoid absent testers
* move db location, .data dir and mail credentials to .env
* write unit tests
* add helper for pretty console printing
* __uncomment notification send in TesterSwitch command!__