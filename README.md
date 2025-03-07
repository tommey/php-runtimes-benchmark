# PHP runtimes benchmark

The repo contains the Symfony 7 application skeleton to be run in different runtimes:

- Apache(prefork mode) + mod_php
- Apache + PHP-FPM
- Nginx + PHP-FPM
- Nginx Unit
- Roadrunner
- Nginx + Roadrunner
- FrankenPHP
- FrankenPHP (worker mode)
- Swoole
- Adapterman
- Caddy + PHP-FPM

It also contains a Symfony 6 application skeleton to be run in different runtimes:

- ReactPHP

## URLs

- http://localhost/
- http://localhost/?firstName=Randomlfirstname&lastName=Randomlastname
- http://localhost/phpinfo
- http://symfony7site/
- http://symfony7site/?firstName=Randomlfirstname&lastName=Randomlastname
- http://symfony7site/phpinfo

## Run load tests

Load tests are executed inside docker containers, the runtimes are defined in their own compose file,
while the load test tools as well, but they are on the same network.

**Exactly one runtime must be started first!**

See `make help` for default values used for the testing as well as all available runtimes and targets.

CLI output is saved in log files named by runtime name, variable values and timestamp in [log](log).

### ALL

Run testing for all runtimes:

```shell
make all tool=k6 vus=100 duration=10 busy=yes # example, define vars as needed
```

### Bombardier

- https://pkg.go.dev/github.com/codesenberg/bombardier
- https://hub.docker.com/r/alpine/bombardier

```shell
make bb
```

### Apache HTTP server benchmarking tool

- https://httpd.apache.org/docs/2.4/programs/ab.html

```shell
make ab
```

### K6 (by Grafana Labs)

- https://k6.io/docs/
- https://k6.io/docs/using-k6/metrics/reference/

See js scripts: [k6_bench.js](001_symfony7_wo_db/testing-tools/k6_bench.js).

#### Local

Reporting to netdata, available at http://localhost:19999
```shell
make k6
```

#### Cloud

Local execution, but Grafana Cloud reporting.

Requires [.env.k6-cloud](001_symfony7_wo_db/testing-tools/.env.k6-cloud.dist), copy the .dist file and modify.

```shell
make k6-cloud
```

## Runtimes

All runtimes have 4 targets:
```shell
make start/% # Start the runtime with build and recreate
make stop/%  # Stop and destroy the runtime containers
make shell/% # Step into the running container

make bench/% # Run load tests
```

Bench will start/% the runtime, wait 3 seconds, check with curl if bench_url responds,
then execute the benchmark tool, finally stop/% the runtime.

### 001: Apache(prefork mode) + mod_php

- http://localhost/server-status


```shell
make bench/001_apache_modphp
```

### 002: Apache + PHP-FPM

- http://localhost/apache-status
- http://localhost/fpm-status?html&full


```shell
make bench/002_apache_phpfpm
```

### 003: Nginx + PHP-FPM

- http://localhost/fpm-status?html&full

To calculate PHP-FPM pm.max_children, the following formula was used:
```
pm.max_children = Memory available to container / Memory consumed by 1 process
```

Memory consumed by 1 process:
```shell
ps --no-headers -o "rss,cmd" -C php-fpm | awk '{ sum+=$1 } END { printf ("%d%s\n", sum/NR/1024,"Mb") }'
```

```shell
make bench/003_nginx_phpfpm
```

### 004: Nginx Unit + PHP Language module

- https://unit.nginx.org/configuration/#php

```shell
make bench/004_nginx_unit
```

### 005: Roadrunner

The symfony/runtime component is used

- https://roadrunner.dev/
- https://github.com/baldinof/roadrunner-bundle

```shell
make bench/005_roadrunner
```

### 006: Nginx + Roadrunner(fcgi mode)

The symfony/runtime component is used

- https://roadrunner.dev/docs/app-server-nginx-with-rr/current/en
- https://github.com/baldinof/roadrunner-bundle

```shell
make bench/006_nginx_roadrunner
```

### 007: Frankenphp

The symfony/runtime component is used

- https://frankenphp.dev/docs/

```shell
make bench/007_frankenphp
make stop/runtime/007-frankenphp
make rebuild/runtime/007-frankenphp
make down/runtime/007-frankenphp
make shell/runtime/007-frankenphp
```

### 008: Frankenphp (workermode)

The symfony/runtime component is used

- https://frankenphp.dev/docs/worker/

```shell
make bench/008_frankenphp_workermode
```

### Issues
- FrankenPHP can't start with production version of php.ini, which is provided with official PHP image


### 009: Swoole

The symfony/runtime component is used

- https://github.com/php-runtime/swoole
- https://swoole.com/docs

```shell
make bench/009_swoole
```


### 010: Adapterman

The symfony/runtime component is used

- https://github.com/joanhey/AdapterMan

```shell
make bench/010_adapterman
```


### 011: Caddy + PHP-FPM

- https://caddyserver.com/

```shell
make bench/011_caddy_phpfpm
```
