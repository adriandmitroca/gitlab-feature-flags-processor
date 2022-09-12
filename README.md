## GitLab Feature Flag CLI Processor

Small Laravel application that fetches and processes
feature flag details (including authors and date of creation)
that GitLab doesn't provide in their UI.

It's especially useful to find owners and find old, obsolete
feature flags easily, within few clicks of the mouse.

## Available Commands

- `php artisan app:stats` - fetches top creators, top removers and all active feature flags sorted from oldest
- `php artisan app:notification` - prepares nicely formatted Slack message with a report of all outstanding feature flags grouped by authors

## Get Started

(considering you have Lando installed)

1. `lando start`
1. `lando composer install`
2. `cp .env.example .env`
3. Fill in `GITLAB_ACCESS_TOKEN` and `GITLAB_PROJECT_ID` in your `.env` file

## Credits

- [Adrian Dmitroca](https://github.com/adriandmitroca)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
