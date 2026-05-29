## Summary

<!-- What does this PR change? -->

## Checklist

- [ ] `composer run-script test` passes
- [ ] `composer run-script lint` (Pint `--test`) passes
- [ ] `composer run-script analyse` (PHPStan / Larastan, level 8) passes
- [ ] `CHANGELOG.md` updated (if user-facing or API-impacting changes)
- [ ] If deprecating public API: `@deprecated` PHPDoc + CHANGELOG `Deprecated` + runtime warning (see RULES `05-deprecation`)
- [ ] If outward behavior / public API changed, related contract/guard tests updated or added
