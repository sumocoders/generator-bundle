# SumoCoders GeneratorBundle

The GeneratorBundle will help you with some boilerplate code by generating Commands, Command handlers 
and service configuration based on a given entity.  

# License

This bundle is under the MIT license. See the complete license at: [LICENSE](LICENSE)

# About

GeneratorBundle is a bundle created by [SumoCoders](https://sumocoders.be) and is intended to be used with the Framework
or with ForkCMS. 

# Usage 

## Symfony

`app/console sumocoders:generate --entityFQN 'AppBundle\Entity\Post`

## ForkCMS

`app/console sumocoders:generate --entityFQN 'Backend\Modules\MyModule\Entity\Post`

# Known Issues

- Class names when not generated with FQN are prefixed with a backslash.

# Other issues?

Feel free to add an Issue on Github, or even better create a Pull Request.
