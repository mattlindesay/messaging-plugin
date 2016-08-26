# messenger-plugin

Messaging plugin for October CMS.

## Alpha

This plugin is not ready to be used yet.

## Features

 * Allows users to send messages to each other. 
 * Messages are forwarded on to the user via email with an embedded message ID. 
 * The message ID allows the user to reply back on the email, which sends the message back to the original sender. (Requires postfix configuration to send to the mailrouter command).
 * Messages can also be sent from the "Admin" account to automate notifications.

## Requirements

 * Rainlab.User
 * ... others

Add an entry to composer.json

  php-mime-mail-parser/php-mime-mail-parser": "dev-master"

and update

  composer update

## Plugin settings

Sorry, no settings just yet. This will come later though.

