# Prescriptor (job application task solution)
The task description is not included.
Read the [reasoning of solutions](RATIONALE.md), which explains choices made here.

You may use git releases, tags or branches to switch between development stages (part 1, 2 and 3).
You can read about the differences between releases and **test results** in [release notes](RELEASES.md).

## Requirements
0) docker (17.12.0+) and docker-compose (1.22.0+) must be installed
0) "make" should be installed

## Make commands

### bootstrap
```
make bootsrap
```
First of all run this command. This will build containers, create configs, download vendor libraries, create database and 
fill it with dummy data.

You may also use this command to recreate containers and database. 
It's recommended when you change values in [.makerc](.makerc).

### load-test
```
make load-test
```
This will run load test and show the stats. Alternatively you may change some parameters of the test.
See help of command:
```
docker-compose run --rm client bin/cli.php --help
```