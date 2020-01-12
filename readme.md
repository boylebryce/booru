## About
This is a booru (a tag-based image board) that I'm building from scratch to learn about web development, version control, dependency management, etc. It currently uses PHP and MySQL with some JavaScript thrown in here and there. 

I'm currently in the process of converting this project into a front-end for an API that I'm working on to handle the behind-the-scenes work. As such, this project on its own doesn't actually do anything yet! You can look at the [v0.2 branch](https://github.com/boylebryce/booru/tree/v0.2) (how does versioning work?) for an older version that does work as an all-in-one basic image board with tagging and searching.

## Dependencies
This project uses [Composer](https://getcomposer.org/) to manage PHP dependencies. I'm following the advice that [the vendor directory should be excluded from the project's repository](https://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md), which means, at the very least, I should list the dependencies currently used here:
- [guzzlehttp/guzzle](https://packagist.org/packages/guzzlehttp/guzzle) v6.5.2
