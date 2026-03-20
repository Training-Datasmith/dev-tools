# Architecture: dev-tools

## Purpose

A Shopware meta-package that aggregates common development and testing dependencies into a single Composer require. Installing this package pulls in a curated set of libraries needed for local development, database seeding, and test infrastructure — avoiding repetitive `require-dev` declarations across Shopware modules.

## Directory Structure

```
composer.json   — Sole source of truth; lists all aggregated dependencies
README.md       — Brief usage instructions
```

No PHP source code — this is a pure dependency aggregation package.

## Aggregated Dependencies

| Package | Purpose |
|---|---|
| `doctrine/sql-formatter` | Pretty-printing SQL for debugging |
| `fakerphp/faker` | Fake data generation for fixtures and tests |
| `maltyxx/images-generator` | Placeholder image generation for fixtures |
| `mbezhanov/faker-provider-collection` | Extended Faker providers |
| `symfony/doctrine-bridge` | Symfony–Doctrine integration layer |
| `symfony/web-profiler-bundle` | Symfony toolbar for dev environment inspection |
| `phpunit/phpunit` | Test runner |
| `symfony/browser-kit` | Headless HTTP client for functional tests |

## Key Design Decisions

- **Single-package install** — projects run `composer require --dev shopware/dev-tools` to get the full development stack without enumerating individual packages.
- **Flexible version constraints** — Symfony constraints support `^5.4 | ^6 | ^7` to remain compatible across Shopware's range of supported Symfony versions.

## Extension Points

None — this package has no code. Projects extend it by adding their own `require-dev` entries alongside it.
