# Changelog

All notable changes to `slash-dw/tagging-kit` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html)
starting from `v0.1.0` (during `v0.0.x` development period breaking changes may occur).

## [Unreleased]

### Added
- Initial package scaffolding (Faz 0)
- `composer.json`, `phpstan.neon.dist` (level 8, no baseline), `pint.json`, `phpunit.xml.dist`
- MIT License
- Public API contracts: `TagTypeContract`, `ActorContextContract`, `TenantContextContract`, `SharedTypesResolverContract`
- `TaggingKitServiceProvider` scaffolding (auto-discover)
- Config file `tagging-kit.php` with 14 flexibility points
- 7 event classes for host side-effect injection
- GitHub Actions CI workflow (`Pint`, `PHPStan`, `PHPUnit`)
- Pull request template
