### Production environment

The application has been deployed on Heroku, being available at the following url:

https://yoda-chat-front.herokuapp.com/

### Dev environment.

The environment has been mounted on 3 docker containers. 2 for the backend (nginx and php) and a node container (to use VueJS) for the frontend.

To set up a dev environment just need to run `make build` in the root folder.

Api keys are provided in api/.env.

### Makefile.

A Makefile file is provided with the following rules:

```
 make build # Build containers and run them
 make tests # Run tests
 make code-analyse # Run phpStan with level 8
 make help # Show all available rules
```

### Testing.

7 tests are included that can be run using the make tests command

### Code Analyser.

In order to detect possible errors in the code, PHPStan(https://phpstan.org/) code analyser has been included.
It can be executed by running `docker-compose exec php vendor/bin/phpstan analyse -l 8 src tests` or `make code-analyse`.
This tool has 9 levels of strictness. By default, we run it with the higher, but you can modify it changing l param.